<?php
include_once dirname(__FILE__) . '/Manager.class.php';
/**
 * weixin user dispatcher
 * 这里是派发器，处理分配微信客户到某个客服，建立微信客户与客服的会话关系.
 *
 */
class DispatcherManager extends Manager
{
	/**
	 * 队列等级
	 * @var array
	 */
	private $_levelList = array(1,2);
	/**
	 * 派发客服等级队列
	 * @var int
	 */
	private $_level;
	/**
	 * 目标客服组
	 * @var int
	 */
	private $_groupId;

	/**
	 * 派发程序入口
	 * @param int $level 派发客服等级队列
	 * @param int $groupId 目标客服组（暂时只用于派发2线客服）
	 * @return bool
	 */
	public function main($level = 1, $groupId = 0)
	{
	    if (! $this->_getAppInfo()) {
	        return false;
	    }
		//处理参数
	    if (! $this->_parseParams($level, $groupId)) {
	    	return false;
	    }

		//获取队列列表
		$queues = $this->_getQueueList();
		if (! $queues) {
		    return true;
		}
		//获取在线客服
		$operators = $this->_getOnlineOperatorSessionList();
		if (! $operators) {
		    return true;
		}

		$this->_dispatcher($queues, $operators);

		return true;
	}

	/**
	 * 解析参数
	 * @param int $level
	 * @param int $groupId
	 */
	private function _parseParams($level, $groupId)
	{
		$this->_level = null;
		$this->_groupId = null;

		//目前客服只分1和2等级
		if (in_array($level, $this->_levelList)) {
			$this->_level = $level;
		} else {
			Logger::error('DispatcherManager->_parseParams() error: level param is error :'
					. $level . ' source IP address: ' . $_SERVER['REMOTE_ADDR']);
			return false;
		}
		//指定客服组
		if ($groupId) {
			$sql = "SELECT group_id, parent_id FROM `wx_operator_group` WHERE ent_id = %d AND group_id = %d";
			try {
				$group = $this->db->getRow(sprintf($sql, $this->entId, $groupId));
				if ($group) {
					$this->_groupId = $groupId;
				} else {
					Logger::error('DispatcherManager->_parseParams() error: groupId param is error :'. $groupId);
				}
			} catch (Exception $e) {
				Logger::error('DispatcherManager->_parseParams() error: '.$e->getMessage().'; sql: '. $sql);
			}
		}
		return true;
	}

	/**
	 * 获取队列消息列表
	 *
	 * @return array
	 */
	protected function _getQueueList()
	{
	    $sql = "SELECT *  FROM `wx_queue`";
		//如果派发指定客服等级 添加指定条件
	    if ($this->_level) {
	    	$sql .= " WHERE level = '{$this->_level}'";
	    //如果level参数错误 那么不派发 level = 2 的队列
	    } else {
	    	$where .= " WHERE level != '2'";
	    }
		//只派发指定组消息
	    if ($this->_groupId) {
			$where .= " AND group_id = {$this->_groupId}";
	    }

	    $sql .= " ORDER BY group_id DESC, message_id ASC";
	    try {
	        $this->sqlError = false;
	        $queues = $this->entWxDb->getAll($sql);
	        if (! $queues) {
	            Logger::debug('DispatcherManager->_getQueueList() debug: not queue list. sql : '. $sql);
	        }
	        return $queues;
	    } catch (Exception $e) {
	        $this->sqlError = true;
	        Logger::error('DispatcherManager->_getQueueList() error: '.$e->getMessage().'; sql: '. $sql);
	        return false;
	    }
	}

	/**
	 * 解析队列数据
	 * @param array $queues
	 * @return array $queueList
	 */
	protected function _parseQueues($queues)
	{
		if (! is_array($queues) && ! $queues) return null;

		$queueList = array();
		foreach ($queues as $key => $queue) {
			if (0 == $queue['group_id']) {
				$queueList['all'][] = $queue;
			} else {
				$queueList[$queue['group_id']][] = $queue;
			}

		}
		return $queueList;
	}

	/**
	 * 获取在线客服会话列表
	 *
	 * @return array
	 */
	protected function _getOnlineOperatorSessionList()
	{
		$sql = "SELECT o.operator_id, o.session_max, COUNT(s.session_id) AS session_num, o.level, o.group_id"
			." FROM `wx_operator_online` AS o "
			." LEFT JOIN `wx_session` AS s ON o.operator_id = s.operator_id"
			." WHERE o.heartbeat_time >= ". (time() - Config::SESSION_EXPIRES) ." AND o.status = 1";
		//如果指定客服等级 添加指定条件
		if ($this->_level) {
			$sql .= " AND o.level = '{$this->_level}'";
		//如果指定客服等级错误，添加排除客服等级为2的客服
		} else {
			$sql .= " AND o.level != '2'";
		}
		//如果指定客服等级为2，并且指定来目标客服组 添加组查询条件
		if ($this->_groupId) {
			$where .= " AND o.group_id = {$this->_groupId}";
		}

		$sql .= " GROUP BY o.operator_id";

		try {
			$this->sqlError = false;
			$operatorList = $this->entWxDb->getAll($sql);
			if (! $operatorList) {
				Logger::debug('DispatcherManager->_getOnlineOperatorSessionList() debug: not online operator');
			}
			return $operatorList;
		} catch (Exception $e) {
			$this->sqlError = true;
			Logger::error('DispatcherManager->_getOnlineOperatorSessionList() error: '.$e->getMessage().'; sql: '. $sql);
			return false;
		}
	}

	/**
	 * 解析客服数据
	 * @param array $operators
	 * @return array $operatorList
	 */
	protected function _parseOperators($operators)
	{
		if (! is_array($operators) && ! $operators) return null;

		$operatorList = array();
		foreach ($operators as $key => $operator) {
			$operatorList[$operator['group_id']][] = $operator;
			$operatorList['all'][] = $operator;
		}
		return $operatorList;
	}

	/**
	 * 派发器
	 * @param array $queues
	 * @param array $operators
	 * @return boolean
	 */
	protected function _dispatcher($queues, $operators)
	{
		switch ($this->_level) {
			case '1':
				$this->_dispatcherToLevel1($queues, $operators);
				break;
			case '2':
				$this->_dispatcherToLevel2($queues, $operators);
				break;
			default:
				break;
		}
		return true;
	}

	/**
	 * 派发一线客服
	 * @param array $queues 所有队列数据
	 * @param array $operators 所有客服数据
	 * @return boolean
	 */
	protected function _dispatcherToLevel1($queues, $operators)
	{
		$queueList = $this->_parseQueues($queues);
		$operatorList = $this->_parseOperators($operators);

		$operatorId = 0;
		foreach ($queueList as $key => $queues) {
			if (! @$operatorList[$key]) {
				continue;
			}
			foreach ($queues as $k => $queue) {
				if ($this->_checkSession($queue['from_user'], $queue['message_id'])) {
					$this->_deleteQueue($queue['from_user']);
					continue;
				}
				$operatorId = $this->_getDispatcherOperator($operatorList[$key], $operatorId);
				if (! $operatorId) {
					break;
				}
				//添加会话
				if ($this->_addSession($queue, $operatorId)) {
					$this->_deleteQueue($queue['from_user']);
					//进入一线人工客服提示消息
					$this->_intoOperatorReplyMsg($queue['from_user']);
				}
			}
		}
		return true;
	}

	protected function _dispatcherToLevel2($queues, $operators)
	{
		$queueList = $this->_parseQueues($queues);
		$operatorList = $this->_parseOperators($operators);

		$operatorId = 0;
		foreach ($queueList as $key => $queues) {
			if (! $operatorList[$key]) {
				continue;
			}
			foreach ($queues as $k => $queue) {
				if ($this->_checkSession($queue['from_user'], $queue['message_id'])) {
					$this->_deleteQueue($queue['from_user']);
					continue;
				}
				$operatorId = $this->_getDispatcherOperator($operatorList[$key], $operatorId);
				if (! $operatorId) {
					break;
				}
				//添加会话
				if ($this->_addSession($queue, $operatorId)) {
					$this->_deleteQueue($queue['from_user']);
				}
			}
		}
		return true;
	}

	/**
	 * 检查会话是否存在
	 *
	 * @param string $fromUser
	 * @param string $messageId
	 * @return bool
	 */
	protected function _checkSession($fromUser, $messageId)
	{
	    $sql = "SELECT session_id, message_id FROM `wx_session` WHERE from_user = '%s'";
	    try {
	        $this->sqlError = false;
	        $session = $this->entWxDb->getRow(sprintf($sql,$fromUser));
	        if (! $session) {
	            return false;
	        }
	        if ($session['message_id'] && $messageId && $session['message_id'] > $messageId) {
	            $this->_updateSeesionMessage($session['session_id'], $messageId);
	        }
	        return true;
	    } catch (Exception $e) {
	        $this->sqlError = true;
	        Logger::error('DispatcherManager->_checkSession() error: '.$e->getMessage().'; sql: '. $sql);
	        return false;
	    }
	}

	/**
	 * 更新会话消息
	 * @param string $sessionId
	 * @param string $messageId
	 * @return void
	 */
	protected function _updateSeesionMessage($sessionId, $messageId)
	{
	    $where = " session_id = {$sessionId}";
	    $this->entWxDb->update('wx_session', $where, array('message_id' => $messageId));
	}

	/**
	 * 添加会话
	 *
	 * @param array $quequ 队列信息
	 * @param int $operatorId
	 * @return bool
	 */
	protected function _addSession($queue, $operatorId)
	{
		$messageId = $queue['message_id'];

		$sessionId = substr(md5(uniqid().$messageId), 8, 16);

		$data = array(
				'ent_id' => $this->entId,
				'from_user' => $queue['from_user'],
				'message_id' => $messageId,
				'operator_id' => $operatorId,
				'queue_time' => $queue['create_time'],
				'prev_session_id' => $queue['prev_session_id'],
				'level' => $queue['level'],
				'group_id' => $queue['group_id'],
				'session_id' => $sessionId,
				'first_session_id' => $queue['first_session_id'] ? $queue['first_session_id'] : $sessionId,
				'create_time' => date('Y-m-d H:i:s'),
		);

	    try {
	        $this->sqlError = false;
	        $result = $this->entWxDb->insert('wx_session', $data);
	        if ($result === false) {
	            Logger::error('DispatcherManager->_addSession() error: add session fail. sql:'. $this->entWxDb->getLastSql());
	            return false;
	        }
	    } catch (Exception $e) {
	        $this->sqlError = true;
	        Logger::error('DispatcherManager->_addSession() error: ' .$e->getMessage(). '; sql:'. $this->entWxDb->getLastSql());
	        return false;
	    }
	    return true;
	}

	/**
	 * 删除队列
	 *
	 * @param  string $fromUser
	 * @return bool
	 */
	protected function _deleteQueue($fromUser)
	{
	    try {
	        $this->sqlError = false;
	        $where = " from_user = '{$fromUser}'";
	        $result = $this->entWxDb->delete('wx_queue', $where);
	        if (!$result) {
	            Logger::error('DispatcherManager->_deleteQueue() error: delete queue fail. sql:'. $this->entWxDb->getLastSql());
	            return false;
	        }
	    } catch (Exception $e) {
	        $this->sqlError = true;
	        Logger::error('DispatcherManager->_deleteQueue() error: ' .$e->getMessage(). '; sql:'. $this->entWxDb->getLastSql());
	        return false;
	    }
	    return true;
	}

	/**
	 * 获取派发客服
	 *
	 * @param array $operators 所有可派发的客服
	 * @param int $lastOperatorId 上次派发的客服ID
	 * @return int $operatorId
	 */
	protected function _getDispatcherOperator(&$operators, $lastOperatorId = 0)
	{
	    $operatorId = 0;
	    if (! $operators) return $operatorId;

	    $result = array();
	    foreach ($operators as $key => $operator) {
	    	$operId = $operator['operator_id'];
	        if ($operId == $lastOperatorId) {
	            $operator['session_num'] += 1;
	        }
	        if ($operator['session_max'] <= 0) {
	        	unset($operators[$key]);
	        	continue;
	        }
	        $rate = (float) $operator['session_num'] / $operator['session_max'];
	        if ($rate >= 1 || $rate < 0) {
	            unset($operators[$key]);
	            continue;
	        }
	        if (! $result) {
	            $operatorId = $operId;
	            $result['rate'] = $rate;
	        } else {
	            if ($rate <= $result['rate']) {
	                $result['rate'] = $rate;
	                $operatorId = $operId;
	            }
	        }
	        $operators[$key] = $operator;
	    }
	    return $operatorId;
	}

	/**
	 * 进入客服回复提示消息
	 */
	protected function _intoOperatorReplyMsg($fromUser)
	{
		Logger::info("进入客服回复消息user: " . $fromUser . '; ent_id:' . $this->entId);

		$setting = $this->getEntSetting($this->entId);
		$replyCon = @$setting[EntSettingKey::OPERATOR_USER_INTO_LEVEL_ONE_CONTENT];
		$messageBody = new WX_Message_Body();
		$messageBody->to_users = $fromUser;
		$messageBody->type = 'text';
		$messageBody->content = $replyCon;

		//linux下的PHP路径
		$php_cli_path = ConfigBase::PHP_CLI_PATH;
		//验证操作系统  方便测试
		$message_body = json_encode($messageBody, JSON_HEX_APOS);
		$dir_path = dirname(dirname(__FILE__)). "/Shells/CPUThread/MessageSendThread.shell.php";

		$cmd = "{$php_cli_path} {$dir_path} {$this->entId} '{$message_body}' >/tmp/timeline.log &";
		Logger::info('DispatcherManager->_intoOperatorReplyMsg cmd :' . $cmd);
		//执行linux命令
		system($cmd);
		//$out = popen($cmd, "r");
		//pclose($out);
	}
}
