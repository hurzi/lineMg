<?php
//require_once "/../WxPay.Api.php";

/**
 * 
 * 刷卡支付实现类
 * @author widyhu
 *
 */
class NativePay
{
	/**
	 * 
	 * 生成扫描支付URL,模式一
	 * @param BizPayUrlInput $bizUrlInfo
	 */
	public function GetPrePayUrl($biz)
	{
		
		
		$values = WxpayApi::bizpayurl($biz);
//                Logger::debug('class:NativePay method:GetPrePayUrl $value:',$values);
		$url = "weixin://wxpay/bizpayurl?" . $this->ToUrlParams($values);
//                 Logger::debug('class:NativePay method:GetPrePayUrl $url:',$url);
		return $url;
	}
	
	/**
	 * 
	 * 参数数组转换为url参数
	 * @param array $urlObj
	 */
	private function ToUrlParams($urlObj)
	{
		$buff = "";
		foreach ($urlObj as $k => $v)
		{
			$buff .= $k . "=" . $v . "&";
		}
		
		$buff = trim($buff, "&");
		return $buff;
	}
	
	/**
	 * 
	 * 生成直接支付url，支付url有效期为2小时,模式二
	 * @param UnifiedOrderInput $input
	 */
	public function GetPayUrl($input,$payKey)
	{
		if($input->GetTrade_type() == "NATIVE")
		{
			$result = WxPayApi::unifiedOrder($input,$payKey);
			return $result;
		}
	}
}