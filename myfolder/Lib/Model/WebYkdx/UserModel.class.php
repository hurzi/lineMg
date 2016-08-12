<?php
class UserModel extends Model
{
	/**
	 * 获取列表列表
	 * @return array
	 */
	public function getList($query = NUll)
	{
		$where = '';
		if(!empty($query['starttime'])){
			$where .= " AND create_time > '{$query['starttime']}'";
		}
		if(!empty($query['endtime'])){
			$where .= " AND create_time <= '{$query['endtime']}'";
		}
		if(!empty($query['openid'])){
			$where .= " AND openid = '{$query['openid']}'";
		}
		if(!empty($query['user_id'])){
			$where .= " AND user_id = '{$query['user_id']}'";
		}
		if(!empty($query['user_number'])){
			$where .= " AND user_number = '{$query['user_number']}'";
		}
		if(!empty($query['user_phone'])){
			$where .= " AND user_phone = '{$query['user_phone']}'";
		}
		if(!empty($query['user_name'])){
			$where .= " AND user_name like '%{$query['user_name']}%'";
		}
		$page = 1;
		$pageRows = C('PAGE_LISTROWS');
		if (isset($query['page']) && ((int)$query['page']) >= 1) {
			$page = (int)$query['page'];
		}
		if (isset($query['page_rows']) && ((int)$query['page_rows']) >= 1) {
			$pageRows = (int)$query['page_rows'];
		}
		$limit = (($page - 1) * $pageRows).",".$pageRows;
		$where = ltrim(trim($where), 'AND');
		if ($where){
			$where = ' WHERE '.$where;
		}
		$result = array('count'=>0, 'list'=>array());
		try{
			$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM `zgykdx_user` ".$where." ORDER BY user_id DESC LIMIT ".$limit;
			$result['list'] = $this->getDb()->getAll($sql);
			$result['count'] = $this->getDb()->getOne('SELECT FOUND_ROWS()');
		}  catch (Exception $e){
			Logger::error(__METHOD__.' db error:' . $e->getMessage() . '; sql:' . $this->getDb()->getLastSql());
			return false;
		}
		if (!$result) {
			$result = array('count'=>0, 'list'=>array());
		}
		$pageObj = new Page($result['count'], $pageRows);
		$page = $pageObj->show();
		$result['pager'] = $page;
		return $result;		
	}

	

	/**
	 * 获取单条用户记录
	 * @param unknown $where
	 * @return boolean
	 */
	public function getUser($user_id)
	{
		$user_id = (int)$user_id;
		if(!$user_id){
			return false;
		}
		$sql = "select * from zgykdx_user where user_id = $user_id";
		try {
			$result = $this->getDb()->getRow($sql);
			return $result;
		} catch ( Exception $e ) {
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
			return false;
		}
	}
	
	/**
	 * 获取微信记录
	 * @param unknown $where
	 * @return boolean
	 */
	public function getWxUser($openid)
	{		
		$sql = "select * from wx_user where openid = $openid";
		try {
			$result = $this->getDb()->getRow($sql);
			return $result;
		} catch ( Exception $e ) {
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
			return false;
		}
	}
	
	/**
	 * 获取单条用户记录
	 * @param unknown $where
	 * @return boolean
	 */
	public function getUserByOpenid($openid)
	{
		if(!$openid){
			return false;
		}
		$cacheKey = GlobalCatchId::ABC_BASE_KEY."user_o_".$openid;
		$cacheValue = Factory::getCacher()->get($cacheKey);
		if($cacheValue){
			return $cacheValue;
		}
		$sql = "select * from zgykdx_user where openid = '$openid'";
		try {
			$result = $this->getDb()->getRow($sql);
			if($result){
				Factory::getCacher()->set($cacheKey,$result,GlobalCatchExpired::ABC_BASE_DURTION);
			}
			return $result;
		} catch ( Exception $e ) {
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
			return false;
		}
	}
	
	/**
	 * 获取单条用户记录通过手机号
	 * @param unknown $where
	 * @return boolean
	 */
	public function getUserByPhone($phone)
	{
		if(!$phone){
			return false;
		}
		$sql = "select * from zgykdx_user where user_phone = '$phone'";
		try {
			$result = $this->getDb()->getRow($sql);
			return $result;
		} catch ( Exception $e ) {
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
			return false;
		}
	}
	
	
	/**
	 * 增加评教
	 */
	public function addUser($data)
	{
		$userData = $this->checkUserData($data);
		if(!$userData){
			return false;
		}
		try {
			$id = $this->getDb()->insert("zgykdx_user",$userData);
			return $id;
		} catch ( Exception $e ) {
			$this->setError(10001, '增加用户失败');
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
			return false;
		}
	}
	/**
	 * 修改用户
	 */
	public function updateUser($user_id,$data)
	{
		$user_id = (int) $user_id;
		if(!$user_id){
			return false;
		}
		if(!$this->checkUserData($data,"update")){
			return false;
		}
		try {
			$id = $this->getDb()->update("zgykdx_user","user_id=".$user_id,$data);
			return $id;
		} catch ( Exception $e ) {
			$this->setError(10001, '修改用户失败');
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
			return false;
		}
	}
	

	/**
	 * 检查数据
	 * @param unknown $data
	 * @param string $type
	 */
	public function checkUserData($data,$type="add"){
		if(!$data || !is_array($data)){
			$this->setError(10001, '数据不能为空');
			return false;
		}
		if(!$data['openid']){
			$this->setError(10001, 'openid必须存在');
			return false;
		}
		if(!$data['user_phone']){
			$this->setError(10001, '手机号必须存在');
			return false;
		}
		$result = array();
		copyArrayItem($data,$result,'openid');
		copyArrayItem($data,$result,'user_phone');
		copyArrayItem($data,$result,'user_number');
		copyArrayItem($data,$result,'user_name');
		copyArrayItem($data,$result,'user_age');
		copyArrayItem($data,$result,'last_update_time',true,date('Y-m-d H:i:s'));
		if($type="add"){
			copyArrayItem($data,$result,'create_time',true,date('Y-m-d H:i:s'));
		}
		//TODO 参数检查
		return $result;
	}
}
