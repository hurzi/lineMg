<?php
class SeUserAction extends BaseAction{
	private $model = null;

	public function __construct(){
		parent::__construct();
		$this->model = loadModel('SeUser');
	}
	public function index(){

		$where = '1=1';
		$keyword = trim($this->getParam('keyword'));
        $level = trim($this->getParam('level'));
		$entName = trim($this->getParam('ent_name'));
		$paged = (int) $this->getParam(Config::VAR_PAGE, 1);
		$pagesize = Config::PAGE_LISTROWS;



		$args['paged'] = $paged;
		$args['pagesize'] = $pagesize;

		$list = $entList = $levelList = array();
		$page = '';
		if ($keyword) {
			$where = "nickname LIKE '%{$keyword}%'";
		}
		
		if($level){
			$where .= " AND level = ".$level;
		}else{
			if(UHome::getUserLevel()<>1){
				$where .= " AND level <> 1 ";
			}
		}
		$totalCount = 0;
		if ($entName) {
			$entIdList = $this->model->getEntListKeyValue("ent_name LIKE '%{$entName}%'");
			if ($entIdList) {
				$ids = array_keys($entIdList);
				$where .= " AND ent_id IN (" .implode(',', $ids). ")";
				$totalCount = $this->model->getTotalCount($where);
			}
		} else {
			$totalCount = $this->model->getTotalCount($where);
		}
		if ($totalCount>0) {
			$pageObj = new Page($totalCount, $pagesize);
			$page    = $pageObj->show();
			$limit = " LIMIT {$pageObj->firstRow},{$pagesize}";
			$list = $this->model->getList($where,$limit);
			$entList = $this->model->getEntListKeyValue();
			$levelList = $this->model->getLevelList();
		}

		$this->assign('keyword', $keyword);
		$this->assign('list',	 $list);
		$this->assign('page', 	$page);
		$this->assign('entList', $entList);
		$this->assign('levelList', $levelList);
		$this->assign('entName', $entName);
		$this->display();
	}

	public function add(){
		$entList = loadModel('SeEnterprise')->getAll();
        $levelList = $this->model->getLevelList();

		$this->assign('entList', $entList);
        $this->assign('levelList', $levelList);
		$this->display();
	}

	public function insert(){
		$username  = trim($this->getParam('username'));
		$nickname  = trim($this->getParam('nickname'));
		$level     = intval($this->getParam('level'));
        $password  = trim($this->getParam('password'));
		$ent_id    = $this->getParam('ent_id');

		$param['username']   = $username ;
		$param['nickname']   = $nickname;
		$param['password']   = $password;
		$param['ent_id']     		  = $ent_id;
		$param['level']				  = $level;
		$param = $this->model->checkData($param);
		if(!$param){
			printJson(0, 1, $this->model->getError());
		}

		$flag = $this->model->insert($param);
		if(!$flag){
			printJson(null,-1,'创建超级管理员失败');
		}
		printJson(null,0,'创建超级管理员成功');
	}

	public function edit(){
		$user_id = (int)$this->getParam('id');
		$where = " user_id = {$user_id}";
		$operator = $this->model->getList($where);
		if($operator){
			$operator = $operator[0];
			$entList = loadModel('SeEnterprise')->getAll();
			$this->assign('entList', $entList);
		}
        $levelList = $this->model->getLevelList();
        $this->assign('levelList', $levelList);
		$this->assign('operator', $operator);
		$this->display();
	}

	public function update(){
		$user_id  = trim($this->getParam('user_id'));
		$username  = trim($this->getParam('username'));
		$nickname  = trim($this->getParam('nickname'));
		$password  = trim($this->getParam('password'));
		$ent_id    = $this->getParam('ent_id');
        $level    = $this->getParam('level');

		$param['user_id']  		  = $user_id ;
		$param['username']   = $username ;
		$param['nickname']   = $nickname;
        $param['level']   = $level;
		if($password){
			$param['password']   = $password;
		}
        $param['ent_id']     		  = $ent_id;
        if($level==1){
            $param['ent_id']     		  = 0;
        }

		$param = $this->model->checkData($param,'udpate');
		if(!$param){
			printJson(0, 1, $this->model->getError());
		}

		$where = " user_id={$param['user_id']}";
		$flag = $this->model->update($param,$where);
		if(!$flag){
			printJson(null,-1,'编辑超级管理员失败');
		}
		printJson(null,0,'编辑超级管理员成功');
	}

	public function delete(){
		$MAX_COUNT = 5;
		$ids = $this->getParam('ids');
		if (! $ids) {
			printJson('', 1, '请选择要删除的栏目！');
		}
		if(!is_array($ids)){
			printJson('', -2, '传入的参数错误！');
		}
		if(count($ids) > $MAX_COUNT){
			printJson('', -3, '一次最多只能删除个'.$MAX_COUNT.'超级管理员！');
		}
		if (! $this->model->delete($ids)) {
			printJson('', 1, '删除超级管理员失败！');
		}
		printJson(1);
	}
	
	
}
