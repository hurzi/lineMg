<?php
/**
 * 这里是带参数二维码通知企业脚本类文件
 */
define("APP_PLATE", 'WXApp');

set_time_limit(0);

include_once dirname(__FILE__) . '/../../Init.php';

$noticeUrl = $argv[1];
$postData = json_decode($argv[2], true);

$qrcParamFilterNotice = new QrcParamFilterNotice();
$qrcParamFilterNotice ->run($noticeUrl, $postData);

class QrcParamFilterNotice
{
	public $_httpCode;
	public $_httpInfo;
	//http error code
	public $_httpErrorCode = 0;
	//http error
	public $_httpError = '';

	const POST = 'POST';

	public function run($noticeUrl, $postData)
	{
		if (! $noticeUrl) {
			Logger::debug('QrcParamNotice_error : noticeUrl参数为空');
			die();
		}

		if (! $postData || ! is_array($postData)) {
			Logger::debug('QrcParamNotice_error: postData参数为空或不是数组', $postData);
			die();
		}
		$respons = $this->_curl($noticeUrl, 'POST', array(), $postData);

		if ($this->_httpCode != 200 || $this->_httpErrorCode != 0) {
			Logger::error('QrcParamNotice_error: http_error_code:'.$this->_httpCode.':'.$this->_httpErrorCode
				. '; http_error: '.$this->_httpError);
		} else {
			Logger::info("QrcParamNotice : http_code:".$this->_httpCode." ;url:".$noticeUrl." ; params: ".json_encode($postData)." ; response: ".$respons);
		}
	}

	/**
	 * curl 请求
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
		if ($method == 'GET') {
			$url = $url . (strpos($url, '?') ? '&' : '?') . (is_array($postfields) ? http_build_query($postfields) : $postfields);
		}
		if (self::POST == $method) {
			curl_setopt($curl, CURLOPT_POST, TRUE);
			if (!empty($postfields)) {
				curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postfields));
			}
		}
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers );
		curl_setopt($curl, CURLINFO_HEADER_OUT, TRUE );

		$response = curl_exec($curl);

		$this->_httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$this->_httpInfo = curl_getinfo($curl);
		$this->_httpErrorCode = curl_errno($curl);
		$this->_httpError = curl_error($curl);
		curl_close($curl);

		return $response;
	}
}
