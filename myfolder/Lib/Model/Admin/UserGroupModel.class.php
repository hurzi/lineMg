<?php
class UserGroupModel extends Model
{
	private static $_CREATE_TYPE = array('1'=>'管理员创建', '2'=>'系统创建','3'=>'自动分组');
	private $_default_name = '默认分组';

	/**
	 * 类型列表
	 */
	public function getCreateType()
	{
		return self::$_CREATE_TYPE;
	}

	/**
	 * 获取一级组列表(11-21)
	 * @param array $data
	 * @return multitype:string Ambigous <multitype:, mixed>
	 */
	public function getFirstGroups ($data)
	{	
		$list = array();
		$page = '';
		$where = ' AND g1.parent_id = 0';
		if (isset($data['keyword']) && !empty($data['keyword']) ) {
			$where .= "AND g1.ug_name LIKE '%{$data['keyword']}%'";
		}
		if(isset($data['type']) && ! empty($data['type'])){
			$where .= " AND g1.create_type = {$data['type']}";
		}
		//过滤门店
		$branchIdStr = loadModel('Admin.User')->getAdminBranchId();
		if($branchIdStr){
			$where .= " AND g1.create_source_id IN(".$branchIdStr.") ";
		}
		//获取分页显示
		$count = $this->getGroupCount($where);
		if($count > 0){
			$p = new page($count,Config::PAGE_LISTROWS);
			$page = $p->show();
			$limit = " LIMIT $p->firstRow,$p->listRows ";
			//获取列表信息
			$list = $this->getFirstGroupList($where,$limit);
		}
		return array('list'=>$list, 'page'=>$page);
	}
	
	/**
	 * 获取二级组列表(11-21)
	 * @param int $firstGroupId 一级组id
	 * @param array $data
	 */
	public function getTwoGroups ($firstGroupId, $data)
	{
		$list = array();
		$page = '';
		$where = " AND g1.parent_id = {$firstGroupId}";
		if (isset($data['keyword']) && !empty($data['keyword']) ) {
			$where .= "AND g1.ug_name LIKE '%{$data['keyword']}%'";
		}
		//过滤门店
		$branchIdStr = loadModel('Admin.User')->getAdminBranchId();
		if($branchIdStr){
			$where .= " AND g1.create_source_id IN(".$branchIdStr.") ";
		}		
		//获取分页显示
		$count = $this->getGroupCount($where);
		if($count > 0){
			$p = new page($count,Config::PAGE_LISTROWS);
			//$page = $p->showJs($data['paged']);
			$page = $p->show($data['callback']);
			$limit = " LIMIT $p->firstRow,$p->listRows ";
			//获取列表信息
			$list = $this->getTwoGroupList($where,$limit);
		}
		return array('list'=>$list, 'page'=>$page);
	}
	
	/**
	 * 获取一级用户组列表(11-21)
	 * @param string $where 附件查询条件
	 * @param string $limit 附件分页limit
	 * @return array 列表数据
	 */
	public function getFirstGroupList ($where = '', $limit = '')
	{
		$where = "WHERE 1=1".$where;
		$order = "ORDER BY g1.create_time DESC,g1.ug_id DESC ";
		$group = "GROUP BY g1.ug_id";
		$sql = "SELECT g1.*,COUNT(g2.ug_id) AS count FROM `wx_user_group` g1 "
			  ." LEFT JOIN `wx_user_group` g2 ON g1.ug_id = g2.parent_id"
			  ." {$where} {$group} {$order} {$limit}";
		//echo $sql;
		try{
			return  $this->dbEnt->getAll($sql);
		}catch(Exception $e){
			Logger::error("获取一级客户组列表失败： ", $e->getMessage()."\n".$sql);
			return array();
		}
	}
	
	/**
	 * 获取二级客户组列表(11-21)
	 * @param string $where 附件查询条件
	 * @param string $limit 附件分页limit
	 * @return array 列表数据
	 */
	public function getTwoGroupList ($where = '', $limit = '')
	{
		$where = "WHERE 1=1".$where;
		$order = "ORDER BY g1.create_time DESC,g1.ug_id DESC ";
		$group = "GROUP BY g1.ug_id";
		$sql  = "SELECT g1.*,COUNT(ugm.ug_id) AS count "
			   ." ,g1.ug_id AS group_id,g1.ug_name AS group_name"  //TODO
			   ." FROM `wx_user_group` g1"
			   ." LEFT JOIN  `wx_user_group_member` ugm ON ugm.ug_id = g1.ug_id"
		       ." {$where} {$group} {$order} {$limit}";
		//echo $sql;
		try{
			return  $this->dbEnt->getAll($sql);
		}catch(Exception $e){
			Logger::error("获取二级客户组列表失败： ", $e->getMessage()."\n".$sql);
			return array();
		}
	}
	
	/**
	* 获取客户组列表总数(11-21)
	* @param string $where
	*/
	public function getGroupCount ($where = '')
	{
		$where = "WHERE 1=1".$where;
		$sql = "SELECT COUNT(*) FROM `wx_user_group` g1 {$where}";
		try{
			return $this->dbEnt->getOne($sql);
		}catch(Exception $e){
			Logger::error("获取客户组列表总数失败： ", $e->getMessage().$sql);
			return 0;
		}
	}	
	
	/**
	 * 通过id获取一级客户分组信息(不区分创建类型)(11-21)
	 * @param int $id
	 * @return array
	 */
	public function getParentGroupById($id)
	{
		$sql = "SELECT * FROM `wx_user_group` WHERE parent_id = 0 AND ug_id = %d";
		try {
			$group = $this->dbEnt->getRow(sprintf($sql, $id), true);
		} catch ( Exception $e ) {
			Logger::error("通过id获取一级客户分组信息失败： ",$e->getMessage()."\n".$sql);
			return false;
		}
		return $group;
	}
	
	/**
	 * 获取分组列表
	 * @param array $args
	 * @return array
	 */
	public function getGroups($args = array())
	{
		$where = '';
		if (isset($args['ug_name']) && ! empty($args['ug_name']) ) {
			$where .= "AND ug.ug_name LIKE '%{$args['ug_name']}%'";
		}
		if(isset($args['create_type']) && ! empty($args['create_type'])){
			$where .= " AND ug.create_type = {$args['create_type']}";
		}

		$sql = "SELECT ug.*, COUNT(ugm.ug_id) AS user_num"
			." FROM `wx_user_group` ug"
			." LEFT JOIN `wx_user_group_member` ugm ON ugm.ug_id = ug.ug_id"
			." WHERE 1 {$where} "
			." GROUP BY ug.ug_id ORDER BY ug.create_type, ug.ug_id DESC ";

		try {
			$list = $this->dbEnt->getAll($sql);
		} catch (Exception $e) {
			Logger::error("获取客户分组列表失败： ", $e->getMessage()."\n".$e->getTraceAsString().$sql);
			return false;
		}

		return $this->_parseGroups($list);
	}

	/**
	 * 处理数据
	 * @param array $list
	 */
	private function _parseGroups($list)
	{
		$list = list_to_tree($list, 'ug_id', 'parent_id', 'children');

		foreach ($list as &$val) {
			$val['type'] = self::$_CREATE_TYPE[$val['create_type']];
			if (@$val['children']) {
				$val['user_num'] = '---';
				foreach ($val['children'] as &$v) {
					$v['type'] = self::$_CREATE_TYPE[$v['create_type']];
				}
			}
		}
		return $list;
	}

	/**
	 * 获取顶级组列表
	 * @param number $group_id
	 * @param string $create_type 创建类型 
	 * @return array
	 */
	public function getParentList($group_id = 0,$create_type='')
	{
		$where = ' AND parent_id = 0 ';
		if($create_type){
			$where .= " AND create_type = {$create_type}";
		}
		if ($group_id) {
			$where .= " AND ug_id != {$group_id}";
		}
		//过滤门店
		$branchIdStr = loadModel('Admin.User')->getAdminBranchId();
		if($branchIdStr){
			$where .= " AND create_source_id IN(".$branchIdStr.") ";
		}
		
		$sql = "SELECT * FROM `wx_user_group` WHERE 1 {$where} ";
		try {
			$list = $this->dbEnt->getAll($sql);
		} catch (Exception $e) {
			Logger::error("获取顶级组列表失败： ", $e->getMessage()."\n".$e->getTraceAsString().$sql);
			return false;
		}
		return $list;
	}

    /**
     * 获取客户分组列表
     *
     * @param  string $where 附加的SQL条件
     * @param  string $limit 分页条件
     * @return array
     */
    public function getList($where = '', $limit = '')
    {
    	$sql = "SELECT ug.*, COUNT(ugm.ug_id) AS user_num"
    		." FROM `wx_user_group` ug"
    		." LEFT JOIN `wx_user_group_member` ugm ON ugm.ug_id = ug.ug_id"
    		." WHERE 1 {$where} "
    		." GROUP BY ug.ug_id ORDER BY ug.create_type, ug.ug_id DESC ";
    	if ($limit) {
    		$sql .= $limit;
    	}
    	$list = array();
	    try {
	    	$list = $this->dbEnt->getAll($sql);
	    } catch (Exception $e) {
	        Logger::error("获取客户分组列表失败： ", $e->getMessage()."\n".$e->getTraceAsString().$sql);
	        return false;
	    }

	    if ($list) {
	    	foreach ($list as $k => &$v) {
	    		$v['type'] = self::$_CREATE_TYPE[$v['create_type']];
	    	}
	    }
	    return $list;
    }

    /**
     * 获取客户分组总数
     *
     * @param  string $where 附加的SQL条件
     * @return int
     */
    public function getTotal($where = '')
    {
    	$sql = "SELECT COUNT(*) FROM `wx_user_group` ug WHERE 1 {$where}";
	    try {
	    	return $this->dbEnt->getOne($sql);
	    } catch (Exception $e) {
	        Logger::error("获取客户分组总数失败： ", $e->getMessage()."\n".$e->getTraceAsString().$sql);
	        return false;
	    }
    }

    /**
     * 检测添加/编辑数据
     *
     * @param array  $data
     * @param string  $type 客户分组类型 add | update 默认add
     * @return array|false  $data
     */
    public function checkData($data, $type = 'add')
    {
    	if (! $data['ug_name']) {
    		$this->setError(1, "客户分组名称不能为空！");
    		return false;
    	}

    	if ($data['parent_id']) {
    		if (! $this->isParentById($data['parent_id'])) {
    			$this->setError(1, "选择的上级组不存在！");
    			return false;
    		}
    	}

    	$group = $this->getGroupByName($data['ug_name'], $data['parent_id']);
    	if ('add' == $type) {
    		if ($group) {
    			$this->setError(1, "客户分组名称已经存在！");
    			return false;
    		}
    		$data['create_type'] = '1';
    		$data['create_time'] = date('Y-m-d H:i:s');
    	} else {
    		$groupInfo = $this->getGroupById($data['ug_id']);
    		if (! $groupInfo) {
    			$this->setError(1, "要编辑的客户分组不存在！");
    			return false;
    		}
    		if ($data['parent_id'] > 0 && $this->isExistChildById($data['ug_id'])) {
    			$this->setError(1, "要编辑的客服组有子级分组不允许变更为子级组！");
    			return false;
    		}
    		if ($group) {
    			if ($group['ug_id'] != $data['ug_id']) {
    				$this->setError(1, "客户分组名称已经存在！");
    				return false;
    			}
    		}
    		if ($data['parent_id'] == $data['ug_id']) {
    			$data['parent_id'] = 0;
    		}
    		unset($data['ug_id']);
    	}
    	return $data;
    }

    /**
     * 是否是父级组
     * @param int $id
     */
    public function isParentById($id)
    {
    	$sql = "SELECT COUNT(*) FROM `wx_user_group` WHERE ug_id = %d AND parent_id = 0";
    	try {
    		return $this->dbEnt->getOne(sprintf($sql, $id));
    	} catch ( Exception $e ) {
    		Logger::error("是否是父级组失败： ",$e->getMessage()."\n".$e->getTraceAsString());
    		return false;
    	}
    }

    /**
     * 获取子级组ID集合
     * @param $id
     * @return array
     */
    public function getChildIdsById($id)
    {
    	$sql = "SELECT ug_id FROM `wx_user_group` WHERE parent_id = %d";
    	try {
    		return $this->dbEnt->getCol(sprintf($sql, $id));
    	} catch ( Exception $e ) {
    		Logger::error("获取子级组ID集合失败： ",$e->getMessage()."\n".$e->getTraceAsString());
    		return false;
    	}
    }

    /**
     * 检测是否有子级
     * @param  int $id
     * @return boolean
     */
    public function isExistChildById($id)
    {
    	$sql = "SELECT COUNT(*) FROM `wx_user_group` WHERE parent_id = %d";
    	try {
    		return $this->dbEnt->getOne(sprintf($sql, $id));
    	} catch ( Exception $e ) {
    		Logger::error("检测是否有子级失败： ",$e->getMessage()."\n".$e->getTraceAsString());
    		return true;
    	}
    }

	/**
	 * 通过id获取客户分组信息
	 * @param int $id
	 * @return array
	 */
	public function getGroupById($id)
	{
		$sql = "SELECT * FROM `wx_user_group` WHERE create_type = '1' AND ug_id = %d";
		try {
			$group = $this->dbEnt->getRow(sprintf($sql, $id), true);
		} catch ( Exception $e ) {
			Logger::error("通过id获取客户分组信息失败： ",$e->getMessage()."\n".$e->getTraceAsString());
			return false;
		}
		return $group;
	}

	/**
	 * 通过name获取客户分组信息
	 * @param string $name
	 * @param int $parent_id
	 * @return array
	 */
	public function getGroupByName($name, $parent_id = 0)
	{
		$sql = "SELECT * FROM `wx_user_group` WHERE create_type = '1' AND ug_name = '%s' AND parent_id = %d";
		try {
			$group = $this->dbEnt->getRow(sprintf($sql, $name, $parent_id), true);
		} catch ( Exception $e ) {
			Logger::error("通过name获取客户分组信息失败： ",$e->getMessage()."\n".$e->getTraceAsString());
			return false;
		}
		return $group;
	}

    /**
     * 添加客户分组
     *
     * @param  array  $data
     * @return int  $id
     */
    public function add($data)
    {
    	$this->dbEnt->startTrans();
    	try{
    		$id = $this->dbEnt->insert('wx_user_group', $data);
    		if (! $id) {
    			return false;
    		}
    		if (0 == $data['parent_id']) {
    			$data['ug_name'] = $this->_default_name;
    			$data['parent_id'] = $id;
    			if (! $this->dbEnt->insert('wx_user_group', $data)) {
    				$this->dbEnt->rollback();
    				return false;
    			}
    		}
    	} catch(Exception $e){
    		$this->dbEnt->rollback();
    		Logger::error("新增客户分组失败： ",$e->getMessage()."\n".$e->getTraceAsString());
    		return false;
    	}
    	$this->dbEnt->commit();
    	return true;
    }

    /**
     * 更新客户分组信息
     *
     * @param  int    $id ID
     * @param  array  $data 更新数据
     * @return bool
     */
    public function update($id, $data)
    {
    	try {
    		$where = "create_type = '1' AND ug_id = {$id}";
    		return $this->dbEnt->update('wx_user_group', $where, $data);
    	} catch (Exception $e){
    		Logger::error("修改客户分组信息失败： ", $e->getMessage()."\n".$e->getTraceAsString());
    		return false;
    	}
    }

    /**
     * 删除客户分组
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
    	if (! $id) {
    		$this->setError(1, "请选择要删除的栏目！");
    		return false;
    	}

    	$group = $this->getGroupById($id);
    	if (! $group) {
    		$this->setError(1, "选择的客户组不存在！");
    		return false;
    	}

    	if ('2' == $group['create_type']) {
    		$this->setError(1, "系统创建组不允许删除！");
    		return false;
    	}

    	if ($this->checkGroupIsUse($id)) {
    		$this->setError(1, "当前客户组已经被使用，无法删除！");
    		return false;
    	}

    	$this->dbEnt->startTrans();
    	try {
    		$where = "ug_id = {$id}";
    		if(($parent_id = $this->checkIsOnlyChild($group['parent_id'])) != false){
    			$where .= " OR ug_id = {$group['parent_id']} ";
    		}
    		//删除组
    		if (! $this->dbEnt->delete('wx_user_group', $where)) {
    			$this->setError(1, "删除操作失败！");
    			return false;
    		}
    		//删除关系
    		if (false === $this->dbEnt->delete('wx_user_group_member', $where)) {
    			$this->dbEnt->rollback();
    			$this->setError(1, "删除操作失败！");
    			return false;
    		}
    	} catch (Exception $e){
    		$this->dbEnt->rollback();
    		Logger::error("删除客户分组失败：", $e->getMessage()."\n".$e->getTraceAsString());
    		$this->setError(1, "删除操作失败！");
    		return false;
    	}
    	$this->dbEnt->commit();
    	return true;
    }

    /**
     * 检测当前组是否是唯一的子组
     * @param int $parent_id
     * @return bool
     */
    public function checkIsOnlyChild($parent_id)
    {
    	$sql = "SELECT COUNT(*) FROM `wx_user_group` WHERE parent_id = %d";
    	try {
    		$count = $this->dbEnt->getOne(sprintf($sql, $parent_id));
    	} catch (Exception $e) {
    		Logger::error("检测当前组是否是唯一的子组失败 ", $e->getMessage()."\n".$e->getTraceAsString());
    		return false;
    	}
    	return 1 == $count;
    }

    /**
     * 检测组是否被使用
     * @param int $id
     * @param int $ent_id
     * @return bool
     */
    public function checkGroupIsUse($id)
    {
    	if ($this->isExistChildById($id)) {
    		return true;
    	}
    	return false;
    }
    
    /**
     * 根据openId获取默认门店客户组(12-6)
     * @param string $user
     * @return array
     */
    public function getBranchIdsByUser($user)
    {
    	$sql = "SELECT g.create_source_id FROM `wx_user_group` g"
    		." LEFT JOIN `wx_user_group_member` ugm ON g.ug_id = ugm.ug_id"
    		." WHERE ugm.user = '{$user}' ";
    
    	try {
    		$ids = $this->dbEnt->getCol($sql);
    	} catch (Exception $e) {
    		Logger::error("根据openId获取默认门店客户组失败： ", $e->getMessage()."\n".$sql);
    		return array();
    	}
    	return $ids ? array_filter(array_unique($ids)) : array();
    }
}
