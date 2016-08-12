<?php
/**
 * 这里是图文原文入口action
 */
class MsgOriginalAction extends Action
{
	public function index ($startT) {
		$model = loadModel('News.MsgOriginal');
		$data = $model->auth();
		if (!$data) {
			$this->display('Common.404');
			exit;
		}
		if ($data['oauth_redirect']) {
			header("Location: " . $data['oauth_redirect']);
			exit;
		}
		/*检测日志*/
		Factory::getMonitorLog()->originalClick($startT, $data['ent_info'], $data['monitor_data'], $data['params']);

		$targetUrl = $model -> getTargetUrl($data['ent_info'], $data['monitor_data'], $data['params']);
		header("Location: " . $targetUrl);
		exit;
	}
}