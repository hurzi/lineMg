<?php
class SeUserModel extends BaseModel
{
	
private $table = "se_user";
    private $level = array(
        1=>'超级后台管理员',
        2=>'企业管理员',
        3=>'公众号超级管理员',
        4=>'公众账号普通管理员',
        5=>'门店核销员',
        6=>'后台普通管理员',
    );
	/**
	 * 查询所有超级管理员列表
	 * @param string $where
	 * @param string $limit
	 * @return Ambigous <NULL, multitype:multitype: >
	 */
	public function getList($where='', $limit=''){
		$sql = "select * from {$this->table}";
		if($where){
			$sql .= " where {$where}";
		}
		$sql .= " order by user_id desc";
		if($limit){
			$sql .= $limit;
		}
		try {
			return $this->dbBase->getAll($sql);
		} catch (Exception $e) {
			Logger::error(__FILE__.' . '.__METHOD__.'  '.__LINE__ ." 查询所有超级管理员列表失败 ", $e->getMessage()."\n".$e->getTraceAsString());
			return false;
		}
	}

    public function getEntListKeyValue($where = ''){
        $condiction = "";
        if($where){
            $condiction = " WHERE {$where} ";
        }
        $sql = "select * from `se_enterprise` {$condiction} order by convert(ent_name using gbk)";

        try {
            $entList = $this->dbBase->getAll($sql);
            $tmpEnt = array();
            if($entList){
                foreach ($entList as $ent){
                    $tmpEnt[$ent['ent_id']] = $ent['ent_name'];
                }
            }
           
            return $tmpEnt;
        } catch (Exception $e) {
            Logger::error(__FILE__.' . '.__METHOD__.'  '.__LINE__ ." 获取企业列表失败 ", $e->getMessage()."\n".$e->getTraceAsString());
            return false;
        }
    }

    public function getLevelList(){
        return $this->level;
    }
	/**
	 * 查询所有记录数
	 * @param string $where
	 * @return Ambigous <NULL, multitype:multitype: >|boolean
	 */
	public function getTotalCount($where='' ){
		$sql = "select count(*) from {$this->table}";
		if($where){
			$sql .= " where {$where}";
		}
		try {
			return $this->dbBase->getOne($sql);
		} catch (Exception $e) {
			Logger::error(__FILE__.' . '.__METHOD__.'  '.__LINE__ ." 查询所有超级管理员总记录数失败 ", $e->getMessage()."\n".$e->getTraceAsString());
			return false;
		}
	}

	/**
	 * 添加企业超级管理员
	 * @param unknown $set
	 * @return Ambigous <boolean, number>
	 */
	public function insert($set){
		try {
			return $this->dbBase->insert($this->table,$set);
		} catch (Exception $e) {
			Logger::error(__FILE__.' . '.__METHOD__.'  '.__LINE__ ." 添加企业超级管理员失败 ", $e->getMessage()."\n".$e->getTraceAsString());
			return false;
		}
	}
	/**
	 * 更新企业超级管理员
	 * @param unknown $set
	 * @param unknown $where
	 * @return boolean
	 */
	public function update($set,$where){
		try {
			return $this->dbBase->update($this->table, $where, $set);
		} catch (Exception $e) {
			Logger::error(__FILE__.' . '.__METHOD__.'  '.__LINE__ ." 更新企业超级管理员失败 ", $e->getMessage()."\n".$e->getTraceAsString());
			return false;
		}
	}
	/**
	 * 删除超级管理员
	 * @param unknown $ids
	 * @return boolean
	 */
	public function delete($ids){
		$where = "user_id in (".$this->dbBase->genSqlInStr($ids).")";
		try {
			return $this->dbBase->delete($this->table, $where);
		} catch (Exception $e) {
			Logger::error(__FILE__.' . '.__METHOD__.'  '.__LINE__ ." 删除超级管理员失败 ", $e->getMessage()."\n".$e->getTraceAsString());
			return false;
		}
	}

	/**
	 * 检测添加/编辑数据
	 * @param array  $data
	 * @param string  $type 管理员类型 add | update 默认add
	 * @return array|false  $data
	 */
	public function checkData($data, $type = 'add')
	{
		if (! $data['nickname']) {
			$this->setError(-1, "管理员姓名不能为空！");
			return false;
		}
		$where    = " username='{$data['username']}' ";
		/* if(isset($data['operator_id'])){
			$where .= " and operator_id != {$data['operator_id']}";
		} */
		$operator = $this->getList($where);
		if ('add' == $type) {
			if (! $data['username']) {
				$this->setError(-11, "管理员登录名不能为空！");
				return false;
			}
			if ($operator) {
				$this->setError(-12, "管理员登陆名已经存在！");
				return false;
			}
			$password   		 = $data['password'];
			$salt 				 = getSalt();
			$data['password'] = getPassword($salt, $password);
			$data['salt'] 	     = $salt;
			$data['create_time'] = time();
		} else {
			$where 			= "  user_id = {$data['user_id']}";
			$operatorInfo   = $this->getList($where);
			if (! $operatorInfo) {
				$this->setError(-13, "要编辑的管理员不存在！");
				return false;
			}
			if ($operator) {
				if ($operator[0]['user_id'] != $data['user_id']) {
					$this->setError(-14, "管理员登陆名已经存在！");
					return false;
				}
			}
			if (isset($data['password']) && $data['password'] == '123456') {
				$password	 = 123456;
				$salt		 = getSalt();
				$data['password'] = getPassword($salt, $password);
				$data['salt'] = $salt;
			}
			//unset($data['operator_id']);
		}
		return $data;
	}

	public function getAccountList($where = '1'){
		if($where){
			$where = " where {$where}";
		}
        $sql = "select ent_id from `se_ent_app` $where";
        $accountList = $this->dbBase->getAll($sql);
        return $accountList[0];
	}

    public function getAccountListKeyValue($where=''){
        $condiction = "";
        if($where){
            $condiction = " WHERE {$where} ";
        }
        $sql = "select * from `se_ent_app` {$condiction} order by convert(app_weixin_name using gbk)";
        try {
            $accountList = $this->dbBase->getAll($sql);
            $tmpAccount = array();
            if($accountList){
                foreach ($accountList as $account){
                    $tmpAccount[$account['account_id']] = $account['app_weixin_name'];
                }
            }
            return $tmpAccount;
        } catch (Exception $e) {
            Logger::error(__FILE__.' . '.__METHOD__.'  '.__LINE__ ." 获取企业列表失败 ", $e->getMessage()."\n".$e->getTraceAsString());
            return false;
        }
    }

    public function getAccountListKeyValues(){
        $sql = "select * from `se_ent_app` order by convert(app_weixin_name using gbk)";
        try {
            $accountList = $this->dbBase->getAll($sql);
            $tmpAccount = array();
            if($accountList){
                foreach ($accountList as $account){
                    $tmpAccount[$account['app_weixin_name'].'_'.$account['app_id'].'_'.$account['app_secret'].'_'.$account['account_id']] = $account['app_weixin_name'];
                }
            }
            return $tmpAccount;
        } catch (Exception $e) {
            Logger::error(__FILE__.' . '.__METHOD__.'  '.__LINE__ ." 获取企业列表失败 ", $e->getMessage()."\n".$e->getTraceAsString());
            return false;
        }
    }

    public function get_secret($app_id){
        $sql = "select app_secret from `se_ent_app` where app_id = '".$app_id."'";
        try {
            return $this->dbBase->getAll($sql);
        } catch (Exception $e) {
            Logger::error(__FILE__.' . '.__METHOD__.'  '.__LINE__ ." 获取企业列表失败 ", $e->getMessage()."\n".$e->getTraceAsString());
            return false;
        }
    }
}
