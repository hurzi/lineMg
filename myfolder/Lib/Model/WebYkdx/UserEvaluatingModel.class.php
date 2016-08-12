<?php
class UserEvaluatingModel extends Model
{

	private $evalModel = null;
	private static $USER_EVAL_STATUS = array (
			1 => '正在进行中',
			2 => '已结束'
	); //消息类型

	public function __construct(){
		$this->evalModel = loadModel("WebYkdx.Evaluating",false);
	}

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
		if(!empty($query['eval_id'])){
			$where .= " AND eval_id = '{$query['eval_id']}'";
		}
		if(!empty($query['status'])){
			$where .= " AND status = '{$query['status']}'";
		}
		if(!empty($query['user_id'])){
			$where .= " AND user_id = '{$query['user_id']}'";
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
			$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM `zgykdx_user_eval` ".$where." ORDER BY ue_id DESC LIMIT ".$limit;
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
	 * 获取单条评教记录
	 * @param unknown $where
	 * @return boolean
	 */
	public function getUserEvaluatingById($ue_id)
	{
		$ue_id = (int)$ue_id;
		if(!$ue_id){
			return false;
		}
		$sql = "select * from zgykdx_user_eval where ue_id=$ue_id";
		try {
			$result = $this->getDb()->getRow($sql);
			return $result;
		} catch ( Exception $e ) {
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
			return false;
		}
	}

	/**
	 * 获取单条评教记录
	 * @param unknown $where
	 * @return boolean
	 */
	public function getUserEvaluating($user_id,$eval_id)
	{
		$user_id = (int)$user_id;
		$eval_id = (int)$eval_id;
		if(!$eval_id){
			return false;
		}
		$sql = "select * from zgykdx_user_eval where user_id=$user_id and eval_id = $eval_id";
		try {
			$result = $this->getDb()->getRow($sql);
			return $result;
		} catch ( Exception $e ) {
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
			return false;
		}
	}
	
	/**
	 * 获取用户答题的总分
	 */
	public function getSumPoint($ue_id){
		try {
			$sql = "select sum(point) sum_point from zgykdx_user_eval_detail where ue_id = $ue_id";
			$res = $this->getDb()->getOne($sql);			
			return $res;
		} catch ( Exception $e ) {
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
			return false;
		}
	}
	
	/**
	 * 获取单条题目记录
	 * @param unknown $where
	 * @return boolean
	 */
	public function getUserEvaluatingDetails($ue_id)
	{
		$ue_id = (int)$ue_id;
		if(!$ue_id){
			return false;
		}
		$sql = "select * from zgykdx_user_eval_detail where ue_id = $ue_id";
		try {
			$res = $this->getDb()->getAll($sql);
			$result = array();
			if($res){
				foreach ($res as $v){
					$result['topic_id'] = $v;
				}
			}
			return $result;
		} catch ( Exception $e ) {
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
			return false;
		}
	}
	
	/**
	 * 获取用户已答题ID
	 * @param unknown $where
	 * @return boolean
	 */
	public function getUserHasTopicId($ue_id)
	{
		$ue_id = (int)$ue_id;
		if(!$ue_id){
			return false;
		}
		$sql = "select topic_id from zgykdx_user_eval_detail where ue_id = $ue_id";
		try {
			$ret = $this->getDb()->getAll($sql);
			$result = array();
			if($ret){
				foreach ($ret as $v){
					$result[] = $v['topic_id'];
				}
			}
			return $result;
		} catch ( Exception $e ) {
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
			return false;
		}
	}
	
	

	/**
	 * 增加用户评教活动
	 */
	public function addUserEvaluating($data)
	{
		$data = $this->checkUserEvalualingData($data);
		if(!$data){
			return false;
		}
		try {
			$id = $this->getDb()->insert("zgykdx_user_evaluating",$data);
			return $id;
		} catch ( Exception $e ) {
			$this->setError(10001, '增加评教失败');
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
			return false;
		}
	}
	/**
	 * 修改评教
	 */
	public function updateUserEvaluating($ue_id,$data)
	{
		$ue_id = (int) $ue_id;
		if(!$ue_id){
			return false;
		}
		if(!$this->checkEvalualingData($data,"update")){
			return false;
		}
		try {
			$ret = $this->getDb()->update("zgykdx_user_eval","ue_id=".$ue_id,$data);
			return $ret;
		} catch ( Exception $e ) {
			$this->setError(10001, '增加评教失败');
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
			return false;
		}
	}
	
	/**
	 * 增加详情
	 */
	public function addUserEvaluatingDetail($evalDetail)
	{
		
		$topicData = $this->checkEvalDetailData($evalDetail);
		if(!$topicData){
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  失败，参数检验失败');
			return false;
		}
		try {
			$id = $this->getDb()->insert("zgykdx_user_eval_detail",$topicData);
			if(!$id){
				throw new Exception("增加用户答题失败");
			}
			return $id;
		} catch ( Exception $e ) {
			$this->setError(10001, '增加评教失败');
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
			return false;
		}
	}
	
	/**
	 * 增加答题
	 */
	public function addUserEvaluatingDetails($ue_id,$evalDetails,$hasNext = true)
	{
		if(!$ue_id || !$evalDetails){
			return false;
		}		
		try {
			$this->getDb()->startTrans();
			foreach ($evalDetails as $evalDetail){
				$topicData = $this->checkEvalDetailData($evalDetail);
				if(!$topicData){
					Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  失败，参数检验失败');
					return false;
				}
				$id = $this->getDb()->insert("zgykdx_user_eval_detail",$topicData);
				if(!$id){
					throw new Exception("增加用户答题失败");
				}
			}
			if(!$hasNext){
				//更新总数据
				$sum_point = $this->userEvalModel->getSumPoint($ue_id);
				$ueParam = array(
						"sum_point"=>$sum_point,
						"status"=>2
				);
				$upRet = $this->updateUserEvaluating($ue_id,$ueParam);
				if(!$upRet){
					throw new Exception("处理答题失败");
				}
			}
			
			$this->getDb()->commit();
			return true;
		} catch ( Exception $e ) {
			$this->getDb()->rollback();
			$this->setError(10001, '增加评教失败');
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
			return false;
		}
	}
	
	/**
	 * 修改题目一题(含选项)
	 */
	public function updateUserEvaluatingDetail($ue_id,$topic_id,$evalDetail)
	{
		if(!$topic_id || !$ue_id){
			$this->setError(10001, '题干必须存在');
			return false;
		}
		$evalInfo = $this->getEvaluatingTopic($topic_id);
		if(!$evalInfo){
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  失败，eval_id所在评教不存在');
			return false;
		}		
		$topicData = $this->checkEvalDetailData($topicData,$evalDetail);
		if(!$topicData){
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  失败，参数检验失败');
			return false;
		}
		try {
			$ret = $this->getDb()->update("zgykdx_user_eval_detail","ue_id=".$ue_id."&topic_id=".$topic_id,$evalDetail);
			if(!$ret){
				throw new Exception("修改答案失败");
			}
			return true;
		} catch ( Exception $e ) {
			$this->setError(10001, '增加评教失败');
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
			return false;
		}
	}

	/**
	 * 检查数据
	 * @param unknown $data
	 * @param string $type
	 */
	public function checkUserEvalualingData($data,$type="add"){
		if(!$data || !is_array($data)){
			$this->setError(10001, '数据不能为空');
			return false;
		}
		if(!$data['user_id']){
			$this->setError(10001, '用户不能为空');
			return false;
		}
		if(!$data['eval_id']){
			$this->setError(10001, '评教不能为空');
			return false;
		}
		$result = array();
		copyArrayItem($data,$result,'openid');
		copyArrayItem($data,$result,'user_id');
		copyArrayItem($data,$result,'eval_id');
		copyArrayItem($data,$result,'sum_point');
		copyArrayItem($data,$result,'status');
		copyArrayItem($data,$result,'memo');
		copyArrayItem($data,$result,'create_time',false,date('Y-m-d H:i:s'));
		copyArrayItem($data,$result,'last_update_time',true,date('Y-m-d H:i:s'));
		
		//TODO 参数检查
		return $result;
	}
	/**
	 * 检查数据
	 * @param unknown $data
	 * @param string $type
	 */
	public function checkEvalDetailData($detailData,$type="add"){
		if(!$detailData || !is_array($detailData)){
			$this->setError(10001, '数据不能为空');
			return false;
		}	
		if(!$detailData['user_id']){
			$this->setError(10001, '用户必须存在');
			return false;
		}
		$result = array();
		copyArrayItem($detailData,$result,'ue_id');
		copyArrayItem($detailData,$result,'user_id');
		copyArrayItem($detailData,$result,'eval_id');
		copyArrayItem($detailData,$result,'topic_id');
		copyArrayItem($detailData,$result,'answer_key');
		copyArrayItem($detailData,$result,'point');
		copyArrayItem($detailData,$result,'content');
		copyArrayItem($detailData,$result,'create_time',false,date('Y-m-d H:i:s'));
		copyArrayItem($detailData,$result,'last_update_time',true,date('Y-m-d H:i:s'));
		
		//TODO 参数检查
		return $result;
	}
	
}
