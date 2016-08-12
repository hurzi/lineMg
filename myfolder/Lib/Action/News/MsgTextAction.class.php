<?php
/**
 * 显示正文
 */

class MsgTextAction extends Action
{
	public function index ($startT = null) {
		$messageId = trim(HttpRequest::get(MonitorHttpParams::MATERIAL_ID, ''));
		$messageIndex = (int)HttpRequest::get(MonitorHttpParams::INDEX, 0);

		$model = loadModel('News.MsgText');
		$data = $model->auth();
		if (!$data) {
			$this->display('Common.404');
			exit;
		}
		if ($data['oauth_redirect']) {
			header("Location: " . $data['oauth_redirect']);
			exit;
		}

		$msgInfo = $model -> getMsgInfo($data['ent_info'], $messageId, $messageIndex);
		if ($msgInfo === false) {
			$this->display('Common.error');
			exit;
		} else if ($msgInfo === null) {
			$this->display('Common.404');
			exit;
		}
		/*检测日志*/
		Factory::getMonitorLog()->textClick($startT, $data['ent_info'], $data['monitor_data'], $data['params']);

		$info = array(
				'originalUrl' => $model -> getNewsOriginalUrl($msgInfo['url'], $data['ent_info'], $data['monitor_data'], $data['params']),
				'fackId' => $data['params'][MonitorHttpParams::FACK_ID],
				);

		$this->assign('message', $msgInfo);
		$this->assign('info', $info);
		$this->assign('entAppInfo', $data['ent_info']);

		$this->display('News.text');
	}

	public function preview () {
		$messageId = trim(HttpRequest::get(MonitorHttpParams::MATERIAL_ID, ''));
		$messageIndex = (int)HttpRequest::get(MonitorHttpParams::INDEX, 0);
		$entId = (int)HttpRequest::get(MonitorHttpParams::ENT_ID, 0);
		if ($entId<=0 || !$messageId || $messageIndex <= 0) {
			$this->display('Common.404');
			exit;
		}
		$model = loadModel('MsgText');
		
		$msgInfo = $model -> getPreviewMsgInfo( $messageId, $messageIndex);
		if ($msgInfo === false) {
			$this->display('Common.error');
			exit;
		} else if ($msgInfo === null) {
			$this->display('Common.404');
			exit;
		}
		$info = array(
				'originalUrl' => $msgInfo['url'],
				'fackId' => '',
		);

		$this->assign('message', $msgInfo);
		$this->assign('info', $info);
		$this->assign('entAppInfo', AbcUtilTools::getDefaultAppInfo());

		$this->display('News.text');
	}

	public function show () {
		$messageId = trim(HttpRequest::get(MonitorHttpParams::MATERIAL_ID,''));
		$messageIndex = (int)HttpRequest::get(MonitorHttpParams::INDEX, 0);
		
		$model = loadModel('News.MsgText');
		
		$msgInfo = $model -> getMsgInfo( $messageId, $messageIndex,false);
		if ($msgInfo === false) {
			$this->display('Common.error');
			exit;
		} else if ($msgInfo === null) {
			$this->display('Common.404');
			exit;
		}
		$info = array(
				'originalUrl' => $msgInfo['url'],
				'fackId' => '',
		);

		$this->assign('message', $msgInfo);
		$this->assign('info', $info);
		
		$this->display('News.text');
	}
}
