<?php
/**
 * 系统警告
 * @author hezq
 *
 */
class SysAlarmAction extends BaseAction {

	private $model = null;

	public function __construct() {
		parent::__construct();
		$this->model = loadModel('SysAlarm');
	}

	/**
	 * 系统日志首页
	 */
	public function index() {
		addSysAlarm("测试系统告警", "测试一下", array("param"=>$this->getParam()),5);
		$system_name = trim($this -> getParam('system_name'));
		$alarm_name = trim($this -> getParam('alarm_name'));
		$level = (int)$this -> getParam('level');
		$start_time= trim($this -> getParam('start_time'));
		$end_time= trim($this -> getParam('end_time'));
		$level = (int)$this -> getParam('level');
		$auto_check = $this -> getParam('auto_check');
		$manual_status = $this -> getParam('manual_status');
		
		$args['pagesize'] = Config::PAGE_LISTROWS;
		$args['system_name'] = $system_name;
		$args['alarm_name'] = $alarm_name;
		$args['level'] = $level;
		$args['auto_check'] = $auto_check;
		$args['manual_status'] = $manual_status;
		$args['start_time'] = $start_time;
		$args['end_time'] = $end_time;
		
		$alarmList = $this->model->getSystemAlarmList($args);
		
		$this->assign('system_name',$system_name);
		$this->assign('alarm_name',$alarm_name);
		$this->assign('level', $level);
		$this->assign('auto_check', $auto_check);
		$this->assign('manual_status', $manual_status);
		$this->assign('start_time', $start_time);
		$this->assign('end_time', $end_time);
		
		$this->assign('list', $alarmList['list']);	
		$this->assign('page', $alarmList['page']);
		$this->display();
	}
	
	/**
	 * 显示日志详情
	 */
	public function showLog() {
		$id = $this->getParam("id");
		$alarmInfo = $this->model->getAlarmInfo($id);
		$this->assign("SysAlarm",$alarmInfo);
		$this->display();
	}

}
