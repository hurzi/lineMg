<?php
class UserAction extends AdminAction
{
	private static $_IS_BIND = array('-1'=>'所有','0'=>'未绑定','1'=>'绑定');
	private static $model;

	public function __construct()
	{
		parent::__construct();
		self::$model = loadModel('Admin.User');
	}

	public function index()
	{
		$keyword = trim($this->getParam('keyword'));
		$gid = $this->getParam('gid', -1);
		$groupName = $this->getParam('groupName', "");
		$sex = $this->getParam('sex', -1);
		$ent_subscribe = $this->getParam('ent_subscribe', -1);
		$content = trim($this->getParam('content'));
		$country = trim($this->getParam('country'));
		$province = trim($this->getParam('province'));
		$city = trim($this->getParam('city'));
		$openid = trim($this->getParam('openid'));
		$is_bind = (int)($this->getParam('is_bind',-1));
		$paged = (int) $this->getParam(Config::VAR_PAGE, 1);
		$pagesize = Config::PAGE_LISTROWS;
		$subscribe = $this->getParam('subscribe', -1);
		if ('' === $gid) {
			$gid = -1;
		}

		if ('' === $sex) {
			$sex = -1;
		}

		if ('' === $ent_subscribe) {
			$ent_subscribe = -1;
		}

		if ('' === $subscribe) {
			$subscribe = -1;
		}
		$args['nickname'] 		= $keyword;
		$args['gid'] 			= (int) $gid;
		$args['sex'] 			= (int) $sex;
		$args['ent_subscribe']  = (int) $ent_subscribe;
		$args['content']		= $content;
		$args['paged'] 			= $paged;
		$args['pagesize'] 		= $pagesize;
		$args['country']		= $country;
		$args['province'] 		= $province;
		$args['city'] 			= $city;
		$args['user'] 		    = $openid;
		$args['is_bind']		= $is_bind;
		$args['subscribe']  = (int) $subscribe;

		//$groupList = self::$model->getGroupList(0, false);
		$selectGroupListJson = self::$model->getSelectGroupListJson();

		$sexList = self::$model->getSexList();
		$entSubscribeList = self::$model->getEntSubscribeList();
		$subscribeList = self::$model->getSubscribeList();

		$list = array();
		$page = '';

		$result = self::$model->getUsers($args);
		//$moveGroupListJson = array();
		$userGroupList = array();
		if ($result) {
			$list = $result['list'];
			$count = $result['count'];
			$pageObj = new Page($count, $pagesize);
			$page = $pageObj->show();

			//$userGroupList = self::$model->getGroupList(1, false);
			//$moveGroupListJson = self::$model->getMoveGroupListJson();
		}

		$this->assign('keyword', $keyword);
		$this->assign('gid', $gid);
		$this->assign('groupName', $groupName);
		$this->assign('sex', $sex);
		$this->assign('ent_subscribe', $ent_subscribe);
		$this->assign('content', $content);
		$this->assign('country', $country);
		$this->assign('province', $province);
		$this->assign('city', $city);
		$this->assign('openid', $openid);
		$this->assign('is_bind', $is_bind);
		$this->assign('subscribe', $subscribe);

		//$this->assign('groupList', $groupList);
		//$this->assign('moveGroupList', $moveGroupListJson);
		$this->assign('selectGroupList', $selectGroupListJson);
		$this->assign('_IS_BIND',self::$_IS_BIND);

		$this->assign('userGroupList', $userGroupList);
		$this->assign('sexList', $sexList);
		$this->assign('entSubscribeList', $entSubscribeList);
		$this->assign('subscribeList', $subscribeList);
		$this->assign('list', $list);
		$this->assign('page', $page);
		$this->display();

	}

	public function edit()
	{
		$id = $this->getParam('id');
		$user = self::$model->getUserByUser($id);
		$groupListJson = array();
		$defaultGroupIds = array();
		$selectedGroup = array();
		if ($user) {
			$groupListJson =  self::$model->getGroupListJson();
			$defaultGroupIds = self::$model->getGroupIdsByUser($id);
			$selectedGroup = self::$model->getGroupListByUserId($id);;
			$this->assign('groupListJson', $groupListJson);
			$this->assign('defaultGroupIds', json_encode($defaultGroupIds));
			$this->assign('selectedGroup', json_encode($selectedGroup));
		}

		$this->assign('user', $user);
		$this->display();
	}

	public function changeGroup()
	{
		$id = $this->getParam('id');
		if(!$id){
			printJson(0, 1,'请选择要修改的客户！');
		}
		$gids = $this->getParam('gids');
		//var_dump($gids);exit();

		if (! self::$model->changeGroup($id, $gids)) {
			printJson(0, 1, '修改失败！');
		}
		printJson(1);

	}

	public function moveGroup()
	{
		$ids = $this->getParam('ids');
		if(!$ids){
			printJson(0, 1,'请选择要操作的客户！');
		}
		$gid = $this->getParam('group_id');

		if (! $gid) {
			printJson(0, 1,'请选择要转移到哪个分组！');
		}

		if (loadModel('Admin.UserGroup')->isParentById($gid)) {
			printJson(0, 1,'请选择子级分组！');
		}

		if (! self::$model->isAdminCreateGroup($gid)) {
			printJson(0, 1,'您选择的是非管理员创建组，请选择其他组！');
		}

		if (! self::$model->moveGroup($ids, $gid)) {
			printJson(0, 1, '操作失败！');
		}
		printJson(1);
	}

	public function updateRemark()
	{
		$openId = trim($this->getParam('openId'));
		$remark = trim($this->getParam('remark'));

	    $flag = self::$model->updateRemark($openId, $remark);
	    if (! $flag) {
	    	printJson(null, self::$model->getErrorCode(), self::$model->getError());
	    }
	    printJson(null, 0, '操作成功！');
	}
}