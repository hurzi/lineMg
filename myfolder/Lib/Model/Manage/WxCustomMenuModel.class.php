<?php
class WxCustomMenuModel extends Model
{

	private $tableName = 'wx_custom_menu';

	private static $MSG_TYPE = array (
			'text' => '文本',
			'news' => '图文'
	); //消息类型

	private static $MENU_TYPE = array (
			'1' => '固定返回',
			'2' => '动态获取',
			'3' => '访问网页'
	);

	/**
	 * 获取自定义菜单列表
	 * @return array
	 */
	public function getList($condition = NUll, $ent_id = null)
	{
		$sql = "SELECT * FROM {$this->tableName} WHERE 1=1 ";
		if (! empty($condition)) {
			$sql .= $condition;
		}
		try {
			$list = $this->getDb()->getAll($sql);
		} catch ( Exception $e ) {
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
			return false;
		}
		return $list;
	}

	/**
	 * 解析推送数据
	 * @param array $list
	 * @return array
	 */
	public function parseList($ent_id, $list)
	{
		if (! $list || !is_array($list)) {
			return array();
		}
		foreach ($list as $k => & $v) {
			if (1 == $v['type']) {
				if ('text' != $v['msg_type']) {
					$material = loadModel('WxMaterial')->getMaterialById($v['material_id']);
					$v = array_merge($v, $material);
				}
			}
		}
		return MessageTools::genCustomMenuMsgList($ent_id, $list);
	}

	/**
	 * 获取单条菜单记录
	 * @param unknown $where
	 * @return boolean
	 */
	public function getSingleRecord($where)
	{
		$sql = "select * from {$this->tableName} where 1=1 {$where}";
		try {
			return $this->getDb()->getRow($sql);
		} catch ( Exception $e ) {
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
			return false;
		}
	}

	/**
	 * 通过ID获取菜单
	 */
	public function getMenuById($id)
	{
		$sql = "SELECT * FROM {$this->tableName} WHERE id = %d";
		try {
			$menu = $this->getDb()->getRow(sprintf($sql, $id));
		} catch ( Exception $e ) {
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
			return false;
		}
		return $this->parseMenu($menu);
	}

	public function parseMenu($menu)
	{
		if (!$menu || !is_array($menu)) {
			return array();
		}

		$content = '';
		if (1 == $menu['type']) {
			$msg = array();
			$msg['msg_type'] = $menu['msg_type'];
			$msg['use_oauth'] = $menu['use_oauth'];
			if ('text' == $menu['msg_type']) {
				$msg['content'] = $menu['content'];
			} else {
				$material = loadModel('WxMaterial')->getMaterialById($menu['material_id']);
				$msg = array_merge($msg, $material);
			}
			$content = MessageTools::escape($msg);
		}
		$menu['message_content'] = $content;
		return $menu;
	}

	/**
	 * 获取菜单个数
	 * @return int
	 */
	public function getCountByCond($condtion = NULL)
	{
		$where = " where 1=1 ";
		if ($where) {
			$where .= ' and ' . $condtion;
		}
		$sql = "select count(*) from {$this->tableName} " . $where;
		try {
			return $this->getDb()->getOne($sql);
		} catch ( Exception $e ) {
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString() . $sql);
			return false;
		}
	}

	/**
	 * 处理数据
	 * @param unknown $data
	 */
	public function handleMenuData(&$data)
	{
		$newsList = array ();
		if (! $data) {
			return;
		}
		foreach ($data as &$val) {
			if ($val['type'] == 1) {
				if ($val['msg_type'] == 'text') {
					//$val['content'] = $val['content']; //title="' . $val['content'] . '"
					$newsList[$val['id']] = $val['content'];
					$val['content'] = '<a href="javascript:;" name="showTW" rel="' . $val['id'] . '"  type="text">查看文本</a>';

				} else if ($val['msg_type'] == 'news') {
					$val['content'] = '<a href="javascript:;" name="showTW" rel="' . $val['id'] . '" type="news">查看图文</a>';
					$newsList[$val['id']] = fhtmlspecialchars(@unserialize($val['articles']));
				}
			} else if ($val['type'] == 2 || $val['type'] == 3) {
				$val['content'] = $val['url'];
			}
			$val['type'] = self::$MENU_TYPE[$val['type']];
		}
		return $newsList;
	}

	/**
	 * 添加菜单
	 * @param unknown $param
	 * @return boolean
	 */
	public function insert($param)
	{
		try {
			return $this->getDb()->insert($this->tableName, $param);
		} catch ( Exception $e ) {
			Logger::error(__FILE__ . ' 添加出错 ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
			return false;
		}
	}

	/**
	 * 更新菜单
	 * @param unknown $param
	 * @return boolean
	 */
	public function update($where, $param)
	{
		try {
			return $this->getDb()->update($this->tableName, $where, $param);
		} catch ( Exception $e ) {
			Logger::error(__FILE__ . ' 修改出错  ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
			return false;
		}
	}

	/**
	 * 删除菜单
	 * @param unknown $where
	 * @return boolean
	 */
	public function delete($where)
	{
		try {
			return $this->getDb()->delete($this->tableName, $where);
		} catch ( Exception $e ) {
			Logger::error(__FILE__ . ' 删除出错 ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
			return false;
		}
	}

	/**
	 * 根据素材id查询单条素材
	 */
	public function getMaterialById($materialId, $msgType)
	{
		$sql = "SELECT * FROM `wx_material` WHERE id = %d AND type = '%s'";
		try {
			return $this->getDb()->getRow(sprintf($sql, $materialId, $msgType));
		} catch ( Exception $e ) {
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
			return false;
		}
	}

	/**
	 * 清除微信菜单
	 */
	public function clearWxMenu($entId)
	{
		$client = $this->getClient($entId);
		$flag = $client->deleteMenu();
		$this->setError($flag, $client->getErrorMessage());
		return $flag;
	}

	public function getWxMenu($entId)
	{
		$client = $this->getClient($entId);
		$arr = $client->getMenu();
		echo '<pre>' . print_r($arr, 1);
		exit();
	}

	/**
	 * 同步菜单到微信
	 */
	public function synchronousWxMenu($entId)
	{
		$client = $this->getClient($entId);
		$menu = $this->creatMenu( $entId);
		Logger::info("------------------同步菜单参数",$menu);
		//var_dump($menu);exit;
		$flagSynch = AiSuoFactory::getApiClient()->createMenu($menu);
		if ($flagSynch) {
			$this->updatePushTime($entId);
		}
		//var_dump($flagSynch,$client-> getErrorMessage());exit;
		$this->setError($flagSynch, $client->getErrorMessage());
		return $flagSynch;
	}

	public function getClient($entId)
	{
		//TODO (pengzhang)获取appinfo
		//$app_id = 'wx16968f12918ca8d3';
		//$app_secret = '4486602fb477d2fbd837e7bda5e0b513';
		$app_id = C('APP_ID');
		$app_secret = C('APP_SECRET');
		
		
		//------------TODO (pengzhang)
		$token = getToken($app_id, $app_secret);
		$client = WeiXinApiCore::getClient($app_id, $app_secret, $token);
		return $client;
	}

	public function creatMenu($entId)
	{
		$condition = " and parent = 0 order by `order`  asc limit 0,3";
		$parent = $this->getList($condition);
		if (! $parent) {
			printJson(null, - 1, '对不起，当前没有菜单数据，无法同步');
		}
		$condition = " and parent != 0  order by `order`  asc";
		$child = $this->getList($condition);
		$array = $this->dwMenuToWx($parent, $child, $entId);
		$menu = new WX_Menu($array);
		return $menu;
	}

	private function dwMenuToWx($parent, $child, $entId)
	{
		if (! $parent) {
			return;
		}
		$tmp = array ();
		foreach ($parent as $val) {
			if(in_array($val['type'], array(1,2))){
				$tmp[$val['id']] = array (
						'type' => 'click',
						'name' => $val['name'],
						'key' => $val['id']
				);
			}else if(in_array($val['type'], array(3))){
				$matchs = array('ENT_ID', 'MENU_ID', 'WEIXIN_NAME', 'TARGET');
				$repalce = array($entId, $val['id'], urlencode(C('APP_WEIXIN_USER')), urlencode($val['url']));
				$tmp[$val['id']] = array (
						'type' => 'view',
						'name' => $val['name'],
						'url' => str_replace($matchs, $repalce, Config::CUSTOM_MENU_LINK_URL)
				);
			}
		}
		foreach ($child as $v) {
			$index = $v['parent'];
			if (isset($tmp[$index])) {
				if(in_array($v['type'], array(1,2))){
					$tmp[$index]['sub_button'][] = array (
							'type' => 'click',
							'name' => $v['name'],
							'key' => $v['id']
					);
				}else if(in_array($v['type'], array(3))){
					$matchs = array('ENT_ID', 'MENU_ID', 'WEIXIN_NAME', 'TARGET');
					$repalce = array($entId, $v['id'], urlencode(C('APP_WEIXIN_USER')), urlencode($v['url']));
					$tmp[$index]['sub_button'][] = array (
							'type' => 'view',
							'name' => $v['name'],
							//'url' => str_replace($matchs, $repalce, Config::CUSTOM_MENU_LINK_URL)
							'url' => $v['url']
						);
				}
				unset($tmp[$index]['type']);
				if(isset($tmp[$index]['key'])){
					unset($tmp[$index]['key']);
				}
				if(isset($tmp[$index]['url'])){
					unset($tmp[$index]['url']);
				}
			}
		}
		$tmpArr = array ();
		foreach ($tmp as $v) {
			$tmpArr[] = $v;
		}
		return $tmpArr;
	}

	/**
	 * 更新自定义菜单同步时间
	 */
	private function updatePushTime($entId)
	{
		$tableName = "wx_ent_setting";
		$flagExist = false;
		$date = date('Y-m-d H:i:s');
		$set['set_value'] = $date;
		$setKey = EntSettingKey::CUSTOM_MENU_LAST_SYNCHRONOUS_TIME;
		$sql = "select * from  {$tableName} where ent_id={$entId} and set_key='{$setKey}'";
		try {
			$flagExist = $this->getDb()->getRow($sql);
			$set['set_value'] = $date;
			if ($flagExist) {
				$where = "  ent_id={$entId} and set_key='{$setKey}' ";
				return $this->getDb()->update($tableName, $where, $set);
			} else {
				$set['ent_id'] = $entId;
				$set['set_key'] = $setKey;
				return $this->getDb()->insert($tableName, $set);
			}
		} catch ( Exception $e ) {
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
			return false;
		}
	}

	/**
	 * 更新自定义菜单最后修改时间
	 * @return boolean
	 */
	public function lastUpdateMenuTime($entId)
	{
		$tableName = "wx_ent_setting";
		$flagExist = false;
		$date = date('Y-m-d H:i:s');
		$set['set_value'] = $date;

		$setKey = EntSettingKey::CUSTOM_MENU_LAST_UPDATE_TIME;
		$sql = "select * from  {$tableName} where ent_id={$entId} and set_key='{$setKey}'";
		try {
			$flagExist = $this->getDb()->getRow($sql);
			$set['set_value'] = $date;
			if ($flagExist) {
				$where = "  ent_id={$entId} and set_key='{$setKey}' ";
				return $this->getDb()->update($tableName, $where, $set);
			} else {
				$set['ent_id'] = $entId;
				$set['set_key'] = $setKey;
				return $this->getDb()->insert($tableName, $set);
			}
		} catch ( Exception $e ) {
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage(), $set);
			return false;
		}
	}

	/**
	 * 获取菜单同步时间
	 * @param int $ent_id
	 * @return array|false
	 */
	public function getMenuPush($ent_id)
	{
		$tableName = "wx_ent_setting";
		$entId = $ent_id;
		$setKey = EntSettingKey::CUSTOM_MENU_LAST_SYNCHRONOUS_TIME;
		$sql = "SELECT set_value   FROM {$tableName} WHERE ent_id= {$entId} and set_key='{$setKey}'";
		try {
			$ent = $this->getDb()->getOne($sql);
		} catch ( Exception $e ) {
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
			return false;
		}
		return $ent;
	}

	public function getMenuLastUpdateTime($ent_id)
	{
		$tableName = "wx_ent_setting";
		$entId = $ent_id;
		$setKey = EntSettingKey::CUSTOM_MENU_LAST_UPDATE_TIME;
		$sql = "SELECT set_value   FROM {$tableName} WHERE ent_id= {$entId} and set_key='{$setKey}'";
		try {
			$ent = $this->getDb()->getOne($sql);
		} catch ( Exception $e ) {
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
			return false;
		}
		return $ent;
	}

	/**
	 * 获取菜单类型
	 * @return array 菜单类型
	 */
	public function getMsgType()
	{
		return self::$MSG_TYPE;
	}

	/**
	 * 根据一级菜单获取二级菜单列表
	 * @return array
	 */
	public function getTwoLevelList($condition = NUll)
	{
		$sql = "select a.id,a.name,a.order,a.parent from {$this->tableName} a where 1=1 ";
		if (! empty($condition)) {
			$sql .= $condition;
		}
		try {
			return $this->getDb()->getAll($sql);
		} catch ( Exception $e ) {
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
			return false;
		}
	}


}
