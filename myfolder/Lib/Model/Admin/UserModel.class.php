<?php
class UserModel extends Model
{
	private static $_sex_names = array(
			0 => '未知',
			1 => '男',
			2 => '女',
	);
	//企业订阅
	private static $_ent_subscribe = array(
			1 => '是',
			0 => '否',
	);

	//关注
	private static $_subscribe = array(
			1 => '是',
			0 => '否',
	);
	
	public function __construct(){
		
	}
	
	/**
	 * 获取客户首页显示数据
	 * @param array $args
	 * @return array
	 */
	public function getUsers($args)
	{
		$where = "WHERE 1";
		$groupby = "";
		if($args['user']){
			$where .=" AND u.`openid`='{$args['user']}'";
		}
		if ($args['nickname']) {
			$where .= " AND (u.nickname LIKE '{$args['nickname']}%' OR u.remark LIKE '{$args['nickname']}%')";
		}
		$joinTable = '';
		$gid = $args['gid'];
		if ($gid != -1) {
			$joinTable .= " LEFT JOIN `wx_user_group_member` ugm ON ugm.user = u.user";
			if ($gid == 0) {
				$where .= " AND ugm.ug_id IS NULL";
			} else {
				$userGroupModel = loadModel('Admin.UserGroup');
				if ($userGroupModel->isParentById($gid)) {
					$gids = $userGroupModel->getChildIdsById($gid);
					$gids = $gids ? $gids : array(0);
					$where .= " AND ugm.ug_id IN ({$this->getDb()->genSqlInStr($gids)})";
				} else {
					$where .= " AND ugm.ug_id = {$gid}";
				}
			}
		}

		//添加门店所属客户组过滤
// 		$level = UHome::getUserLevel();
// 		if($level==-3){
// 			if($gid==-1){
// 				$joinTable .= " LEFT JOIN `wx_user_group_member` ugm ON ugm.user = u.user";
// 			}
// 			$joinTable .= " LEFT JOIN `wx_user_group` ug ON ug.ug_id = ugm.ug_id";
// 			$where .= " AND ug.ug_id > 0 ";
// 			$branchIdStr = $this->getAdminBranchId();
// 			if($branchIdStr){
// 				$where .= " AND ug.create_source_id IN(".$branchIdStr.") ";
// 			}else{
// 				//防止数据库连接异常获取不到门店ID所设置
// 				$where .= " AND ug.create_source_id IN('-3') ";
// 			}
// 			$groupby .= "GROUP BY u.user";
// 		}

		//会员绑定
		$joinTable .= " LEFT JOIN `as_user` mb ON mb.openid = u.user ";
		if($args['is_bind'] == 1){
			$where .= " AND mb.member_id IS NOT NULL ";
		}else if($args['is_bind'] == 0){
			$where .= " AND mb.member_id IS  NULL ";
		}

		if ($args['sex'] != -1) {
			$where .= " AND u.sex = {$args['sex']}";
		}
		if ($args['ent_subscribe'] != -1) {
			$where .= " AND u.ent_subscribe = {$args['ent_subscribe']}";
		}
		if ($args['subscribe'] != -1) {
			$where .= " AND u.subscribe = {$args['subscribe']}";
		}
		if ($args['content']) {
			$joinTable .= " LEFT JOIN `wx_dialog2` dia on dia.wx_user = u.user";
			$where .= " AND dia.type = 1 AND dia.content LIKE '{$args['content']}%'";
		}

		if (isset($args['country']) && $args['country']) {
			$where .= " AND u.country = '{$args['country']}'";
		}

		if (isset($args['province']) && $args['province']) {
			$where .= " AND u.province = '{$args['province']}'";
		}

		if (isset($args['city']) && $args['city']) {
			$where .= " AND u.city = '{$args['city']}'";
		}

		$limit = '';
		if ($args['paged'] && $args['pagesize']) {
			$limit = " LIMIT ".($args['paged'] -1) * $args['pagesize'].",".$args['pagesize'];
		}

		$order = " ORDER BY u.create_time DESC";

		$sql = "SELECT SQL_CALC_FOUND_ROWS u.* , mb.member_id FROM `wx_user` u"
			." {$joinTable} {$where} {$groupby} {$order} {$limit}";

		$list = array();
		try {
			$list = $this->getDb()->getAll($sql);
			$count = $this->getDb()->getOne('SELECT FOUND_ROWS()');
		} catch (Exception $e) {
			Logger::error(__FILE__.' '.__CLASS__.' '.__METHOD__,'查询出错');
			return false;
		}

		if ($list) {
			foreach ($list as $k => &$v) {
				$v['sex_name'] = self::$_sex_names[$v['sex']];
				$v['group'] = '';
				$v['groupTotal'] = 0;
				$v['groupAllInfo']  = '';
				$groupNames = $this->getGroupNamesByUser($v['user']);
				if ($groupNames) {
					foreach ($groupNames as $value) {
						$v['groupTotal'] ++ ;
						if($v['groupTotal']<=3){
							$v['group'] .= '['.$value.']<br/>';
						}else if($v['groupTotal'] == 4){
							$v['group'] .= '...';
						}
						$v['groupAllInfo'] .= '['.$value.']';
						//$v['groupAllInfo'] .= '['.$value.']<br/>';
					}
				} else {
					$v['group'] = '[未分组]';
				}
			}
		}
		return array('list' => $list, 'count' => $count);
	}

    /**
     * 获取客户列表
     *
     * @param  string $where 附加的SQL条件
     * @param  string $limit 分页条件
     * @return array
     */
    public function getList($where = '', $limit = '')
    {
    	$sql = "SELECT * FROM `wx_user` WHERE 1 {$where}"
    		." ORDER BY create_time DESC ";
    	if ($limit) {
    		$sql .= $limit;
    	}
    	$list = array();
	    try {
	    	$list = $this->getDb()->getAll($sql);
	    } catch (Exception $e) {
	        Logger::error("获取客户列表失败： ", $e->getMessage()."\n".$e->getTraceAsString());
	        return false;
	    }

	    if ($list) {
	    	foreach ($list as $k => &$v) {
	    		$v['sex_name'] = self::$_sex_names[$v['sex']];
	    		$v['group'] = '';
	    		$groupNames = $this->getGroupNamesByUser($v['user']);
	    		if ($groupNames) {
					foreach ($groupNames as $value) {
						$v['group'] .= '['.$value.']';
					}
	    		} else {
	    			$v['group'] = '[未分组]';
	    		}
	    	}
	    }
	    return $list;
    }

    /**
     * 获取客户总数
     *
     * @param  string $where 附加的SQL条件
     * @return int
     */
    public function getTotal($where = '')
    {
    	$sql = "SELECT COUNT(*) FROM `wx_user` WHERE 1 {$where}";
	    try {
	    	return $this->getDb()->getOne($sql);
	    } catch (Exception $e) {
	        Logger::error("获取客户总数失败： ", $e->getMessage()."\n".$e->getTraceAsString());
	        return false;
	    }
    }

    /**
     * 获取客户组列表
     * @param int $type 组类型 0所有；1管理员创建；2系统创建
     * @return array
     */
    public function getGroupList($type = 0, $isSubscribe = true)
    {
    	$where = "WHERE 1 ";

    	$countFiled = 'u.user';
    	if (! $isSubscribe) {
    		$countFiled = 'ugm.ug_id';
    	}

    	switch ($type) {
    		case 1:
    			$where .= " AND ug.create_type = '1' ";
    			break;
    		case 2:
    			$where .= " AND ug.create_type = '2' ";
    			break;
    	}

    	//添加门店所属客户组过滤
//     	$level = UHome::getUserLevel();
//     	if($level==-3){
//     		$branchIdStr = $this->getAdminBranchId();
//     		if($branchIdStr){
//     			$where .= " AND ug.create_source_id IN(".$branchIdStr.") ";
//     		}
//     		else{
//     			//防止数据库连接异常获取不到门店ID所设置
//     			$where .= " AND ug.create_source_id IN('-3') ";
//     		}
//     	}

    	$sql = "SELECT ug.ug_id, ug.ug_name, ug.parent_id, COUNT({$countFiled}) AS user_num,"
    		." SUM(CASE u.ent_subscribe  WHEN 1 THEN 1 ELSE 0 END) as ent_user_num "
    		." FROM `wx_user_group` ug"
			." LEFT JOIN `wx_user_group_member` ugm ON ugm.ug_id = ug.ug_id"
    		." LEFT JOIN `wx_user` u ON u.user = ugm.user AND u.subscribe = 1"
    		." {$where} GROUP BY ug.ug_id";

		try {
			return $this->getDb()->getAll($sql);
		} catch (Exception $e) {
			Logger::error("获取客户组列表失败： ", $e->getMessage()."\n".$e->getTraceAsString());
			return false;
		}
    }

   
   

    /**
     * 获取性别列表
     * @return array
     */
    public function getSexList()
    {
    	return self::$_sex_names;
    }

    /**
     * 获取企业订阅列表
     * @return array
     */
    public function getEntSubscribeList()
    {
    	return self::$_ent_subscribe;
    }

    /**
     * 获取关注列表
     * @return array
     */
    public function getSubscribeList()
    {
    	return self::$_subscribe;
    }

	/**
	 * 获取客户组名称集合
	 * @param string $openid
	 * @return array
	 */
	public function getGroupNamesByUser($openid)
	{
		$sql = "SELECT ug.ug_name FROM `wx_user_group_member` ugm"
			." LEFT JOIN `wx_user_group` ug ON ug.ug_id = ugm.ug_id WHERE ugm.user = '{$openid}' AND ug.ug_id >0";
		try {
			return $this->getDb()->getCol($sql);
		} catch (Exception $e) {
			Logger::error("获取客户组名称集合失败： ", $e->getMessage()."\n".$e->getTraceAsString());
			return false;
		}
	}

	/**
	 * 获取客户组ID集合
	 * @param string $openid
	 * @return array
	 */
	public function getGroupIdsByUser($openid)
	{
		$sql = "SELECT ug.ug_id FROM `wx_user_group_member` ugm"
			." LEFT JOIN `wx_user_group` ug ON ug.ug_id = ugm.ug_id"
			." WHERE ugm.user = '{$openid}'";
		try {
			$ids = $this->getDb()->getCol($sql);
		} catch (Exception $e) {
			Logger::error("获取客户组ID集合失败： ", $e->getMessage()."\n".$e->getTraceAsString());
			return array();
		}
		return $ids ? $ids : array();
	}

	/**
	 * 通过id获取客户信息
	 * @param string $openid
	 * @return array
	 */
	public function getUserByUser($openid)
	{
		$sql = "SELECT * FROM `wx_user` WHERE `openid` = '%s'";
		try {
			return $this->getDb()->getRow(sprintf($sql, $openid), true);
		} catch ( Exception $e ) {
			Logger::error("通过id获取客户信息失败： ",$e->getMessage()."\n".$e->getTraceAsString());
			return false;
		}
	}

	/**
	 * 获取转移组列表json数据
	 * @return string
	 */
	public function getMoveGroupListJson()
	{
		$groupList = $this->getGroupList(1, false);
		$list = array();
		if ($groupList) {
			$list = list_to_tree($groupList, 'ug_id', 'parent_id', 'children');
			foreach ($list as &$val) {
				if (@$val['children']) {
					foreach ($val['children'] as &$v) {
						$val['user_num'] += $v['user_num'];
						$v['id'] = $v['ug_id'];
						$v['text'] = $v['ug_name'].'('.$v['user_num'].')';
					}
				}
				$val['id'] = $val['ug_id'];
				$val['text'] = $val['ug_name'].'('.$val['user_num'].')';
			}
		}
		$list = array_merge(array(array('id' => 0, 'text' => '请选择客户组')), $list);
		return json_encode($list);
	}

	/**
	 * 获取搜索用组列表
	 */
	public function getSelectGroupListJson()
	{
		$groupList = $this->getGroupList(0, false);
		$list = array();
		if ($groupList) {
			$list = list_to_tree($groupList, 'ug_id', 'parent_id', 'children');
			foreach ($list as &$val) {
				if (@$val['children']) {
					foreach ($val['children'] as &$v) {
						$val['user_num'] += $v['user_num'];
						$v['id'] = $v['ug_id'];
						$v['text'] = $v['ug_name'].'('.$v['user_num'].')';
					}
				}
				$val['id'] = $val['ug_id'];
				$val['text'] = $val['ug_name'].'('.$val['user_num'].')';
			}
		}
		$list = array_merge(array(array('id' => -1, 'text' => '请选择分组'), array('id' => 0, 'text' => '未分组')), $list);
		return json_encode($list);
	}

	/**
	 * 获取搜索用组列表
	 */
	public function getMassUserGroupListJson()
	{
		$groupList = $this->getGroupList(0, true);
		$list = array();
		if ($groupList) {
			$list = list_to_tree($groupList, 'ug_id', 'parent_id', 'children');
			foreach ($list as &$val) {
				if (@$val['children']) {
					$val['user_num'] = '--';
					foreach ($val['children'] as &$v) {
						//$val['user_num'] += $v['user_num'];
						$v['id'] = $v['ug_id'];
						$v['text'] = $v['ug_name'].'('.$v['user_num'].')';
					}
				}
				$val['id'] = $val['ug_id'];
				$val['text'] = $val['ug_name'];
			}
		}
		$list = array_merge(array(array('id' => -1, 'text' => '全部'), array('id' => 0, 'text' => '未分组')), $list);
		return json_encode($list);
	}

	/**
	 * 获取模板群发客户组列表信息
	 */
	public function getTemplateUserGroupListJson()
	{
		$groupList = $this->getGroupList(0, true);
		$list = array();
		if ($groupList) {
			$list = list_to_tree($groupList, 'ug_id', 'parent_id', 'children');
			foreach ($list as &$val) {
				if (@$val['children']) {
					$val['user_num'] = '--';
					foreach ($val['children'] as &$v) {
						//$val['user_num'] += $v['user_num'];
						$val['ent_user_num'] += $v['ent_user_num'];
						$v['no_ent_user_num'] = $v['user_num'] - $v['ent_user_num'];
						$v['id'] = $v['ug_id'];
						$v['text'] = $v['ug_name'].'('.$v['user_num'].'/'.$v['ent_user_num'].'/'.$v['no_ent_user_num'].')';
					}
				}
				$val['no_ent_user_num'] = '--';
				$val['text'] = $val['ug_name'];
				//$val['no_ent_user_num'] = $val['user_num'] - $val['ent_user_num'];
				$val['id'] = $val['ug_id'];
				//$val['text'] = $val['ug_name'].'('.$val['user_num'].'/'.$val['ent_user_num'].'/'.$val['no_ent_user_num'].')';
			}
		}

		//$list = array_merge(array(array('id' => -1, 'text' => '全部'), array('id' => 0, 'text' => '未分组')), $list);
		$list = array(array('id' => -1, 'text' => '全部','children'=>array_merge(array( array('id' => 0, 'text' => '未分组')), $list)));
		return json_encode($list);
	}

	/**
	 * 获取用户分组Json列表
	 * @return array
	 */
	public function getGroupListJson()
	{
		$groupList = loadModel('Admin.UserGroup')->getList("AND create_type ='1'");
		$list = array();
		if ($groupList) {
			foreach ($groupList as $k => $v) {
				$list[$k]['id'] = $v['ug_id'];
				$list[$k]['text'] = $v['ug_name'];
				$list[$k]['parent_id'] = $v['parent_id'];
			}
			$list = list_to_tree($list, 'id', 'parent_id', 'children');
		}
		return json_encode($list);
	}

	/**
	 * 修改客户组
	 * @param string $openid
	 * @param array $gids
	 * @return bool
	 */
	public function changeGroup($openid, $gids)
	{
		if (! is_array($gids)) {
			$gids = array();
		}
		//先过滤一下数据
		$admin_create_gids = $this->getAdminCreateGroupIds($gids);
		$old_gids = $this->getGroupIdsByUser($openid);
		//用前端提交数据比数据库数据 是添加数据
		$insert_gids = array_diff($admin_create_gids, $old_gids);
		//反之删除数据
		$delete_gids = array_diff($old_gids, $admin_create_gids);

		$this->getDb()->startTrans();
		//添加关联表数据
		if ($insert_gids) {
			if (! $this->insertUserGroupMember($openid, $insert_gids)) {
				$this->getDb()->rollback();
				return false;
			}
		}
		//删除关联表数据
		if ($delete_gids) {
			if (! $this->deleteUserGroupMember($openid, $delete_gids)) {
				$this->getDb()->rollback();
				return false;
			}
		}
		$this->getDb()->commit();
		return true;
	}

	/**
	 * 获取管理员创建组ID集合
	 * @param unknown $gids
	 */
	public function getAdminCreateGroupIds($gids)
	{
		$sql = "SELECT ug_id FROM `wx_user_group`"
			." WHERE parent_id != 0 AND `ug_id` IN (" .$this->getDb()->genSqlInStr($gids).")";
		try {
			$ids = $this->getDb()->getCol($sql);
		} catch ( Exception $e ) {
			Logger::error("获取管理员创建组ID集合失败： ",$e->getMessage()."\n".$e->getTraceAsString());
			return array();
		}
		return $ids ? $ids : array();
	}

	/**
	 * 添加客户组关系
	 * @param string $openid
	 * @param array $gids
	 * @return bool
	 */
	public function insertUserGroupMember($openid, $gids)
	{
		if (! $openid || (! $gids || !is_array($gids))) {
			return false;
		}
		try {
			//添加关联表数据
			$values = '';
			$time = date('Y-m-d H:i:s');
			foreach ($gids as $g_id) {
				if (empty($values)) {
					$values = "('". $openid ."',". $g_id . ", '" . $time . "')";
				} else {
					$values .= ", ('". $openid ."',". $g_id . ",'" . $time . "')";
				}
			}
			$sql = "INSERT INTO `wx_user_group_member` (`openid`, `ug_id`, `create_time`) VALUES ".$values;
			return $this->getDb()->query($sql);
		} catch (Exception $e) {
			Logger::error("添加客户组关系失败： ",$e->getMessage()."\n".$e->getTraceAsString());
			return false;
		}
	}

	/**
	 * 删除客户组关系
	 * @param string $openid
	 * @param array $gids
	 * @return bool
	 */
	public function deleteUserGroupMember($openid, $gids)
	{
		if (! $openid || (! $gids || !is_array($gids))) {
			return false;
		}
		try {
			$where ="user = '{$openid}' AND ug_id IN (" .$this->getDb()->genSqlInStr($gids).")";
			return $this->getDb()->delete('wx_user_group_member', $where);
		} catch (Exception $e) {
			Logger::error("删除客户组关系失败： ", $e->getMessage()."\n".$e->getTraceAsString());
			return false;
		}
	}

	/**
	 * 转移客服组
	 * @param array $openid
	 * @param id $gid
	 */
	public function moveGroup($openids, $gid)
	{
		if (! is_array($openids)) {
			$openids = array();
		}

		if (loadModel('Admin.UserGroup')->isParentById($gid)) {
			return false;
		}
		$old_users = $this->getGroupUsersByGroup($gid);
		//用前端提交数据比数据库数据 是添加数据
		$insert_users = array_diff($openids, $old_users);
		//添加关联表数据
		if ($insert_users) {
			try {
				//添加关联表数据
				$values = '';
				$time = date('Y-m-d H:i:s');
				foreach ($insert_users as $openid) {
					if (empty($values)) {
						$values = "('". $openid ."',". $gid . ", '" . $time . "')";
					} else {
						$values .= ", ('". $openid ."',". $gid . ",'" . $time . "')";
					}
				}
				$sql = "INSERT INTO `wx_user_group_member` (`openid`, `ug_id`, `create_time`) VALUES ".$values;
				return $this->getDb()->query($sql);
			} catch (Exception $e) {
				Logger::error("添加客户组关系失败： ",$e->getMessage()."\n".$e->getTraceAsString());
				return false;
			}
		}

		return true;
	}

	/**
	 * 通过分组ID获取客户组user集合
	 * @param string $openid
	 * @return array
	 */
	public function getGroupUsersByGroup($gid)
	{
		$sql = "SELECT user FROM `wx_user_group_member` WHERE ug_id = '{$gid}'";
		try {
			$openids = $this->getDb()->getCol($sql);
		} catch (Exception $e) {
			Logger::error("通过分组ID获取客户组user集合失败： ", $e->getMessage()."\n".$e->getTraceAsString());
			return array();
		}
		return $openids ? $openids : array();
	}

	/**
	 * 是否是管理员创建组
	 * @param int $gid
	 */
	public function isAdminCreateGroup($gid)
	{
		$group = loadModel('Admin.UserGroup')->getGroupById($gid);
		return $group;
	}

	public function updateRemark($openid, $remark)
	{
		if(! $openid){
			$this->setError(-1, '操作失败,微信ID参数错误！');
			return false;
		}
		$charLen = 10;
		if (mb_strlen($remark,'utf-8') > $charLen) {
			$this->setError(-1, '操作失败,微信客户备注长度不能超过'.$charLen.'个字符！');
			return false;
		}

		$where = "`openid`='{$openid}'";
		$set['remark'] = $remark;

		try {
			return $this->getDb()->update('wx_user', $where, $set);
		} catch (Exception $e) {
			Logger::error(__FILE__.' . '.__METHOD__.'  '.__LINE__ ." 编辑微信客户备注失败 ", $e->getMessage()."\n".$e->getTraceAsString());
			return false;
		}
	}

	/**
	 * 根据用户ID获取用户所属客户组集合
	 * @param string $openid
	 * @return array
	 */
	public function getGroupListByUserId($openidId)
	{
		$sql = "SELECT ug.ug_id, ug.parent_id, ug.ug_name, ug.create_type, ug.create_source, ug.create_source_id,"
				." ug1.ug_id AS p_ug_id, ug1.ug_name AS p_ug_name, co.count AS count "
				." FROM `wx_user_group_member` ugm"
				." LEFT JOIN `wx_user_group` ug ON ug.ug_id = ugm.ug_id"
				." LEFT JOIN `wx_user_group` ug1 ON ug1.ug_id = ug.parent_id"
				." LEFT JOIN (SELECT g1.*,COUNT(ugm1.ug_id) AS count   FROM `wx_user_group` g1"
				." LEFT JOIN  `wx_user_group_member` ugm1 ON ugm1.ug_id = g1.ug_id  GROUP BY g1.ug_id ) co ON co.ug_id = ug.ug_id "
				." WHERE ugm.user = '{$openidId}' AND ug.ug_id > 0";
		try {
			$list = $this->getDb()->getAll($sql);
		} catch (Exception $e) {
			Logger::error("根据用户ID获取用户所属客户组集合失败： ", $e->getMessage()."\n".$e->getTraceAsString());
			return array();
		}
		$groupList = array();
		if($list){
			$parent_ids = array();
			foreach ($list as $value){
				array_push($parent_ids, $value['p_ug_id']);
			}
			$parent_ids = array_values(array_unique($parent_ids));
			foreach ($parent_ids as $key => $value){
				$arr = array();
				$arr['id'] = $value;
				$childArr = array();
				foreach ($list as $k=>$v){
					if($value == $v['p_ug_id']){
						$arr['name'] = $v['p_ug_name'];
						$child = array('id'=>$v['ug_id'], 'name'=>$v['ug_name'], 'count'=>$v['count']);
						array_push($childArr,$child);
					}
				}
				$arr['child'] = $childArr;
				array_push($groupList, $arr);
			}
		}
		return $groupList;
	}


}
