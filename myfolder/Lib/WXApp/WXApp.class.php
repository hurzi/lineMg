<?php
/**
 * 微信应用处理类
 */
class WXApp
{
	/**
	 * 微信消息
	 * @var WX_Message
	 */
	private static $_message;
	/**
	 * 微信原XML消息
	 * @var string
	 */
	private static $_msgStr;

	/**
	 * 执行方法
	 * @param WX_Message $message
	 * @param string $msgStr
	 * @return array
	 */
	public static function main($message, $msgStr)
	{
		self::$_message = $message;
		self::$_msgStr = $msgStr;		
		
		$msgResult = self::_callMessageManager();

		//消息保存失败直接返回
		if (! $msgResult) {
			return false;
		}

		$filterResult = self::_callFilterManager();

		if ($filterResult && $filterResult['status']) {
			return $filterResult;
		}

		//self::_callQueueManager();

		//self::_callDispatcherManager();
		return false;
	}

	/**
	 * 调用消息管理
	 */
	private static function _callMessageManager()
	{
		$messageManager = new MessageManager(self::$_message, self::$_msgStr);
		$result = $messageManager->save();

		if (! $result) {
			return false;
		}

		//TODO 暂时先不处理自动上报地理位置和取消关注信息
		if (self::$_message->event && 'event' == self::$_message->msgType) {
			switch (strtolower(self::$_message->event->eventType)) {
				case MessageEventType::LOCATION :
					return false;
				case MessageEventType::UNSUBSCRIBE :
					return false;
			}
		}
		return true;
	}

	/**
	 * 调用过滤器
	 */
	private static function _callFilterManager()
	{
		//调用过滤器
		$filterManager = new FilterManager(self::$_message, self::$_msgStr);
		$filterResult = $filterManager->main();

		Logger::debug('WXApp::_callFilterManager() : filterResult ', $filterResult);
		//事件消息
		if (isset(self::$_message->event) && ! empty(self::$_message->event) && is_object(self::$_message->event)) {
			if ($filterResult && @$filterResult['status'] == true) {
				$messageBody = @$filterResult['message_body'];
				if ($messageBody) {
					if (false == @$filterResult['sent']) {
						//保存下行信息 1 上行 2 下行
						$pluginKey =  @$filterResult['plugin_key'];
						$pluginInfo =  @$filterResult['plugin_info'];
						self::_addFilterDialog(2, $pluginKey, $messageBody, $pluginInfo);
					}
				}
			}
		//普通消息
		} else {
			if (! $filterResult || @$filterResult['status'] == false) {
				//更改消息状态标识为插件未命中
				self::_updateMessageStatus(self::$_message->msgId, 0);
			} else {
				$messageBody = @$filterResult['message_body'];
				$pluginKey =  @$filterResult['plugin_key'];
				$pluginInfo =  @$filterResult['plugin_info'];
				//保存上行信息 1 上行 2 下行
				self::_addFilterDialog(1, $pluginKey, self::$_message, $pluginInfo);
				if ($messageBody) {
					if (false == @$filterResult['sent']) {
						//保存下行信息 1 上行 2 下行
						self::_addFilterDialog(2, $pluginKey, $messageBody, $pluginInfo);
					}
				}
				//更改消息状态标识为插件自动回复
				self::_updateMessageStatus(self::$_message->msgId);
			}
		}
		return $filterResult;
	}

	/**
	 * 添加过滤器对话信息
	 * @param int $type 信息类型1上行微信用户消息；2下行插件回复信息
	 * @param string $pluginKey 插件key
	 * @param WX_Message_Body $message
	 * @return bool
	 */
	private static function _addFilterDialog($type, $pluginKey, $message, $pluginInfo = null)
	{
		$messageManager = new MessageManager(self::$_message, self::$_msgStr);
		$result = $messageManager->addDialog($type, $pluginKey, $message, $pluginInfo);
		return $result;
	}

	/**
	 * 调用队列器
	 */
	private static function _callQueueManager()
	{
		//调用队列
		include_once $this->_cpuLibPath . '/QueueManager.class.php';
		$queue = new QueueManager(self::$_message, self::$_msgStr);
		$queueResult = $queue->main();
	}

	/**
	 * 调用派发器
	 */
	private static function _callDispatcherManager()
	{
		//调用派发器
		$dispatcher = new DispatcherManager(self::$_message, self::$_msgStr);
		$dispatcherResult = $dispatcher->main();
	}

	/**
	 * 更新消息状态
	 * @param string $messageId
	 * @param int $status
	 */
	private static function _updateMessageStatus($msgId, $status = 1)
	{
		$messageManager = new MessageManager(self::$_message, self::$_msgStr);
		$result = $messageManager->updateStatus($msgId, $status);
	}
}