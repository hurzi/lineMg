<?php
class EvaluatingModel extends Model
{


	public static $EVAL_STATUS = array (
			1 => '正常',
			2 => '暂停无效'
	); //消息类型

	public static $EVAL_TYPE = array (
			'1' => '问券调查',
			'2' => '评教打分',
			'3' => '考试(无问答题)'
	);
	
	public static $EVAL_TOPIC_TYPE = array (
			'1' => '单选题',
			'2' => '多选题',
			'3' => '打分题',
			'4' => '单选打分题',
			'5' => '问题题'
	);

	/**
	 * 获取列表列表
	 * @return array
	 */
	public function getList($query = NUll)
	{
		$where = '';
		if(!empty($query['eval_starttime'])){
			$where .= " AND eval_starttime > '{$query['eval_starttime']}'";
		}
		if(!empty($query['eval_endtime'])){
			$where .= " AND eval_endtime <= '{$query['eval_endtime']}'";
		}
		if(!empty($query['eval_type'])){
			$where .= " AND eval_type = '{$query['eval_type']}'";
		}
		if(!empty($query['eval_status'])){
			$where .= " AND eval_status = '{$query['eval_status']}'";
		}
		if(!empty($query['eval_name'])){
			$where .= " AND eval_name like '%{$query['eval_name']}%'";
		}
		$page = 1;
		$pageRows = Config::PAGE_LISTROWS;
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
			$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM `zgykdx_evaluating` ".$where." ORDER BY eval_id DESC LIMIT ".$limit;
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
		$page = $pageObj->show_5();
		$result['pager'] = $page;
		return $result;		
	}

	

	/**
	 * 获取单条评教记录
	 * @param unknown $where
	 * @return boolean
	 */
	public function getEvaluating($eval_id)
	{
		$eval_id = (int)$eval_id;
		if(!$eval_id){
			return false;
		}
		$cacheKey = GlobalCatchId::ABC_BASE_KEY."evalid_".$eval_id;
		$cacheValue = Factory::getCacher()->get($cacheKey);
		if($cacheValue){
			return $cacheValue;
		}
		$sql = "select * from zgykdx_evaluating where eval_id = $eval_id";
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
	 * 获取单条题目记录
	 * @param unknown $where
	 * @return boolean
	 */
	public function getEvaluatingTopic($topic_id,$isGetItmes = true)
	{
		$topic_id = (int)$topic_id;
		if(!$topic_id){
			return false;
		}
		$cacheKey = GlobalCatchId::ABC_BASE_KEY."evaltopicid_".$topic_id."_".$isGetItmes;
		$cacheValue = Factory::getCacher()->get($cacheKey);
		if($cacheValue){
			return $cacheValue;
		}
		
		$sql = "select * from zgykdx_evaluating_topic where topic_id = $topic_id";
		try {
			$result = $this->getDb()->getRow($sql);
			if($result && $isGetItmes){
				$result['items'] = $this->getEvaluatingItemByTopicId($topic_id);
			}
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
	 * 获取一个试题的所有topicid
	 * @param unknown $eval_id
	 */
	public function getEvaluationTopicIdAll($eval_id){
		$list = $this->getEvaluatingTopicAll($eval_id);
		$result = array();
		if($list){
			foreach ($list as $v){
				$result[] = $v['topic_id'];
			}
		}
		return $result;
	}
	
	/**
	 * 获取所有题(带题项)
	 * @param unknown $where
	 * @return boolean
	 */
	public function getEvaluatingTopicAll($eval_id,$appendItems = true)
	{
		$eval_id = (int)$eval_id;
		if(!$eval_id){
			return false;
		}
		$sql = "select * from zgykdx_evaluating_topic where eval_id = $eval_id";
		try {
			$topicRes = $this->getDb()->getAll($sql);
			if($topicRes && $appendItems){
				$itemsql = "select * from zgykdx_evaluating_item where eval_id = $eval_id order by item_order";
				$itemRes = $this->getDb()->getAll($itemsql);				
				$itemmap = array();
				foreach ($itemRes as $v){
					$itemmap[$v['topic_id']][] = $v;
				}
				foreach ($topicRes as &$tv){
					$items = &$itemmap[$tv['topic_id']];
					if($items){
						$tv['items'] = $items;
					}
				}
			}				
			return $topicRes;
		} catch ( Exception $e ) {
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
			return false;
		}
	}

	/**
	 * 获取某一题的所有题项
	 * @param unknown $topic_id
	 * @return boolean|Ambigous <NULL, multitype:multitype: >
	 */
	public function getEvaluatingItemByTopicId($topic_id){
		$topic_id = (int)$topic_id;
		if(!$topic_id){
			return false;
		}
		$sql = "select * from zgykdx_evaluating_item where topic_id = $topic_id";
		try {
			$result = $this->getDb()->getAll($sql);
			return $result;
		} catch ( Exception $e ) {
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
			return false;
		}
	}
	/**
	 * 增加评教
	 */
	public function addEvaluating($data)
	{
		$data = $this->checkEvalualingData($data);
		if(!$data){
			return false;
		}
		try {
			$id = $this->getDb()->insert("zgykdx_evaluating",$data);
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
	public function updateEvaluating($eval_id,$data)
	{
		$eval_id = (int) $eval_id;
		if(!$eval_id){
			return false;
		}
		if(!$this->checkEvalualingData($data,"update")){
			return false;
		}
		try {
			$evalInfo = $this->getEvaluating($eval_id);
			if(isset($data['eval_type']) && $evalInfo['topic_count']>0 && $evalInfo['eval_type'] != $data['eval_type']){
				$this->setError(10001, "已经存在题目的情况下不能修改类型");
				return false;
			}
			
			$id = $this->getDb()->update("zgykdx_evaluating","eval_id=".$eval_id,$data);
			
			$this->cleanCache($eval_id,null); //清除缓存
					
			return $id;
		} catch ( Exception $e ) {
			$this->setError(10001, '增加评教失败');
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
			return false;
		}
	}
	
	/**
	 * 删除一题(含选项)
	 */
	public function delEvaluatingTopic($topic_id)
	{
		$topic_id = (int) $topic_id;
		if(!$topic_id){
			return false;
		}
		try {
			$this->getDb ()->startTrans ();
			
			//检查数据状态
			if($this->getDb()->getOne("select count(1) from zgykdx_user_eval_detail where topic_id=".$topic_id)>0){
				$this->setError(10001, "该题已经有人答题不允许删除");
				throw new Exception("该题已经有人答题不允许删除");
			}
			$topicInfo = $this->getEvaluatingTopic($topic_id,false);
			if(!$topicInfo){
				$this->setError(10001, "题目信息不存在");
				throw new Exception("题目信息不存在");
			}
			
			//删除所有选项
			$this->getDb()->delete("zgykdx_evaluating_item", "topic_id=".$topic_id);
			
			//删除题目
			$this->getDb()->delete("zgykdx_evaluating_topic",  "topic_id=".$topic_id);
			
			//更新题库数
			$topicCount = $this->getDb()->getOne("select count(1) from zgykdx_evaluating_topic where eval_id=".$topicInfo['eval_id']);
			$updateStatus = $this->getDb()->query("update zgykdx_evaluating set topic_count=$topicCount where eval_id=".$topicInfo['eval_id']);
			if(!$updateStatus){
				throw new Exception("更新题数失败");
			}
			//清缓存
			$this->cleanCache($topicInfo['eval_id'],$topic_id); //清除缓存
					
			$this->getDb ()->commit ();
			return true;
		} catch ( Exception $e ) {
			$this->getDb ()->rollback ();
			$this->setError ( 10001, '增加评教失败' );
			Logger::error ( __FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage () . "\n" . $e->getTraceAsString () );
			return false;
		}
	}
	
	/**
	 * 增加题目一题(含选项)
	 */
	public function addEvaluatingTopicAndItem($topicData,$itemsData)
	{
		if(!$topicData['eval_id']){
			$this->setError(10001, '参数不正确');
			return false;
		}
		$evalInfo = $this->getEvaluating($topicData['eval_id']);
		if(!$evalInfo){
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  失败，eval_id所在评教不存在');
			return false;
		}
		$topicData = $this->checkTopicData($topicData,$itemsData);
		if(!$topicData){
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  失败，参数检验失败');
			return false;
		}
		try {
			$this->getDb()->startTrans();
			$id = $this->getDb()->insert("zgykdx_evaluating_topic",$topicData);
			if(!$id){
				throw new Exception("增加题干失败");
			}
			$topicData['topic_id'] = $id;
			if(in_array($topicData['topic_type'],array(1,2,4))){
				foreach ($itemsData as $v){
					$v['topic_id'] = $id;
					$v['eval_id'] = $topicData['eval_id'];
					$checkItemData = $this->checkTopicItemData($topicData, $v);
					if(!$checkItemData){
						throw new Exception("检查题项数据失败".$this->getError());
					}					
					$itemid = $this->getDb()->insert("zgykdx_evaluating_item",$checkItemData);
					if(!$itemid){
						throw new Exception("增加题项失败");
					}
				}
			}
			//更新题库数
			$topicCount = $this->getDb()->getOne("select count(1) from zgykdx_evaluating_topic where eval_id=".$topicData['eval_id']);
			$updateStatus = $this->getDb()->query("update zgykdx_evaluating set topic_count=$topicCount where eval_id=".$topicData['eval_id']);
			if(!$updateStatus){
				throw new Exception("更新题数失败");
			}
			//清缓存
			$this->cleanCache($topicData['eval_id']);
			$this->getDb()->commit();
			return $id;
		} catch ( Exception $e ) {
			$this->getDb()->rollback();
			$this->setError(10001, '增加评教失败'.$this->getError());
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
			return false;
		}
	}
	
	/**
	 * 修改题目一题(含选项)
	 */
	public function updateEvaluatingTopicAndItem($topic_id,$topicData,$itemsData)
	{
		if(!$topic_id){
			$this->setError(10001, '题干必须存在');
			return false;
		}
		$topicInfo = $this->getEvaluatingTopic($topic_id);
		if(!$topicInfo){
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  失败，topic_id所在题目不存在');
			return false;
		}		
		$topicData['eval_id'] = $topicInfo['eval_id'];
		$topicData = $this->checkTopicData($topicData,$itemsData);
		if(!$topicData){
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  失败，参数检验失败');
			return false;
		}
		try {
			$this->getDb()->startTrans();
			$ret = $this->getDb()->update("zgykdx_evaluating_topic","topic_id=".$topic_id,$topicData);
			if(!$ret){
				throw new Exception("增加题干失败");
			}
			$this->getDb()->query("delete from zgykdx_evaluating_item where topic_id=".$topic_id);
			if(in_array($topicData['topic_type'],array(1,2,4))){
				foreach ($itemsData as $v){
					$v['topic_id'] = $topic_id;
					$v['eval_id'] = $topicData['eval_id'];
					$checkItemData = $this->checkTopicItemData($topicData, $v);
					if(!$checkItemData){
						throw new Exception("检查题项数据失败");
					}
					$itemid = $this->getDb()->insert("zgykdx_evaluating_item",$checkItemData);
					if(!$itemid){
						throw new Exception("增加题项失败");
					}
				}
			}
			$this->cleanCache(null,$topic_id); //清除缓存
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
	 * 检查数据
	 * @param unknown $data
	 * @param string $type
	 */
	public function checkEvalualingData($data,$type="add"){
		if(!$data || !is_array($data)){
			$this->setError(10001, '数据不能为空');
			return false;
		}
		if(!$data['eval_name']){
			$this->setError(10001, '评教名称必须存在');
			return false;
		}
		$result = array();
		copyArrayItem($data,$result,'eval_name');
		copyArrayItem($data,$result,'eval_descript');
		copyArrayItem($data,$result,'eval_starttime');
		copyArrayItem($data,$result,'eval_endtime');
		copyArrayItem($data,$result,'eval_type');
		copyArrayItem($data,$result,'eval_status');
		copyArrayItem($data,$result,'eval_max_topic');
		copyArrayItem($data,$result,'last_update_time',true,date('Y-m-d H:i:s'));
		if($type=="add"){
			copyArrayItem($data,$result,'create_time',true,date('Y-m-d H:i:s'));			
		}
		//TODO 参数检查
		return $result;
	}
	/**
	 * 检查数据
	 * @param unknown $data
	 * @param string $type
	 */
	public function checkTopicData($topicData,$itemsData,$type="add"){
		if(!$topicData || !is_array($topicData)){
			$this->setError(10001, '数据不能为空');
			return false;
		}		
		if(in_array($topicData['topic_type'],array(1,2,4)) && !$itemsData){
			$this->setError(10001,'选择题必须有子项存在');
			return false;
		}
		$topicData['item_count']=count($itemsData);
		if(in_array($topicData['topic_type'],array(3,4)) && ! $topicData['topic_point']){
			$this->setError(10001,'打分题必须设置分数');
		}
		if(!$topicData['topic_title']){
			$this->setError(10001, '题干必须存在');
			return false;
		}
		$result = array();
		copyArrayItem($topicData,$result,'eval_id');
		copyArrayItem($topicData,$result,'topic_type',true,1);
		copyArrayItem($topicData,$result,'topic_point');
		copyArrayItem($topicData,$result,'topic_title');
		copyArrayItem($topicData,$result,'topic_tip');
		copyArrayItem($topicData,$result,'topic_content');
		copyArrayItem($topicData,$result,'item_count');
		copyArrayItem($topicData,$result,'last_update_time',true,date('Y-m-d H:i:s'));
		if($type=="add"){
			copyArrayItem($topicData,$result,'create_time',true,date('Y-m-d H:i:s'));
		}
		//TODO 参数检查
		return $result;
	}
	
	/**
	 * 检查数据
	 * @param unknown $data
	 * @param string $type
	 */
	public function checkTopicItemData($topicData,$itemData,$type="add"){
		Logger::info("------",$itemData);
		if(!$itemData || !is_array($itemData)){
			$this->setError(10001, '数据不能为空');
			return false;
		}
		if($topicData['topic_type']== 4 && !isset($itemData['item_point'])){
			$this->setError(10001,'选择打分题必须设置选项的分数');
			return false;
		}
		if(!$itemData['item_key'] || !$itemData['item_name']){
			$this->setError(10001, '选项必须存在');
			return false;
		}
		$result = array(); 
		copyArrayItem($itemData,$result,'topic_id');
		copyArrayItem($itemData,$result,'eval_id');
		copyArrayItem($itemData,$result,'item_order',true,1);
		copyArrayItem($itemData,$result,'item_key');
		copyArrayItem($itemData,$result,'item_name');
		copyArrayItem($itemData,$result,'item_point');
		copyArrayItem($itemData,$result,'last_update_time',true,date('Y-m-d H:i:s'));
		if($type=="add"){
			copyArrayItem($itemData,$result,'create_time',true,date('Y-m-d H:i:s'));
		}
		return $result;
	}

	/**
	 * 清除缓存
	 * @param unknown $eval_id
	 * @param unknown $topic_id
	 */
	public function cleanCache($eval_id=null,$topic_id=null){
		if($eval_id){
			$cacheKey = GlobalCatchId::ABC_BASE_KEY."evalid_".$eval_id;
			Factory::getCacher()->clear($cacheKey);
		}
		if($topic_id){
			$cacheKeyTrue = GlobalCatchId::ABC_BASE_KEY."evaltopicid_".$topic_id."_".true;
			$cacheKeyFalse = GlobalCatchId::ABC_BASE_KEY."evaltopicid_".$topic_id."_".false;
			Factory::getCacher()->clear($cacheKeyTrue);
			Factory::getCacher()->clear($cacheKeyFalse);
		}
	}
	
}
