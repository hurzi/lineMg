<?php
/**
 * 微信用户信息处理类
 */
class WxUserFetcher
{
	private $_openid;
	private $_eventType;
	private $_timeout = false;
	private $_updateTime = 86400; //更新时间限制 单位秒
	private $_db;
	private $_msgTime;
	private $_sqlError;

	public function __construct($openid, $eventType, $msgTime)
	{
		$this->_openid = trim($openid);
		$this->_eventType = strtolower(trim($eventType));
		$this->_msgTime = intval($msgTime);
	}

	/**
	 * 执行入口函数
	 */
	public function run()
	{
		//判断参数
		if ($this->_openid) {
			$this->_db = Factory::getDb();
			$this->_save();
		} else {
			Logger::error('WxUserFetcher->run() error: openid is null.');
		}
	}

	/**
	 * 用户保存
	 */
	private function _save()
	{
		//保存用户主动发消息时间 不包括取消关注用户时触发的时间
		if ($this->_eventType != MessageEventType::UNSUBSCRIBE) {
			$this->_saveUserMsgTime($this->_openid, $this->_msgTime);
		}

		$exist = $this->_checkUserIsExist($this->_openid);
		if (! $exist) {
			if ($this->_eventType != MessageEventType::UNSUBSCRIBE) {
				$user = $this->_getUser($this->_openid);
				$this->_saveUser($user);
			}
		} else {
			//如果不是取消关注并且更新超时 更新用户
			if (true == $this->_timeout && $this->_eventType != MessageEventType::UNSUBSCRIBE) {
				$user = $this->_getUser($this->_openid);
				$this->_updateUser($user);
			} else if ($this->_eventType) {
				//如果不超时并且时关注或取消关注，更新关注状态
				$eventTypeArray = array (
						MessageEventType::SUBSCRIBE,
						MessageEventType::UNSUBSCRIBE
				);
				if (in_array($this->_eventType, $eventTypeArray)) {
					$stauts = ($this->_eventType == MessageEventType::SUBSCRIBE) ? 1 : 0;
					$this->_updateUserSubscribeStatus($this->_openid, $stauts);
				}
			}
		}
	}

	/**
	 * 检测发送消息用户是否存在
	 * @param string $openid 用户ID
	 * @return bool
	 */
	private function _checkUserIsExist($openid)
	{
		$sql = "SELECT openid, last_update_time FROM `wx_user` WHERE openid = '{$openid}' LIMIT 1";
		try {
			$this->_sqlError = false;
			$user = $this->_db->getRow($sql);
			if (! $user) {
				return false;
			}
			if ((time() - strtotime($user['last_update_time'])) > $this->_updateTime) {
				$this->_timeout = true;
			}
			return true;
		} catch ( Exception $e ) {
			$this->_sqlError = true;
			Logger::error('WxUserFetcher->_checkUserIsExist() error: ' . $e->getMessage() . '; sql: ' . $sql);
			return false;
		}
	}

	/**
	 * 去微信平台获取用户信息
	 * @param string $openid 用户ID
	 * @return WX_User
	 */
	private function _getUser($openid)
	{
		if (! class_exists('WeiXinApiCore', false)) {
			include_once dirname(__FILE__) . '/../../../AbcPHP/API/WeiXinApiCore.class.php';
		}
		$appId = C('APP_ID');
		$appSecret = C('APP_SECRET');

		$token = getToken($appId, $appSecret);
		$wxClient = WeiXinApiCore::getClient($appId, $appSecret, $token);

		$user = $wxClient->getUser($openid);

		if (! $user) {
			$code = $wxClient->getErrorCode();
			$retryArr = array (
					WX_Error::INVALID_CREDENTIAL_ERROR,
					WX_Error::CONNECTION_ERROR,
					WX_Error::TOKEN_EXPIRED_ERROR,
					WX_Error::KOTEN_MISSING_ERROR,
					WX_Error::SERVICE_UNAVAILABLE_ERROR
			);
			$resetTokenArr = array (
					WX_Error::CONNECTION_ERROR,
					WX_Error::TOKEN_EXPIRED_ERROR,
					WX_Error::KOTEN_MISSING_ERROR
			);
			if (in_array($code, $resetTokenArr)) {
				$token = getToken($token, $wxClient);
				$wxClient->setToken($token);
			}
			if (in_array($code, $retryArr)) {
				$user = $wxClient->getUser($openid);
			}
		}

		if (! $user) {
			Logger::error('WxUserFetcher->_getUser() error: get weixin user fail. openid: ' . $openid . ' error: ' . $wxClient->getErrorMessage());
			return null;
		}
		return $user;
	}

	/**
	 * 保存用户
	 * @param WX_User $user
	 * @return bool
	 */
	private function _saveUser($user)
	{
		if (! is_object($user) || ! $user) {
			Logger::error('WxUserFetcher->_saveUser() error: user is not object or is null.', $user);
			return false;
		}
		$data = array (
				'openid' => $user->openid,
				'nickname' => (string) faddslashes($user->nickname),
				'sex' => (int) $user->sex,
				'country' => $user->country,
				'province' => $user->province,
				'headimgurl' => faddslashes($user->headimgurl),
				'city' => $user->city,
				'language' => $user->language,
				'subscribe' => (int) $user->subscribe,
				'subscribe_time' => (int) $user->subscribeTime,
				'create_time' => date('Y-m-d H:i:s'),
				'last_update_time' => date('Y-m-d H:i:s')
		);

		try {
			$this->_sqlError = false;
			$result = $this->_db->insert('wx_user', $data);
			if (false === $result) {
				Logger::error('WxUserFetcher->_saveUser() error: save user fail. sql:' . $this->_db->getLastSql());
				return false;
			}
		} catch ( Exception $e ) {
			$this->_sqlError = true;
			Logger::error('WxUserFetcher->_saveUser() error: ' . $e->getMessage() . '; sql:' . $this->_db->getLastSql());
			return false;
		}
		return true;
	}

	/**
	 * 更新用户用户
	 * @param WX_User $user
	 * @return bool
	 */
	private function _updateUser($user)
	{
		if (! is_object($user) || ! $user) {
			Logger::error('WxUserFetcher->_updateUser() error: user is not object or is null.', $user);
			return false;
		}
		$data = array (
				'openid' => $user->user,
				'nickname' => (string) faddslashes($user->nickname),
				'sex' => (int) $user->sex,
				'country' => $user->country,
				'province' => $user->province,
				'headimgurl' => faddslashes($user->headimgurl),
				'city' => $user->city,
				'language' => $user->language,
				'subscribe' => (int) $user->subscribe,
				'subscribe_time' => (int) $user->subscribeTime,
				'last_update_time' => date('Y-m-d H:i:s')
		);
		try {
			$this->_sqlError = false;
			return $this->_db->update('wx_user', "openid = '{$user->openid}'", $data);
		} catch ( Exception $e ) {
			$this->_sqlError = true;
			Logger::error('WxUserFetcher->_updateUser() error: ' . $e->getMessage() . '; sql:' . $this->_db->getLastSql());
			return false;
		}
		return true;
	}

	/**
	 * 修改用户关注状态
	 * @param string $openid
	 * @param int $status
	 * @return boolean
	 */
	private function _updateUserSubscribeStatus($openid, $stauts)
	{
		try {
			$this->_sqlError = false;
			$where = " openid = '{$openid}'";
			$set = array (
					'subscribe' => (int) $stauts,
					'last_update_time' => date('Y-m-d H:i:s')
			);
			$result = $this->_db->update('wx_user', $where, $set);
			if (false === $result) {
				Logger::error('WxUserFetcher->_updateUserSubscribeStatus() error: update user fail. sql:' . $this->_db->getLastSql());
				return false;
			}
		} catch ( Exception $e ) {
			$this->_sqlError = true;
			Logger::error('WxUserFetcher->_updateUserSubscribeStatus() error: ' . $e->getMessage() . '; sql:' . $this->_db->getLastSql());
			return false;
		}
	}

	/**
	 * 保存用户发送消息时间
	 * @param string $openid
	 * @param int $msgTime
	 * @return bool
	 */
	private function _saveUserMsgTime($openid, $msgTime)
	{
		if (! $openid) {
			Logger::error('WxUserFetcher->_saveUserMsgTime() error: openid is null');
			return false;
		}

		$time = $msgTime ? $msgTime : time();

		$data = array (
				'openid' => $openid,
				'last_time' => $time
		);

		try {
			$result = $this->_db->insert('wx_user_message_time', $data, true);
			if (false === $result) {
				Logger::error('WxUserFetcher->_saveUserMsgTime() error: save user send message time fail. sql:' . $this->_db->getLastSql());
				return false;
			}
		} catch ( Exception $e ) {
			Logger::error('WxUserFetcher->_saveUserMsgTime() error: ' . $e->getMessage() . '; sql:' . $this->_db->getLastSql());
			return false;
		}
		return true;
	}
}