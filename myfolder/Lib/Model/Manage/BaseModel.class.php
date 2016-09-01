<?php
/**
 * Base后台里所有其他model的父类
 */
class BaseModel extends Model
{
    /**
     * @info base数据库对象
     * @var MySql
     */
    protected $dbBase;
    /**
     * @info 企业数据库对象
     * @var MySql
     */
    protected $dbEnt;
    /**
     * 数据库连接
     * @var unknown
     */
    private $accountDbArr = array();

    protected $error = '';
    protected $errorCode = 0;

    public function __construct()
    {
        parent::__construct();
        $this->dbBase = Factory::getDb();
       // $this->dbEnt = Factory::getDbByHost(UHome::getEntDbHost(), UHome::getEntDbName());
    }

    /**
     * @info 获取企业app info
     * @param int $account_id 公众号表ID
     * @param bool $refesh 是否刷新缓存
     * @return array|false
     */
    public function getAppInfo($account_id, $refesh = false)
    {
        $catcher = Factory::getGlobalCacher();
        $catcherId = GlobalCatchId::ENT_APP_INFO.$account_id;

        if (false == $refesh) {
            $info = $catcher->get($catcherId);
            if ($info) return $info;
        }

        $sql = "SELECT a.*,e.ent_name,e.telephone FROM `wx_ent_app` a left join `wx_enterprise` e on a.ent_id=e.ent_id WHERE a.account_id = %d";
        try {
            $app = $this->dbBase->getRow(sprintf($sql, $account_id));
        } catch (Exception $e) {
            return false;
        }
        if ($app) {
            $catcher->set($catcherId, $app, GlobalCatchExpired::ENT_APP_INFO);
        }

        return $app;
    }

    /**
     * 获取特定的数据库连接
     * @param unknown $accountId
     * @return Ambigous <Mysql, NULL, multitype:>
     */
    public  function getAccountDb($accountId) {
    	if(isset($this->accountDbArr[$accountId])){
    		return $this->accountDbArr[$accountId];
    	}
    	$appInfo = $this->getAppInfo($accountId);
    	$db = Factory::getDbByHost($appInfo['db_group'], $appInfo['db_name']);
    	if($db){
    		$this->accountDbArr[$accountId] = $db;
    		return $db;
    	}
    	return null;
    }
    
    /**
     * @info 生成json返回数据格式
     * @param string $data
     * @param int $error
     * @param string $msg
     * @return array
     */
    protected function genResult($data, $error = 0, $msg = '')
    {
        return array('data'=>$data, 'error'=>$error, 'msg'=>$msg);
    }

}

