<?php
/**
 * 微信自定义菜单
 * @author  zp
 *
 */
class CustomMenuAction extends AdminAction
{
	const LIMIT_PARENT_BUTTON_MAX_COUNT = 3; //父按钮最大个数
	const LIMIT_CHILD_BUTTON_MAX_COUNT = 5; //子按钮最大个数
	const LIMIT_PARENT_BUTTON_LENGTH = 16;
	const LIMIT_CHILD_BUTTON_LENGTH = 40;
	const LIMIT_TEXT_CONTENT_LENGTH = 600;

	private $_model;
	private $_isThread;

	public function __construct()
	{
		parent::__construct();
		$this->_model = loadModel('Admin.CustomMenu');
		$this->_isThread = (int) $this->getParam('is_thread', 0);
		$this->assign('isThread', $this->_isThread);
	}

	/**
	 * 自定义菜单列表
	 */
	public function index()
	{
		$entId = Config::ENT_ID;

		$condition = " order by `order` asc ";
		$list = $this->_model->getList($condition, $entId);
		$result = $this->_model->parseList($entId, $list);
		$list = array();
		$msgList = array();
		if ($result) {
			$list = $result['list'];
			$msgList = $result['jsonData'];
			$list = list_to_tree($list, 'id', 'parent', 'children');
		}

		$lastSynchronousTime = $this->_model->getMenuPush($entId);
		$menuLastTime = $this->_model->getMenuLastUpdateTime($entId);

		$this->assign('msgList', json_encode($msgList));
		$this->assign('list', $list);
		$this->assign('lastSynchronousTime', $lastSynchronousTime);
		$this->assign('lastUpdateTime', $menuLastTime);
		$this->display();
	}

	public function add()
	{
		$condition = " and parent = 0 ";
		$parentList = $this->_model->getList($condition);

		$this->assign('parentList', $parentList);
		$this->display();
	}

	public function insert()
	{
		$menuOrder = intval($this->getParam('menuOrder'));
		$menuName = trim($this->getParam('menuName', '', false));
		$parent = (int) $this->getParam('parentId', 0);
		$menuType = (int) $this->getParam('menuType');
		$msgType = trim($this->getParam('msgType'));
		$content = trim($this->getParam('content', '', false, 'all'));
		$materialId = (int) $this->getParam('materialId', 0);
		$url = trim($this->getParam('url', '', false, 'all'));
		$isOauth = (int) $this->getParam('use_oauth', 0);

		$options['parent'] = $parent;
		$options['menuName'] = $menuName;
		$this->commonValid($options);

		$param = array ();
		if ($menuName == '') {
			printJson(null, - 1, '请输入菜单名称');
		}
		if ($parent == 0) {
			if (mb_strlen($menuName) > self::LIMIT_PARENT_BUTTON_LENGTH) {
				printJson(null, - 1, '菜单名称不能超过' . self::LIMIT_PARENT_MENU_LENGTH . '个字节');
			}
		} else {
			if (mb_strlen($menuName) > self::LIMIT_CHILD_BUTTON_LENGTH) {
				printJson(null, - 1, '子菜单名称不能超过' . self::LIMIT_CHILD_BUTTON_LENGTH . '个字节');
			}
		}
		if (! is_int($menuOrder)) {
			printJson(null, - 1, '菜单排序值必须是一个整数');
		}
		if ($menuType < 1 || $menuType > 3) {
			printJson(null, - 1, '请求错误');
		}

		if ($menuType == 1) {
			if ($msgType == 'text') {
				if ($content == '') {
					printJson(null, - 1, '请输入文本消息');
				}
				if (mb_strlen($content) > self::LIMIT_TEXT_CONTENT_LENGTH) {
					printJson(null, - 1, '文本消息长度不能超过' . self::LIMIT_TEXT_CONTENT_LENGTH . '的字符');
				}
				$param['content'] = $content;
			} else {
				if ($materialId == 0) {
					printJson(null, - 1, '请选择素材');
				}
				//没有更换素材
				if (-1 != $materialId) {
					$material = loadModel('Admin.Material')->getMaterialById($materialId);
					if (! $material) {
						printJson(null, - 1, '选择的素材不存在');
					}
				}
			}
			$param['msg_type'] = $msgType;
		} else if ($menuType == 2 || $menuType == 3) {
			if ($url == '') {
				printJson(null, - 1, '请输入url地址');
			}
			if(stripos($url,'http://') === false &&	stripos($url,'https://') === false){
				$url = 'http://' . $url;
			}
			//继续验证....
			$param['url'] = $url;
		}

		$param['name'] = $menuName;
		$param['order'] = $menuOrder;
		$param['parent'] = $parent;
		$param['type'] = $menuType;
		$param['material_id'] = $materialId;
		$param['use_oauth'] = $isOauth;
		$param['create_time'] = date('Y-m-d H:i:s');
		$param['last_update_time'] = date('Y-m-d H:i:s');

		$flagInsert = $this->_model->insert($param);
		if ($flagInsert === false) {
			printJson(null, - 1, '添加菜单失败');
		}
		$entId = Config::ENT_ID;
		$this->_model->lastUpdateMenuTime($entId);
		printJson(null, 0, '添加菜单成功');
	}

	/**
	 * 显示页面编辑模板
	 */
	public function edit()
	{
		$id = (int) $this->getParam('id', 0);
		$menu = $this->_model->getMenuById($id);
		$messageContent = '';
		$parentList = array ();
		if ($menu) {
			$condition = " AND parent = 0 ";
			$parentList = $this->_model->getList($condition);
			$messageContent = $menu['message_content'];
		}

		$this->assign('parentList', $parentList);
		$this->assign('menu', $menu);
		$this->assign('messageContent', json_encode($messageContent));
		$this->display();
	}

	/**
	 * 更新菜单
	 */
	public function update()
	{
		$id = (int) ($this->getParam('menuId'));
		$menuOrder = intval($this->getParam('menuOrder'));
		$menuName = trim($this->getParam('menuName', '', false));
		$parent = (int) $this->getParam('parentId', 0);
		$menuType = (int) $this->getParam('menuType');
		$msgType = trim($this->getParam('msgType'));
		$content = trim($this->getParam('content', '', false, 'all'));
		$materialId = (int) $this->getParam('materialId', 0);
		$url = trim($this->getParam('url', '', false, 'all'));
		$isOauth = (int) $this->getParam('use_oauth', 0);

		$menu = $this->_model->getMenuById($id);
		if (! $menu) {
			printJson(null, - 1, '要编辑的菜单不存在');
		}
		//验证菜单名称是否存在
		//$condtion = " id != {$id} and name = '{$menuName}' ";
		//$currNameCount = $this->_model->getCountByCond($condtion);
		//if ($currNameCount > 0) {
		//	printJson(null, - 1, '当前菜单名称已经存在，请重新输入，谢谢。');
		//}

		$param = array ();
		if ($menuName == '') {
			printJson(null, - 1, '请输入菜单名称');
		}
		if ($parent == 0) {
			if (mb_strlen($menuName) > self::LIMIT_PARENT_BUTTON_LENGTH) {
				printJson(null, - 1, '菜单名称不能超过' . self::LIMIT_PARENT_BUTTON_LENGTH . '个字节');
			}
		} else {
			if (mb_strlen($menuName) > self::LIMIT_CHILD_BUTTON_LENGTH) {
				printJson(null, - 1, '子菜单名称不能超过' . self::LIMIT_CHILD_BUTTON_LENGTH . '个字节');
			}
		}
		if (! is_int($menuOrder)) {
			printJson(null, - 1, '菜单排序值必须是一个整数');
		}
		if ($menuType < 1 || $menuType > 3) {
			printJson(null, - 1, '请求错误');
		}
		if ($menuType == 1) {
			if ($msgType == 'text') {
				if ($content == '') {
					printJson(null, - 1, '请输入文本消息');
				}
				if (mb_strlen($content) > self::LIMIT_TEXT_CONTENT_LENGTH) {
					printJson(null, - 1, '文本消息长度不能超过' . self::LIMIT_TEXT_CONTENT_LENGTH . '的字符');
				}
				$param['content'] = $content;
			} else {
				if ($materialId == 0) {
					printJson(null, - 1, '请选择素材');
				}
				//没有更换素材
				if ($menu['material_id'] != $materialId) {
					$material = loadModel('Admin.Material')->getMaterialById($materialId);
					if (! $material) {
						printJson(null, - 1, '选择的素材不存在');
					}
				}
			}
			$param['msg_type'] = $msgType;
		} else if ($menuType == 2 || $menuType == 3) {
			if ($url == '') {
				printJson(null, - 1, '请输入url地址');
			}
			if(stripos($url,'http://') === false &&	stripos($url,'https://') === false){
				$url = 'http://' . $url;
			}
			//继续验证....
			$param['url'] = $url;
		}

		$param['name'] = $menuName;
		$param['order'] = $menuOrder;
		$param['parent'] = $parent;
		$param['type'] = $menuType;
		$param['material_id'] = $materialId;
		$param['use_oauth'] = $isOauth;
		$param['last_update_time'] = date('Y-m-d H:i:s');

		$where = " id = {$id} ";
		$flagUpd = $this->_model->update($where, $param);
		if ($flagUpd === false) {
			printJson(null, - 1, '更新菜单失败');
		}

		//验证是否需要更新菜单最后一次修改时间
		if ($menu['name'] != $menuName) {
			$entId = Config::ENT_ID;
			$this->_model->lastUpdateMenuTime($entId);
		}
		printJson(null, 0, '更新菜单成功');
	}

	/**
	 * 删除
	 */
	public function delete()
	{
		$id = $this->getParam('ids');
		if (is_array($id)) {
			$id = $id[0];
		}
		if ($id < 1) {
			printJson(null, - 1, '请求数据错误');
		}
		$where = " and id = {$id}  ";
		$checkExist = $this->_model->getSingleRecord($where);
		if (! $checkExist) {
			printJson(null, - 1, '对不起，你要删除的数据不存在');
		}
		$where = " id = {$id} OR `parent`= {$id} ";
		$flagDel = $this->_model->delete($where);
		if ($flagDel === false) {
			printJson(null, - 1, '删除失败');
		}
		$entId = Config::ENT_ID;
		$this->_model->lastUpdateMenuTime($entId);
		printJson(null, 0, '删除成功');
	}

	/**
	 * 同步菜单到微信
	 */
	public function synchronousMenu()
	{
		$entId = Config::ENT_ID;
		$flagSynch = $this->_model->synchronousWxMenu($entId);
		if ($flagSynch) {
			printJson(null, 0, '同步成功');
		}
		printJson(null, - 1, $this->_model->getError());
	}

	public function clearWxMenu()
	{
		$entId = Config::ENT_ID;
		$flag = $this->_model->clearWxMenu($entId);
		if ($flag) {
			printJson(null, 0, '清除成功');
		}
		printJson(null, - 1, $this->_model->getError());
	}

	/**
	 * 添加和修改的公共验证部分
	 * @param array $options
	 */
	private function commonValid($options)
	{
		$parent = $options['parent'];
		$where = " `parent`= {$parent} ";
		$totalMenuCount = $this->_model->getCountByCond($where);
		if ($parent == 0) {
			if ($totalMenuCount >= self::LIMIT_PARENT_BUTTON_MAX_COUNT) {
				printJson(null, - 1, '父菜单个数已达到最大限制，不能再继续添加，谢谢。');
			}
		} else {
			if ($totalMenuCount >= self::LIMIT_CHILD_BUTTON_MAX_COUNT) {
				printJson(null, - 1, '当前父菜单下的子菜单个数已达到最大限制，不能再继续添加,谢谢。');
			}
		}

		//验证菜单名称是否存在
		//$menuName = $options['menuName'];
		//$condtion = " name = '{$menuName}' ";
		//$currNameCount = $this->_model->getCountByCond($condtion);
		//if ($currNameCount > 0) {
		//	printJson(null, - 1, '当前菜单名称已经存在，请重新输入，谢谢。');
		//}
	}
}