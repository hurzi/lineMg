<?php
/**
 * 客户分组功能
 *
 */
class UserGroupAction extends AdminAction
{

	private static $model;

	public function __construct()
	{
		parent::__construct();
		self::$model = loadModel('Admin.UserGroup');
	}

	/**
	 * 客户分组首页(11-21)
	 */
	public function index()
	{
		$keyword = trim($this->getParam('keyword'));
		$type = (int)$this->getParam('type');
		$data = array(
				'keyword' => $keyword,
				'type'=>$type
		);
		//创建类型
		$createType = self::$model->getCreateType();
		//列表数据
		$result = self::$model->getFirstGroups($data);
		
		$this->assign('createType', $createType);
		$this->assign('type', $type);
		$this->assign('keyword', $keyword);
		$this->assign('list',$result['list']);
		$this->assign('page', $result['page']);
		$this->display();
	}
	
	/**
	 * 显示二级分组(11-21)
	 */
	public function showTwoGroups()
	{
		$parent_id = (int)$this->getParam('parent_id');
		$callback   = trim($this->getParam('callback'));
		//$paged = (int)$this->getParam(Config::VAR_PAGE, 1);
		if(!$parent_id){
			printJson(0,1,'参数错误！');
		}
		//验证parant_id
		$parentGroupInfo = self::$model->getParentGroupById($parent_id);
		if(!$parentGroupInfo){
			printJson(0,1,'操作失败，请重试！');
		}
		//创建类型
		$createType = self::$model->getCreateType();		
		$keyword = trim($this->getParam('keyword'));
		$data = array(
				'keyword' => $keyword,
				//'paged' => $paged < 1 ? 1 : $paged
				'callback' => $callback
		);
		//列表数据
		$result = self::$model->getTwoGroups($parent_id, $data);
		$result['create_type'] = $createType;
		printJson($result);
	}
	
	/**
	 * 客户分组首页
	 */
	public function index_old()
	{
		$keyword 	= trim($this->getParam('keyword'));
		$type 		= (int)$this->getParam('type');
		$args['ug_name'] = $keyword;
		$args['create_type'] = $type;

		$list = self::$model->getGroups($args);

		$createType = self::$model->getCreateType();
		$this->assign('keyword', $keyword);
		$this->assign('list', $list);
		$this->assign('createType', $createType);
		$this->assign('type', $type);
		$this->display();
	}

	/**
	 * 添加客户分组界面
	 */
	public function add()
	{
		$groupList = self::$model->getParentList();
		$this->assign('groupList', $groupList);
		$this->display();
	}

	/**
	 * 添加企业操作操作
	 */
	public function insert()
	{
		$checkData = array(
				'ug_name' => trim($this->getParam('name')),
				'parent_id' => (int) $this->getParam('pid'),
		);
		$data = self::$model->checkData($checkData);
		if(!$data){
			printJson(0, 1, self::$model->getError());
		}
		//查找一级组是否有数据的来源id
		$ParentGroupInfo = self::$model->getParentGroupById($data['parent_id']);
		if($ParentGroupInfo['create_source_id']){
			$data['create_source_id'] = $ParentGroupInfo['create_source_id'];
		}
		$id =  self::$model->add($data);
		if (! $id) {
			printJson(0, 1, '添加客户分组失败！');
		}
		printJson(1);

	}

	/**
	 * 编辑操作模版调用
	 */
	public function edit()
	{
		$id = (int) $this->getParam('id');
		$group = self::$model->getGroupById($id);
		$groupList = array();
		if ($group) {
			if (! self::$model->isExistChildById($id)) {
				$groupList = self::$model->getParentList($id);
			}
		}
		$this->assign('group', $group);
		$this->assign('groupList', $groupList);
		$this->display();
	}

	/**
	 * 编辑操作
	 */
	public function update()
	{
		$id = (int) $this->getParam('id');
		if(!$id){
			printJson(0, 1,'请选择要编辑的客户分组！');
		}

		$checkData = array(
				'ug_id' => $id,
				'ug_name' => trim($this->getParam('name')),
				'parent_id' => (int) $this->getParam('pid')
		);

		$data = self::$model->checkData($checkData, 'update');
		if(!$data){
			printJson(0, 1, self::$model->getError());
		}

		if (! self::$model->update($id, $data)) {
			printJson(0, 1, '修改客户分组失败！');
		}
		printJson(1);
	}

	/**
	 * 删除操作
	 *
	 */
	public function delete()
	{
		$id = $this->getParam('ids');
		if (! $id) {
			printJson('', 1, '请选择要删除的栏目！');
		}

		if (! self::$model->delete($id)) {
			printJson('', 1, self::$model->getError());
		}
		printJson(1);
	}

	public function getFirstGroupJson () {
		$paged = (int)$this->getParam(Config::VAR_PAGE, 1);
		$groupName = trim($this->getParam('group_name', ''));
		$createType = (int)$this->getParam('type');
		$model = loadModel("Common.G_UserGroup");
		$param = array('groupName'=>$groupName,
						'createType'=>$createType < 0 ? 0 : $createType,
						'paged' => $paged < 1 ? 1 : $paged,
						'pageSize' => Config::PAGE_LISTROWS);
		$groupList = $model->getGroupFirstList($param);
		printJson($groupList);
	}

	public function getTwoGroupJson () {
		$paged = (int)$this->getParam(Config::VAR_PAGE, 0);
		$goupId = (int)$this->getParam('group_id', 0);
		$model = loadModel("Common.G_UserGroup");
		$param = array(
				'paged' => $paged,
				'pageSize' => Config::PAGE_LISTROWS);
		$groupRet = $model->getGroupTwoList($goupId, $param);
		printJson($groupRet);
	}
	
	public function getTempTwoGroupJson () {
		$paged = (int)$this->getParam(Config::VAR_PAGE, 0);
		$goupId = (int)$this->getParam('group_id', 0);
		$model = loadModel("Common.G_UserGroup");
		$param = array(
				'paged' => $paged,
				'pageSize' => Config::PAGE_LISTROWS);
		$groupRet = $model->getTempGroupTwoList($goupId, $param);

		printJson($groupRet);
	}
}