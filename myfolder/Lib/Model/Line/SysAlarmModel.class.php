<?php
/**
 * 系统告警
 * @author grh
 * @since 2013-09-24
 *
 */
class SysAlarmModel extends Model
{
	/**
	 * 获取系统告警列表 和 分页信息
	 * @param array $data 数据
	 */
	public function getSystemAlarmList($data)
	{
		$system_name = $data['system_name'];
		$alarm_name = $data['alarm_name'];
		$level = $data['level'];	
		$auto_check = $data['auto_check'];
		$manual_status = $data['manual_status'];	

		$start_time = $data['start_time'];
		$end_time = $data['end_time'];
		
		$where = '';
		$list = array();
		$entList = array();
		$page = '';
		if($level){
			$where .= " AND `level` = '{$level}'";
		}
		if($auto_check){
			$where .= " AND `auto_check` = '{$auto_check}'";
		}
		if($manual_status){
			$where .= " AND `manual_status` = '{$manual_status}'";
		}
		if($system_name){
			$where .= " AND `system_name` = '{$system_name}'";
		}
		if($alarm_name){
			$where .= " AND `alarm_name` = '{$alarm_name}'";
		}
		if($start_time){
			$where .= " AND `create_time` >= '{$start_time}'";
		}
		if($end_time){
			$where .= " AND `create_time` <= '{$end_time}'";
		}
		//获取分页显示
		$count = $this->getAlarmCount($where);
		if($count > 0){
			if ($data['pagesize']) {
				$p = new page($count, $data['pagesize']);
				$page = $p->show();
				$limit = " LIMIT $p->firstRow, $p->listRows";
			}
			//获取通知信息
			$list = $this->getAlarmList($where,$limit);			
		}
		return array('list'=>$list, 'page'=>$page);
	}

	/**
	 * 获取系统告警列表
	 * @param string $where 附件查询条件
	 * @param string $limit 附件分页limit
	 * @return array 列表数据
	 */
	public function getAlarmList ($where = '', $limit = '')
	{
		$where = "WHERE 1=1".$where;
		$order = "ORDER BY `id` DESC ";
		$sql = "SELECT * FROM `wx_sys_alarm` "
			  ." {$where} {$order} {$limit}";
		try{
			return  $this->getDb()->getAll($sql);
		}catch(Exception $e){
			Logger::error("获取系统告警列表失败： ", $e->getMessage()."\n".$e->getTraceAsString());
			return array();
		}
	}
	
	/**
	* 获取系统告警列表总数
	* @param string $where 附件查询条件
	* @return int 列表数据总数
	*/
	public function getAlarmCount ($where = '')
	{
		$where = " WHERE 1=1".$where;
		$sql = "SELECT COUNT(*) FROM `wx_sys_alarm` {$where}";
		try{
			return $this->getDb()->getOne($sql);
		}catch(Exception $e){
			Logger::error("获取系统告警列表总数失败： ", $e->getMessage()."\n".$e->getTraceAsString());
			return 0;
		}
	}
	
	/**
	 * 获取系统告警列表总数
	 * @param string $where 附件查询条件
	 * @return int 列表数据总数
	 */
	public function getAlarmInfo ($id)
	{
		$id = intval($id);
		$sql = "SELECT * FROM `wx_sys_alarm` where id= {$id}";
		try{
			return $this->getDb()->getRow($sql);
		}catch(Exception $e){
			Logger::error("获取系统告警列表总数失败： ", $e->getMessage()."\n".$e->getTraceAsString());
			return 0;
		}
	}
	
}
