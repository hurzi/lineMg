<?php
/**
 * 这里是下载媒体和媒体格式转换的脚本
 */
define("APP_PLATE", 'WXApp');

set_time_limit(0);

include_once dirname(__FILE__) . '/../../Init.php';
//媒体转换格式服务器地址
//define('MEDIA_REFORMAT_SERVER_HOST', 'http://192.168.5.2:8084/winxinAudioConvert');
define('MEDIA_REFORMAT_SERVER_HOST', 'http://192.168.5.222:8087/tool2');
//远程保存媒体文件ULR
define('REMOTE_MEDIA_SAVE_URL', WEB_PATH.'Common/wx_media_upload.php');


$msgId = @$_SERVER ["argv"] [1];//msg_id 消息ID（必须项）
$msgType = @$_SERVER ["argv"] [2];//msg_type 消息类型 image/voice/video（必须项）
$mediaUrl = @$_SERVER ["argv"] [3];//media_url 媒体URL（必须项）
$mediaId = @$_SERVER ["argv"] [4];//media_id 媒体ID（必须项）
$thumbMediaUrl = @$_SERVER ["argv"] [5];//thumb_media_url 视频缩略图URL （可选项，但类型为video时，为必须项）
$thumbMediaId = @$_SERVER ["argv"] [6];//thumb_media_id 视频缩略图ID （可选项，但类型为video时，为必须项）

$mediaDownload = new MediaDownload($msgId, $msgType, $mediaUrl, $mediaId, $thumbMediaUrl, $thumbMediaId);
$mediaDownload->run();

/**
 * 消息媒体下载类
 */
class MediaDownload
{
	/**
	 * 消息ID
	 * @var string
	 */
	private $_msgId;
	/**
	 * 消息类型
	 * @var string 3种类型 image/voice/video/
	 */
	private $_msgType;
	/**
	 * 媒体url
	 * @var string
	 */
	private $_mediaUrl;
	/**
	 * 缩略图url
	 * @var string
	 */
	private $_thumbMediaUrl;
	/**
	 * 媒体ID
	 * @var string
	 */
	private $_mediaId;
	/**
	 * 缩略图ID
	 * @var string
	 */
	private $_thumbMediaId;
	/**
	 * http request code
	 * @var int
	 */
	private $_httpCode;
	/**
	 * http request result
	 * @var array
	 */
	private $_httpInfo;
	/**
	 * 数据库类
	 * @var DB
	 */
	private $_db;

	const GET  = 'GET';
	const POST = 'POST';

	const FILE_UPLOAD_NAME = 'up_file';

	/**
	 * boundary of multipart
	 * @ignore
	 */
	private $_boundary = '';

	private $_thumbDownload = false;

	public function __construct($msgId, $msgType, $mediaUrl, $mediaId, $thumbMediaUrl = null, $thumbMediaId = null)
	{
		$this->_msgId = trim($msgId);
		$this->_msgType = trim($msgType);
		$this->_mediaUrl = trim($mediaUrl);
		$this->_mediaId = trim($mediaId);
		$this->_thumbMediaUrl = trim($thumbMediaUrl);
		$this->_thumbMediaId = trim($thumbMediaId);
	}

	/**
	 * 入口方法
	 */
	public function run()
	{
		//解析请求参数
		if (! $this->_checkParams()) {
			return false;
		}
		//下载媒体附件
		$mediaUrls = $this->_download();
		//保存数据到数据库
		return $this->_add($mediaUrls);
	}

	/**
	 * 解析请求参数
	 */
	private function _checkParams()
	{

		if (!$this->_msgId || !$this->_msgType || !$this->_mediaUrl) {
			Logger::error('MediaDownload->_parseRequestParams(): 缺少必要的参数', @$_SERVER ["argv"]);
			return false;
		}

		if ('image' != $this->_msgType && 'voice' != $this->_msgType && 'video' != $this->_msgType) {
			Logger::error('MediaDownload->_parseRequestParams():  媒体类型无效', @$_SERVER ["argv"]);
			return false;
		}

		if ('voice' == $this->_msgType || 'video' != $this->_msgType) {
			if (! $this->_mediaUrl) {
				Logger::error('MediaDownload->_parseRequestParams(): 缺少必要的参数(media_url)', @$_SERVER ["argv"]);
				return false;
			}
		}
		return true;
	}

	/**
	 * 附件下载
	 * @return array|false $mediaUrls array('media_url' => 媒体URL, 'thumb_media_url' => 缩略图URL)
	 */
	private function _download()
	{
		switch ($this->_msgType) {
			/* case 'image':
				$mediaUrls = $this->_imageDownload();
			break; */
			case 'voice':
				$mediaUrls = $this->_voiceDownload();
				break;
			case 'video':
				$mediaUrls = $this->_videoDownload();
				break;
			default:
				return false;
		}

		return $mediaUrls;
	}

	/**
	 * 数据保存到数据库中
	 * @param array|false $mediaUrls 媒体url
	 * @return bool
	 */
	private function _add($mediaUrls)
	{
		if (! $mediaUrls || (! @$mediaUrls['media_url'] && ! @$mediaUrls['media_orig_url'])) {
			return false;
		}

		$this->_db = Factory::getDb();

		$data = array(
				'msg_id' => $this->_msgId,
				'media_type' => $this->_msgType,
				'media_local_url' => faddslashes($mediaUrls['media_url']),
				'media_local_orig_url' => faddslashes($mediaUrls['media_orig_url']),
				'thumb_media_local_url' => faddslashes(@$mediaUrls['thumb_media_url']),
				'thumb_media_local_orig_url' => faddslashes(@$mediaUrls['thumb_media_orig_url']),
				'local_media_id' => $this->_mediaId,
				'local_thumb_media_id' => $this->_thumbMediaId,
				'create_time' => date('Y-m-d H:i:s'),
		);
		try {
			$result = $this->_db->insert('wx_message_media_local', $data, true);
			if ($result === false) {
				Logger::error('MediaDownload->_add() error: insert data fail; error:'.$this->_db->last_error.'; sql: '. $this->_db->getLastSql());
			}
		} catch (Exception $e) {
			Logger::error('MediaDownload->_add() error: '.$e->getMessage().'; sql: '. $this->_db->getLastSql());
			return false;
		}

		return $result;
	}

	/**
	 * 图片下载
	 * @return string 图片url
	 */
	private function _imageDownload()
	{
		$url = urlencode($this->_mediaUrl);

		$imageUrls = $this->_downloadOperate($url, 'image', 'jpg', $this->_getHeaders());

		return array('media_url' => $imageUrls['formatUrl'], 'media_orig_url' => $imageUrls['origUrl']);
	}

	/**
	 * 音频下载
	 * @return string|false
	 */
	private function _voiceDownload()
	{
		$url = $this->_getReformatUrl('voice');

		$voiceUrls = $this->_downloadOperate($url, 'voice', 'mp3');

		return array('media_url' => $voiceUrls['formatUrl'], 'media_orig_url' => $voiceUrls['origUrl']);
	}

	/**
	 * 视频下载
	 * @return string|false
	 */
	private function _videoDownload()
	{
		$url = $this->_getReformatUrl('video');

		$videoUrls = $this->_downloadOperate($url, 'video', 'mp4');
		$thumbUrls = array();

		if ($videoUrls && ($videoUrls['formatUrl'] || $videoUrls['origUrl'])) {
			$thumbUrls = $this->_thumbDownload();
		}

		return array(
				'media_url' => $videoUrls['formatUrl'],
				'media_orig_url' => $videoUrls['origUrl'],
				'thumb_media_url' => @$thumbUrls['media_url'],
				'thumb_media_orig_url' => @$thumbUrls['media_orig_url']
		);
	}

	/**
	 * 下载缩略图
	 * @return array
	 */
	private function _thumbDownload()
	{
		$url = $this->_thumbMediaUrl;
		$this->_thumbDownload = true;

		$thumbUrls = $this->_downloadOperate($url, 'thumb', 'jpg', $this->_getHeaders());

		return array('media_url' => $thumbUrls['formatUrl'], 'media_orig_url' => $thumbUrls['origUrl']);
	}

	/**
	 * 获取headers
	 * @return array
	 */
	private function _getHeaders()
	{
		$headers = array (
				'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
				'Accept-Encoding' => 'Accept-Encoding',
				'Accept-Language' => 'Accept-Language',
				'Connection' => 'keep-alive',
				'User-Agent' => 'Mozilla/5.0 (Windows NT 5.1; rv:17.0) Gecko/20100101 Firefox/17.0'
		);
		return $headers;
	}

	/**
	 * 下载具体操作
	 * @param string $url
	 * @param string $type
	 * @param string $suffix
	 * @param array $headers
	 * @return array
	 */
	private function _downloadOperate($url, $type, $suffix, $headers = array())
	{
		if ($this->_thumbDownload) {
			$fileUrl = $this->_thumbMediaUrl;
		} else {
			$fileUrl = $this->_mediaUrl;
		}

		$formatFileUrl = '';
		$origFileUrl = '';
		$mediaUrls = array(
				'formatUrl' => $formatFileUrl,
				'origUrl' => $origFileUrl
		);

		$methodName = '_' . $type . 'Download';

		//图片只处理带后缀名一次就可以了
		if ('jpg' != $suffix) {
			$origFileResource = $this->_http($fileUrl, self::GET);
			$orgJson = @json_decode($origFileResource, true);
			if ($orgJson && @$orgJson['errcode']) {
				Logger::error('MediaDownload->_downloadOperate() '.$methodName.' response error, url:  '.$fileUrl, $origFileResource);
				return $mediaUrls;
			}
			$origFileUrl = $this->_remoteSaveMedia($origFileResource);
		}

		$response = $this->_http($url, self::GET, $headers);
		if (200 != $this->_httpCode) {
			Logger::error('MediaDownload->_downloadOperate()' . $methodName . ' 请求失败, code ' . $this->_httpCode, $this->_httpInfo);
			//return false;
		} else {
			$formatFileResource = $response;
			$formatFileUrl = $this->_remoteSaveMedia($formatFileResource, $suffix);
		}

		$mediaUrls = array(
				'formatUrl' => $formatFileUrl,
				'origUrl' => $origFileUrl
		);
		return $mediaUrls;
	}

	/**
	 * 远程保存媒体附件
	 * @param resource $resource 文件资源
	 * @param stirng $suffix 文件后缀名
	 * @return string $fileUrl
	 */
	private function _remoteSaveMedia($resource, $suffix = '')
	{
		$url = REMOTE_MEDIA_SAVE_URL;
		$params['suffix'] = $suffix;
		$params[self::FILE_UPLOAD_NAME] = $resource;

		$result = $this->_http($url, self::POST, array('Expect:'), $params, true);

		if (200 != $this->_httpCode) {
			Logger::error('_remoteSaveMedia(): 请求失败, code ' . $this->_httpCode, $this->_httpInfo);
			return false;
		}
		$result = json_decode($result, true);
		if ($result['error'] != 0) {
			Logger::error('_remoteSaveMedia(): 请求失败, error ' . $result['error'], $result['msg']);
			return false;
		}
		return $result['file'];
	}

	/**
	 * 获取媒体转格式url
	 * @param string $type
	 * @return string $url
	 */
	private function _getReformatUrl($type)
	{
		if ('voice' == $type) {
			$path = 'audio';
			$format = 'mp3';
		} else if ('video' == $type) {
			$path = 'VideoServlet';
			$format = 'mp4';
		}

		$url = MEDIA_REFORMAT_SERVER_HOST . '/' . $path . '?reqResFormat=' . $format . '&reqResURL='. urlencode($this->_mediaUrl);
		//$url = MEDIA_REFORMAT_SERVER_HOST . '?reqResFormat=' . $format . '&reqResURL='. urlencode($this->_mediaUrl);
		//Logger::error('_getReformatUrl(): debug: ' .$url);
		return $url;
	}

	/**
	 * Make an HTTP request
	 * @param string $url
	 * @param string $method
	 * @param array  $headers
	 * @param string $postfields
	 * @param bool $multi
	 * @return string results
	 */
	private function _http($url, $method, $headers = array(), $params = null, $multi = false)
	{
		if ($this->_checkCurl()) {
			$body = '';
			if (self::GET == $method) {
				$url = $url . (strpos($url, '?') ? '&' : '?') . (is_array($params) ? http_build_query($params) : $params);
			} else {
				if (! $multi) {
					$body = $params;
				} else {
					$body = $this->_buildHttpQueryMulti($params);
					$headers[] = "Content-Type: multipart/form-data; boundary=" . $this->_boundary;
				}
			}
			return $this->_curl($url, $method, $headers, $body);
		}else{
			return $this->_socket($url, $params);
		}
	}

	/**
	 * @ignore
	 */
	private function _buildHttpQueryMulti($params)
	{
		if (!$params) return '';

		uksort($params, 'strcmp');

		$pairs = array();

		$this->_boundary = $boundary = uniqid('----------');

		$MPboundary = '--'.$boundary;
		$endMPboundary = $MPboundary. '--';
		$multipartbody = '';

		foreach ($params as $param => $value) {
			if( self::FILE_UPLOAD_NAME == $param && @$value{0} == '@' ) {

				$url = ltrim( $value, '@' );
				$content = file_get_contents( $url );
				$array = explode( '?', basename( $url ) );
				$filename = $array[0];
				$multipartbody .= $MPboundary . "\r\n";
				$multipartbody .= 'Content-Disposition: form-data; name="' . $param . '"; filename="' . $filename . '"'. "\r\n";
				$multipartbody .= 'Content-Type: application/octet-stream\r\n\r\n';
				$multipartbody .= $content. "\r\n";
			} else {
				$multipartbody .= $MPboundary . "\r\n";
				$multipartbody .= 'Content-Disposition: form-data; name="' . $param . "\"\r\n\r\n";
				$multipartbody .= $value."\r\n";
			}
		}
		$multipartbody .= $endMPboundary;
		return $multipartbody;
	}

	/**
	 * @name  _socket TODO:未完成
	 */
	private function _socket($url)
	{
		$url_info = parse_url(urldecode($url));
		$url_host = $url_info['host'];
		$url_port = isset($url_info['port']) ? $url_info['port'] : 80;
		$errorn = 0;
		$error = '';

		$handle = fsockopen($url_host, $url_port, $errorn, $error, 30);
		if (! $handle) {
			Logger::error("Fail to open socket handle: host: {$url_host}, port: {$url_port}");
			return false;
		}

		$post_str = '';

		$content_length = strlen($post_str);
		$post_header = "POST $url HTTP/1.1\r\n";
		$post_header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$post_header .= "User-Agent: Mozilla/5.0 (Windows NT 5.1; rv:17.0) Gecko/20100101 Firefox/17.0\r\n";
		$post_header .= "Host: " . $url_host . "\r\n";
		$post_header .= "Content-Length: " . $content_length . "\r\n";
		$post_header .= "Connection: close\r\n\r\n";
		$post_header .= $post_str . "\r\n\r\n";
		if (! fwrite($handle, $post_header)) {
			//TODO log
			//trigger_error("Fail to write socket");
			Logger::error("Fail to write socket");
			fclose($handle);
			return false;
		}

		$response = '';
		while ( ! feof($handle) ) {
			$input = fread($handle, 1024);
			if ($input === false) {
				break; // Error or timeout.
			}
			$response .= $input;
		}
		fclose($handle);

		if (! $response) {
			Logger::error("Empty response, return");
			return $response;
		}
		//解析response
		if (strrpos($response, 'Transfer-Encoding: chunked')) {
			$info = explode("\r\n\r\n", $response);
			$res = explode("\r\n", $info[1]);
			$t = array_slice($res, 1, - 1);
			$returnInfo = implode('', $t);
		} else {
			$response = explode("\r\n\r\n", $response);
			$returnInfo = $response[1];
		}
		//转成utf-8编码
		$response = iconv("utf-8", "utf-8//ignore", $returnInfo);
		return $response;
	}

	/**
	 * @name 验证是否开启Curl扩展
	 * @return bool
	 */
	private function _checkCurl()
	{
		if ( ! function_exists( 'curl_init' ) || ! function_exists( 'curl_exec' ) ) {
			return false;
		}
		return true;
	}

	/**
	 * @ name curl
	 */
	private function _curl($url, $method = 'POST', $headers = array(), $postfields = NULL)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_HEADER, false);

		if (self::POST == $method) {
			curl_setopt($curl, CURLOPT_POST, TRUE);
			if (!empty($postfields)) {
				curl_setopt($curl, CURLOPT_POSTFIELDS, $postfields);
			}
		}

		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers );
		curl_setopt($curl, CURLINFO_HEADER_OUT, TRUE );

		$response = curl_exec($curl);

		$this->_httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$this->_httpInfo = curl_getinfo($curl);
		curl_close($curl);

		return $response;
	}

	public function __destruct()
	{

	}
}