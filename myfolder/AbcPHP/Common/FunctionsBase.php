<?php
/**
 * 全局共用函数
 *
 * @author paizhang  2012-05-10
 */
//获取配置信息
function C ($name = null, $value = null) {
	if ($value !== null) {
		return AbcPHPConfig::set($name, $value);
	}
	return AbcPHPConfig::get($name);
}
//加载model
function M ($model) {
	return loadModel($model);
}
//加载action
function A ($action) {
	return loadAction($action);
}

/**
 * 获取ip地址
 */
/*function getIp() {
	if (getenv ( 'HTTP_CLIENT_IP' )) {
		$ip = getenv ( 'HTTP_CLIENT_IP' );
	} elseif (getenv ( 'HTTP_X_FORWARDED_FOR' )) {
		$ip = getenv ( 'HTTP_X_FORWARDED_FOR' );
	} elseif (getenv ( 'HTTP_X_FORWARDED' )) {
		$ip = getenv ( 'HTTP_X_FORWARDED' );
	} elseif (getenv ( 'HTTP_FORWARDED_FOR' )) {
		$ip = getenv ( 'HTTP_FORWARDED_FOR' );
	} elseif (getenv ( 'HTTP_FORWARDED' )) {
		$ip = getenv ( 'HTTP_FORWARDED' );
	} else {
		$ip = @$_SERVER ['REMOTE_ADDR'];
	}
	return empty ( $ip ) ? '0' : $ip;
}
*/
function getIp(){
	$realip = '';
	$unknown = '';
	if (isset($_SERVER)){
		if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']) && strcasecmp($_SERVER['HTTP_X_FORWARDED_FOR'], $unknown)){
			$arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
			foreach($arr as $ip){
				$ip = trim($ip);
				if ($ip != 'unknown'){
					$realip = $ip;
					break;
				}
			}
		}else if(isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP']) && strcasecmp($_SERVER['HTTP_CLIENT_IP'], $unknown)){
			$realip = $_SERVER['HTTP_CLIENT_IP'];
		}else if(isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR']) && strcasecmp($_SERVER['REMOTE_ADDR'], $unknown)){
			$realip = $_SERVER['REMOTE_ADDR'];
		}else{
			$realip = $unknown;
		}
	}else{
		if(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), $unknown)){
			$realip = getenv("HTTP_X_FORWARDED_FOR");
		}else if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), $unknown)){
			$realip = getenv("HTTP_CLIENT_IP");
		}else if(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), $unknown)){
			$realip = getenv("REMOTE_ADDR");
		}else{
			$realip = $unknown;
		}
	}
	$realip = preg_match('/[\d\.]{7,15}/', $realip, $matches) ? $matches[0] : $unknown;
	return $realip;
}

/**
 * 转义字符
 *
 * @param mixed $var
 */
function faddslashes($var) {
	if (is_array ( $var )) {
		foreach ( $var as $k => $v ) {
			$var [$k] = faddslashes ( $v );
		}
	} else {
		$var = addslashes ( $var );
	}
	return $var;
}
/**
 * 去除转义标签
 * @param mixed $var
 * @return mixed
 */
function tripslashes ($var) {
	if (is_array ( $var )) {
		foreach ( $var as $k => $v ) {
			$var [$k] = tripslashes( $v );
		}
	} else {
		$var = stripcslashes ( $var );
	}
	return $var;
}

/**
 * 转义html字符
 *
 * @param string|array $var
 */
function fhtmlspecialchars($var) {
	if (is_array ( $var )) {
		foreach ( $var as $k => $v ) {
			$var [$k] = fhtmlspecialchars ( $v );
		}
	} else if (is_string ( $var )) {
		$var = htmlspecialchars ( $var, ENT_COMPAT, 'UTF-8' );
	}
	return $var;
}

/**
 * 过滤html标签.
 *
 * @param string $var target string
 * @param string $tags 允许保留到标签,all 为去全部
 */
function fstripTags($var, $tags = 'all') {
	$tags = strval ( $tags );
	if ($tags !== 'all') {
		if (is_array ( $var )) {
			foreach ( $var as $k => $v ) {
				$var [$k] = fstripTags ( $v );
			}
		} else if (is_string ( $var )) {
			$var = strip_tags ( $var, $tags );
		}
	}
	return $var;
}

/**
 * 加载action
 *
 * @param string $action package 格式 从Action目录开始使用 "."分割
 */
function loadAction($action) {
	if (empty ( $action ))
		return null;
	static $actions = array ();
	if (isset ( $actions [$action] )) {
		return $actions [$action];
	}
	$actArr = explode ( '.', $action );
	if (count ( $actArr ) > 1) {
		$actionName = $actArr [count ( $actArr ) - 1] . 'Action';
	} else {
		$actionName = $action . 'Action';
	}
	$file = LIB_PATH . '/Action/' . str_replace ( '.', '/', $action ) . 'Action.class.php';
	if (! file_exists ( $file )) {
		if (class_exists('Logger')) {
			Logger::error ( "action file not exist : " . $file );
		}
		return null;
	}
	include_once ($file);
	$actions [$action] = new $actionName ();
	return $actions [$action];
}

/**
 * 加载model
 *
 * @param string $model	package 格式 从Model目录开始使用 "."分割
 */
function loadModel($model,$appendGroupName = true) {
	if (empty ( $model ))
		return null;
	if ($appendGroupName && AbcPHPConfig::getAppGroup() && stripos($model, AbcPHPConfig::getAppGroup())!==0) {
		$model = AbcPHPConfig::getAppGroup() . '.' . $model;
	}
	static $models = array ();
	if (isset ( $models [$model] )) {
		return $models [$model];
	}
	$modelArr = explode ( '.', $model );
	if (count ( $modelArr ) > 1) {
		$modelName = $modelArr [count ( $modelArr ) - 1] . 'Model';
	} else {
		$modelName = $model . 'Model';
	}
	$file = LIB_PATH . '/Model/' . str_replace ( '.', '/', $model ) . 'Model.class.php';
	if (! file_exists ( $file ) && class_exists('Logger')) {
		Logger::error ( "model file not exist : " . $file );
		return null;
	}
	include_once ($file);
	$models [$model] = new $modelName ();
	return $models [$model];
}

/**
 * 获取当前堆栈.
 */
function getBacktrace() {
	$traces = debug_backTrace ();
	$str = "\n\nback trace:";
	for($i = 1; $i < count ( $traces ); $i ++) {
		$trace = $traces [$i];
		$class = @$trace ['class'] ? @$trace ['class'] . @$trace ['type'] : '';
		$str .= "\n##$i " . @$trace ['file'] . " (" . @$trace ['line'] . "), call function $class" . @$trace ['function'] . "(";
		if ($i > 1) {
			foreach ( @$trace ['args'] as $arg ) {
				if (is_array ( $arg )) {
					$str .= "Array, ";
				} else if (is_Object ( $arg )) {
					$str .= "Object, ";
				} else if (is_bool ( $arg )) {
					$str .= $arg ? 'true, ' : 'false, ';
				} else {
					$str .= "$arg, ";
				}
			}
		}
		$str .= ");";
	}
	return $str;
}

/**
 * 加载某个目录下的php文件
 */
function loadDir($dir) {
	$dir = realpath ( $dir );
	$h = @opendir ( $dir );
	if (! $h) {
		return;
	}
	while ( false !== ($file = readdir ( $h )) ) {
		if (substr ( $file, 0, 1 ) == '.' || strtolower ( substr ( $file, - 3, 3 ) ) != 'php') {
			continue;
		}

		$realFile = $dir . '/' . $file;

		if (is_file ( $realFile )) {
			include_once $realFile;
		} else if (is_dir ( $realFile )) {
			loadDir ( $realFile );
		}
	}
	closedir ( $h );
}

/**
 * 截取字符串 参考 discuz
 */
function cutstr($string, $length, $dot = '...') {
	if (strlen ( $string ) <= $length) {
		return $string;
	}

	$pre = chr ( 1 );
	$end = chr ( 1 );
	$string = str_replace ( array (
			'&amp;',
			'&quot;',
			'&lt;',
			'&gt;'
	), array (
			$pre . '&' . $end,
			$pre . '"' . $end,
			$pre . '<' . $end,
			$pre . '>' . $end
	), $string );

	$strcut = '';
	if (strtolower ( 'utf-8' ) == 'utf-8') {

		$n = $tn = $noc = 0;
		while ( $n < strlen ( $string ) ) {

			$t = ord ( $string [$n] );
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

		$strcut = substr ( $string, 0, $n );
	} else {
		for($i = 0; $i < $length; $i ++) {
			$strcut .= ord ( $string [$i] ) > 127 ? $string [$i] . $string [++ $i] : $string [$i];
		}
	}

	$strcut = str_replace ( array (
			$pre . '&' . $end,
			$pre . '"' . $end,
			$pre . '<' . $end,
			$pre . '>' . $end
	), array (
			'&amp;',
			'&quot;',
			'&lt;',
			'&gt;'
	), $strcut );

	$pos = strrpos ( $strcut, chr ( 1 ) );
	if ($pos !== false) {
		$strcut = substr ( $strcut, 0, $pos );
	}
	return $strcut . $dot;
}


/**
 * 截取小数点位数
 *
 * @param int $number 需要格式化的数字
 * @param int $precision 小数点后几位 默认是两位
 * @return string
 */
function subNumber($num, $prec = 2) {
	return sprintf ( "%01.2f", ($num), $prec );
}

function myRound ($val, $precision = 0) {
	$precision = (int)$precision;
	return sprintf ( "%.".$precision."f", $val);
}

/**
 * 获取password
 *
 * @param string $salt
 * @param string[optional] $passd
 * @return string MD5
 */
function getPassword($salt, $passd = "123456") {
	if ($passd == null) {
		$passd = "123456";
	}
	return md5 ( md5 ( $passd ) . $salt );
}

/**
 * 获取salt值
 *
 * @param int $minLength 密钥的最小长度
 * @param int $maxLength 密钥最大长度
 * @return string
 */
function getSalt($minLength = 5, $maxLength = 10) {
	$salt_data = array (
			'0','1','2','3','4','5','6','7','8','9','a','b','c','d','e','f','g',
			'!','@','#','$','%','^','&','*','(',')','-','+','='
	);
	$num = rand ( $minLength, $maxLength );
	$salt = '';
	for($i = 0; $i <= $num; $i ++) {
		$salt .= $salt_data [array_rand ( $salt_data )];
	}
	return $salt;
}

/**
 * 计算时间差
 * @param string|timestamp $begin_time
 * @param string $end_time
 * @return string
 */
function time_diff($begin_time, $end_time = null)
{
	if (! is_numeric($begin_time)) {
		$begin_time = strtotime($begin_time);
	}
	$end_time = $end_time ? $end_time : time();

	if($begin_time < $end_time){
		$starttime = $begin_time;
		$endtime = $end_time;
	}
	else{
		$starttime = $end_time;
		$endtime = $begin_time;
	}

	$timediff = $endtime-$starttime;
	$days = intval($timediff/86400);
	$remain = $timediff%86400;
	$hours = intval($remain/3600);
	$remain = $remain%3600;
	$mins = intval($remain/60);
	$secs = $remain%60;

	$time_format = '';
	if ($days) {
		$time_format .= $days.'天';
	}
	if ($hours) {
		$time_format .= $hours.'小时';
	}
	if ($mins) {
		$time_format .= $mins.'分钟';
	}
	if ($secs) {
		$time_format .= $secs.'秒';
	}
	return $time_format;
}
//将参数添加到指定url后
function resetUrl ($url, $queryData = array()) {
	if (empty($queryData) || !is_array($queryData)) {
		return $url;
	}
	$fragment = '';
	$findex = strpos($url, '#');
	if (false !== $findex) {
		$fragment = substr($url, $findex);
	}
	$url = rtrim(str_replace($fragment, '', $url), '&');
	$url = $url.(false == strrpos($url, '?')?'?':'&').http_build_query($queryData).$fragment;
	return $url;
}

/**
 * 判断是否为ajax请求
 * @return bool
 */
function isAjax () {
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) ) {
		if('xmlhttprequest' == strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])) {
			return true;
		}
	}
	if(HttpRequest::get(AbcPHPConfig::get('VAR_AJAX_SUBMIT'))) {
		// 判断Ajax方式提交
		return true;
	}
	return false;
}

/**
 *  URL重定向
 */
function redirect($url,$time=0,$msg='')
{
	//多行URL地址支持
	$url = str_replace(array("\n", "\r"), '', $url);
	if(empty($msg))
		$msg    =   "系统将在{$time}秒之后自动跳转到{$url}！";
	if (!headers_sent()) {
		// redirect
		if(0===$time) {
			header("Location: ".$url);
		}else {
			header("refresh:{$time};url={$url}");
			echo($msg);
		}
		exit();
	}else {
		$str    = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
		if($time!=0)
			$str   .=   $msg;
		exit($str);
	}
}

/**
 * 输出json数据
 * @param mixed $data 主数据
 * @param int $error error code
 * @param string $msg error message
 */
function printJson ($data = null, $error = 0, $msg = '', $exit = true) {
	echo json_encode(array('data'=>$data, 'error'=>$error, 'msg'=>$msg));
	if ($exit === true) {
		myExit();
	}
}

/**
 * 终止程序函数
 */
function myExit($msg = '') {
	//TODO 处理终止前程序
	Factory::getSystemLog()->flush();
	if ($msg) echo $msg;
	exit();
}

//生成url
function url($action = null, $method = null, $params = array(), $prefixUrl = null,$useRootPath = false) {
	$params[AbcPHPConfig::ACTION_NAME] = $action;
	$params[AbcPHPConfig::METHOD_NAME] = $method;
	$query = http_build_query($params);
	if(null === $prefixUrl){
		return  HttpRequest::getUri(). '/index.php' . ($query ? '?'.$query : '');
	}else{
		$prefixUrl = ltrim($prefixUrl,'/');
		if($useRootPath){
			return resetUrl('http://'.$_SERVER['HTTP_HOST']. '/'.$prefixUrl,$params);
		}else{
			return resetUrl(HttpRequest::getUri(). '/'.$prefixUrl,$params);
		}
	}
}

//子模板调用
function tpl($tplName) {
	Template::include_tpl($tplName);
}

/**
 * 获取微信token
 *
 * @param string $appId
 * @param string $appSercet
 * @param bool $refresh  如果cache中不存在是否刷新cache
 * @return string
 */
function getToken($appId, $appSercet, $refresh = true){

	$cacherId = GlobalCatchId::WX_API_TOKEN . $appId;
	$cacher = Factory::getCacher();
	$token = $cacher->get ( $cacherId );
	//TODO test
	//$token = '2FsxZXKoX6NAS9eV28UIZQz3YwoPXvBf2Gjr1O8bNl9nzKBpZub7_1zZ4gsWC1_LdzcwAJ7lW9oWLDghMWXvAn3w3Gcj63pX7ljpHprqCUE';
	if (true !== $refresh) {
		return $token;
	}
	if (! $token) {
		// 引入微信api
		if (! class_exists ( "WeiXinClient" )) {
			include_once dirname ( __FILE__ ) . "/../API/WeiXinApiCore.class.php";
		}
		$weixnApi = WeiXinApiCore::getClient ( $appId, $appSercet );
		$token = $weixnApi->getToken ();
		if ($token) {
			$token = $token->token;
			$cacher->set ( $cacherId, $token, 6600/*一小时50分钟*/);
		}
	}
	return $token;
}
/**
 * 获取公众号授权token
 * @date 2015-2-6
 * @param string $compCoupon 汇卡授权信息
 * @return string
 */
function getAuthorizerAccessToken($compCoupon, $refresh = true){

	$cacherId = GlobalCatchId::WX_API_COMPONECT_AUTHORIZER_ACCESS_TOKEN .$compCoupon['comp_appid'].':'.$compCoupon['authorizer_appid'];
	$cacher = Factory::getCacher();
	$token = $cacher->get ( $cacherId );
	//TODO test
	//$token = '2FsxZXKoX6NAS9eV28UIZQz3YwoPXvBf2Gjr1O8bNl9nzKBpZub7_1zZ4gsWC1_LdzcwAJ7lW9oWLDghMWXvAn3w3Gcj63pX7ljpHprqCUE';
	if (! $token) {
		// 引入微信api
		if (! class_exists ( "WeiXinClient" )) {
			include_once dirname ( __FILE__ ) . "/../API/WeiXinApiCore.class.php";
		}
		//TODO 到时候再统一处理
		if (!isset($compCoupon['authorizer_refresh_token'])) {
			try {
				$db = Factory::getDb();
				$compInfo = $db->getRow("SELECT * FROM `wx_comp_app` WHERE comp_appid = '{$compCoupon['comp_appid']}' AND authorizer_appid = '{$compCoupon['authorizer_appid']}'");
				$compCoupon['authorizer_refresh_token'] = $compInfo['authorizer_refresh_token'];
			} catch (Exception $e) {
				if (class_exists("Logger")) {
					Logger::error('getAuthorizerAccessToken get compCouponInfo error:'.$e->getMessage().'; sql:'.$db->getLastSql());
				}
			}
		}
        $ticket = getComponectTicket($compCoupon['comp_appid']);
        $token = getComponectToken($compCoupon['comp_appid'], $compCoupon['comp_app_secret'], $ticket);
		$weixnApi = WeiXinApiCore::getComponectClient($compCoupon['comp_appid'], $compCoupon['comp_app_secret'], $ticket, $token);
		$accessToken = $weixnApi->getAuthorizerAccessToken ($compCoupon['authorizer_appid'],$compCoupon['authorizer_refresh_token']);
		if ($accessToken) {
			try {
				$db = Factory::getDb();
				$where = "comp_appid = '{$compCoupon['comp_appid']}' AND authorizer_appid = '{$compCoupon['authorizer_appid']}'";
				$db->update('wx_comp_app', $where, array('authorizer_refresh_token'=>$accessToken->refresh_token, 'updatetime'=>time()));
			} catch (Exception $e) {
				if (class_exists("Logger")) {
					Logger::error('update authorizer_refresh_token error:'.$e->getMessage().'; sql:'.$db->getLastSql());
				}
			}
			$cacher->set ( $cacherId, $accessToken->token, $accessToken->expires_in - 600);
            $token = $accessToken->token;
		} else {
			return false;
		}
	}
	return $token;
}

/**
 * 获取微信组件token
 *
 * @param string $appId
 * @param string $appSercet
 * @param bool $refresh  如果cache中不存在是否刷新cache
 * @return string
 */
function getComponectToken($appId, $appSercet,$ticket = null ,$refresh = true){
        //获取ticket @todo add DB
        if(!$ticket){
            $ticket = getComponectTicket($appId);
        }
	$cacherId = GlobalCatchId::WX_API_COMPONECT_TOKEN . $appId;
	$cacher = Factory::getCacher();
	$token = $cacher->get ( $cacherId );
	//TODO test
	//$token = '2FsxZXKoX6NAS9eV28UIZQz3YwoPXvBf2Gjr1O8bNl9nzKBpZub7_1zZ4gsWC1_LdzcwAJ7lW9oWLDghMWXvAn3w3Gcj63pX7ljpHprqCUE';
	if (true !== $refresh) {
		return $token;
	}
	if (! $token) {
		// 引入微信api
		if (! class_exists ( "WeiXinComponectClient" )) {
			include_once dirname ( __FILE__ ) . "/../API/WeiXinApiCore.class.php";
		}
		$weixnComponectApi = WeiXinApiCore::getComponectClient ( $appId, $appSercet,$ticket );
		$token = $weixnComponectApi->getToken ();
		if ($token) {
			$token = $token->token;
			$cacher->set ( $cacherId, $token, ($token->expires_in-600)/*一小时50分钟*/);
                           //保存预授权码
                          $preAuthCode = $weixnComponectApi->getPreAuthCode($token);
                          $cacher->set(GlobalCatchId::WX_API_COMPONECT_PRE_AUTH_CODE . $appId, $preAuthCode,  GlobalCatchExpired::WX_API_COMPONECT_PRE_AUTH_CODE);
		}
	}
	return $token;
}

/**
 * 获取微信组件通信的ticket
 *
 * @param string $appId 组件的appid
 * @return string 从缓存里取，取不到由应用到数据库中取。
 * 		该值是微信定时推送过来的。
 */
function getComponectTicket($appId){
	$cacherId = GlobalCatchId::WX_API_COMPONECT_TICKET . $appId;
	$cacher = Factory::getCacher();
	$ticket = $cacher->get ( $cacherId );
	if (!$ticket && class_exists('Logger')) {
		Logger::error('getComponectTicket error: cache_id:'.$cacherId);
	}
	return $ticket;
}
/**
 * 获取第三方平台预售权码
 *
 * @param string $appId 组件的appid
 * @return string 从缓存里取
 */
function getComponectPreAuthCode($appId){
	$cacherId = GlobalCatchId::WX_API_COMPONECT_PRE_AUTH_CODE . $appId;
	$cacher = Factory::getCacher();
	$code = $cacher->get ( $cacherId );
	return $code;
}
/**
 * 获取组件公众号授权信息
 *@date 2015-2-6
 * @param string 组件的授权信息$compInfo
 * @return string 从缓存里取
 */
function getAuthorizationInfo($compInfo){
	$cacherId = GlobalCatchId::WX_API_COMPONECT_AUTHORIZATIONINFO . $compInfo['comp_appid'].':'.$compInfo['account_id'];
	$cacher = Factory::getCacher();
	$info = $cacher->get ( $cacherId );
        if ($info) {
            return $info;
        }
        if (! $info) {
            // 引入微信api
            if (! class_exists ( "WeiXinComponectClient" )) {
                include_once dirname ( __FILE__ ) . "/../API/WeiXinApiCore.class.php";
            }
            $ticket = getComponectTicket($compInfo['comp_appid']);
            $token = getComponectToken($compInfo['comp_appid'], $compInfo['comp_app_secret'], $ticket);
            $weixnComponectApi = WeiXinApiCore::getComponectClient ( $compInfo['comp_appid'], $compInfo['comp_app_secret'],$ticket,$token );
            $info = $weixnComponectApi->getAuthorizationInfo ($compInfo['authorizer_appid']);
            if ($info) {
                $cacher->set ( $cacherId, $info,7200);
            }
        }
        return $info;
}


//清除微信token缓存
function clearToken ($appId) {
	$cacherId = GlobalCatchId::WX_API_TOKEN . $appId;
	$cacher = Factory::getCacher();
	return $cacher->clear($cacherId);
}

//清除微信组件token缓存
function clearComponectToken ($comAppId) {
	$cacherId = GlobalCatchId::WX_API_COMPONECT_TOKEN . $comAppId;
	$cacher = Factory::getCacher();
	return $cacher->clear($cacherId);
}
/**
 * 清除公众号对组件的授权AccessToken
 * @param string $compAppId  组件app id
 * @param string $appId 公众号app id
 */
function clearAuthorizerAccessToken ($type = 'COUPON', $appId, $compAppId = null) {
	if (!$compAppId) {
		switch (strtoupper($type)) {
			case 'COUPON':
				$couponInfo = ComponectConfig::get('COUPON');
				$compAppId = $couponInfo['APP_ID'];
				break;
		}
	}
	$cacherId = GlobalCatchId::WX_API_COMPONECT_AUTHORIZER_ACCESS_TOKEN .$compAppId.':'.$appId;
	$cacher = Factory::getCacher();
	return $cacher->clear($cacherId);
}

//清除微信组件ticket缓存
function clearComponectTicket ($appId) {
	$cacherId = GlobalCatchId::WX_API_COMPONECT_TICKET . $appId;
	$cacher = Factory::getCacher();
	return $cacher->clear($cacherId);
}

/**
 * 获取随机字符串
 * @param int $length
 * @return string
 */
function getRandStr($length = 16){
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str ="";
    for ( $i = 0; $i < $length; $i++ ) $str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);
    return $str;
}

/**
 * @info 执行shell 统一入口
 * @param string $shell_key shell标识
 * @param array $args_arr 参数列表
 * @param string $log_file_name 命令回显的内容,默认不存储,存储方式:追加到文件尾
 * @return null
 */
function runShell($shell_key,$args_arr = array(),$log_file_name = '')
{
    /* 注意:参数列表本身有顺序,不可改动 */
    $args_tpl = array(
        'Msg.SendOne' => array('accountId','openId','msgType','sessionId','title'
        					  ,'description','mediaUrl','thumbUrl','up_message_id'
        					  ,'pluginKey','pluginInfo'),
        'Media.Download'    => array('accountId','messageId','type','mediaId','thumbMediaId'),
        'User.Fetcher'      => array('accountId','openId','eventType','createTime'),
        'MassMsg.AdvanceMassBack' => array('account_id','message_id','status','total_count'
        								  ,'filter_count','sent_count','error_count'),
    	'User.FetchUserAll' => array('accountId'),
    );

    if(!array_key_exists($shell_key,$args_tpl)){
        return ;
    }

    $args_str = '';
    foreach($args_tpl[$shell_key] as $value){
        $args_str .= isset($args_arr[$value]) ? " '".faddslashes($args_arr[$value])."'" : " ''";
    }

    $cmd = '';

    $cmd .= AbcPHPConfig::get('PHP_CLI_PATH') . ' ';

    $cmd .= ABC_PHP_PATH . '/../Shell/Web/' . str_replace('.', '/', $shell_key) . '.shell.php ';

    $cmd .= $args_str;

    $cmd .= $log_file_name ? ' >> ' . $log_file_name : ' > /dev/null';

    $cmd .= ' &';

    $out = popen($cmd,'r');
    if (class_exists('Logger')) {
    	Logger::debug('runShell : '.$cmd.'  ; status: '.intval($out));
    }

    pclose($out);

}

/**
 * 获取微信web链接token
 * #note:参数 $param 中禁止使用'=',';' ,key中禁止使用'sig','time' ,并且是一维数组#
 *
 * @param array $param
 * @return string
 */
function encodeWxWebToken($param, $isTimer = true) {
	$arr = array ();
	if ($param) {

		foreach ( $param as $k => $v ) {
			array_push ( $arr, $k . '=' . $v );
		}
	}
	if ($isTimer) {
		$arr [] = 'time=' . time ();
	}
	$arr [] = 'sig=' . genWxWebTokenSig_ ( implode ( ';', $arr ) );
	$str = implode ( ';', $arr );
	return base64_encode ( $str );
}


/**
 * 生成签名,
 *
 * @param string $str
 * @return string
 * @internal
 *
 */
function genWxWebTokenSig_($str) {
	return md5 ( $str . C("WX_WEB_TOKEN_KEY") );
}
/**
 * 生成随视 api key, 如果prefix为空返回15位长度字符串
 * @param string $prefix
 * @return string
 */
function createApiKey ($prefix = '') {
	if (!is_string($prefix)) $prefix = '';
	return uniqid($prefix).rand(10, 99);
}

/**
 * 微信code和随视code转换（数字与字母之间）
 * @param string $str
 * @param string $type encode | decode
 * @return string
 */
function changeCode($str, $type = 'encode') {
	$arr = array (
			0 => 'LF',
			1 => 'VT',
			2 => 'FD',
			3 => 'CR',
			4 => 'SO',
			5 => 'SI',
			6 => 'AM',
			7 => 'GE',
			8 => 'BS',
			9 => 'HK'
	);
	$arr = array_unique ( $arr );
	if ($type == 'encode') {
		$rep_str = str_replace ( array_keys ( $arr ), array_values ( $arr ), $str );
	} else if ($type == 'decode') {
		// $arr = array_flip($arr);
		$rep_str = str_replace ( array_values ( $arr ), array_keys ( $arr ), $str );
	}
	return $rep_str;
}

/**
 * 判断是否为null，如果为null则返回默认值
 * @param unknown $param
 * @param unknown $defaultValue
 */
function isnull($param,$defaultValue=''){
	if($param!=null){
		return $param;
	}else{
		return $defaultValue;
	}
}

/**
 * 发送短信
 * @param string $mobilePhone
 * @param string $content
 * @return boolean
 */
function sendSms($telephone,$msg){
	if (!class_exists('HttpClient')) {
		include_once dirname ( __FILE__ ) . "/HttpClient.class.php";
	}
	
	 $regResult = HttpClient::quickPost('http://192.168.5.236:8811/index.php', array(
	 		'tels' => $telephone,
	 		'message' => $msg
	 ));
	$regResult = json_decode($regResult,true);
	if($regResult['error']!=0){
		Logger::error("发送短信验证码失败,[{$telephone}|{$msg}]",$regResult);
		//printJson(0,1,"发送短信验证码失败");
		return false;
	}
	/*
	//借用一下宝洁的
	$regResult = HttpClient::quickPost('http://sms.4006555441.com/webservice.asmx/mt', array(
			'Sn'=>'SDK-ZQ-BJSH-0665',
			'Pwd'=>'888888',
			'mobile' => $telephone,
			'content' => $msg
	));
	$regResult = simplexml_load_string($regResult);
	if($regResult->int!=0){
		if (class_exists('Logger')) {
			Logger::error("发送短信验证码失败,[{$telephone}|{$msg}]",$regResult);
		}
		addSysAlarm("短信发送接口异常", "发放失败", array("param"=>array("mobile"=>$telephone,"content"=>$msg),"response"=>$regResult),1,1,1,600);
		return false;
	}
	*/
	return true;
	
}
/**
 * 解析url中参数，如果传递name只返回name对应参数值
 * @param string $url
 * @param string $name
 * @return array | string
 */
function parseParamFromUrl ($url, $name = null) {
	$urlarr=parse_url($url);
	$queryArr = array();
	if (isset($urlarr['query'])) {
		parse_str($urlarr['query'], $queryArr);
	}
	return ($name && is_string($name)) ? @$queryArr[$name] : $queryArr;
}

/**
 * 将数组value值用“，”分割后base64
 * 后前两位和后两位对调
 * 后前加两位后加4位
 * $param 中不可以有“,|,”，否则将无法反解
 * @param array $param
 * @return string
 */
function encryptParam ($param) {
	if (!$param || !is_array($param)) {
		return '';
	}
	$pArr = array_values($param);
	array_push($pArr, getRandStr(4));
	$dataStr = base64_encode(implode(',|,', $pArr));
	if (strlen($dataStr) > 6) {
		//前两位和后两位对调
		$pre = substr($dataStr, 0, 2);
		$last = substr($dataStr, -2);
		$dataStr = substr_replace(substr_replace($dataStr, $last, 0, 2), $pre, -2, 2);
	}
	$dataStr = getRandStr(2).$dataStr.getRandStr(4);
	return $dataStr;
}
/**
 * 反解encryptParam
 * @param string $string
 * @return array  如果null说明没解开
 */
function decryptParam ($string) {
	$string = trim($string);
	if (!$string || !is_string($string)) {
		return null;
	}
	$string = substr_replace($string, '', 0, 2);//前两位去掉
	$string = substr_replace($string, '', -4, 4);//后四位去掉
	if (strlen($string) > 6) {
		//前两位和后两位对调
		$pre = substr($string, 0, 2);
		$last = substr($string, -2);
		$string = substr_replace(substr_replace($string, $last, 0, 2), $pre, -2, 2);
	}
	$string = @base64_decode($string);
	if (!$string) {
		return null;
	}
	$data = explode(',|,', $string);
	array_pop($data);
	return $data;
}

/**
 * 获取IP对应的省市区
 * @param string $ip
 * @return boolean
 */
function getIpAnalyze($ip=null){
	if(!$ip){
		$ip = getIp();
	}
	if (!class_exists('IpUtil')) {
		include_once dirname ( __FILE__ ) . "/IpUtil.class.php";
	}
	$result = IpUtil::find($ip);
	return $result["data"];
}

function myEcho ($str, $default = '') {
	$str OR $str = $default;
	echo $str;
}

/**
 *
 * @param unknown $action
 * @param string $method
 */
function forward($action,$method = null){
	static $existPathArr = array();
	$method = !is_string($method)&&empty($method)?C("DEFAULT_METHOD"):$method;
	if(!$action || (__ACTION_NAME__ == $action && __ACTION_METHOD__ == $method) ||
		in_array($action.".".$method,$existPathArr)){
		if (class_exists('Logger')) {
			Logger::error("系统要求进行重定向，但不符合重定向参数，当前a/m[".__ACTION_NAME__."/".__ACTION_METHOD__."]，转向action/name:[".$action."/{$method}]，本次线程中已存在的跳转:".json_encode($existPathArr));
		}
		return ;
	}
	$existPathArr[] = $action.".".$method;
	if (class_exists('Logger')) {
		Logger::debug("正在进行服务内转向,当前a/m[".__ACTION_NAME__."/".__ACTION_METHOD__."]，转向action/method:[".$action."/{$method}]，本次线程中已存在的跳转:".json_encode($existPathArr));
	}
	call_user_func_array(array(loadAction($action),$method),array());
	myExit(); //终止
}


/**
 * 增加系统警告
 * @param unknown $title	错误标题
 * @param unknown $desc		描述
 * @param unknown $paramlog	重要日志
 * @param number $level		错误等级（1:非常严重，2：较严重，3：严重，4：一般，5：提示性警告'）
 * @param number $needPhone	是否发送短信
 * @param number $needEmail	发送邮件
 * @param number $catchTime  同一类型错误多长时间不重复提交
 * @return boolean
 */
function addSysAlarm ($title,$desc,$paramlog,$level=3,$needPhone=0,$needEmail=0,$catchTime=3600) {
	try {
		if(!$title){
			return;
		}
		$catchTime = $catchTime<300||$catchTime>3600*24?3600:$catchTime;
		$db = Factory::getDb();
		$param = array();
		$param['system_name'] = APP_NAME;
		$param['alarm_name'] = $title;
		$param['level'] = $level;
		$param['action_name'] = __ACTION_NAME__;
		$param['method_name'] = __ACTION_METHOD__;
		$param['alarm_desc'] = $desc;
		$param['param_log'] = json_encode(array("requestParam"=>HttpRequest :: get(),"codelog"=>$paramlog));
		$param['need_email'] = $needEmail;
		$param['need_phone'] = $needPhone;
		$param['create_time'] = time();
		$param['update_time'] = time();
		$catchKey = $param['alarm_name']."_".$param['system_name']."_".$param['action_name']."_".$param['method_name'];
		$catchService = Factory::getCacher();
		$catchValue = $catchService->get($catchKey);
		if($catchValue){
			return;
		}
		$data = $db->insert("wx_sys_alarm",$param);
		$catchService->set($catchKey,1,$catchTime);
	} catch (Exception $e) {
		if (class_exists('Logger')) {
			Logger::error(__METHOD__.' db error:' . $e->getMessage() . '; sql:' . $db->getLastSql());
		}
		return false;
	}
}

function makePlatePageUrl ($params) {
	$plateUrl = '';
	$query =  array(
			'account_id' => $params['account_id'],
			'ib_id' => isset($params['ib_id'])?$params['ib_id']:0,
			'page_id' => $params['page_id'],
			'source_id' => $params['source_id'],
			'sub_source' => isset($params['sub_source'])?$params['sub_source']:'',
			'store_id' => isset($params['store_id'])?$params['store_id']:0,
			'pla_store_id' => isset($params['pla_store_id'])?$params['pla_store_id']:0,
			'sm_id' => isset($params['sm_id'])?$params['sm_id']:0,
			'source' => isset($params['source'])?$params['source']:'',
			'relation_id' => isset($params['relation_id'])?$params['relation_id']:0,
			'entity_type' => isset($params['entity_type'])?$params['entity_type']:'',
			'entity_id' => isset($params['entity_id'])?$params['entity_id']:0,
	);
	$enQuery = encryptParam($query);
	$plateUrl = resetUrl($params['page_url'], array('s'=>$enQuery));
	return $plateUrl;
}

function analyzePlatePageParam ($s) {
	$params = decryptParam($s);
	if (!$params) return null;
	$arr['account_id'] = @$params[0];
	$arr['ib_id'] = @$params[1];
	$arr['page_id'] = @$params[2];
	$arr['source_id'] = @$params[3];
	$arr['sub_source'] = @$params[4];
	$arr['store_id'] = @$params[5];
	$arr['pla_store_id'] = @$params[6];
	$arr['sm_id'] = @$params[7];
	$arr['source'] = @$params[8];
	$arr['relation_id'] = @$params[9];
	$arr['entity_type'] = @$params[10];
	$arr['entity_id'] = @$params[11];
	if (class_exists("Logger")) {
		$aa = @$_GET['a'];$mm = @$_GET['m'];
		Logger::debug("a:m[{$aa}:{$mm}]".'function_base:'.__METHOD__." params：".json_encode($params));
	}
	//兼容处理
	if ($arr ['source'] != "cardrack") {
		if ($arr ['ib_id']) {
			$arr ['source'] = "shakearound";
			$arr ['relation_id'] = $arr ['ib_id'];
		} else if ($arr ['sm_id']) {
			$arr ['source'] = "specific_media";
			$arr ['relation_id'] = $arr ['sm_id'];
		}
	}
	return $arr;
}

/**
 * 自定义加密字符串
 * @param string $string
 * @return string
 */
function strEncode ($string) {
	$string = strval($string);
	$string = base64_encode($string);
	if ($string) {
		if (strlen($string) >= 4) {
			//前两位和后两位对调
			$pre = substr($string, 0, 2);
			$last = substr($string, -2);
			$string = substr_replace(substr_replace($string, $last, 0, 2), $pre, -2, 2);
		}
		$string = getRandStr(2).$string.getRandStr(4);
	}
	return $string;
}
/**
 * 还原自定义加密字符串
 * @param string $string
 * @return string
 */
function strDecode ($string) {
	$string = str_replace('%3D', '=', strval($string));
	if ($string) {
		if (strlen($string) > 6) {
			$string = substr_replace($string, '', 0, 2);//前两位去掉
			$string = substr_replace($string, '', -4, 4);//后四位去掉
			if (strlen($string) >= 4) {
				//前两位和后两位对调
				$pre = substr($string, 0, 2);
				$last = substr($string, -2);
				$string = substr_replace(substr_replace($string, $last, 0, 2), $pre, -2, 2);
			}
		}
		$string = @base64_decode($string);
	}
	return $string;
}


/**
 *
 * @param unknown $phone	要发送的手机号
 * @param unknown $param	模板对接参数json,示例中的参数为{"code":"1234","product":"随视"};
 * @param string $SignName	签名参数(阿里大鱼中获得)
 * @param string $TemplateCode	短信模板ID(阿里大鱼中获得)
 * @param string $extend	扩展参数，可用于记录哪个员工发的等
 */
function sendSMSByAli($phone,$param,$SignName="注册验证",$TemplateCode="SMS_4051078",$extend=""){
	//阿里大鱼的短信接口
	include_once dirname ( __FILE__ ) ."./../Org/taobao_sms/TopSdk.php";
	//date_default_timezone_set('Asia/Shanghai');
	$client = new ClusterTopClient("23299811","047602f853cd8a04155b14d15a99df21");
	$client->gatewayUrl = "http://api.daily.taobao.net/router/rest";
	$client->format="json";
	$req = new AlibabaAliqinFcSmsNumSendRequest;
	$req->setExtend($extend);
	$req->setSmsType("normal");
	$req->setSmsFreeSignName($SignName);
	$req->setSmsParam($param);
	$req->setRecNum($phone);
	$req->setSmsTemplateCode($TemplateCode);
	$regResult = $client->execute($req);
	if($regResult->code == 0){
		return true;
	}else{
		if (class_exists('Logger')) {
			Logger::error("发送短信验证码失败,[{$phone}|{$param}]",$regResult);
		}
		addSysAlarm("短信发送接口异常(阿里大鱼)", "发放失败", array("param"=>array("mobile"=>$phone,"content"=>$param),"response"=>$regResult),1,1,1,600);
		return false;
	}
}
