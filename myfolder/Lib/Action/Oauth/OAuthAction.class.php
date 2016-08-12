<?php

class OAuthAction extends Action {

    protected $_error = 0;
    protected $_errorMsg = '';
    protected $_apiKey;
    protected $_apiSecret;
    protected $_appId;
    protected $_appSecret;
    protected $_logT;
    //白名单
    protected $_DomainWhite = array('oauth.hysci.com.cn');
    public function __construct() {
        parent::__construct();
        $this->_logT = 'm=' . __ACTION_METHOD__ . ';api_key=';
    }

    /**
     * 来源签名验证
     */
    protected function _authAndInitData($isAuth = true) {
        $timestamp = HttpRequest::get('timestamp');
        $sig = HttpRequest::get('sig');
        if (true == $isAuth) {
            if (!$timestamp || !$sig) {
                $this->_setError(1);
                Logger::error($this->_logT . ' auth params(sig|timestamp) missing :', $_GET);
                return false;
            }
        }

        $appInfo['app_id'] = C('APP_ID');
        $appInfo['app_secret'] = C('APP_SECRET');
        if (!$appInfo) {
            return false;
        }
        if($isAuth){
            //验证权限,2015-4-28取消验证
//            $checkInfo = $this->_checkAuth($appInfo);
//            if (!$checkInfo) {
//                return false;
//            }
        }
        $this->_appId = $appInfo['app_id'];
        $this->_appSecret = $appInfo['app_secret'];

        if (true == $isAuth) {
            $authData = $_GET;
            $authData [ThirdPartyReqParams::APP_SECRET] = $appInfo ['app_secret'];
            unset($authData [ThirdPartyReqParams::SIG]);
            $newSig = Helper::md5Sign($authData, false, false, '');
            if ($newSig != $sig) {
                $this->_setError(1);
                Logger::error($this->_logT . ' auth sig error :', $_GET);
                return false;
            }
        }
        return true;
    }
    /**
     * 检测权限
     * @date 2015-4-9
     * @param type $data
     */
    protected function _checkAuth($data) {
        $isSub = isset($data['oauth_domain']);
        //2015-3-31 检测域名,时效
        if($isSub){
            if($data['end_time']+86400<time()){
                $this->_setError(1);
                return false;
            }
        }
        $domainList = $this->_DomainWhite;
        $setList = $isSub?$data['oauth_domain']:$data['app_domain'];
        $hasList = json_decode($setList,1);
        if(is_array($hasList)){
            $domainList = array_merge($hasList,$domainList);
        }
        //回调域名必须在白名单里
        $url = parse_url($this->getParam('url'));
        $isExist = false;
        foreach ($domainList as $d){
            if(strpos($url['host'],  preg_replace('/http(s)?:\/\//i', '', $d))!==FALSE){
                $isExist = true;
                break;
            }
        }
        //非法域名
        if(!$isExist){
            Logger::error("页面授权非法域名".$url['host']);
            $this->_setError(3);
            return false;
        }
        return true;
    }
    
    protected function _setError($errno) {
        $this->_error = $errno;
        switch ($errno) {
            case 1:
                $this->_errorMsg = '无效的请求';
                break;
            case 2:
                $this->_errorMsg = '请求异常请重试';
                break;
            case 3:
                $this->_errorMsg = '无效的请求来源';
                break;
        }
    }

    //从缓存中获取数据
    public function getBrowserCache($responseType) {
    	$skey = 'wx_abc_oauth2.0_' . $responseType . $this->_appId;
        $key = md5($skey);
        $dataStr = @$_COOKIE[$key];
        //Logger::info($this->_logT.' cacheget key:'.$key.' s_key:'.$skey.' value:'.$dataStr);
        if (!$dataStr) {
            return null;
        }
        $cData = @json_decode(base64_decode($dataStr), true);
        unset($dataStr);
        if (!$cData || empty($cData['openid'])) {
        	Logger::info($this->_logT.' 解析cache 数据失败  '.$dataStr);
            return null;
        }
        return $cData;
    }

    //从缓存中获取数据
    public function setBrowserCache($responseType, $data) {
        if (!$data || !@$data['openid']) {
            return;
        }
        $value = base64_encode(json_encode($data));
        $skey = 'wx_abc_oauth2.0_' . $responseType . $this->_appId;
        $key = md5($skey);
        $set = @setcookie($key, $value, time() + 600);
        //Logger::info($this->_logT.' setBrowserCache key:'.$key.' skey:'.$skey.' value:'.$value, $data);
        if (!$set) {
        	Logger::debug($this->_logT.' setBrowserCache error key:'.$key, $data);
        }
    }

    //从cache数据中生成目标url参数数据
    public function createCallbackDataFromCache($responseType) {
        $cData = $this->getBrowserCache($responseType);
        if (!$cData) {
            return null;
        }
        $params = null;
        if ('openid' == $responseType) {
            if (@$cData['openid']) {
                $params['openid'] = $cData['openid'];
            }
        } else if (in_array($responseType, array('userinfo', 'userinfoall'))) {
            if (@$cData['openid']) {
                $params = $cData;
            }
        }
        if (empty($params)) {
            return null;
        }
        $params = array_merge(array(
            ThirdPartyReqParams::APP_ID => $this->_appId,
            ThirdPartyReqParams::APP_SECRET => $this->_appSecret,
            ThirdPartyReqParams::TIMESTAMP => time(),
            ), $params);
        $params[ThirdPartyReqParams::SIG] = Helper::md5Sign($params);
        unset($params[ThirdPartyReqParams::APP_SECRET]);
        return $params;
    }

}
