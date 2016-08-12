<?php
class RegistUserModel extends Model
{
	private static $_tablename;
	
	private static $_sex_names = array(
			0 => '未知',
			1 => '男',
			2 => '女',
	);
	
	public function __construct(){
		self::$_tablename = "as_user";
	}
	
	/**
	 *修改个人信息
	 */
	public function UpdateUserInfoArrByUid($uid,$arrInfo){
		try{
			return $this->getDb()->update(self::$_tablename,"uid='".$uid."'",$arrInfo);
		}catch(Exception $e){
			Logger::error("操作用户表失败".$e->getMessage());
			return false;
		}
	}
	/**
	 * 获取所有马甲
	 */
	public function getObjMajia()  {
		try{
			return self::$_db->getRow("select * from ".self::$_tablename." where is_majia = 1");
		} catch(Exception $e) {
			Logger::error("操作用户表失败".$e->getMessage());
			return false;
		}
	}
	
	/**
	 * 通过uid获取详情
	 */
	public function getObjByUid($uid)  {
		try{
			return $this->getDb()->getRow("select * from ".self::$_tablename." where uid = '{$uid}'");
		} catch(Exception $e) {
			Logger::error("操作用户表失败".$e->getMessage());
			return false;
		}
	}
	
	/**
	 * 查询某一邮编的使用个数
	 */
	public function getUidByAsCode($ascode)  {
		try{
			return $this->getDb()->getOne("select uid from ".self::$_tablename." where as_code = '{$ascode}'");
		} catch(Exception $e) {
			Logger::error("操作用户表失败".$e->getMessage());
			return false;
		}
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
		if($args['openid']){
			$where .=" AND u.`openid`='{$args['openid']}'";
		}
		if ($args['truthname']) {
			$where .= " AND (u.truthname LIKE '{$args['truthname']}%')";
		}
		$joinTable = '';
		

		if ($args['sex'] != -1) {
			$where .= " AND u.sex = {$args['sex']}";
		}
		if ($args['is_majia'] != -1) {
			$where .= " AND u.is_majia = {$args['is_majia']}";
		}
		if ($args['mobile']) {
			$where .= " AND u.mobile = {$args['mobile']}";
		}
		if ($args['as_status'] != -1) {
			$where .= " AND u.as_status = {$args['as_status']}";
		}
		$limit = '';
		if ($args['paged'] && $args['pagesize']) {
			$limit = " LIMIT ".($args['paged'] -1) * $args['pagesize'].",".$args['pagesize'];
		}

		$order = " ORDER BY u.create_time DESC";

		$sql = "SELECT SQL_CALC_FOUND_ROWS u.*  FROM `as_user` u"
			." {$joinTable} {$where} {$groupby} {$order} {$limit}";

		$list = array();
		
		Logger::error("sql:".$sql);
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
			}
		}
		return array('list' => $list, 'count' => $count);
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
     * 通过微信id获取用户扫描二维码数据
     */
    public function saveOrUpdateAsUser($data,$isupdate=false)  {
    	try{
    		if(empty($data['truthname'])){
    			return false;
    		}
    		if(empty($data['sex'])){
    			unset($data['sex']);
    		}
    		if(empty($data['headimgurl'])){
    			$data['headimgurl'] = AbcConfig::BASE_WEB_DOMAIN_PATH."AisuoWeb/images/aslogo.jpg";
    		}
    			
    		if($isupdate){
    			$this->getDb()->update(self::$_tablename, "openid = '".$data['openid']."'",$data );
    		}else{
    			$this->getDb()->insert(self::$_tablename,$data);
    		}
    		return true;
    	} catch(Exception $e) {
    		Logger::error("操作用户表失败".$e->getMessage());
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
	 * 通过id获取客户信息
	 * @param string $user
	 * @return array
	 */
	public function getUserByUser($user)
	{
		$sql = "SELECT * FROM `as_user` WHERE `openid` = '%s'";
		try {
			return $this->getDb()->getRow(sprintf($sql, $user), true);
		} catch ( Exception $e ) {
			Logger::error("通过id获取客户信息失败： ",$e->getMessage()."\n".$e->getTraceAsString());
			return false;
		}
	}

	/**
	 * 通过id获取客户信息
	 * @param string $user
	 * @return array
	 */
	public function getStatusCount()
	{
		$sql = "SELECT count(1) sum_count
					,sum(case when as_status = 0 then 1 else 0 end) nobind_count
					,sum(case when as_status = 1 then 1 else 0 end) bind_count
					,sum(case when as_status = 2 then 1 else 0 end) unbind_count FROM `as_user` where is_majia=0 ";
		try {
			return $this->getDb()->getRow($sql, true);
		} catch ( Exception $e ) {
			Logger::error("通过id获取客户信息失败： ",$e->getMessage()."\n".$e->getTraceAsString());
			return false;
		}
	}

}
