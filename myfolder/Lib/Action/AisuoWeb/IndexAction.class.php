<?php
class IndexAction extends Action
{
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function sendTest(){
		$openid = getCurrOpenid();
		
		$sendMsg['type'] = "text";
		$sendMsg['to_users'] = 'oImZwt0TO5VaNzSPsAIi1ksz7IB4';
		$sendMsg['content'] = "asdfwetwetwe";
		echo AiSuoFactory::getSendMessage()->send($sendMsg);
		echo "Over";
		
		$sendMsg['type'] = "news";
		$sendMsg['to_users'] = 'oImZwt0TO5VaNzSPsAIi1ksz7IB4';
		$sendMsg['articles'][0]['title'] = "asdfwetwetwe";
		$sendMsg['articles'][0]['description'] = "asdfwetwetwe";
		$sendMsg['articles'][0]['picurl'] = "http://ww4.sinaimg.cn/bmiddle/58a342d7tw1eii56uf8y2j20a005k752.jpg";
		$sendMsg['articles'][0]['url'] = "http://www.baidu.com";
		echo AiSuoFactory::getSendMessage()->send($sendMsg);
		echo "Over";
	}
	
	/**
	 * 错误页
	 */
	public function error(){
		$msg = $this->getParam("msg");
		$msg = urldecode($msg);
		$this->assign("msg",$msg);
		$this->display();
	}
	/**
	 * 直接跳转页面获取openid,中转入口
	 */
	public function index()
	{
		$this->assign('abc','abc');
		$openid = $this->getParam("openid");		
		//设置cookie及缓存
		$cookieid = md5($openid);
		$sendRet = setcookie(AbcConfig::COOKIE_UID_TOKEN,$cookieid,time()+3600*24*365*10);
		Factory::getCacher()->set($cookieid,$openid,AbcConfig::OPENID_VALID_DURTION);
		echo $sendRet;
		$this->display();
	}
	
	/**
	 * 协议
	 */
	public function agreement(){
		$this->display();
	}
	
	/**
	 * 公共信息显示页
	 */
	public function info(){
		$msg = $this->getParam("msg");
		$jumpUrl = $this->getParam("jumpUrl");
		$jumpName = $this->getParam("jumpName");
		
		if(!$msg || !$jumpUrl || !$jumpName){
			Logger::info("公共信息显示页");
		}
		
		$this->assign("msg",$msg);
		$this->assign("jumpUrl",$jumpUrl);
		$this->assign("jumpName",$jumpName);
		
		$this->display("Index.info");
	}
	
	public function jump(){
		$jumpUrl = htmlspecialchars_decode(stripslashes(urldecode(trim($this->getParam('url')))));
		echo $jumpUrl;
		$this->Oauth($jumpUrl);
	}
	
	public function aouthJump(){
		$a = $this->getParam("ta");
		$m = $this->getParam("tm");
		$p = htmlspecialchars_decode(stripslashes(urldecode($this->getParam("tp"))));
		$tokenArr = explode('&', $p);
		$tokenParam = array ();
		if($tokenArr){
			foreach ($tokenArr as $k => $v) {
				$oneArr = explode('=', $v);
				if ($oneArr && @$oneArr[0]) {
					$tokenParam[$oneArr[0]] = @$oneArr[1];					
				}
			}
		}		
		$jumpUrl = url($a,$m,$tokenParam);
		$this->Oauth($jumpUrl);
	}
	
	/**
	 * OAuth 获取授权
	 */
	private function Oauth ($link)
	{
		$app_id = Config::APP_ID;
		$redirect_uri = Config::REDIRET_URI.'&url='.urlencode($link);
		//TODO test
		$matchs = array('APP_ID','REDIRET_URI');
		$replace = array($app_id, urlencode($redirect_uri));
		$wxurl = str_replace($matchs, $replace, Config::WX_AUTH_PATH);
		Logger::debug('Oauth wxUrl:'.$wxurl);
		
		header("location:".$wxurl);
		exit;
	}
	
	/**
	 * OAuth 获取授权
	 */
	private function OauthWeb ()
	{
		$link = $this->getParam("link");
		$app_id = Config::APP_ID;
		$redirect_uri = Config::REDIRET_URI.'&url='.urlencode($link);
		//TODO test
		$matchs = array('APP_ID','REDIRET_URI');
		$replace = array($app_id, urlencode($redirect_uri));
		$wxurl = str_replace($matchs, $replace, Config::WX_AUTH_PATH);
		Logger::debug('Oauth wxUrl:'.$wxurl);
	
		header("location:".$wxurl);
		exit;
	}
	
	/**
	 * 根据OAuth2.0接口获取用户openid
	 */
	public function WxOAuthCallBack ()
	{
		$data = $this->getParam();
		$jumpUrl = urldecode($data['url']);
		$code = $data['code'];
		$state = $data['state'];
		if (!$code || !$state || $state != Config::STATE) {
			Logger::errror('WxOAuthCallBack wx Oauth error:'.$data);
			die('微信授权失败！！');
		}
		$app_id = Config::APP_ID;
		$app_secret = Config::APP_SECRET;
		$wxOAuthApi = WeiXinApiCore::getOAuthClient($app_id, $app_secret);
		$accessToken = $wxOAuthApi->getAccessToken($code);
		if (!$accessToken) {
			Logger::errror('WxOAuthCallBack accessToken error:'.$accessToken);
			die('系统出错，请联系管理员！！');
		}
		$openid = $accessToken->openId;	
		
		//设置cookie及缓存
		$cookieid = md5($openid);
		setcookie(AbcConfig::COOKIE_UID_TOKEN,$cookieid,3600*24*365*10);
		Factory::getCacher()->set($cookieid,$openid,AbcConfig::OPENID_VALID_DURTION);
		
		$jumpUrl = resetUrl($jumpUrl, array('openid'=>$openid));
		
		header("Location: ".$jumpUrl);
		exit;
	}
	
	/**
	 * 介绍
	 */
	public function introduce(){
		$this->display();
	}
	
}