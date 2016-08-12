<?php
class CWishModel extends Model
{

	private $_tableName = 'yh_wed';

	private static $USER_TYPE = array (
			'1' => '默认',
			'2' => '群众'
	); //消息类型

	private static $COME_TYPE = array (
			'1' => '不来',
			'2' => '来'
	);

	/**
	 *修改
	 */
	public function update($id,$arrInfo){
		try{
			return $this->getDb()->update($this->_tableName,"id='".$id."'",$arrInfo);
		}catch(Exception $e){
			Logger::error("操作用户表失败".$e->getMessage());
			return false;
		}
	}
	/**
	 * 获取所有
	 */
	public function getAll()  {
		try{
			return self::$_db->getRow("select * from ".$this->_tableName."");
		} catch(Exception $e) {
			Logger::error("操作用户表失败".$e->getMessage());
			return false;
		}
	}
	
	/**
	 * 通过id获取详情
	 */
	public function getById($id)  {
		try{
			return $this->getDb()->getRow("select * from ".$this->_tableName." where id = '{$id}'");
		} catch(Exception $e) {
			Logger::error("操作用户表失败".$e->getMessage());
			return false;
		}
	}
	
	/**
	 * 通过id获取详情
	 */
	public function delById($id)  {
		try{
			return $this->getDb()->delete($this->_tableName,"id = '{$id}'");
		} catch(Exception $e) {
			Logger::error("操作用户表失败".$e->getMessage());
			return false;
		}
	}
	/**
	 * 通过cookieid获取详情
	 */
	public function getByCookieId($cookieId)  {
		try{
			return $this->getDb()->getRow("select * from ".$this->_tableName." where ccookieid = '{$cookieId}'");
		} catch(Exception $e) {
			Logger::error("操作用户表失败".$e->getMessage());
			return false;
		}
	}
	
	
	/**
	 * 获取首页显示数据
	 * @param array $args
	 * @return array
	 */
	public function getList($args)
	{
		$where = "WHERE 1";
		$groupby = "";
		if(isset($args['cphone'])){
			$where .=" AND u.`cphone`='{$args['cphone']}'";
		}
		if (isset($args['cname'])) {
			$where .= " AND (u.cname LIKE '%{$args['cname']}%')";
		}
		$joinTable = '';		

		if (isset($args['type']) && in_array($args['type'],array(1,2))) {
			$where .= " AND u.type = {$args['type']}";
		}
		if (isset($args['status'])) {
			$where .= " AND u.status = {$args['status']}";
		}
		$limit = '';
		if (isset($args['paged']) && isset($args['pagesize'])) {
			$limit = " LIMIT ".($args['paged'] -1) * $args['pagesize'].",".$args['pagesize'];
		}

		$order = " ORDER BY u.create_time DESC";

		$sql = "SELECT SQL_CALC_FOUND_ROWS u.*  FROM `".$this->_tableName."` u"
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

		return array('list' => $list, 'count' => $count);
	}


    /**
     * 获取客户总数
     *
     * @param  string $where 附加的SQL条件
     * @return int
     */
    public function getTotalCount($where = '')
    {
    	$sql = "SELECT COUNT(*) FROM `".$this->_tableName."` WHERE 1 {$where}";
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
    		if(empty($data['cname']) || empty($data['cphone']) || empty($data['cwish'])){
    			return false;
    		}
    		if(empty($data['type'])){
    			$data['type'] = 1;
    		}
    		if(!$isupdate && empty($data['ccookieid'])){
    			$cookieid = trim(@$_COOKIE["c_cookieid"]);
    			if(!$cookieid){
    				$cookieid = getRandStr(10);
    				setcookie('c_cookieid', $cookieid, time()+3600*24*900);
    			}
    			$data['ccookieid'] = $cookieid;    			
    		}
    		if(empty($data['ctype'])){
    			$data['ctype'] = 1;
    		}
    		if(empty($data['ccount'])){
    			$data['ccount'] = 1;
    		}
    		if($isupdate){
    			unset($data['create_time']);
    			$this->getDb()->update($this->_tableName, "ccookieid = '".$data['ccookieid']."'",$data );
    		}else{
    			$this->getDb()->insert($this->_tableName,$data);
    		}
    		return true;
    	} catch(Exception $e) {
    		Logger::error("操作用户表失败".$e->getMessage());
    		return false;
    	}
    }
   
   

    /**
     * 获取类型
     * @return array
     */
    public function getComeList()
    {
    	return self::$COME_TYPE;
    }

}
