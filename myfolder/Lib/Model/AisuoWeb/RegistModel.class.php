<?php
/**
 * 二维码操作表
 */
class RegistModel extends Model {
	
	private static $_db;
	private static $_tablename;
	
	public function __construct(){
		self::$_db = Factory::getDb();
		self::$_tablename = "as_user";
	}

	/**
	 *修改个人信息
	 */
	public function UpdateUserInfoArr($openid,$arrInfo){
		try{
			return self::$_db->update(self::$_tablename,"openid='".$openid."'",$arrInfo);
		}catch(Exception $e){
			Logger::error("操作用户表失败".$e->getMessage());
			return false;
		}
	}
	
	
	
	/**
	*修改个人信息
	*/
	public function UpdateUserName($openid,$truthname){
		try{
			return self::$_db->update(self::$_tablename,"openid='".$openid."'",array("truthname"=>$truthname));
		}catch(Exception $e){
			Logger::error("操作用户表失败".$e->getMessage());
			return false;
		}
	}
	
	/**
	 * 通过微信id获取用户扫描二维码数据
	 */
	public function saveOrUpdateAsUser($data,$isupdate=false)  {
		try{
			if(empty($data['truthname'])){
				return false;
			}
			if(empty($data['sex'])){
				unset($data['sex']);
			}
			if(empty($data['headimgurl'])){
				$data['headimgurl'] = AbcConfig::BASE_WEB_DOMAIN_PATH."AisuoWeb/images/aslogo.jpg";
			}
			
			if($isupdate){
				self::$_db->update(self::$_tablename, "openid = '".$data['openid']."'",$data );
			}else{
				self::$_db->insert(self::$_tablename,$data);				
			}	
			return true;
		} catch(Exception $e) {
			Logger::error("操作用户表失败".$e->getMessage());
			return false;
		}
	}
	
	/**
	 * 通过微信id获取用户扫描二维码数据
	 */
	public function updateLastReadyTime($uid,$lastReadyTime)  {
		try{
			self::$_db->update(self::$_tablename, "uid = '".$uid."'",array("last_ready_time"=>$lastReadyTime) );
			return true;
		} catch(Exception $e) {
			Logger::error("操作用户表失败".$e->getMessage());
			return false;
		}
	}
	
	/**
	 * 通过手机号获取微信ID
	 */
	public function getOpenidByMobile($mobile)  {
		try{
			return self::$_db->getOne("select openid from ".self::$_tablename." where mobile = '{$mobile}'");
		} catch(Exception $e) {
			Logger::error("操作用户表失败".$e->getMessage());
			return false;
		}
	}
	/**
	 * 通过微信id获取手机号
	 */
	public function getMobileByOpenid($openid)  {
		try{
			return self::$_db->getOne("select mobile from ".self::$_tablename." where openid = '{$openid}'");
		} catch(Exception $e) {
			Logger::error("操作用户表失败".$e->getMessage());
			return false;
		}
	}
	/**
	 * 通过微信id获取详情
	 */
	public function getObjByOpenid($openid)  {
		try{
			return self::$_db->getRow("select * from ".self::$_tablename." where openid = '{$openid}'");
		} catch(Exception $e) {
			Logger::error("操作用户表失败".$e->getMessage());
			var_dump($e);
			return false;
		}
	}
	/**
	 * 通过uid获取详情
	 */
	public function getObjByUid($uid)  {
		try{
			return self::$_db->getRow("select * from ".self::$_tablename." where uid = '{$uid}'");
		} catch(Exception $e) {
			Logger::error("操作用户表失败".$e->getMessage());
			return false;
		}
	}
	
	/**
	 * 获取所有马甲
	 */
	public function getObjMajia()  {
		try{
			return self::$_db->getAll("select * from ".self::$_tablename." where is_majia = 1");
		} catch(Exception $e) {
			Logger::error("操作用户表失败".$e->getMessage());
			return false;
		}
	}
	
	/**
	 * 通过手机号获取详情
	 */
	public function getObjByMobile($mobile)  {
		try{
			return self::$_db->getRow("select * from ".self::$_tablename." where mobile = '{$mobile}'");
		} catch(Exception $e) {
			Logger::error("操作用户表失败".$e->getMessage());
			return false;
		}
	}
	
	/**
	 * 把数组中的数据增加用户信息
	 */
	public function addUserInfo($arrData)  {
		try{
			if(!$arrData){
				return $arrData;
			}
			$openids = array();
			foreach ($arrData as $v){
				$openids[$v['openid']] = $v['openid'];
			}
			
			$queryData = self::$_db->getAll("select * from ".self::$_tablename." where openid in ('".implode("','",$openids)."') ");
			$tmpData = array();
			if($queryData){
				foreach ($queryData as $v){
					$tmpData[$v['openid']] = $v;
				}
			}
			for($i = 0 ;$i<count($arrData);$i++){
			//foreach ($arrData as $v){
				$v = $arrData[$i];
				$tmpuser = @$tmpData[$v['openid']];
				$arrData[$i]['a_truthname'] = $tmpuser['truthname'];
				$arrData[$i]['a_headimgurl'] = $tmpuser['headimgurl'];
				if($v['is_private']){
					$arrData[$i]['u_truthname'] = AbcConfig::DEFAULT_USER_TRUTHNAME;
					$arrData[$i]['u_headimgurl'] = AbcConfig::DEFAULT_USER_HEADIMGURL;
					continue;
				}
				if($tmpuser){
					$arrData[$i]['u_truthname'] = $tmpuser['truthname'];
					$arrData[$i]['u_sex'] = $tmpuser['sex'];
					$arrData[$i]['u_headimgurl'] = $tmpuser['headimgurl'];
					$arrData[$i]['u_mobile'] = $tmpuser['mobile'];
				}else{
					$arrData[$i]['u_truthname'] = AbcConfig::DEFAULT_USER_TRUTHNAME;
					$arrData[$i]['u_headimgurl'] = AbcConfig::DEFAULT_USER_HEADIMGURL;					
				}
			}
			
			return $arrData;
		} catch(Exception $e) {
			Logger::error("操作用户表失败".$e->getMessage());
			return false;
		}
	}
}