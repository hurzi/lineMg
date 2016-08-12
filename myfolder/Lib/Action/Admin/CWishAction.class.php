<?php
/**
 * 首页
 */
class CWishAction extends Action{
	private $_model;
	public function __construct()
	{
		parent::__construct();
		$this->_model = loadModel('Wed.CWish');
	}
	/**
     * 显示页面编辑模板
     */
    public function index()
    {
    	$paged = (int) $this->getParam(Config::VAR_PAGE, 1);
    	$pagesize = Config::PAGE_LISTROWS;
    	
    	$args['paged'] 			= $paged;
		$args['pagesize'] 		= $pagesize;
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
    		setcookie('c_cookieid', $cookieid, 3600*24*900);
    	}
    }
    
    /**
     * 显示页面编辑模板
     */
    public function add()
    {
    	$cookieid = $this->getCookieId();
    	$info = self::$model->getByCookieId($cookieid);
    
    	$id =trim($this->getParam('id'));
    	if($id){
    		$info = self::$model->getById($id);
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
     * 增加
     */
    public function ajax_del()
    {
    	$id =trim($this->getParam('id'));
    	if(!$id){
    		printJson('',-1,"删除失败，没有id");
    	}
    	$boolean = $this->_model->delById($id);
    	if(!$boolean){
    		printJson('',-1,"删除失败");
    	}else{
    		printJson('删除成功');
    	}
    }
    
    
    /**
     * 增加
     */
    public function insert()
    {
    	$type =trim($this->getParam('type',1));
    	$cname =trim($this->getParam('cname',''));
    	$cphone = trim($this->getParam('cphone',''));
    	$cwish = trim($this->getParam('cwish',''));
    	$ctype = trim($this->getParam('ctype',''));
    	$ccount = trim($this->getParam('ccount',''));
    	$opt_type = trim($this->getParam("opt_type"));
    	
    	
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
    	$param['create_time'] = date('Y-m-d H:i:s');
    	$param['last_update_time'] = date('Y-m-d H:i:s');
    
    		
    	$flagInsert = self::$model->saveOrUpdateAsUser($param,($opt_type=="update"));
    	if ($flagInsert === false) {
    		printJson(null, - 1, '我们已收到您的祝福成功！非常感谢！--何钟强&杨华');
    	}
    	printJson(null, 0, '服务开了小差了，一会再来。');
    }
    
    public function updateRegistUser()
    {
    	$uid = intval($this->getParam('uid'));
    	$asCode = $this->getParam('as_code');
    
    	if ($asCode == '') {
    		printJson(null, - 1, '请输入邮编');
    	}
    
    	$codeuid = self::$model->getUidByAsCode($asCode);
    	if($codeuid && $uid!=$codeuid){
    		printJson(null, - 1, '邮编已使用');
    	}
    
    	$param['as_code'] = $asCode;
    	$param['last_update_time'] = date('Y-m-d H:i:s');
    	$flagInsert = self::$model->UpdateUserInfoArrByUid($uid,$param);
    	if ($flagInsert === false) {
    		printJson(null, - 1, '修改用户失败');
    	}
    	printJson(null, 0, '修改用户成功');
    }
    
}
