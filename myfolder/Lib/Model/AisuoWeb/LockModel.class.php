<?php
/**
 * 爱锁表
 */
class LockModel extends Model {
	
	private static $_db;
	private static $_tablename;
	
	public function __construct(){
		self::$_db = Factory::getDb();
		self::$_tablename = "as_lock_apply";
	}
	
	/**
	 * 增加或修改申请表
	 */
	public function saveOrUpdateAsApplay($data,$isupdate=false)  {
		try{
			if($isupdate){
				self::$_db->update(self::$_tablename, "id = '".$data['id']."'",$data );
			}else{
				self::$_db->insert(self::$_tablename,$data);				
			}	
			return true;
		} catch(Exception $e) {
			Logger::error("操作申请表失败".$e->getMessage());
			return false;
		}
	}
	
	/**
	 * 通过微信id获取最后一次申请时间
	 */
	public function getLastObjByOpenid($openid)  {
		try{
			return self::$_db->getRow("select * from ".self::$_tablename." where openid = '{$openid}' order by apply_time desc limit 1");
		} catch(Exception $e) {
			Logger::error("操作申请表失败".$e->getMessage());
			return false;
		}
	}
}