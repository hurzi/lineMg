<?php

/**
 * Class EnterpriseModel 企业信息相关操作
 */
class SeEnterpriseModel extends BaseModel
{
	const STATUS_AUDITING_DOING = 1;
    const STATUS_AUDITING_NO = 2;
    const STATUS_AUDITING_YES = 3;

    private static $_status_names = array(
        1 => '审核中',
        2 => '审核未通过',
        3 => '审核通过',
    );

    /**
     * 添加企业信息
     */
    public function insertEntInfo($param) {

        if ($this->isExist($param['ent_name'])) {
            return false;
        }

        $param ['create_time'] = time();

        return $this->dbBase->insert('se_enterprise', $param);
    }

    /**
     * 获取无公众号的企业列表
     * @param array $args
     * @return array
     */
    public function getUnAccountEntList($args = array(), $orderby = null) {
        $where = '';
        $db = array();
        if (isset($args['keyword']) && !empty($args['keyword'])) {
            //$where = " AND `ent_name` like '%{$args['keyword']}%' ";
            $db[] = "e.`ent_name` like '%{$args['keyword']}%' ";
        }
        if (isset($args['status']) && is_array($args['status'])) {
            //$where = " AND `status` in ( ".implode(",", $args['status']).") ";
            $db[] = "e.`status` in ( " . implode(",", $args['status']) . ") ";
        } else if (isset($args['status']) && is_int($args['status'])) {
            //$where = " AND `status`= '{$args['status']}' ";
            $db[] = "e.`status`= '{$args['status']}' ";
        }
        $db[] ="a.`account_id` is null";
        if ($db) {
            $where = 'WHERE ' . implode(' AND ', $db);
        }
        
        $order = $orderby ? $orderby : " e.ent_id DESC";
        $sql = "SELECT e.* FROM `se_enterprise` e left join `se_ent_app` a on e.ent_id=a.ent_id " . $where . " ORDER BY " . $order;
        $count_sql = "SELECT COUNT(*) FROM `se_enterprise` e left join `se_ent_app` a on e.ent_id=a.ent_id " . $where;

        $list = array();
        $page = '';
        $limit = '';


        //获取总数
        $count = $this->dbBase->getOne($count_sql);
        if ($count) {
            if (isset($args['pagesize']) && $args['pagesize']) {
                $p = new page($count, $args['pagesize']);
                $page = $p->show();
                $limit = " LIMIT $p->firstRow, $p->listRows";
            }
            $sql .= $limit;
            $list = $this->dbBase->getAll($sql);
        }


        return array('list' => $list, 'page' => $page);
    }
    
    
    /**
     * 获取企业列表
     * @param array $args
     * @return array
     */
    public function getEntList($args = array(), $orderby = null) {
        $where = '';
        $db = array();
        if (isset($args['keyword']) && !empty($args['keyword'])) {
            //$where = " AND `ent_name` like '%{$args['keyword']}%' ";
            $db[] = "`ent_name` like '%{$args['keyword']}%' ";
        }
        if (isset($args['status']) && is_array($args['status'])) {
            //$where = " AND `status` in ( ".implode(",", $args['status']).") ";
            $db[] = "`status` in ( " . implode(",", $args['status']) . ") ";
        } else if (isset($args['status']) && is_int($args['status'])) {
            //$where = " AND `status`= '{$args['status']}' ";
            $db[] = "`status`= '{$args['status']}' ";
        }
        if ($db) {
            $where = 'WHERE ' . implode(' AND ', $db);
        }
        $order = $orderby ? $orderby : " ent_id DESC";
        $sql = "SELECT * FROM `se_enterprise` " . $where . " ORDER BY " . $order;
        $count_sql = "SELECT COUNT(*) FROM `se_enterprise` " . $where;

        $list = array();
        $page = '';
        $limit = '';


        //获取总数
        $count = $this->dbBase->getOne($count_sql);
        if ($count) {
            if (isset($args['pagesize']) && $args['pagesize']) {
                $p = new page($count, $args['pagesize']);
                $page = $p->show();
                $limit = " LIMIT $p->firstRow, $p->listRows";
            }
            $sql .= $limit;
            $list = $this->dbBase->getAll($sql);
        }


        return array('list' => $list, 'page' => $page);
    }

    public function getEntInfo($entId) {
        $where = '';
        if ($entId) {
            $where = " AND `ent_id` = '{$entId}' ";
        }
        $sql = "SELECT * FROM `se_enterprise` WHERE 1 " . $where;
        return $this->dbBase->getRow($sql);
    }

    public function updateEnt($entId, $info) {
        return $this->dbBase->update('se_enterprise', "ent_id={$entId}", $info);
    }

    /**
     * 审核企业
     * @param unknown $entId
     * @param unknown $status
     * @param unknown $auditingMemo
     * @return boolean
     */
    public function auditingEnt($entId, $status, $auditingMemo) {
        $entinfo = $this->getEntInfo($entId);
        if (!$entinfo) {
            return false;
        }
        if (!self::$_status_names[$status]) {  //检查状态是否存在
            return false;
        }
        $param['status'] = $status;
        $param['auditing_memo'] = "[" . date("Y-m-d") . "][" . self::$_status_names[$status] . "]" . $auditingMemo . "/r/n" . $entinfo['auditing_memo'];
        $param['auditing_time'] = time();
        return $this->dbBase->update('se_enterprise', "ent_id={$entId}", $param);
    }

    public function isExist($entName) {
        return $this->dbBase->getOne("select count(*) from se_enterprise where `ent_name`='{$entName}'");
    }
   
    /**
     * 获取所有企业列表
     * @return Array
     * TODO 简单获取没处理任何事情
     */
    public function getAll(){
    	try {
    		return $this->dbBase->getAll("SELECT ent_id, ent_name FROM `se_enterprise`");
    	} catch (Exception $e) {
    		Logger::error(__FILE__ . ' ' . __CLASS__ . ' ' . __METHOD__ . ' ' . __LINE__ . ' ',
    		$e->getMessage() . "\n" . $e->getTraceAsString());
    		return null;
    	}
    }
}