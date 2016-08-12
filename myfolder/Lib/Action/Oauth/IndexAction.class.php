<?php
class IndexAction extends OAuthAction
{	
	/**
	 * 直接跳转页面获取openid,中转入口
	 */
	public function index()
	{		
		Logger::error("-----------test oauth log");		
		$url = trim($this->getParam('url', '', false, 'all'));
		$scope = strtolower($this->getParam('scope', ''));
		$responseType = strtolower($this->getParam('response_type', ''));
		Logger::info("param",$this->getParam());
		if (!$this->_authAndInitData()) {
			//error
			$this->_displayError($this->_errorMsg);
		}
		if (!in_array($scope, array('snsapi_base', 'snsapi_userinfo'))) {
			$scope = 'snsapi_base';
		}
		if (!in_array($responseType, array('code', 'openid', 'userinfo','userinfoall'))) {
			$responseType = 'openid';
		}
		if($scope == 'snsapi_base' && !in_array($responseType, array('code', 'openid'))){
			$responseType = 'openid';
		}
		//优先cache 数据
		$cacheData = $this->createCallbackDataFromCache($responseType);
		if ($cacheData) {
			$targetUrl = resetUrl($url, $cacheData);
			Logger::debug($this->_logT.' target_url:'.$targetUrl);
			Factory::getSystemLog()->push('cached', 1);
			header("Location: ".$targetUrl);
			myExit();
		} else {
			Factory::getSystemLog()->push('cached', 0);
		}
		$redirectParam = array(
				ThirdPartyReqParams::APP_ID => $this->_appId,
				'url' => $url,
				'response_type' => $responseType
		);
		$redirectUri = resetUrl(C("OAUTH_REDIRET_URI"), $redirectParam);
		$replace = array(
				'APP_ID' => $this->_appId,
				'REDIRET_URI' => urlencode($redirectUri),
				'SCOPE' => $scope,
				'STATE' => C("OAUTH_STATE"),
		);
		$oauthUri = str_replace(array_keys($replace), array_values($replace), C('OAUTH_WX_AUTH_PATH'));
		Logger::debug($this->_logT.' wx_oauth_url:'.$oauthUri);
		header("Location: ".$oauthUri);
	}
	
	/**
	 * 根据OAuth2.0接口获取用户openid
	 */
	public function callback () {
		$code = trim($this->getParam('code', ''));
		$url = trim($this->getParam('url', '', false, 'all'));
		$scope = strtolower($this->getParam('scope', ''));
		$responseType = strtolower($this->getParam('response_type', ''));
		
		if (!in_array($scope, array('snsapi_base', 'snsapi_userinfo'))) {
			$scope = 'snsapi_base';
		}
		if (!in_array($responseType, array('code', 'openid', 'userinfo','userinfoall'))) {
			$responseType = 'openid';
		}
		
		if (!$this->_authAndInitData(false)) {
			//error
			$this->_displayError($this->_errorMsg);
		}
		if (!$code || !$url) {
			Logger::error($this->_logT.'缺少必要参数请重试http_param_error:',$this->getParam());
			$this->_displayError('缺少必要参数请重试');
		}
		if ('authdeny' == $code) {
			Logger::error($this->_logT.'用户已取消授权:');
			$this->_displayError('已取消授权');
		}
		$params = array(
				ThirdPartyReqParams::APP_ID => $this->_appId,
				ThirdPartyReqParams::APP_SECRET => $this->_appSecret,
				ThirdPartyReqParams::TIMESTAMP => time(),
				);
		if ('code' == $responseType) {
			$params['code'] = $code;
		} else {
			$st = microtime(true);
			$result = $this->_getCodeResult($code, $responseType);
			Factory::getSystemLog()->push('code_to_token_t', sprintf("%.4f", (microtime(true) - $st) * 1000));
			if (!$result) {
				$this->_displayError('通信异常请重试');
			}
			//设置浏览器缓存
			$this->setBrowserCache($responseType, $result);
			//$params = array_merge($params, $result);  //不需要那么多参数进行签名
			$params['openid'] = $result['openid'];
		}
		$params[ThirdPartyReqParams::SIG] = Helper::md5Sign($params);
		unset($params[ThirdPartyReqParams::APP_SECRET]);
		$targetUrl = resetUrl($url, $params);
		Logger::debug($this->_logT.' target_url:'.$targetUrl);
		header("Location: ".$targetUrl);
	}
	/**
	 *
	 * @param string $code
	 * @return boolean|Ambigous <WX_OAuthToken, mixed>
	 */
	protected function _getCodeResult ($code, $responseType) {
		$oauthApi = WeiXinApiCore::getOAuthClient($this->_appId, $this->_appSecret);
		$oauthToken = $oauthApi->getAccessToken($code);
		if (!$oauthToken) {
			Logger::error($this->_logT.' code转换token失败;error:'
					.$oauthApi->getErrorCode().':'.$oauthApi->getErrorMessage());
			return false;
		}
		$data = array(
				'openid' => $oauthToken->openId
		);
		if ('openid' != $responseType) {
			$userInfo = $oauthApi->getUserInfo($oauthToken->openId, $oauthToken->accessToken);
			if ($userInfo) {
				if ('userinfo' == $responseType) {
					$data['nickname'] = $userInfo->nickname;
					$data['headimgurl'] = $userInfo->headimgurl;
				} else if ('userinfoall' == $responseType) {
					$data['nickname'] = $userInfo->nickname;
					$data['headimgurl'] = $userInfo->headimgurl;
					$data['sex'] = $userInfo->sex;
					$data['country'] = $userInfo->country;
					$data['province'] = $userInfo->province;
					$data['city'] = $userInfo->city;
				}
			}
		}
		
		return $data;
	}
	
	protected function _displayError ($message) {
		include LIB_PATH . "/Tpl/Common/h5error.php";
		myExit();
	}
}