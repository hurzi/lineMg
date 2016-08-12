<?php
/**
 * 这是处理message显示类
 * @author paizhang
 *
 */
class MessageTools
{
	public static $msgTypeArr = array (
						'text' => '文本',
						'news' => '图文',
						'music' => '音乐',
						'image' => '图片',
						'voice' => '语音',
						'video' => '视频'
					);

	/**
	 * 统一处理群发历史消息列表数据
	 * @param int $entId
	 * @param array $msgList
	 * @param array $stateArr 任务状态汉化数组
	 * @param array $msgTypeArr 消息类型汉化数组
	 * @return array
	 *  array('viewData'=>array(包含html数据), 'list' => array(全部数据));
	 */
	public static function parseMassMsgList($entId, $msgList, $stateArr, $msgTypeArr, $templateList = null)
	{
		$result = array (
				'jsonData' => array (),
				'list' => array ()
		);
		if (! $msgList || ! is_array($msgList)) {
			return $result;
		}
		foreach ($msgList as $index => $msg) {
			$escapData = self::escape($msg, $msgTypeArr, $templateList);
			$escapData['state_name'] = $stateArr[$msg['send_state']];
			$escapData['html'] = self::massFormat($entId, $escapData, $index, $templateList);
			$result['list'][] = $escapData;
			$result['jsonData'][$msg['task_id']] = self::getViewData($msg['msg_type'], $escapData);
		}
		return $result;
	}

	/**
	 * 统一处理dialog历史消息列表数据
	 * @param int $entId
	 * @param array $msgList
	 * @param array $msgTypeArr 消息类型汉化数组
	 * @return array
	 *  array('viewData'=>array(), 'list' => array());
	 */
	public static function parseDialogMsgList($entId, $msgList, $msgTypeArr, $templateList = null)
	{
		$result = array (
				'jsonData' => array (),
				'list' => array ()
		);
		if (! $msgList || ! is_array($msgList)) {
			return $result;
		}
		foreach ($msgList as $index => $msg) {
			$msg['msg_type'] = $msg['msgtype'];
			$escapData = self::escape($msg, $msgTypeArr, $templateList);
			$escapData['html'] = self::dialogFormat($entId, $escapData, $index, $templateList);
			$result['list'][] = $escapData;
			$result['jsonData'][$msg['dialog_id']] = self::getViewData($msg['msgtype'], $escapData);
		}
		return $result;
	}

	/**
	 * 生成页面json 数据
	 * @param string $msgType
	 * @param array $msg
	 * @return array
	 */
	public static function getViewData($msgType, $msg)
	{
		$ret = array (
				'type' => $msgType,
				'data' => null
		);
		switch ($msgType) {
			case "text" :
				$ret['data'] = WX_Face::parseFace($msg['content']);
				break;
			case "news" :
				$ret['data'] = $msg['articles'];
				break;
			case "music" :
				$ret['data']['title'] = $msg['title'];
				$ret['data']['description'] = $msg['description'];
				$ret['data']['thumb_url'] = @$msg['thumb_url'];
				$ret['data']['music_url'] = @$msg['music_url'];
				$ret['data']['hq_music_url'] = @$msg['hq_music_url'];
				break;
			case "image" :
			case "voice" :
			case "video" :
				$ret['data'] = @$msg['media_url'];
				break;
			case 'template' :
				$ret['type'] = 'text';
				$ret['data'] = str_replace("\n", '<br/>', $msg['content']);
				break;
		}
		return $ret;
	}

	/**
	 * 这个是处理群发历史显示函数
	 * @param int $entId
	 * @param  array $data 内部字段名词对应数据库weixin_service2.wx_message_tasks
	 * @return string html string
	 */
	public static function massFormat($entId, $data, $index, $templateList = null)
	{
		$html = '';
		switch ($data['msg_type']) {
			case "text" :
				$more = '...<a class="more" type="text" href="javascript:;" task_id="' . $data['task_id'] . '"> 更多</a>';
				$html = cutstr_dis(@$data['content'], 300, $more);
				$html = WX_Face::parseFace($html);
				break;
			case "news" :
				$articles = @$data['articles'][0];
				if (! $articles)
					return $html;
				$taskId = $data['task_id'];
				$url = @$articles['url'] ? str_replace('Tracker/preview.php', 'Tracker/show.php', $articles['url']) : '';
				$picurl = @$articles['picurl'];
				$title = cutstr_dis(@$articles['title'], 34);
				$desc = cutstr_dis(@$articles['description'], 128);
				$html = <<<EOF
				<div class="msg_his_img_con">
					<a class="more" type="news" task_id="{$taskId}" href="javascript:;" title="图文消息">
						<img src="{$picurl}" />
					</a>
				</div>
				<div class="msg_his_text_con">
					<div class="msg_his_text_title"><a target='_blank' href="{$url}">{$title}</a></div>
					<a target='_blank' href="{$url}" class="msg_his_text_desc">{$desc}</a>
				</div>
EOF;
				break;
			case "image" :
				if (! @$data['media_local_url']) {
					$picUrl = parseImageUrl($data['media_url']);
				} else {
					$picUrl = $data['media_local_url'];
				}
				$html = '<p><img class="his_mini_img" onclick="window.top.ligImg.showImg(this,\'' . $picUrl . '\');" src="' . $picUrl . '" title="图片消息" /></p>';
				break;
			case "voice" :
				if (! @$data['media_local_url']) {
					if (@$data['media_url']) {
						//如果是mp3 直接使用
						if ('.mp3' == substr($data['media_url'], strrpos($data['media_url'], '.'))){
							$voiceUrl = $data['media_url'];
						} else {
							$voiceUrl = parseMediaUrl($data['media_url']);
						}
					} else {
						$voiceUrl = getMediaUrlByTaskId($entId, $data['task_id']);
					}
				} else {
					$voiceUrl = $data['media_local_url'];
				}
				$html = <<<EOF
					<div id="jquery_jplayer_{$index}" class="jp-jplayer"></div>
                	<div onClick="JPlayer('{$index}','{$voiceUrl}');"  id="jp_container_{$index}" class="jp-audio">
                    <a href="javascript:;" class="jp-play" tabindex="1">播放语音</a>
                    <a href="javascript:;" class="jp-pause" style="display:none;" tabindex="1">播放中...</a>
               		</div>
EOF;
				break;
			case "video" :
				$html = '视频消息';
				break;

			case 'location' :
				$desc = $data['label'] ? '<br/>' . $data['label'] : '';
				$html = <<<EOF
					<div>
						<span>经度： {$data['location_y']}</span><br/><span>纬读： {$data['location_x']}</span>
						{$desc}
					</div>
EOF;
				break;
			case "music" :
				$musicUrl = @$data['music_url'];
				$thmubUrl = @$data['thumb_url'];
				$title = cutstr_dis(@$data['title'], 34);
				$desc = cutstr_dis(@$data['description'], 128);
				$html = <<<EOF
				<div class="his_music_img_con"><a href="{$musicUrl}" target="_blank"><img src="{$thmubUrl}" title="音乐消息" /></a></div>
				<div class="msg_his_text_con">
					<div class="msg_his_text_title"><a target='_blank' href="{$musicUrl}">{$title}</a></div>
					<a target='_blank' href="{$musicUrl}" class="msg_his_text_desc">{$desc}</a>
				</div>
EOF;
				break;
			case "template" : //模板消息
				if ($templateList) {
					$tempData = empty($data['data']) ? '' : unserialize($data['data']);
					//var_dump($templateList[$data['template_id']], $data['data']);exit;
					$more = '...<a class="more" type="text" href="javascript:;" task_id="' . $data['task_id'] . '"> 更多</a>';
					$html = self::parseTemplateData(@$templateList[$data['template_id']]['content'], $tempData);
					$html = cutstr_dis($html, 300, $more);
				}
				break;
		}
		return $html;
	}

	/**
	 * 这个是处理群发历史显示函数
	 * @param int $entId
	 * @param  array $data 内部字段名词对应数据库weixin_service2.wx_message_task
	 * @return string html string
	 */
	public static function dialogFormat($entId, $data, $index, $templateList = null)
	{
		$html = '';
		switch ($data['msg_type']) {
			case "text" :
				$more = '...<a class="more" type="text" href="javascript:;" dialog_id="' . $data['dialog_id'] . '"> 更多</a>';
				$html = cutstr_dis(@$data['content'], 200, $more);
				$html = WX_Face::parseFace($html);
				break;
			case "news" :
				$articles = @$data['articles'][0];
				if (! $articles)
					return $html;
				$dialogId = $data['dialog_id'];
				$url = @$articles['url'];
				$picurl = @$articles['picurl'];
				$title = cutstr_dis(@$articles['title'], 15);
				$desc = cutstr_dis(@$articles['description'], 45);
				$html = <<<EOF
				<div class="his_dialog_img_con">
					<a class="more" type="news" dialog_id="{$dialogId}" href="javascript:;" title="图文消息">
						<img src="{$picurl}" />
					</a>
				</div>
				<div class="his_dialog_text_con">
					<div class="msg_his_text_title"><a target='_blank' href="{$url}">{$title}</a></div>
					<a target='_blank' href="{$url}" class="msg_his_text_desc">{$desc}</a>
				</div>
EOF;
				break;
			case "image" :
				if (! @$data['media_local_url']) {
					$picUrl = parseImageUrl($data['media_url']);
				} else {
					$picUrl = $data['media_local_url'];
				}
				$html = '<p><img title="图片消息" class="his_mini_img" onclick="window.top.ligImg.showImg(this,\'' . $picUrl . '\');" src="' . $picUrl . '"/></p>';
				break;
			case "voice" :
				if (! @$data['media_local_url']) {
					$voiceUrl = getMediaUrlByDialogId($entId, $data['dialog_id']);
				} else {
					$voiceUrl = $data['media_local_url'];
				}
				$html = <<<EOF
					<div id="jquery_jplayer_{$index}" class="jp-jplayer"></div>
                	<div onClick="JPlayer('{$index}','{$voiceUrl}');"  id="jp_container_{$index}" class="jp-audio">
                    <a href="javascript:;" class="jp-play" tabindex="1">播放语音</a>
                    <a href="javascript:;" class="jp-pause" style="display:none;" tabindex="1">播放中...</a>
               		</div>
EOF;
				break;
			case "video" :
				$html = '视频消息';
				break;

			case 'location' :
				$desc = $data['label'] ? '<br/>' . $data['label'] : '';
				$html = <<<EOF
				<div>
					<span>经度： {$data['location_y']}</span><br/><span>纬读： {$data['location_x']}</span>
					{$desc}
				</div>
EOF;
				break;
			case "music" :
				$musicUrl = @$data['music_url'];
				$thmubUrl = @$data['thumb_url'];
				$title = cutstr_dis(@$data['title'], 15);
				$desc = cutstr_dis(@$data['description'], 45);
				$html = <<<EOF
				<div class="his_music_img_con"><a href="{$musicUrl}" target="_blank"><img title="音乐消息" src="{$thmubUrl}" /></a></div>
				<div class="his_dialog_text_con">
					<div class="msg_his_text_title"><a target='_blank' href="{$musicUrl}">{$title}</a></div>
					<a target='_blank' href="{$musicUrl}" class="msg_his_text_desc">{$desc}</a>
				</div>
EOF;
				break;
			case "template" : //模板消息
				if ($templateList) {
					$tempData = empty($data['data']) ? '' : unserialize($data['data']);
					//var_dump($templateList[$data['template_id']], $data['data']);exit;
					$more = '...<a class="more" type="text" href="javascript:;" dialog_id="' . $data['dialog_id'] . '"> 更多</a>';
					$html = self::parseTemplateData($templateList[$data['template_id']]['content'], $tempData);
					$html = cutstr_dis($html, 300, $more);
				}
				break;
		}
		return $html;
	}

	/**
	 * 将消息信息解析和html转义
	 * @param array $msg
	 * @param array $msgTypeArr 消息类型汉化数组
	 * @return Array
	 */
	public static function escape($msg, $msgTypeArr = null, $templateList = null)
	{
		if (! $msg || ! is_array($msg)) {
			return $msg;
		}
		$msgTypeArr or $msgTypeArr = self::$msgTypeArr;
		$msg['type_name'] = @$msgTypeArr[$msg['msg_type']];
		switch ($msg['msg_type']) {
			case "text" :
				$msg['content'] = htmlspecialchars(@$msg['content']);
				break;
			case "news" :
				if (isset($msg['articles']) && is_string($msg['articles'])) {
					$news = @unserialize($msg['articles']);
				} else {
					$news = @$msg['articles'];
				}

				$news_articles = array();
				if ($news && is_array($news)) {
					foreach ($news as $k => $oneNews) {
						array_push($news_articles, array (
								'title' => htmlspecialchars($oneNews['title']),
								'description' => htmlspecialchars($oneNews['description']),
								'url' => $oneNews['url'],
								'picurl' => $oneNews['picurl'],
								'text_url' => @$oneNews['text_url']
						));
					}
				}
				$msg['articles'] = $news_articles;
				break;
			case "music" :
				$msg['title'] = htmlspecialchars(@$msg['title']);
				$msg['description'] = htmlspecialchars(@$msg['description']);
				break;
			case "template" :
				if ($templateList) {
					$tempData = empty($msg['data']) ? '' : unserialize($msg['data']);
					$msg['content'] = self::parseTemplateData(@$templateList[$msg['template_id']]['content'], $tempData);
					$msg['content'] = htmlspecialchars($msg['content']);
				}
				break;
		}
		return $msg;
	}

	/**
	 * 将模板数据和模板合并成消息
	 * @param string $templateContent
	 * @param array $data
	 * @return string
	 */
	public static function parseTemplateData($templateContent, $data)
	{
		if (! $data || ! $templateContent || ! is_array($data)) {
			return '';
		}
		//提取模板内的动态数据标识符
		$tempDataIndex = array ();
		preg_match_all('/\{\{\w+\.DATA\}\}/', $templateContent, $tempDataIndex);
		$replace = array ();
		if (@$tempDataIndex[0]) {
			$replace = array_fill_keys($tempDataIndex[0], '');
		}

		foreach ($data as $dk => $dv) {
			$k = '{{' . $dk . '.DATA}}';
			$replace[$k] = $dv;
		}
		return str_replace(array_keys($replace), array_values($replace), $templateContent);
	}

	/**
	 * 生成欢迎词内容html
	 * @param array $data
	 * @param string $templateList
	 * @return string
	 */
	public static function genWelcomeContentHtml($msg)
	{
		$msgTypeArr = array (
				'text' => '文本',
				'news' => '图文',
				'music' => '音乐',
				'image' => '图片',
				'voice' => '语音',
				'video' => '视频'
		);
		$data = self::escape($msg, $msgTypeArr);

		return $data;
	}

	/**
	 * 生成地位位置推送数据列表
	 * @param int $entId
	 * @param arry $data
	 * @param unknown $index
	 * @param string $templateList
	 * @return Ambigous <string, unknown>
	 */
	public static function genLocationMsgList($entId, $msgList)
	{
		$result = array('jsonData'=>array(), 'list' => array());
		if (!$msgList || !is_array($msgList)) {
			return $result;
		}

		$msgTypeArr  = array(
				'text' => '文本',
				'news' => '图文',
				'music'=> '音乐',
				'voice'=> '语音',
				'video'=> '视频',
				'image'=> '图片',
				'location' => '地理位置'
		);

		foreach ($msgList as $index => $msg) {
			$escapData = self::escape($msg, $msgTypeArr);
			$escapData['html'] = self::genLocationMsgListHtml($entId, $escapData, $index);
			$result['list'][] = $escapData;
			$result['jsonData'][$msg['id']] = self::getViewData($msg['msg_type'], $escapData);
		}
		return $result;
	}

	/**
	 * 生成地理位置推送数据html
	 * @param int $entId
	 * @param array $data
	 * @param int $index
	 * @return string
	 */
	public static function genLocationMsgListHtml($entId, $data, $index)
	{
		$html = '';
		switch ($data['msg_type']) {
			case "text":
				$more = '...<a class="more" type="text" href="javascript:;" name="'.$data['id'].'"> 更多</a>';
				$html = cutstr_dis(@$data['content'], 200, $more);
				$html = WX_Face::parseFace($html);
				break;
			case "news":
				$articles = @$data['articles'][0];
				if ($articles) {
					$url = @$articles['text_url'] ? @$articles['text_url'] : @$articles['url'];
					$picurl = @$articles['picurl'];
					$title = cutstr_dis(@$articles['title'], 15);
					$desc = cutstr_dis(@$articles['description'], 45);
					$html = <<<EOF
					<div class="his_dialog_img_con">
						<a class="more" type="news" href="javascript:;" name="{$data['id']}" title="查看">
							<img src="{$picurl}" title="图文消息"/>
						</a>
					</div>
					<div class="his_dialog_text_con">
						<div class="msg_his_text_title"><a target='_blank' href="{$url}">{$title}</a></div>
						<a target='_blank' href="{$url}" class="msg_his_text_desc">{$desc}</a>
					</div>
EOF;
				}
				break;
			case "image":
				if (! @$data['media_local_url']) {
					$picUrl = parseImageUrl($data['media_url']);
				} else {
					$picUrl = $data['media_local_url'];
				}
				$html = '<p><img title="图片消息" class="his_mini_img" onclick="window.top.ligImg.showImg(this,\'' . $picUrl . '\');" src="' . $picUrl . '"/></p>';
				break;
			case "voice":
				$voiceUrl = '/media.php?url='.@$data['media_url'];
				$html = <<<EOF
					<div id="jquery_jplayer_{$index}" class="jp-jplayer"></div>
					<div onClick="JPlayer('{$index}','{$voiceUrl}');"  id="jp_container_{$index}" class="jp-audio">
					<a href="javascript:;" class="jp-play" tabindex="1">播放语音</a>
					<a href="javascript:;" class="jp-pause" style="display:none;" tabindex="1">播放中...</a>
					</div>
EOF;
				break;
			case "video":
				$html = '视频消息';
				break;
			case 'location':
				$desc = @$data['label'] ? '<br/>'. @$data['label'] : '';
				$location_y = @$data['location_y'];
				$location_x = @$data['location_x'];
				$html = <<<EOF
				<div>
					<span>经度： {$location_y}</span><br/><span>纬读： {$location_x}</span>
					{$desc}
				</div>
EOF;
				break;
			case "music":
				$title = cutstr_dis(@$data['title'], 34);
				$description = cutstr_dis(@$data['description'], 128);
				$musicUrl = @$data['music_url'];
				$thmubUrl = @$data['thumb_url'];
				$html = <<<EOF
				<div class="his_music_img_con"><a href="{$musicUrl}" target="_blank"><img title="音乐消息" src="{$thmubUrl}" /></a></div>
				<div class="his_dialog_text_con">
					<div class="msg_his_text_title"><a target='_blank' href="{$musicUrl}">{$title}</a></div>
					<a href="javascript:void(0);" class="msg_his_text_desc">{$description}</a>
				</div>
EOF;
				break;
		}
		return $html;
	}

	/**
	 * 生成地理位置插件内容html
	 * @param array $data
	 * @param string $templateList
	 * @return string
	 */
	public static function genLocationContentHtml($msg)
	{
		$msgTypeArr = array (
				'text' => '文本',
				'news' => '图文',
				'music' => '音乐',
				'image' => '图片',
				'voice' => '语音',
				'video' => '视频',
				'location' => '地理位置'
		);
		$data = self::escape($msg, $msgTypeArr);
		$html = '';
		switch ($data['msg_type']) {
			case 'text' :
				$html = $data['content'];
				break;
			case "news" :
				$news_data = $data['articles'];
				$news_count = count($news_data);
				$html = '<div class="TW_box" style="width: 320px;">';
				if ($news_count) {
					if ($news_count > 1) {
						$html .= '<div class="appTwb1">';
						$html .= '<div class="reveal news_first" style="background-image:url(\'' . $news_data[0]['picurl'] . '\')">';
						$html .= '<h5 class="tw_z"><a class="z_title" href="javascript:;">' . $news_data[0]['title'] . '</a></h5>';
						$html .= '</div></div>';
						$html .= '<div class="appTwb2">';
						for($i = 1; $i < $news_count; $i ++) {
							$html .= '<div class="tw_li">';
							$html .= '<a class="atext" href="javascript:;">' . $news_data[$i]['title'] . '</a>';
							$html .= '<img width="70" height="70" src="' . $news_data[$i]['picurl'] . '" />';
							$html .= '</div>';
						}
						$html .= '</div>';
					} else {
						$html .= '<div class="appTwb1">';
						$html .= '<h3 class="twh3"><a href="javascript:;">' . $news_data[0]['title'] . '</a></h3>';
						$html .= '<p class="twp">' . date('Y-m-d') . '</p>';
						$html .= '<div class="reveal news_first" style="background-image:url(\'' . $news_data[0]['picurl'] . '\')"></div>';
						$html .= '</div>';
						$html .= '<div class="appTwb2">';
						$html .= '<div class="tw_text">';
						$html .= '<p>' . $news_data[0]['description'] . '</p>';
						$html .= '</div></div>';
					}
				}
				$html .= '</div>';
				break;
			case "image" :
				$picUrl = @$data['media_url'];
				$html = <<<EOF
					<div class="TW_box" style="width: 320px;">
					<div class="appTwb1">
					<div style="height:auto;" class="reveal news_first">
					<img src="{$picUrl}" />
					</div>
					</div>
					</div>
EOF;
				break;
			case "voice" :
				$voiceUrl = '/media.php?url=' . @$data['media_url'];
				$html = <<<EOF
					<div class="TW_box" style="width: 320px;">
					<div class="appTwb1">
					<div class="dhLb he">
					<div class="cloud cloudText">
					<div class="cloudPannel">
					<div class="cloudBody" style="width:290px;">
					<div class="cloudContent">
					<div class="jp-jplayer" id="jquery_jplayer_0"></div>
					<div style="background-color: #B2CF73;width:270px;" class="jp-audio" id="jp_container_181" onclick="JPlayer('0','{$voiceUrl}')">
					<a tabindex="1" class="jp-play" href="javascript:;"></a>
					<a tabindex="1" style="display:none;" class="jp-pause" href="javascript:;"></a>
					</div></div></div>
					<div class="cloudArrow"></div>
					</div></div></div></div></div>
EOF;
				break;
			case "video" :
				$video_url = @$data['media_url'];
				$html = <<<EOF
					<div class="TW_box" style="width: 320px;">
					<div class="appTwb1" style="margin:10px;">
					<div style="position:relative;z-index: 100;" id="video_0">
					<div id="a244_176" style="color: rgb(255, 221, 0);">
					<object width="300" align="middle" height="250" name="ckplayer_a_0" id="ckplayer_a_0"
					codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=10,0,0,0"
					classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" pluginspage="http://www.macromedia.com/go/getflashplayer">
					<param value="always" name="allowScriptAccess" />
					<param value="false" name="allowFullScreen" />
					<param value="high" name="quality" />
					<param value="#000" name="bgcolor" />
					<param value="./Admin/Public_1/cj/ckplayer/ckplayer.swf?v=2.1" name="movie" />
					<param value="f={$video_url}&amp;c=0&amp;b=1" name="flashvars" />
					<embed width="300" align="middle" height="250" pluginspage="http://www.macromedia.com/go/getflashplayer"
					type="application/x-shockwave-flash" id="ckplayer_a_0" name="ckplayer_a_0" flashvars="f={$video_url}&amp;c=0&amp;b=1"
					src="./Admin/Public_1/cj/ckplayer/ckplayer.swf?v=2.1" bgcolor="#000" quality="high" allowfullscreen="false"
					allowscriptaccess="always" />
					</object>
					</div></div></div></div>
EOF;
				break;
			case "music" :
				$title = cutstr_dis(@$data['title'], 34);
				$description = cutstr_dis(@$data['description'], 128);
				$musicUrl = @$data['music_url'];
				$thmubUrl = @$data['thumb_url'];
				$html = <<<EOF
				<div class="TW_box" style="width: 320px;">
				<div class="appTwb1">
				<div class="con_Ivredit" style="overflow:hidden;">
				<div class="twp" style="height: 100px;width: 100px;float:left;overflow:hidden;">
				<img width="100" height="100" id="img" style="position:absolute;" src="{$thmubUrl}">
				<div style="position:absolute;left:10px;top:35px;"><div class="jp-jplayer" id="jquery_jplayer_0"></div>
				<div class="jp-audio" id="jp_container_0" onclick="JPlayer('0','{$musicUrl}');">
				<a tabindex="1" class="jp-play audioImgBarBtn audioPlayBtn" href="javascript:;" style="margin: 2px 0 0 30px"></a>
				<a tabindex="1" style="display:none;" class="jp-pause audioImgBarBtn audioStopBtn" href="javascript:;"style="margin: 2px 0 0 30px"></a>
				</div></div></div><div class="twp" style="float:left;width:180px; max-width:none;"><h3>
				<a href="javascript:void(0);">{$title}</a></h3><p>{$description}</p>
				</div></div></div></div>
EOF;
				break;
			case 'location':
				$desc = @$data['label'] ? '<br/>'. @$data['label'] : '';
				$location_y = @$data['location_y'];
				$location_x = @$data['location_x'];
				$html = <<<EOF
				<div>
					<span>经度： {$location_y}</span><br/><span>纬读： {$location_x}</span>
					{$desc}
				</div>
EOF;
		}
		return $html;
	}

	/**
	 * 生成关键词消息html
	 * @param array $msg
	 * @return string
	 */
	public static function genKeywordContentHtml($msg)
	{
		$msgTypeArr = array (
				'text' => '文本',
				'news' => '图文',
				'music' => '音乐',
				'image' => '图片',
				'voice' => '语音',
				'video' => '视频'
		);
		$data = self::escape($msg, $msgTypeArr);
		$html = '';

		switch ($data['msg_type']) {
			case 'text':
				$data['content'] = WX_Face::parseFace($data['content']);
				$html = <<<EOF
					<div id="message_{$data['id']}" class="TW_box">
						<div class="tw_edit">
							<div class="czx">
								<a onclick="editMessage({$data['id']})" href="javascript:;" class="edit"></a>
								<a onclick="deleteMessage({$data['id']})" href="javascript:;" class="del"></a>
							</div>
						</div>
						<div class="tw_aa"><a href="javascript:void(0);">{$data['content']}</a></div>
					</div>
EOF;
				break;
			case "news" :
				$news_data = $data['articles'];
				$news_count = count($news_data);
				$html = '<div id="message_'.$data['id'].'" class="TW_box" style="width: 330px;">';
				if ($news_count > 1) {
					$html .= '<div class="tw_edit">';
					$html .= '<div class="czx">';
					$html .= '<a class="del" onclick="deleteMessage(\''.$data['id'].'\')"href="javascript:;"></a>';
					$html .= '</div></div>';
					if ($news_count) {
						$html .= '<div class="appTwb1">';
						$html .= '<div class="reveal news_first" style="background-image:url(\'' . $news_data[0]['picurl'] . '\')">';
						$html .= '<h5 class="tw_z"><a class="z_title" href="javascript:;">' . $news_data[0]['title'] . '</a></h5>';
						$html .= '</div></div>';
						$html .= '<div class="appTwb2">';
						for($i = 1; $i < $news_count; $i ++) {
							$html .= '<div class="tw_li">';
							$html .= '<a class="atext" href="javascript:;">' . $news_data[$i]['title'] . '</a>';
							$html .= '<img width="70" height="70" src="' . $news_data[$i]['picurl'] . '" />';
							$html .= '</div>';
						}
					}
					$html .= '</div>';
				} else {
					$html .= '<div class="tw_edit">';
					$html .= '<div class="czx">';
					$html .= '<a class="del" onclick="deleteMessage(\''.$data['id'].'\')"href="javascript:;"></a>';
					$html .= '</div></div>';
					if ($news_count) {
						$html .= '<div class="appTwb1">';
						$html .= '<h3 class="twh3"><a href="javascript:;">' . $news_data[0]['title'] . '</a></h3>';
						$html .= '<p class="twp">' . date('Y-m-d') . '</p>';
						$html .= '<div class="reveal news_first" style="background-image:url(\'' . $news_data[0]['picurl'] . '\')"></div>';
						$html .= '</div>';
						$html .= '<div class="appTwb2">';
						$html .= '<div class="tw_text">';
						$html .= '<p>' . $news_data[0]['description'] . '</p>';
					}
					$html .= '</div></div>';
				}
				$html .= '</div>';
				break;
			case "music" :
				$title = cutstr_dis(@$data['title'], 34);
				$description = cutstr_dis(@$data['description'], 128);
				$musicUrl = @$data['music_url'];
				$thmubUrl = @$data['thumb_url'];
				$html = <<<EOF
				<div id="message_{$data['id']}" class="TW_box" style="width: 330px;">
				<div class="tw_edit">
				<div class="czx"><a class="del" onclick="deleteMessage({$data['id']})" href="javascript:;"></a></div>
				</div>
				<div class="appTwb1">
				<div class="con_Ivredit" style="overflow:hidden;">
				<div class="twp" style="height: 100px;width: 100px;float:left;overflow:hidden;">
				<img width="100" height="100" id="img" style="position:absolute;" src="{$thmubUrl}">
				<div style="position:absolute;left:10px;top:35px;"><div class="jp-jplayer" id="jquery_jplayer_0"></div>
				<div class="jp-audio" id="jp_container_0" onclick="JPlayer('0','{$musicUrl}');">
				<a tabindex="1" class="jp-play audioImgBarBtn audioPlayBtn" href="javascript:;" style="margin: 2px 0 0 30px"></a>
				<a tabindex="1" style="display:none;" class="jp-pause audioImgBarBtn audioStopBtn" href="javascript:;"style="margin: 2px 0 0 30px"></a>
				</div></div></div><div class="twp" style="float:left;width:180px; max-width:none;"><h3>
				<a href="javascript:void(0);">{$title}</a></h3><p>{$description}</p>
				</div></div></div></div>
EOF;
				break;
			case "image" :
				$picUrl = $data['media_url'];
				$html = <<<EOF
				<div id="message_{$data['id']}" class="TW_box" style="width: 330px;">
				<div class="tw_edit">
				<div class="czx"><a class="del" onclick="deleteMessage({$data['id']})" href="javascript:;"></a></div>
				</div>
				<div class="appTwb1">
				<h3 class="twh3">
				<a href="javascript:void(0);">{$data['title']}</a>
				</h3>
				<div style="height:auto;" class="reveal news_first">
				<img src="{$picUrl}" />
				</div>
				</div>
				</div>
EOF;
				break;
			case "voice" :
				$voiceUrl = '/media.php?url=' . @$data['media_url'];
				$html = <<<EOF
				<div id="message_{$data['id']}" class="TW_box" style="width: 330px;">
				<div class="tw_edit">
				<div class="czx"><a class="del" onclick="deleteMessage({$data['id']})" href="javascript:;"></a></div>
				</div>
				<div class="appTwb1">
				<h3 class="twh3">
				<a href="javascript:void(0);">{$data['title']}</a>
				</h3>
				<div class="dhLb he">
				<div class="cloud cloudText">
				<div class="cloudPannel">
				<div class="cloudBody" style="width:290px;">
				<div class="cloudContent">
				<div class="jp-jplayer" id="jquery_jplayer_0"></div>
				<div style="background-color: #B2CF73;width:270px;" class="jp-audio" id="jp_container_181" onclick="JPlayer('0','{$voiceUrl}')">
				<a tabindex="1" class="jp-play" href="javascript:;"></a>
				<a tabindex="1" style="display:none;" class="jp-pause" href="javascript:;"></a>
				</div></div></div>
				<div class="cloudArrow"></div>
				</div></div></div></div></div>
EOF;
				break;
			case "video" :
				$video_url = @$data['media_url'];
				$date = date('Y-m-d');
				$html = <<<EOF
				<div id="message_{$data['id']}" class="TW_box" style="width: 330px;">
				<div class="tw_edit">
				<div class="czx"><a class="del" onclick="deleteMessage({$data['id']})" href="javascript:;"></a></div>
				</div>
				<div class="appTwb1" style="margin:10px;">
				<h3>
				<a href="javascript:void(0);">{$data['title']}</a>
				</h3>
				<p style="margin-left:0px;" class="twp create_time">{$date}</p>
				<div style="position:relative;z-index: 100;" id="video_0">
				<div id="a244_176" style="color: rgb(255, 221, 0);">
				<object width="300" align="middle" height="250" name="ckplayer_a_0" id="ckplayer_a_0"
				codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=10,0,0,0"
				classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" pluginspage="http://www.macromedia.com/go/getflashplayer">
				<param value="always" name="allowScriptAccess" />
				<param value="false" name="allowFullScreen" />
				<param value="high" name="quality" />
				<param value="#000" name="bgcolor" />
				<param value="./Admin/Public_1/cj/ckplayer/ckplayer.swf?v=2.1" name="movie" />
				<param value="f={$video_url}&amp;c=0&amp;b=1" name="flashvars" />
				<embed width="300" align="middle" height="250" pluginspage="http://www.macromedia.com/go/getflashplayer"
				type="application/x-shockwave-flash" id="ckplayer_a_0" name="ckplayer_a_0" flashvars="f={$video_url}&amp;c=0&amp;b=1"
				src="./Admin/Public_1/cj/ckplayer/ckplayer.swf?v=2.1" bgcolor="#000" quality="high" allowfullscreen="false"
				allowscriptaccess="always" />
				</object>
				</div></div></div>
				<div class="appTwb2">
				<div style="padding-left:10px;min-height: 50px;">
				<p>{$data['description']}</p>
				</div>
				</div>
				</div>
EOF;
				break;
		}
		return $html;
	}

	/**
	 * 生成自定义菜单内容html
	 * @param array $data
	 * @param string $templateList
	 * @return string
	 */
	public static function genCustomMenuContentHtml($msg)
	{
		$msgTypeArr = array (
				'text' => '文本',
				'news' => '图文',
				'music' => '音乐',
				'image' => '图片',
				'voice' => '语音',
				'video' => '视频'
		);
		$data = self::escape($msg, $msgTypeArr);

		return $data;
	}

	/**
	 * 生成自定义菜单推送数据列表
	 * @param int $entId
	 * @param arry $data
	 * @param unknown $index
	 * @param string $templateList
	 * @return Ambigous <string, unknown>
	 */
	public static function genCustomMenuMsgList($entId, $msgList)
	{
		$result = array('jsonData'=>array(), 'list' => array());
		if (!$msgList || !is_array($msgList)) {
			return $result;
		}
		foreach ($msgList as $index => $msg) {
			switch ($msg['type']) {
				case 1 :
					$escapData = self::escape($msg);
					$escapData['html'] = self::genCustomMenuMsgListHtml($entId, $escapData, $index);
					break;
				case 2 :
					$msg['msg_type'] = '';
					$msg['type_name'] = '动态获取';
					$msg['html'] = $msg['url'];
					$escapData = $msg;
					break;
				case 3 :
					$msg['msg_type'] = '';
					$msg['type_name'] = '访问网页';
					$msg['html'] = $msg['url'];
					$escapData = $msg;
					break;
			}
			$result['list'][] = $escapData;
			$result['jsonData'][$msg['id']] = self::getViewData($msg['msg_type'], $escapData);
		}
		return $result;
	}

	/**
	 * 生成地理位置推送数据html
	 * @param int $entId
	 * @param array $data
	 * @param int $index
	 * @return string
	 */
	public static function genCustomMenuMsgListHtml($entId, $data, $index)
	{
		$html = '';
		switch ($data['msg_type']) {
			case "text":
				$more = '...<a class="more" type="text" href="javascript:;" name="'.$data['id'].'"> 更多</a>';
				$html = cutstr_dis(@$data['content'], 200, $more);
				$html = WX_Face::parseFace($html);
				break;
			case "news":
				$articles = @$data['articles'][0];
				if ($articles) {
					$url = @$articles['text_url'] ? @$articles['text_url'] : @$articles['url'];
					$picurl = @$articles['picurl'];
					$title = cutstr_dis(@$articles['title'], 15);
					$desc = cutstr_dis(@$articles['description'], 45);
					$html = <<<EOF
					<div class="his_dialog_img_con">
						<a class="more" type="news" href="javascript:;" name="{$data['id']}" title="查看">
							<img src="{$picurl}"/>
						</a>
					</div>
					<div class="his_dialog_text_con">
						<div class="msg_his_text_title"><a target='_blank' href="{$url}">{$title}</a></div>
						<a target='_blank' href="{$url}" class="msg_his_text_desc">{$desc}</a>
					</div>
EOF;
				}
				break;
			case "image":
				if (! @$data['media_local_url']) {
					$picUrl = parseImageUrl($data['media_url']);
				} else {
					$picUrl = $data['media_local_url'];
				}
				$html = '<p><img class="his_mini_img" onclick="window.top.ligImg.showImg(this,\'' . $picUrl . '\');" src="' . $picUrl . '"/></p>';
				break;
			case "voice":
				$voiceUrl = '/media.php?url='.@$data['media_url'];
				$html = <<<EOF
					<div id="jquery_jplayer_{$index}" class="jp-jplayer"></div>
					<div onClick="JPlayer('{$index}','{$voiceUrl}');"  id="jp_container_{$index}" class="jp-audio">
					<a href="javascript:;" class="jp-play" tabindex="1">播放语音</a>
					<a href="javascript:;" class="jp-pause" style="display:none;" tabindex="1">播放中...</a>
					</div>
EOF;
				break;
			case "video":
				$html = '视频消息';
				break;
			case "music":
				$title = cutstr_dis($data['title'], 34);
				$description = cutstr_dis($data['description'], 128);
				$musicUrl = $data['music_url'];
				$thmubUrl = $data['thumb_url'];
				$html = <<<EOF
				<div class="his_music_img_con"><a href="{$musicUrl}" target="_blank"><img src="{$thmubUrl}" /></a></div>
				<div class="his_dialog_text_con">
					<div class="msg_his_text_title"><a target='_blank' href="{$musicUrl}">{$title}</a></div>
					<a href="javascript:void(0);" class="msg_his_text_desc">{$description}</a>
				</div>
EOF;
				break;
		}
		return $html;
	}

	/**
	 * 生成FAQ素材管理列表
	 * @param arry $msgList
	 * @return array
	 */
	public static function genFaqMsgList($msgList)
	{
		$result = array('jsonData'=>array(), 'list' => array());
		if (!$msgList || !is_array($msgList)) {
			return $result;
		}
		foreach ($msgList as $index => $msg) {
			$escapData = self::escape($msg);
			$escapData['html'] = self::genFaqMsgListHtml($escapData, $index);

			$result['list'][] = $escapData;
			$result['jsonData'][$msg['faq_id']] = self::getViewData($msg['msg_type'], $escapData);
		}
		return $result;
	}

	/**
	 * 生成FAQ素材管理html
	 * @param array $data
	 * @param int $index
	 * @return string
	 */
	public static function genFaqMsgListHtml($data, $index)
	{
		$html = '';
		switch ($data['msg_type']) {
			case "text":
				$more = '...<a class="more" type="text" href="javascript:;" name="'.$data['faq_id'].'"> 更多</a>';
				$html = cutstr_dis(@$data['content'], 200, $more);
				$html = WX_Face::parseFace($html);
				break;
			case "news":
				$articles = @$data['articles'][0];
				if ($articles) {
					$url = @$articles['text_url'] ? @$articles['text_url'] : @$articles['url'];
					$picurl = @$articles['picurl'];
					$title = cutstr_dis(@$articles['title'], 15);
					$desc = cutstr_dis(@$articles['description'], 45);
					$html = <<<EOF
					<div class="his_dialog_img_con" >
						<a class="more" type="news" href="javascript:;" name="{$data['faq_id']}" title="查看">
							<img src="{$picurl}" title="图文消息"/>
						</a>
					</div>
					<div class="his_dialog_text_con" style="width:150px;">
						<div class="msg_his_text_title"><a target='_blank' href="{$url}">{$title}</a></div>
						<a target='_blank' href="{$url}" class="msg_his_text_desc">{$desc}</a>
					</div>
EOF;
				}
				break;
			case "image":
				if (! @$data['media_local_url']) {
					$picUrl = parseImageUrl($data['media_url']);
				} else {
					$picUrl = $data['media_local_url'];
				}
				$html = '<p><img title="图片消息" class="his_mini_img" onclick="window.top.ligImg.showImg(this,\'' . $picUrl . '\');" src="' . $picUrl . '"/></p>';
				break;
			case "voice":
				$voiceUrl = '/media.php?url='.@$data['media_url'];
				$html = <<<EOF
					<div id="jquery_jplayer_{$index}" class="jp-jplayer"></div>
					<div onClick="JPlayer('{$index}','{$voiceUrl}');"  id="jp_container_{$index}" class="jp-audio">
					<a href="javascript:;" class="jp-play" tabindex="1">播放语音</a>
					<a href="javascript:;" class="jp-pause" style="display:none;" tabindex="1">播放中...</a>
					</div>
EOF;
				break;
			case "video":
				$html = '视频消息';
				break;
			case "music":
				$title = cutstr_dis(@$data['title'], 34);
				$description = cutstr_dis(@$data['description'], 128);
				$musicUrl = @$data['music_url'];
				$thmubUrl = @$data['thumb_url'];
				$html = <<<EOF
				<div class="his_music_img_con"><a href="{$musicUrl}" target="_blank"><img title="音乐消息" src="{$thmubUrl}" /></a></div>
				<div class="his_dialog_text_con">
					<div class="msg_his_text_title"><a target='_blank' href="{$musicUrl}">{$title}</a></div>
					<a href="javascript:void(0);" class="msg_his_text_desc">{$description}</a>
				</div>
EOF;
				break;
		}
		return $html;
	}
}