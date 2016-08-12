<?php
/**
 * 用户答题首页
 */

class UserEvalAction extends YkdxCommonAction{
	private $evalModel;
	private $userModel;
	private $userEvalModel;
    public function __construct() {
        parent::__construct();
        $this->evalModel = loadModel("Evaluating");
        $this->userModel = loadModel("User");
        $this->userEvalModel = loadModel("UserEvaluating");
    }
    
    public function index(){
        $this->checkStatus();       
        $this->display();
    }
    
    /**
     * 下一题
     */
    public function nextTopic(){
    	$this->checkStatus();
    	$this->display();
    }
    
    /**
     * 检查答题状态
     */
    private function checkStatus(){
    	$openid = $this->getParam('openid');
    	$user_id = $this->getParam('user_id');
    	$eval_id = $this->getParam('eval_id',0);
    	if(!$eval_id){
    		$this->showErrorH5("没有选择一个评教");
    	}
    	$evalInfo = $this->evalModel->getEvaluating($eval_id);
    	if(!$evalInfo){
    		$this->showErrorH5("没有选择一个评教");
    	}
    	if(strtotime($evalInfo['eval_starttime'])>time()){
    		$this->showErrorH5("还未开始");
    	}
    	if(strtotime($evalInfo['eval_endtime'])<time()){
    		$this->showErrorH5("已经结束");
    	}
    	if($evalInfo['eval_status']==2){
    		$this->showErrorH5("已经结束");
    	}
    	    	
    	$user = $this->userModel->getUserByOpenid($openid);
    	if($user){
    		redirect(url("User","Regist",$this->getParam()),3,"用户未注册，自动跳转注册页");
    	}
    	
    	$this->assign("evalInfo",$evalInfo);
    	$this->assign("user",$user);
    	
    	$userEval = $this->userEvalModel->getUserEvaluating($user['user_id'],$eval_id);
    	if($userEval){
    		$action = __ACTION_NAME__.'.'.__ACTION_METHOD__;    		 
    		$this->assign("isUserEval",1);
    		if($userEval['status'] == 2){
    			forward("UserEval","hasEval");  //直接重定向新的action
    		}else if($userEval['status'] == 1){
    			if ($action != 'UserEval.nextTopic') {
    				redirect(url("UserEval","nextTopic",$this->getParam()),3,"上次未答完，正在跳转继续答题...");
    			}	
    		}
    	}
    }
    
    
    /**
     * 开始答题(整页显示答题的)
     */
    public function startEvalAllTopic(){
    	$ue_id = $this->getParam('ue_id',0);
    	if(!$ue_id){
    		$this->showErrorH5("参数错误");
    	}
    
    	$userEval = $this->userEvalModel->getUserEvaluatingById($ue_id);
    	if(!$userEval){
    		redirect(url("User","Regist"),3,"您未确认答题，自动跳转确认页");    		
    	}
    	if($userEval['status'] == 2){
    		redirect(url("UserEval","hasEval",$this->getParam()));
        }
        //获取所有题及题项
        $topics = $this->evalModel->getEvaluatingTopicAll($userEval['eval_id']);
        //所有已答题(用于页面自动填充答案)
        $hasTopics = $this->userEvalModel->getUserEvaluatingDetails($ue_id);
        
    	$this->assign("userEval",$userEval);
    	$this->assign("topics",$topics);
    	$this->assign("hasTopics",$hasTopics);
    	$this->display();
    }
    
    /**
     * 提交答题(整页显示答题的)
     */
    public function submitAllTopic(){
    	$ue_id = $this->getParam('ue_id',0);
    	$userTopics = $this->getParam('userTopics');
    	if(!$ue_id){
    		$this->showErrorH5("参数错误");
    	}
    	if(!$userTopics){
    		$this->showErrorH5("参数错误");
    	}
    
    	$evalRet = $this->userEvalModel->addUserEvaluatingDetails($ue_id,$userTopics,false);
    	if(!$evalRet){
    		$this->showErrorH5("提交答案出错");
    	}
    	$this->display();
    }
    
    
    
    /**
     * 确认进行评教
     */
    public function ajax_joinEval(){
    	$eval_id = (int)$this->getParam("eval_id",0);
        $openid = $this->getParam('openid');
        $user_id = $this->getParam('user_id');
        if(!$openid){
        	printJson("",-1,"没有取到openid");
        }
        if(!$eval_id){
        	printJson("",-1,"没有选择评教");
        }
        $param = array(
        		"openid"=>$openid,
        		"user_id"=>$user_id,
        		"status"=>1,
        		"eval_id"=>$eval_id
        );
        
        $userEval = $this->userEvalModel->getUserEvaluating($user_id,$eval_id);
        if($userEval){
        	Logger::info("用户[$user_id]已参加评教，不应该再调用该接口，请检查程序");
        	printJson("已参加评教");
        }
        
        $ret = $this->userEvalModel->addUserEvaluating($param);
        if(!$ret){
        	printJson("",-1,"开始评教失败");
        }
        printJson(array("ue_id"=>$ret));
    }
    
    /**
     * 异步提交答案
     */
    private function ajax_addUserTopic(){
    	$eval_id = (int)$this->getParam("eval_id",0);
    	$user_id = $this->getParam('user_id');
    	$topic_id = $this->getParam('topic_id');
    	$ue_id = $this->getParam('ue_id');
    	$answer_key = $this->getParam('answer_key');
    	$content = $this->getParam('content');
    	$point = $this->getParam('point');
    	$hasNext = $this->getParam('hasNext',true);
    	if(!$ue_id){
    		printJson("",-1,"用户还没有确认答题");
    	}
    	if(!$eval_id){
    		printJson("",-1,"没有选择评教");
    	}
    	if(!$topic_id){
    		printJson("",-1,"没有选择题目");
    	}
    	$param = array(
    			"user_id"=>$user_id,
    			"eval_id"=>$eval_id,
    			"ue_id"=>$ue_id,
    			"topic_id"=>$topic_id,
    			"answer_key"=>$answer_key,
    			"point"=>$point,
    			"content"=>$content
    	);
    	    	
    	$ret = $this->userEvalModel->answer_key($param);
    	if(!$ret){
    		printJson("",-1,"答题失败");
    	}
    	$sum_point = $this->userEvalModel->getSumPoint($ue_id);
    	$resultData = array(
    			"hasNext"=>$hasNext,
    			"sum_point"=>$sum_point,
    	);
    	  	
    	//没有下一题了，则代表结束了
    	if(!$hasNext){    	
    		$ueParam = array(
    				"sum_point"=>$sum_point,
    				"status"=>2    				
    		);
    		$upRet = $this->userEvalModel->updateUserEvaluating($ue_id,$ueParam);
    		if(!$upRet){
    			printJson("",-1,"统计总分失败");
    		}
    	}    	
    	printJson($resultData);
    }
    
    /**
     * 获取下一道题
     */
    public function ajax_getNextTopic(){
    	$eval_id = (int)$this->getParam("eval_id",0);
    	$ue_id = $this->getParam('ue_id');
    	$get_num = $this->getParam('get_num',1);
    	if(!$ue_id){
    		printJson("",-1,"用户还没有确认答题");
    	}
    	if(!$eval_id){
    		printJson("",-1,"没有选择评教");
    	}
    	$evalInfo = $this->evalModel->getEvaluating($eval_id);
    	if(!$evalInfo){
    		printJson("",-1,"参数有数");
    	}
    	
    	$topics = $this->randomTopic($ue_id, $eval_id,$get_num,$evalInfo['eval_max_topic']);
    	
    	printJson($topics);
    }
    
    /**
     * 随机取题
     * @param unknown $ue_id
     * @param unknown $eval_id
     * @param unknown $getNum
     * @param unknown $allNum
     */
    public function randomTopic($ue_id,$eval_id,$getNum=1,$maxNum=0){
    	$result = array(
    			"hasTopicCount"=>0,
    			"maxCount"=>$maxNum,
    			"hasNext" => true,
    			"chsTopic" =>array()
    	);
    	$hasTopicIds = $this->userEvalModel->getUserHasTopicId($ue_id);
    	$allTopicIds = $this->evalModel->getEvaluationTopicIdAll($eval_id);
    	$hasCount = count($hasTopicIds);
    	$allCount = count($allTopicIds);
    	if($allCount-$hasCount<=$getNum){  //计算是否有下一次随机取
    		$result['hasNext'] = false;
    		$getNum = $allCount-$hasCount;
    	}
    	if($maxNum<0) $maxNum = 0;
    	if($maxNum>$allCount) $maxNum = $allCount;
    	$resultp['hasTopicCount'] = $hasCount;
    	$resultp['maxCount'] = $maxNum;
    	 
    	if($hasCount+$getNum>$maxNum){  //如果要取的数量超过了总数，则调整为可取的数量
    		$getNum = $maxNum-$hasCount;
    	}
    	$diffArr = array_diff($allTopicIds,$hasTopicIds);//剩下的topicids,
    	$diffArr = shuffle($diffArr);
    	$chsArr = array_slice($diffArr, 0, $getNum);
    	foreach ($chsArr as $v){
    		$result['chsTopic'][] = $this->evalModel->getEvaluatingTopic($v,true);
    	}
    	return $result;
    }
}
