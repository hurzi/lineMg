<?php
/**
 * 这里是生成5秒回复接口消息消息模板类文件
 */
class MessageTemplate
{
	/**
	 * 获取下行消息XML
	 * @param string $developerWechat
	 * @param WX_Message_Body $messageBody
	 * @return string
	 */
	public static function get($developerWechat, $messageBody)
	{
		$xml = '';
		if ($messageBody && $developerWechat && isset($messageBody->msgType)) {
			$date = strtotime(date('Y-m-d H:i:s'));
			switch ($messageBody->msgType) {
				case 'text' :
					$xml = <<<EOF
"<xml>
<ToUserName><![CDATA[{$messageBody->toUser}]]></ToUserName>
<FromUserName><![CDATA[{$developerWechat}]]></FromUserName>
<CreateTime>{$date}</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[{$messageBody->content}]]></Content>
</xml>";
EOF;
					break;
				case 'music' :
					$xml = <<<EOF
"<xml>
<ToUserName><![CDATA[{$messageBody->toUser}]]></ToUserName>
<FromUserName><![CDATA[{$developerWechat}]]></FromUserName>
<CreateTime>{$date}</CreateTime>
<MsgType><![CDATA[music]]></MsgType>
<Music>
<Title><![CDATA[{$messageBody->title}]]></Title>
<Description><![CDATA[{$messageBody->description}]]></Description>
<MusicUrl><![CDATA[{$messageBody->musicUrl}]]></MusicUrl>
<HQMusicUrl><![CDATA[{$messageBody->hqMusicUrl}]]></HQMusicUrl>
<ThumbMediaId><![CDATA[{$messageBody->thumbMediaId}]]></ThumbMediaId>
</Music>
</xml>";
EOF;
					break;
				case 'news' :
					$count = count($messageBody->articles);
					$xml = <<<EOF
"<xml>
<ToUserName><![CDATA[{$messageBody->toUser}]]></ToUserName>
<FromUserName><![CDATA[{$developerWechat}]]></FromUserName>
<CreateTime>{$date}</CreateTime>
<MsgType><![CDATA[news]]></MsgType>
<ArticleCount>{$count}</ArticleCount>
<Articles>";
EOF;
					if ($messageBody->articles) {
						foreach ($messageBody->articles as $article) {
							$xml .= <<<EOF
"<item>
<Title><![CDATA[{$article['title']}]]></Title>
<Description><![CDATA[{$article['description']}]]></Description>
<PicUrl><![CDATA[{$article['picurl']}]]></PicUrl>
<Url><![CDATA[{$article['url']}]]></Url>
</item>";
EOF;
						}
					}
					$xml .= <<<EOF
"</Articles>
</xml> ";
EOF;
					break;
				default :
					$xml = <<<EOF
"<xml>
<ToUserName><![CDATA[{$messageBody->toUser}]]></ToUserName>
<FromUserName><![CDATA[{$developerWechat}]]></FromUserName>
<CreateTime>{$date}</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[this is a default value ! please check]]></Content>
</xml>";
EOF;
					break;
			}
		}
		return $xml;
	}
}