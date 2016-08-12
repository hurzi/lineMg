<?php 

/**
 * 检查openid并授权回原地址
 * @param string $scope
 */
function checkAndOauth($callbackUrl=null,$scope='snsapi_base', $code = false){
	$result = checkOauthParam();
	if(!$result){		
		oauthBack($callbackUrl,$scope,$code);
	}
	return true;
}

function checkOauthParam(){
	$timestamp = HttpRequest::get('timestamp',null);
	$sig = HttpRequest::get('sig',null);
	$appid = HttpRequest::get('app_id',null);
	$openid = HttpRequest::get('openid',null);
	$code = HttpRequest::get('code');
	if(!$openid || !$timestamp || !$sig || !$appid){
		return false;
	}
	$params = array(
			ThirdPartyReqParams::APP_ID => $appid,
			ThirdPartyReqParams::APP_SECRET => C('APP_SECRET'),
			ThirdPartyReqParams::TIMESTAMP => $timestamp,
	);
	if(!$code){
		$params[ThirdPartyReqParams::OPEN_ID] = $openid;
	}else{
		$params['code'] = $code;
	}
	$tmpsig = Helper::md5Sign($params);
	if($tmpsig != $sig){
		return false;
	}
	return true;
}

/**
 * 授权后跳转回原页
 * @param string $scope
 */
function oauthBack($callbackUrl = null,$scope='snsapi_base', $code = false){	
	if(!$callbackUrl) {
		$getParam = $_GET;
		unset($getParam [ThirdPartyReqParams::SIG]);
		unset($getParam [ThirdPartyReqParams::OPEN_ID]);
		unset($getParam [ThirdPartyReqParams::APP_ID]);
		unset($getParam [ThirdPartyReqParams::TIMESTAMP]);
		$jumpurl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
		$callbackUrl = resetUrl($jumpurl,$getParam);
	}
	$url = getAuthUrl($callbackUrl, $scope);
	header("Location: $url");
}

/**
 * 授权地址
 * @param unknown $url  授权跳转回来的地址
 * @param unknown $scope 授权范围
 * @param string $code  是否只返回code
 * @return Ambigous <unknown, string>
 */
function getAuthUrl($url, $scope, $code = false) {
        //auth2.0转换URL链接
        //$oauthurl = C("OAUTH_BASE_URL");
        $params = array(
            ThirdPartyReqParams::APP_ID => C('APP_ID'),
            ThirdPartyReqParams::APP_SECRET => C('APP_SECRET'),
            'url' => $url,
            ThirdPartyReqParams::TIMESTAMP => '1459160779',
        );
        $oauthurl = C("OAUTH_URI");
        if ($scope && in_array($scope, array('snsapi_userinfo', 'snsapi_base'))) {
            $params['scope'] = $scope;
        } else {
            $params['scope'] = 'snsapi_base';
        }
        //userinfo,兼容code
        $params['response_type'] = 'userinfoall';
        $params['sig'] = Helper::md5Sign($params);
        unset($params[ThirdPartyReqParams::APP_SECRET]);
       	$resetUrl = resetUrl($oauthurl, $params);
        return $resetUrl;
}



/**
 * 截取字符串 参考 discuz
 */
function cutstr_dis($string, $length, $dot = '...')
{
	if (strlen($string) <= $length) {
		return $string;
	}

	$pre = chr(1);
	$end = chr(1);
	$string = str_replace(array (
			'&amp;',
			'&quot;',
			'&lt;',
			'&gt;'
	), array (
			$pre . '&' . $end,
			$pre . '"' . $end,
			$pre . '<' . $end,
			$pre . '>' . $end
	), $string);

	$strcut = '';
	if (strtolower('utf-8') == 'utf-8') {

		$n = $tn = $noc = 0;
		while ( $n < strlen($string) ) {

			$t = ord($string[$n]);
			if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
				$tn = 1;
				$n ++;
				$noc ++;
			} elseif (194 <= $t && $t <= 223) {
				$tn = 2;
				$n += 2;
				$noc += 2;
			} elseif (224 <= $t && $t <= 239) {
				$tn = 3;
				$n += 3;
				$noc += 2;
			} elseif (240 <= $t && $t <= 247) {
				$tn = 4;
				$n += 4;
				$noc += 2;
			} elseif (248 <= $t && $t <= 251) {
				$tn = 5;
				$n += 5;
				$noc += 2;
			} elseif ($t == 252 || $t == 253) {
				$tn = 6;
				$n += 6;
				$noc += 2;
			} else {
				$n ++;
			}

			if ($noc >= $length) {
				break;
			}
		}
		if ($noc > $length) {
			$n -= $tn;
		}

		$strcut = substr($string, 0, $n);
	} else {
		for ($i = 0; $i < $length; $i ++) {
			$strcut .= ord($string[$i]) > 127 ? $string[$i] . $string[++ $i] : $string[$i];
		}
	}

	$strcut = str_replace(array (
			$pre . '&' . $end,
			$pre . '"' . $end,
			$pre . '<' . $end,
			$pre . '>' . $end
	), array (
			'&amp;',
			'&quot;',
			'&lt;',
			'&gt;'
	), $strcut);

	$pos = strrpos($strcut, chr(1));
	if ($pos !== false) {
		$strcut = substr($strcut, 0, $pos);
	}
	return $strcut . $dot;
}


/**
 * 复制数组里的元素
 * @param unknown $sourceArr
 * @param unknown $targetArr
 * @param unknown $key
 * @param string $copyNull
 * @param string $nullDefaultValue
 */
function copyArrayItem($sourceArr,&$targetArr,$key,$copyNoKey = false,$nullDefaultValue = null){
	if(!is_array($sourceArr) || !is_array($targetArr)){
		return;
	}
	if(isset($sourceArr[$key])){
		$targetArr[$key] = isnull($sourceArr[$key],$nullDefaultValue);
	}else{
		if($copyNoKey){
			$targetArr[$key] = $nullDefaultValue;
		}
	}
}