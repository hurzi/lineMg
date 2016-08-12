<?php
/**
 * 二维码操作表
 */
class RegistModel extends Model {
	
	/**
	 * 通过微信id获取用户扫描二维码数据
	 */
	public function saveOrUpdateAsUser($date,$isupdate=false) {
		try{
			$strsql = "SELECT * FROM `wx_user_qrc` WHERE `openid` = '{$openid}'";
			return $this->getDb(Config::DB_NISSAN)->getRow($strsql);	
		} catch(Exception $e) {
			Logger::error("通过微信id获取用户二维码信息错误".$e->getMessage());
			return false;
		}
	}
}