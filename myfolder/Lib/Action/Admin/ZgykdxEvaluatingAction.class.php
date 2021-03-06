<?php
/**
 * 评教管理
 * @author  zp
 *
 */
class ZgykdxEvaluatingAction extends AdminAction
{
	private $evalModel;
	private $suerEvalModel;
	
	public function __construct()
	{
		parent::__construct();
		$this->evalModel = loadModel('WebYkdx.Evaluating',false);
		$this->userEvalModel = loadModel('WebYkdx.UserEvaluating',false);
		$this->assign("evalType",EvaluatingModel::$EVAL_TYPE);
		$this->assign("evalStatus",EvaluatingModel::$EVAL_STATUS);
		$this->assign("evalTopicType",EvaluatingModel::$EVAL_TOPIC_TYPE);
	}

	/**
	 * 自定义菜单列表
	 */
	public function index()
	{		
		$param = array(
				"eval_starttime"=>$this->getParam("eval_starttime"),
				"eval_endtime"=>$this->getParam("eval_endtime"),
				"eval_type"=>$this->getParam("eval_type"),
				"eval_status"=>$this->getParam("eval_status"),
				"eval_name"=>$this->getParam("eval_name"),
				"page"=>(int) $this->getParam(Config::VAR_PAGE, 1)
		);
		
		$result = $this->evalModel->getList($param);
				
		$this->assign('param', $param);
		$this->assign('list', $result['list']);
		$this->assign('page', $result['pager']);
		$this->display();
	}

	public function add()
	{		
		$this->display();
	}
	
	public function update()
	{
		$eval_id = $this->getParam("eval_id");
		if(!$eval_id){
			$this->showError("参数错误");
		}
		$evalInfo = $this->evalModel->getEvaluating($eval_id);
		if(!$evalInfo){
			$this->showError("取不到评教的信息");
		}
		$this->assign("evalInfo",$evalInfo);
		$this->display();
	}
	
	public function addTopic()
	{
		$eval_id = $this->getParam("eval_id");
		if(!$eval_id){
			$this->showError("参数错误");
		}
		$evalInfo = $this->evalModel->getEvaluating($eval_id);
		if(!$evalInfo){
			$this->showError("取不到评教的信息");
		}
		$this->assign("evalInfo",$evalInfo);
		$this->display();
	}
	
	public function updateTopic()
	{
		$eval_id = $this->getParam("eval_id");
		$topic_id = $this->getParam("topic_id");
		if(!$eval_id || !$topic_id){
			$this->showError("参数错误");
		}
		$evalInfo = $this->evalModel->getEvaluating($eval_id);
		if(!$evalInfo){
			$this->showError("取不到评教的信息");
		}
		$topicInfo = $this->evalModel->getEvaluatingTopic($topic_id);
		if(!$topicInfo){
			$this->showError("取不到题目的信息");
		}
		$this->assign("evalInfo",$evalInfo);
		$this->assign("topicInfo",$topicInfo);
		$this->display();
	}
	
	public function ajax_addEvaluating(){
		$eval_name = $this->getParam('eval_name');
		$eval_descript = $this->getParam('eval_descript');
		$eval_starttime = $this->getParam('eval_starttime');
		$eval_endtime = $this->getParam('eval_endtime');
		$eval_type = $this->getParam('eval_type');
		$eval_status = $this->getParam('eval_status');
		$eval_max_topic = $this->getParam('eval_max_topic');
		if(!$eval_name || !$eval_descript || !$eval_starttime || !$eval_endtime
			|| !$eval_type || !$eval_status || !$eval_max_topic){
			printJson("",-1,"参数不全");
		}
		$param = array(
				"eval_name"=>$eval_name,
				"eval_descript"=>$eval_descript,
				"eval_starttime"=>$eval_starttime,
				"eval_endtime"=>$eval_endtime,
				"eval_type"=>$eval_type,
				"eval_status"=>$eval_status,
				"eval_max_topic"=>$eval_max_topic
		);
		
		$ret = $this->evalModel->addEvaluating($param);
		if(!$ret){
			printJson("",-1,"增加失败");
		}
		printJson("增加成功");
	}
	
	public function ajax_updateEvaluating(){
		$eval_id = $this->getParam('eval_id');
		$eval_name = $this->getParam('eval_name');
		$eval_descript = $this->getParam('eval_descript');
		$eval_starttime = $this->getParam('eval_starttime');
		$eval_endtime = $this->getParam('eval_endtime');
		$eval_type = $this->getParam('eval_type');
		$eval_status = $this->getParam('eval_status');
		$eval_max_topic = $this->getParam('eval_max_topic');
		if(!$eval_id || !$eval_name || !$eval_descript || !$eval_starttime || !$eval_endtime
		|| !$eval_type || !$eval_status || !$eval_max_topic){
			printJson("",-1,"参数不全");
		}
		$param = array(
				"eval_name"=>$eval_name,
				"eval_descript"=>$eval_descript,
				"eval_starttime"=>$eval_starttime,
				"eval_endtime"=>$eval_endtime,
				"eval_type"=>$eval_type,
				"eval_status"=>$eval_status,
				"eval_max_topic"=>$eval_max_topic
		);
	
		$ret = $this->evalModel->updateEvaluating($eval_id,$param);
		if(!$ret){
			printJson("",-1,"操作失败");
		}
		printJson("操作成功");
	}
	
	public function ajax_addEvaluatingTopic(){
		$eval_id = $this->getParam('eval_id');
		$topic_type = $this->getParam('topic_type');
		$topic_point = $this->getParam('topic_point');
		$topic_title = $this->getParam('topic_title');
		$topic_tip = $this->getParam('topic_tip');
		$topic_content = $this->getParam('topic_content');
		
		$items = $this->getParam('items');
		if(!$eval_id || !$topic_type || !$topic_title){
			printJson("",-1,"参数不全");
		}
		$param = array(
				"eval_id" =>$eval_id,
				"topic_type"=>$topic_type,
				"topic_point"=>$topic_point,
				"topic_title"=>$topic_title,
				"topic_tip"=>$topic_tip,
				"topic_content"=>$topic_content
		);
	
		$ret = $this->evalModel->addEvaluatingTopicAndItem($param,$items);
		if(!$ret){
			printJson("",-1,"操作失败");
		}
		printJson("操作成功");
	}
	


	public function ajax_updateEvaluatingTopic(){
		$topic_id = $this->getParam('topic_id');
		$topic_type = $this->getParam('topic_type');
		$topic_point = $this->getParam('topic_point');
		$topic_title = $this->getParam('topic_title');
		$topic_tip = $this->getParam('topic_tip');
		$topic_content = $this->getParam('topic_content');
	
		$items = $this->getParam('items');
		if(!$topic_id || !$topic_type || !$topic_title){
			printJson("",-1,"参数不全");
		}
		$param = array(
				"topic_type"=>$topic_type,
				"topic_point"=>$topic_point,
				"topic_title"=>$topic_title,
				"topic_tip"=>$topic_tip,
				"topic_content"=>$topic_content
		);
	
		$ret = $this->evalModel->updateEvaluatingTopicAndItem($topic_id,$param,$items);
		if(!$ret){
			printJson("",-1,"操作失败");
		}
		printJson("操作成功");
	}
}