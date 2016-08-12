<?php
/**
 * 爱锁表
 */
class MessageModel extends Model {
	
	private static $_db;
	private static $_tablename;
	private static $_tablename_user;
	private static $_tablename_history;
	
	public function __construct(){
		self::$_db = Factory::getDb();
		self::$_tablename = "as_message";
		self::$_tablename_user = "as_user";
		self::$_tablename_history = "as_message_history";
	}
	
	/**
	 * 增加消息表
	 */
	public function saveMessage($data)  {
		try{
			self::$_db->insert(self::$_tablename,$data);	
			return true;
		} catch(Exception $e) {
			Logger::error("操作消息表失败".$e->getMessage());
			return false;
		}
	}
	
	/**
	 * 获取发给我的私信总数
	 */
	public function getCountToMe($uid)  {
		try{
			return self::$_db->getOne("select count(1) from ".self::$_tablename." where to_uid = '{$uid}'");
		} catch(Exception $e) {
			Logger::error("操作消息表失败".$e->getMessage());
			return false;
		}
	}
	
	/**
	 * 获取发给我的私信列表
	 */
	public function getListToMe($uid,$pagesize,$pageIndex)  {
		try{
			$start = ($pageIndex-1)*$pagesize;
			return self::$_db->getAll("select tmpa.id,tmpa.message_code
						,tmpa.uid,tmpb.truthname,tmpb.headimgurl
						,tmpa.to_uid,tmpa.content,tmpa.create_time
					from ".self::$_tablename." tmpa,".self::$_tablename_user." tmpb 
					where tmpa.uid= tmpb.uid 
						and to_uid = '{$uid}' 
					order by tmpa.create_time desc 
					limit $start,$pagesize");
		} catch(Exception $e) {
			Logger::error("操作消息表失败".$e->getMessage());
			return false;
		}
	}
	
	/**
	 * 获取我发出的私信总数
	 */
	public function getCountFromMe($uid)  {
		try{
			return self::$_db->getOne("select count(1) from ".self::$_tablename." where uid = '{$uid}'");
		} catch(Exception $e) {
			Logger::error("操作消息表失败".$e->getMessage());
			return false;
		}
	}
	
	/**
	 * 获取我发出的私信列表
	 */
	public function getListFromMe($uid,$pagesize,$pageIndex)  {
		try{
			$start = ($pageIndex-1)*$pagesize;
			return self::$_db->getAll("select tmpa.id,tmpa.message_code
						,tmpa.uid,tmpb.truthname,tmpb.headimgurl
						,tmpa.to_uid,tmpa.content,tmpa.create_time
					from ".self::$_tablename." tmpa,".self::$_tablename_user." tmpb 
					where tmpa.uid= tmpb.uid 
						and tmpa.uid = '{$uid}' 
					order by tmpa.create_time desc 
					limit $start,$pagesize");
		} catch(Exception $e) {
			Logger::error("操作消息表失败".$e->getMessage());
			return false;
		}
	}
	
	/**
	 * 获取我发出的私信列表
	 */
	public function updateMessageToMe($uid,$fromuid,$datetime)  {
		try{
			return self::$_db->query("update ".self::$_tablename." set to_uid='{$uid}' where uid='{$fromuid}'  and create_time>'{$datetime}'");
		} catch(Exception $e) {
			Logger::error("操作消息表失败".$e->getMessage());
			return false;
		}
	}
	
	/**
	 * 清空私信收信
	 * @param unknown $uid
	 */
	public function clearMessage($uid){
		try{
			self::$_db->query("insert into ".self::$_tablename_history." select * from ".self::$_tablename." where uid='{$uid}'");
			self::$_db->query("insert into ".self::$_tablename_history." select * from ".self::$_tablename." where to_uid='{$uid}'");
			self::$_db->query("delete from ".self::$_tablename." where uid='{$uid}'");
			self::$_db->query("delete from ".self::$_tablename." where to_uid='{$uid}'");
			return true;
		} catch(Exception $e) {
			Logger::error("操作消息表失败".$e->getMessage());
			return false;
		}
	}
	
	/**
	 * 清空收到的收信
	 * @param unknown $uid
	 */
	public function clearSendToMeMessage($uid){
		try{
			self::$_db->query("insert into ".self::$_tablename_history." select * from ".self::$_tablename." where to_uid='{$uid}'");
			self::$_db->query("delete from ".self::$_tablename." where to_uid='{$uid}'");
			return true;
		} catch(Exception $e) {
			Logger::error("操作消息表失败".$e->getMessage());
			return false;
		}
	}
}