<?php
/**
 * 首页
 */
class IndexAction extends Action{
	private $_model;
	public function __construct()
	{
		parent::__construct();
		$this->_model = loadModel('Wed.CWish');
	}
	/**
     *  主管理界面
     */
    public function index(){
    		$this->display();    
    }
    
    
    /**
     * 显示页面编辑模板
     */
    public function allWish()
    {
    	$paged = (int) $this->getParam(Config::VAR_PAGE, 1);
    	$pagesize = Config::PAGE_LISTROWS;
    	
    	$args['paged'] 			= $paged;
		$args['pagesize'] 		= $pagesize;
		$args['type'] 		= $this->getParam("type","");
		$listResult = $this->_model->getList($args);
    	if($listResult){
    		$list = $listResult['list'];
    		$count = $listResult['count'];
    		$pageObj = new Page($count, $pagesize);
    		$page = $pageObj->show();
    	}		
    	$this->assign('list', $list);
    	$this->assign('count', $count);
    	$this->assign('page', $page);
    	
    	$this->display();
    }
    
    public function getCookieId(){
    	$cookieid = trim(@$_COOKIE["c_cookieid"]);
    	if(!$cookieid){
    		$cookieid = getRandStr(10);
    		setcookie('c_cookieid', $cookieid, time()+3600*24*900);
    	}
    	return $cookieid;
    }
    
    /**
     * 显示页面编辑模板
     */
    public function add()
    {
    	$cookieid = $this->getCookieId();
    	$info = $this->_model->getByCookieId($cookieid);
    
    	$id =trim($this->getParam('id'));
    	if($id){
    		$info = $this->_model->getById($id);
    	}
    	if($info){
    		$this->assign('opt_type', "update");
    	}else{
    		$this->assign('opt_type', "add");
    	}
    	$this->assign('info', $info);    
    	$this->display();
    }
    
    /**
     * 显示页面编辑模板
     */
    public function admin_add()
    {
    	$cookieid = $this->getCookieId();
    	$info = $this->_model->getByCookieId($cookieid);
    
    	$this->assign('opt_type', "add");
    	$this->display();
    }
    
    /**
     * 显示显示地图
     */
    public function map()
    {    	
    	$this->display();
    }
    
    public function header()
    {
    	$this->display();
    }
    public function footer()
    {
    	$this->display();
    }
    
    /**
     * 显示显示地图
     */
    public function showPic()
    {
    	$this->display();
    }

    /**
     * 邀请
     */
    public function yaoqing()
    {
    	$p = $this->getParam("s",null,true,"all");
    	$p = decryptParam($p);
    	if (!$p){
    		//echo "aaaa";exit;
    		Logger::error("-----------------s 解析有错".$p);
    		redirect(url("Index","map"));
    		return;
    	}
    	$id = $p[0];
    	$info = $this->_model->getById($id);
    	if(!$info || $info['type']!=2){
    		Logger::error("_________-----------根据ID,查不到专属消息");
    		redirect(url("Index","map"));
    		return;
    	}
    	
    	$shareConf = array(
    			"title"=>$info['cname']."专属邀请函",
    			"desc"=>"结婚啦！欢迎来参加何钟强&杨华的婚礼(正月初八)"
    	);
    	$this->assign("shareConf",$shareConf) ;
    	$this->assign("name",$info['cname']) ;  	
    	
    	$this->display();
    }
    /**
     * 显示显示地图
     */
    public function love()
    {
    	$this->display();
    }
    public function love2()
    {
    	$this->display();
    }
    public function base_bottom()
    {
    	$this->display();
    }
    public function test()
    {
    	$this->display();
    }
    
    
    
    /**
     * 增加
     */
    public function ajax_insert()
    {
    	$type =trim($this->getParam('type',1));
    	$cname =trim($this->getParam('cname',''));
    	$cphone = trim($this->getParam('cphone',''));
    	$cwish = trim($this->getParam('cwish',''));
    	$ctype = trim($this->getParam('ctype',''));
    	$ccount = trim($this->getParam('ccount',''));
    	$opt_type = trim($this->getParam("opt_type"));
    	$cookieid = $this->getCookieId();
    	if ($cname == '') {
    		printJson(null, - 1, '请留下您的真实姓名哦');
    	}
    	if ($cphone == '') {
    		printJson(null, - 1, '请留下您的手机号哦');
    	}
    	if($cwish==''){
    		$cwish='恭喜恭喜';
    	}
    
    	$param['type'] = $type;
    	$param['cname'] = $cname;
    	$param['cphone'] = $cphone;
    	$param['cwish'] = $cwish;
    	$param['ctype'] = $ctype;
    	$param['ccount'] = $ccount;
    	$param['ccookieid'] = $cookieid;
    	$param['create_time'] = date('Y-m-d H:i:s');
    	$param['last_update_time'] = date('Y-m-d H:i:s');
    
    		
    	$flagInsert = $this->_model->saveOrUpdateAsUser($param,($opt_type=="update"));
    	if ($flagInsert === false) {
    		printJson(null, - 1, '服务开了小差了，一会再来。');
    	}
    	printJson(null, 0, '我们已收到您的祝福成功！非常感谢！--何钟强&杨华');
    }
    
    public function updateRegistUser()
    {
    	$uid = intval($this->getParam('uid'));
    	$asCode = $this->getParam('as_code');
    
    	if ($asCode == '') {
    		printJson(null, - 1, '请输入邮编');
    	}
    
    	$codeuid = $this->_model->getUidByAsCode($asCode);
    	if($codeuid && $uid!=$codeuid){
    		printJson(null, - 1, '邮编已使用');
    	}
    
    	$param['as_code'] = $asCode;
    	$param['last_update_time'] = date('Y-m-d H:i:s');
    	$flagInsert = $this->_model->UpdateUserInfoArrByUid($uid,$param);
    	if ($flagInsert === false) {
    		printJson(null, - 1, '修改用户失败');
    	}
    	printJson(null, 0, '修改用户成功');
    }
    
    
}
