<?php
/**
 * 这里是过滤管理器，将自动读取企业插件配置进行循环过滤
 *
 * 从这里调用插件两种方式：
 * 	local本地加载方式：将企业ID和message以方法参数方式传递
 * 	remote远程http请求方式： post发送数据 array('message' => string 序列化对象(WX_Message)的字符串)
 */
class FilterManager extends Manager
{
	private $_messageBody;

	private $_noThreadMsgType = array ('text', 'news', 'music');

	/**
	 * 入口函数
	 * @return bool true:被过滤器命中，否则为false.
	 */
	public function main()
	{
		$return = array (
				'status' => false,
				'message_body' => '',
				'plugin_key' => '',
				'plugin_info' => '',
				'sent' => false
		);

		$plugins = $this->_getPlugins();
		if ($plugins) {
			foreach ($plugins as $k => $plugin) {
				$pluginResult = $this->_callPlugin($plugin);
				if ($pluginResult && @$pluginResult['status']) {
					$return = $pluginResult;
					break;
				}
			}
		}

		if (isset($return['message_body']) && $return['message_body']) {
			$return['sent'] = false;
			$messageBody = $return['message_body'];
			$pluginKey = $return['plugin_key'];
			$pluginInfo =  @$return['plugin_info'];
			if (! in_array($messageBody->msgType, $this->_noThreadMsgType)) {
				$return['sent'] = true;
				$this->_threadSend($messageBody, $pluginKey, $pluginInfo);
			}
		}
		return $return;
	}

	/**
	 * 获取插件列表
	 * @return array
	 */
	protected function _getPlugins()
	{
		$plugins = C('PLUGINS');
		return $plugins;
	}

	/**
	 * 插件调用
	 * @param array $plugin
	 * $plugin = array(
	 * 				'plugin_id' => int 插件ID
	 * 				'exec_type' => 'local'|'remote' 本地加载方式|http请求方式
	 * 				'file_path' => string   插件地址或url地址
	 * 				'class_name' => string  exec_type = local,调用的类名称
	 * 				'method_name' => string exec_type = local,调用的类方法
	 * 				'class_type' => instance|static exec_type = local,调用的类的类型 实例和静态
	 * 			)
	 * @return bool
	 */
	protected function _callPlugin($plugin)
	{
		if (! $plugin || ! $plugin['plugin_id'] || ! $plugin['exec_type'] || ! $plugin['file_path']) {
			Logger::error('FilterManager->_callPlugin() error: plugin data missing key value : '
					. 'plugin_id or exec_type or file_path ', $plugin);
			return false;
		}

		$pluginId = $plugin['plugin_id'];
		$execType = $plugin['exec_type'];
		//本地加载插件方式
		if ('local' == $execType) {
			$pluginPath = C('PLUGIN_PATH');
			$filePath = $pluginPath . $plugin['file_path'];
			if (! $plugin['class_name'] || ! $plugin['method_name'] || ! $plugin['class_type']) {
				Logger::error('FilterManager->_callPlugin() error: plugin data missing key value ：'
						. 'class_name or method_name or class_type. plugin_id : ' . $pluginId, $plugin);
				return false;
			}

			$className = $plugin['class_name'];
			$methodName = $plugin['method_name'];
			$classType = $plugin['class_type'];

			if (! file_exists($filePath)) {
				Logger::error('FilterManager->_callPlugin() error: plugin type is local file not exist : '
						. $filePath . ' plugin_id : ' . $pluginId, $plugin);
				return false;
			}

			include_once $filePath;

			if (! class_exists($className, false) || ! method_exists($className, $methodName)) {
				Logger::error('FilterManager->_callPlugin() error: plugin type is local class or method not exist.'
						. ' class_name : ' . $className . 'method_name : ' . $methodName
						. ' plugin_id : ' . $pluginId, $plugin);
				return false;
			}

			if ('instance' == $classType) {
				$obj = new $className();
				$return = call_user_func(array($obj, $methodName), $this->message);
				return $return;
			} else if ('static' == $classType) {
				$return = call_user_func(array($className, $methodName), $this->message);
				return $return;
			} else {
				Logger::error('FilterManager->_callPlugin() error: plugin class type is not instance or static.'
						. ' plugin_id : ' . $pluginId, $plugin);
			}
			//远程http请求调用方式
		} else if ('http' == $execType) {
			$filePath = $plugin['file_path'];
			if (strrpos($filePath, 'http://') === false) {
				Logger::error('FilterManager->_callPlugin() error: plugin remote url error:', $plugin);
				return false;
			}
			$result = ThirdPartyTools::pluginThirdPush($filePath, $this->message, $this->_msgStr);
			$return = array (
					'status' => false,
					'message_body' => array(),
					'plugin_key' => $plugin['key'],
					'sent' => false
			);
			if ($result === true) {
				$return['status'] = true;
			} else if (is_object($result)) {
				$return['status'] = true;
				$return['message_body'] = $result;
			} else {
				//如果http请求出错
				if (ThirdPartyTools::getError()) {
					Logger::error('FilterManager->_callPlugin(): ThirdPartyTools::pluginThirdPush() error: plugin type is remote Http request fail.'
							. ' plugin_id : ' . $pluginId . '; http_code :' . ThirdPartyTools::getHttpCode()
							. '; error :' . ThirdPartyTools::getError()
							. "\nhttp_res: " . ThirdPartyTools::getResponse(), $filePath);
				}
			}
			return $return;
		}

		return false;
	}

	/**
	 * 进程脚本发送
	 * @param WX_Message_Body $messageBody
	 * @param string $pluginKey
	 * @return void
	 */
	private function _threadSend($messageBody, $pluginKey, $pluginInfo = null)
	{
		//linux下的PHP路径
		$phpCliPath = C('PHP_CLI_PATH');

		$messageBodyJson = json_encode($messageBody, JSON_HEX_APOS);
		$pluginInfo = json_encode($pluginInfo, JSON_HEX_APOS);

		$dirPath = LIB_PATH . "/Shell/WXAppThread/FilterSendThread.shell.php";

		$cmd = "{$phpCliPath} {$dirPath} '{$messageBodyJson}' '{$pluginKey}' '{$pluginInfo}' &";
		Logger::info('FilterManager->_threadSend() cmd :' . $cmd);
		//执行linux命令
		//system($cmd);
		$out = popen($cmd, "r");
		pclose($out);
	}
}