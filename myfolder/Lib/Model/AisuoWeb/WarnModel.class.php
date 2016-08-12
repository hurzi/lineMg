<?php
/**
 * 爱锁表
 */
class WarnModel extends Model {
	
	private static $_db;
	private static $_tablename;
	private static $_tablename_user;
	private static $_tablename_type;
	
	public function __construct(){
		self::$_db = Factory::getDb();
		self::$_tablename = "as_warn";
		self::$_tablename_user = "as_user";
		self::$_tablename_type = "as_warn_type";
	}
	
	/**
	 * 增加提醒表
	 */
	public function saveWarn($data,$isupdate = FALSE)  {
		try{
			if($isupdate){
				self::$_db->update(self::$_tablename," id='{$data[id]}'",$data);
			}else{
				self::$_db->insert(self::$_tablename,$data);
			}	
			return true;
		} catch(Exception $e) {
			Logger::error("操作提醒表失败".$e->getMessage());
			return false;
		}
	}
	
	/**
	 * 提醒类型
	 */
	public function getAllWarnType()  {
		try{
			return self::$_db->getAll("select * from ".self::$_tablename_type);
		} catch(Exception $e) {
			Logger::error("操作提醒表失败".$e->getMessage());
			return false;
		}
	}
	
	/**
	 * 获得提醒名称
	 */
	public function getWarnTypeNameById($warnTypeId)  {
		try{
			return self::$_db->getOne("select type_name from ".self::$_tablename_type." where warn_type_id='{$warnTypeId}'");
		} catch(Exception $e) {
			Logger::error("操作提醒表失败".$e->getMessage());
			return false;
		}
	}
	
	/**
	 * 删除提醒表
	 */
	public function deleteWarn($id)  {
		try{
			self::$_db->delete(self::$_tablename,"where id='{$id}'");
			return true;
		} catch(Exception $e) {
			Logger::error("操作提醒表失败".$e->getMessage());
			return false;
		}
	}
	
	/**
	 * 获取发给我的私信总数
	 */
	public function getCountByUid($uid)  {
		try{
			return self::$_db->getOne("select count(1) from ".self::$_tablename." where uid = '{$uid}'");
		} catch(Exception $e) {
			Logger::error("操作提醒表失败".$e->getMessage());
			return false;
		}
	}
	
	/**
	 * 获取发给我的私信列表
	 */
	public function getListByUid($uid)  {
		try{
			$currDate = date('Y-m-d');
			return self::$_db->getAll("select tmpa.id,tmpa.uid,tmpa.openid
						,tmpa.warn_type_id,tmpb.type_name,tmpa.warn_date
						,tmpa.create_time
					from ".self::$_tablename." tmpa,".self::$_tablename_type." tmpb 
					where tmpa.warn_type_id= tmpb.warn_type_id 
						and uid = '{$uid}' 
						and tmpa.warn_date>='{$currDate}'
					order by tmpa.warn_date");
		} catch(Exception $e) {
			Logger::error("操作提醒表失败".$e->getMessage());
			return false;
		}
	}
}