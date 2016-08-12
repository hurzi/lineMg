<?php
//require_once "WxPay.Exception.php";
//require_once "WxPay.Config.php";
//require_once "WxPay.Data.php";

/**
 * 
 * 接口访问类，包含所有微信支付API列表的封装，类中方法为static方法，
 * 每个接口有默认超时时间（除提交被扫支付为10s，上报超时时间为1s外，其他均为6s）
 * @author widyhu
 *
 */
class WxPayApi
{
	/**
	 * 
	 * 统一下单，
	 * @param WxPayUnifiedOrder $inputObj
	 * @param int $timeOut
         * @param string $payKey 支付秘钥
	 * @throws WxPayException
	 * @return 成功时返回，其他抛异常
	 */
	public static function unifiedOrder($inputObj,$payKey=null, $timeOut = 6)
	{
		$url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
		//检测必填参数
		if(!$inputObj->IsOut_trade_noSet()) {
			throw new WxPayException("缺少统一支付接口必填参数out_trade_no！");
		}else if(!$inputObj->IsBodySet()){
			throw new WxPayException("缺少统一支付接口必填参数body！");
		}else if(!$inputObj->IsTotal_feeSet()) {
			throw new WxPayException("缺少统一支付接口必填参数total_fee！");
		}else if(!$inputObj->IsTrade_typeSet()) {
			throw new WxPayException("缺少统一支付接口必填参数trade_type！");
		}else if(!$inputObj->IsAppidSet()) {
			throw new WxPayException("缺少必填参数appid！");
		} else if(!$inputObj->IsMch_idSet()) {
			throw new WxPayException("缺少必填参数mch_id！");
		} else if(!$inputObj->IsNonce_strSet()) {
			throw new WxPayException("缺少必填参数nonce_str！");
		} else if(!$inputObj->IsSignSet()) {
			throw new WxPayException("缺少必填参数sign！");
		} else if(!$inputObj->IsSpbill_create_ipSet()) {
			throw new WxPayException("缺少必填参数spbill_create_ip！");
		} 
		
		//关联参数
		if($inputObj->GetTrade_type() == "JSAPI" && !$inputObj->IsOpenidSet()){
			throw new WxPayException("统一支付接口中，缺少必填参数openid！trade_type为JSAPI时，openid为必填参数！");
		}
		if($inputObj->GetTrade_type() == "NATIVE" && !$inputObj->IsProduct_idSet()){
			throw new WxPayException("统一支付接口中，缺少必填参数product_id！trade_type为JSAPI时，product_id为必填参数！");
		}
		
		//异步通知url未设置，则使用配置文件中的url
		if(!$inputObj->IsNotify_urlSet()){
			$inputObj->SetNotify_url(WxPayConfig::NOTIFY_URL);//异步通知url
		}
		
		
		$xml = $inputObj->ToXml();
		Logger::info("==============================unifiedOrder start===========================\n");
		Logger::debug('unifiedOrder post xml:'.$xml);
		$startTimeStamp = self::getMillisecond();//请求开始时间
		$response = self::postXmlCurl($xml, $url, false, $timeOut);
                Logger::debug('unifiedOrder response:',$response);
                Logger::info("==============================unifiedOrder end===========================\n");
		$result = WxPayResults::Init($response,$payKey);
		self::reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间
		
		return $result;
	}
	
	/**
	 * 
	 * 查询订单，WxPayOrderQuery中out_trade_no、transaction_id至少填一个
	 * @param WxPayOrderQuery $inputObj
	 * @param int $timeOut
         * @param string $payKey  支付秘钥；
	 * @throws WxPayException
	 * @return 成功时返回，其他抛异常
	 */
	public static function orderQuery($inputObj,$payKey, $timeOut = 6)
	{
		$url = "https://api.mch.weixin.qq.com/pay/orderquery";
		//检测必填参数
		if(!$inputObj->IsOut_trade_noSet() && !$inputObj->IsTransaction_idSet()) {
			throw new WxPayException("订单查询接口中，out_trade_no、transaction_id至少填一个！");
		}else if(!$inputObj->IsAppidSet()) {
			throw new WxPayException("缺少必填参数appid！");
		} else if(!$inputObj->IsMch_idSet()) {
			throw new WxPayException("缺少必填参数mch_id！");
		} else if(!$inputObj->IsNonce_strSet()) {
			throw new WxPayException("缺少必填参数nonce_str！");
		} else if(!$inputObj->IsSignSet()) {
			throw new WxPayException("缺少必填参数sign！");
		}
		
		$xml = $inputObj->ToXml();
                Logger::info("==============================orderQuery start===========================\n");
		Logger::debug('order query post xml:'.$xml);
		$startTimeStamp = self::getMillisecond();//请求开始时间
		$response = self::postXmlCurl($xml, $url, false, $timeOut);
                Logger::debug('order query response:',$response);
                Logger::info("==============================orderQuery end===========================\n");
		$result = WxPayResults::Init($response,$payKey);
		self::reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间
		
		return $result;
	}
	
	/**
	 * 
	 * 关闭订单，WxPayCloseOrder中out_trade_no必填
	 * appid、mchid、spbill_create_ip、nonce_str不需要填入
	 * @param WxPayCloseOrder $inputObj
	 * @param int $timeOut
         * @param string $payKey  支付秘钥；
	 * @throws WxPayException
	 * @return 成功时返回，其他抛异常
	 */
	public static function closeOrder($inputObj,$payKey, $timeOut = 6)
	{
		$url = "https://api.mch.weixin.qq.com/pay/closeorder";
		//检测必填参数
		if(!$inputObj->IsOut_trade_noSet()) {
			throw new WxPayException("订单查询接口中，out_trade_no必填！");
		}else if(!$inputObj->IsAppidSet()) {
			throw new WxPayException("缺少必填参数appid！");
		} else if(!$inputObj->IsMch_idSet()) {
			throw new WxPayException("缺少必填参数mch_id！");
		} else if(!$inputObj->IsNonce_strSet()) {
			throw new WxPayException("缺少必填参数nonce_str！");
		} else if(!$inputObj->IsSignSet()) {
			throw new WxPayException("缺少必填参数sign！");
		}

		$xml = $inputObj->ToXml();
		Logger::info("==============================closeOrder start===========================\n");
		Logger::debug('closeOrder post xml:',$xml);
		$startTimeStamp = self::getMillisecond();//请求开始时间
		$response = self::postXmlCurl($xml, $url, false, $timeOut);
                Logger::debug('closeOrder response:',$response);
                Logger::info("==============================closeOrder end===========================\n");
		$result = WxPayResults::Init($response,$payKey);
		self::reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间
		
		return $result;
	}

	/**
	 * 
	 * 申请退款，WxPayRefund中out_trade_no、transaction_id至少填一个且
	 * @param WxPayRefund $inputObj
	 * @param int $timeOut
         * @param string $payKey  支付秘钥；
	 * @throws WxPayException
	 * @return 成功时返回，其他抛异常
	 */
	public static function refund($inputObj,$payKey, $curl_credentials,$timeOut = 6)
	{
		$url = "https://api.mch.weixin.qq.com/secapi/pay/refund";
		//检测必填参数
		if(!$inputObj->IsOut_trade_noSet() && !$inputObj->IsTransaction_idSet()) {
			throw new WxPayException("退款申请接口中，out_trade_no、transaction_id至少填一个！");
		}else if(!$inputObj->IsOut_refund_noSet()){
			throw new WxPayException("退款申请接口中，缺少必填参数out_refund_no！");
		}else if(!$inputObj->IsTotal_feeSet()){
			throw new WxPayException("退款申请接口中，缺少必填参数total_fee！");
		}else if(!$inputObj->IsRefund_feeSet()){
			throw new WxPayException("退款申请接口中，缺少必填参数refund_fee！");
		}else if(!$inputObj->IsOp_user_idSet()){
			throw new WxPayException("退款申请接口中，缺少必填参数op_user_id！");
		}else if(!$inputObj->IsAppidSet()) {
			throw new WxPayException("缺少必填参数appid！");
		}else if(!$inputObj->IsMch_idSet()) {
			throw new WxPayException("缺少必填参数mch_id！");
		} else if(!$inputObj->IsNonce_strSet()) {
			throw new WxPayException("缺少必填参数nonce_str！");
		} else if(!$inputObj->IsSignSet()) {
			throw new WxPayException("缺少必填参数sign！");
		} 
		
		$xml = $inputObj->ToXml();
                Logger::info("==============================refund start===========================\n");
		Logger::debug('refund post xml:',$xml);
		$startTimeStamp = self::getMillisecond();//请求开始时间
		$response = self::postXmlCurl($xml, $url, true, $timeOut,$curl_credentials);
                Logger::debug('refund response:',$response);
                Logger::info("==============================refund end===========================\n");
		$result = WxPayResults::Init($response,$payKey);
		self::reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间
		
		return $result;
	}
	
	/**
	 * 
	 * 查询退款
	 * 提交退款申请后，通过调用该接口查询退款状态。退款有一定延时，
	 * 用零钱支付的退款20分钟内到账，银行卡支付的退款3个工作日后重新查询退款状态。
	 * WxPayRefundQuery中out_refund_no、out_trade_no、transaction_id、refund_id四个参数必填一个
	 * @param string $payKey  支付秘钥；
	 * @param WxPayRefundQuery $inputObj
	 * @param int $timeOut
	 * @throws WxPayException
	 * @return 成功时返回，其他抛异常
	 */
	public static function refundQuery($inputObj, $payKey,$timeOut = 6)
	{
		$url = "https://api.mch.weixin.qq.com/pay/refundquery";
		//检测必填参数
		if(!$inputObj->IsOut_refund_noSet() &&
			!$inputObj->IsOut_trade_noSet() &&
			!$inputObj->IsTransaction_idSet() &&
			!$inputObj->IsRefund_idSet()) {
			throw new WxPayException("退款查询接口中，out_refund_no、out_trade_no、transaction_id、refund_id四个参数必填一个！");
		}else if(!$inputObj->IsAppidSet()) {
			throw new WxPayException("缺少必填参数appid！");
		}else if(!$inputObj->IsMch_idSet()) {
			throw new WxPayException("缺少必填参数mch_id！");
		} else if(!$inputObj->IsNonce_strSet()) {
			throw new WxPayException("缺少必填参数nonce_str！");
		} else if(!$inputObj->IsSignSet()) {
			throw new WxPayException("缺少必填参数sign！");
		} 
		$xml = $inputObj->ToXml();
		Logger::info("==============================refundQuery start===========================\n");
		Logger::debug('refundQuery post xml:',$xml);
		$startTimeStamp = self::getMillisecond();//请求开始时间
		$response = self::postXmlCurl($xml, $url, false, $timeOut);
                Logger::debug('refundQuery response:',$response);
                Logger::info("==============================refundQuery end===========================\n");
		$result = WxPayResults::Init($response,$payKey);
		self::reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间
		
		return $result;
	}
	
	/**
	 * 下载对账单
	 * @param WxPayDownloadBill $inputObj
	 * @param int $timeOut
	 * @throws WxPayException
	 * @return 成功时返回，其他抛异常
	 */
	public static function downloadBill($inputObj, $timeOut = 6)
	{
		$url = "https://api.mch.weixin.qq.com/pay/downloadbill";
		//检测必填参数
		if(!$inputObj->IsBill_dateSet()) {
			throw new WxPayException("对账单接口中，缺少必填参数bill_date！");
		}else if(!$inputObj->IsAppidSet()) {
			throw new WxPayException("缺少必填参数appid！");
		}else if(!$inputObj->IsMch_idSet()) {
			throw new WxPayException("缺少必填参数mch_id！");
		} else if(!$inputObj->IsNonce_strSet()) {
			throw new WxPayException("缺少必填参数nonce_str！");
		} else if(!$inputObj->IsSignSet()) {
			throw new WxPayException("缺少必填参数sign！");
		} 
		
		$xml = $inputObj->ToXml();
		
		$response = self::postXmlCurl($xml, $url, false, $timeOut);
		if(substr($response, 0 , 5) == "<xml>"){
			return "";
		}
		return $response;
	}
	
	/**
	 * 提交被扫支付API
	 * 收银员使用扫码设备读取微信用户刷卡授权码以后，二维码或条码信息传送至商户收银台，
	 * @param string $payKey  支付秘钥；
	 * @param WxPayWxPayMicroPay $inputObj
	 * @param int $timeOut
         * @param string $payKey 支付秘钥
	 */
	public static function micropay($inputObj,$payKey, $timeOut = 10)
	{
		$url = "https://api.mch.weixin.qq.com/pay/micropay";
		//检测必填参数
		if(!$inputObj->IsBodySet()) {
			throw new WxPayException("提交被扫支付API接口中，缺少必填参数body！");
		} else if(!$inputObj->IsOut_trade_noSet()) {
			throw new WxPayException("提交被扫支付API接口中，缺少必填参数out_trade_no！");
		} else if(!$inputObj->IsTotal_feeSet()) {
			throw new WxPayException("提交被扫支付API接口中，缺少必填参数total_fee！");
		} else if(!$inputObj->IsAuth_codeSet()) {
			throw new WxPayException("提交被扫支付API接口中，缺少必填参数auth_code！");
		}else if(!$inputObj->IsAppidSet()) {
			throw new WxPayException("缺少必填参数appid！");
		} else if(!$inputObj->IsMch_idSet()) {
			throw new WxPayException("缺少必填参数mch_id！");
		} else if(!$inputObj->IsNonce_strSet()) {
			throw new WxPayException("缺少必填参数nonce_str！");
		} else if(!$inputObj->IsSignSet()) {
			throw new WxPayException("缺少必填参数sign！");
		} else if(!$inputObj->IsSpbill_create_ipSet()) {
			throw new WxPayException("缺少必填参数spbill_create_ip！");
		} 
		
		
		$xml = $inputObj->ToXml();
		Logger::info("==============================micropay start===========================\n");
		Logger::debug('micropay post xml:',$xml);
		$startTimeStamp = self::getMillisecond();//请求开始时间
		$response = self::postXmlCurl($xml, $url, false, $timeOut);
                Logger::debug('micropay response:',$response);
                Logger::info("==============================micropay end===========================\n");
		$result = WxPayResults::Init($response,$payKey);
		self::reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间
		
		return $result;
	}
	
	/**
	 * 
	 * 撤销订单API接口
	 * @param WxPayReverse $inputObj
         * @param string $payKey  支付秘钥；
	 * @param int $timeOut
	 * @throws WxPayException
	 */
	public static function reverse($inputObj,$payKey,$curl_credentials, $timeOut = 6)
	{
		$url = "https://api.mch.weixin.qq.com/secapi/pay/reverse";
		//检测必填参数
		if(!$inputObj->IsOut_trade_noSet() && !$inputObj->IsTransaction_idSet()) {
			throw new WxPayException("撤销订单API接口中，参数out_trade_no和transaction_id必须填写一个！");
		}else if(!$inputObj->IsAppidSet()) {
			throw new WxPayException("缺少必填参数appid！");
		} else if(!$inputObj->IsMch_idSet()) {
			throw new WxPayException("缺少必填参数mch_id！");
		} else if(!$inputObj->IsNonce_strSet()) {
			throw new WxPayException("缺少必填参数nonce_str！");
		} else if(!$inputObj->IsSignSet()) {
			throw new WxPayException("缺少必填参数sign！");
		} 
		$xml = $inputObj->ToXml();
		Logger::info("==============================reverse start===========================\n");
		Logger::debug('reverse post xml:',$xml);
		$startTimeStamp = self::getMillisecond();//请求开始时间
		$response = self::postXmlCurl($xml, $url, true, $timeOut,$curl_credentials);
                Logger::debug('reverse response:',$response);
                Logger::info("==============================reverse end===========================\n");
                
		$result = WxPayResults::Init($response,$payKey);
		self::reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间
		
		return $result;
	}
	
	/**
	 * 
	 * 测速上报，该方法内部封装在report中，使用时请注意异常流程
	 * @param WxPayReport $inputObj
	 * @param int $timeOut
	 * @throws WxPayException
	 * @return 成功时返回，其他抛异常
	 */
	public static function report($inputObj, $timeOut = 1)
	{
		$url = "https://api.mch.weixin.qq.com/payitil/report";
		//检测必填参数
		if(!$inputObj->IsInterface_urlSet()) {
			throw new WxPayException("接口URL，缺少必填参数interface_url！");
		} if(!$inputObj->IsReturn_codeSet()) {
			throw new WxPayException("返回状态码，缺少必填参数return_code！");
		} if(!$inputObj->IsResult_codeSet()) {
			throw new WxPayException("业务结果，缺少必填参数result_code！");
		} if(!$inputObj->IsUser_ipSet()) {
			throw new WxPayException("访问接口IP，缺少必填参数user_ip！");
		} if(!$inputObj->IsExecute_time_Set()) {
			throw new WxPayException("接口耗时，缺少必填参数execute_time_！");
		}else if(!$inputObj->IsAppidSet()) {
			throw new WxPayException("缺少必填参数appid！");
		} else if(!$inputObj->IsMch_idSet()) {
			throw new WxPayException("缺少必填参数mch_id！");
		} else if(!$inputObj->IsNonce_strSet()) {
			throw new WxPayException("缺少必填参数nonce_str！");
		} else if(!$inputObj->IsSignSet()) {
			throw new WxPayException("缺少必填参数sign！");
		}else if(!$inputObj->IsUser_ipSet()) {
			throw new WxPayException("缺少必填参数user_ip！");
		}else if(!$inputObj->IsTimeSet()) {
			throw new WxPayException("缺少必填参数time！");
		}

		 

		$xml = $inputObj->ToXml();
		
		$startTimeStamp = self::getMillisecond();//请求开始时间
		$response = self::postXmlCurl($xml, $url, false, $timeOut);
		return $response;
	}
	
	/**
	 * 
	 * 生成二维码规则,模式一生成支付二维码
	 * appid、mchid、spbill_create_ip、nonce_str不需要填入
	 * @param WxPayBizPayUrl $inputObj
	 * @param int $timeOut
	 * @throws WxPayException
	 * @return 成功时返回，其他抛异常
	 */
	public static function bizpayurl($inputObj, $timeOut = 6)
	{
		if(!$inputObj->IsProduct_idSet()){
			throw new WxPayException("生成二维码，缺少必填参数product_id！");
		}else if(!$inputObj->IsAppidSet()) {
			throw new WxPayException("缺少必填参数appid！");
		} else if(!$inputObj->IsMch_idSet()) {
			throw new WxPayException("缺少必填参数mch_id！");
		} else if(!$inputObj->IsNonce_strSet()) {
			throw new WxPayException("缺少必填参数nonce_str！");
		} else if(!$inputObj->IsSignSet()) {
			throw new WxPayException("缺少必填参数sign！");
		}else if(!$inputObj->IsTime_stampSet()) {
			throw new WxPayException("缺少必填参数time_stamp！");
		}
		
		
                
		return $inputObj->GetValues();
	}
	
	/**
	 * 
	 * 转换短链接
	 * 该接口主要用于扫码原生支付模式一中的二维码链接转成短链接(weixin://wxpay/s/XXXXXX)，
	 * 减小二维码数据量，提升扫描速度和精确度。
	 * appid、mchid、spbill_create_ip、nonce_str不需要填入
	 * @param WxPayShortUrl $inputObj
	 * @param int $timeOut
	 * @throws WxPayException
	 * @return 成功时返回，其他抛异常
	 */
	public static function shorturl($inputObj, $timeOut = 6)
	{
		$url = "https://api.mch.weixin.qq.com/tools/shorturl";
		//检测必填参数
		if(!$inputObj->IsLong_urlSet()) {
			throw new WxPayException("需要转换的URL，签名用原串，传输需URL encode！");
		}else if(!$inputObj->IsAppidSet()) {
			throw new WxPayException("缺少必填参数appid！");
		} else if(!$inputObj->IsMch_idSet()) {
			throw new WxPayException("缺少必填参数mch_id！");
		} else if(!$inputObj->IsNonce_strSet()) {
			throw new WxPayException("缺少必填参数nonce_str！");
		} else if(!$inputObj->IsSignSet()) {
			throw new WxPayException("缺少必填参数sign！");
		} 
		$xml = $inputObj->ToXml();
		
		$startTimeStamp = self::getMillisecond();//请求开始时间
		$response = self::postXmlCurl($xml, $url, false, $timeOut);
		$result = WxPayResults::Init($response);
		self::reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间
		
		return $result;
	}
	
 	/**
 	 * 
 	 * 支付结果通用通知
 	 * @param function $callback
 	 * 直接回调函数使用方法: notify(you_function);
 	 * 回调类成员函数方法:notify(array($this, you_function));
 	 * $callback  原型为：function function_name($data){}
         * @param string $payKey  支付秘钥；
 	 */
	public static function notify($callback,$payKey,$xml, &$msg)
	{
		//获取通知的数据
		
                Logger::debug('weixin native notify data xml:',$xml);
		//如果返回成功则验证签名
		try {
			$result = WxPayResults::Init($xml,$payKey);
		} catch (WxPayException $e){
			$msg = $e->errorMessage();
			return false;
		}
		
		return call_user_func($callback, $result);
	}
	
	/**
	 * 
	 * 产生随机字符串，不长于32位
	 * @param int $length
	 * @return 产生的随机字符串
	 */
	public static function getNonceStr($length = 32) 
	{
		$chars = "abcdefghijklmnopqrstuvwxyz0123456789";  
		$str ="";
		for ( $i = 0; $i < $length; $i++ )  {  
			$str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);  
		} 
		return $str;
	}
	
	/**
	 * 直接输出xml
	 * @param string $xml
	 */
	public static function replyNotify($xml)
	{
		echo $xml;
	}
	
	/**
	 * 
	 * 上报数据， 上报的时候将屏蔽所有异常流程
	 * @param string $usrl
	 * @param int $startTimeStamp
	 * @param array $data
	 */
	private static function reportCostTime($url, $startTimeStamp, $data)
	{
		//如果不需要上报数据
		if(WxPayConfig::REPORT_LEVENL == 0){
			return;
		} 
		//如果仅失败上报
		if(WxPayConfig::REPORT_LEVENL == 1 &&
			 array_key_exists("return_code", $data) &&
			 $data["return_code"] == "SUCCESS" &&
			 array_key_exists("result_code", $data) &&
			 $data["result_code"] == "SUCCESS")
		 {
		 	return;
		 }
		 
		//上报逻辑
		$endTimeStamp = self::getMillisecond();
		$objInput = new WxPayReport();
		$objInput->SetInterface_url($url);
		$objInput->SetExecute_time_($endTimeStamp - $startTimeStamp);
		//返回状态码
		if(array_key_exists("return_code", $data)){
			$objInput->SetReturn_code($data["return_code"]);
		}
		//返回信息
		if(array_key_exists("return_msg", $data)){
			$objInput->SetReturn_msg($data["return_msg"]);
		}
		//业务结果
		if(array_key_exists("result_code", $data)){
			$objInput->SetResult_code($data["result_code"]);
		}
		//错误代码
		if(array_key_exists("err_code", $data)){
			$objInput->SetErr_code($data["err_code"]);
		}
		//错误代码描述
		if(array_key_exists("err_code_des", $data)){
			$objInput->SetErr_code_des($data["err_code_des"]);
		}
		//商户订单号
		if(array_key_exists("out_trade_no", $data)){
			$objInput->SetOut_trade_no($data["out_trade_no"]);
		}
		//设备号
		if(array_key_exists("device_info", $data)){
			$objInput->SetDevice_info($data["device_info"]);
		}
		
		try{
                    $objInput->SetUser_ip($_SERVER['REMOTE_ADDR']);//终端ip
                    $objInput->SetTime(date("YmdHis"));//商户上报时间	
			self::report($objInput);
		} catch (WxPayException $e){
			//不做任何处理
		}
	}

	/**
	 * 以post方式提交xml到对应的接口url
	 * 
	 * @param string $xml  需要post的xml数据
	 * @param string $url  url
         * @param array $curl_credentials 证书路径 当$useCert为ture时必须指定
	 * @param bool $useCert 是否需要证书，默认不需要
	 * @param int $second   url执行超时时间，默认30s
	 * @throws WxPayException
	 */
	private static function postXmlCurl($xml, $url, $useCert = false, $second = 30,$curl_credentials=null)
	{		
		$ch = curl_init();
		//设置超时
		curl_setopt($ch, CURLOPT_TIMEOUT, $second);
		
		//如果有配置代理这里就设置代理
		if(WxPayConfig::CURL_PROXY_HOST != "0.0.0.0" 
			&& WxPayConfig::CURL_PROXY_PORT != 0){
			curl_setopt($ch,CURLOPT_PROXY, WxPayConfig::CURL_PROXY_HOST);
			curl_setopt($ch,CURLOPT_PROXYPORT, WxPayConfig::CURL_PROXY_PORT);
		}
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,TRUE);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);//严格校验
		//设置header
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		//要求结果为字符串且输出到屏幕上
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	
		if($useCert&&$curl_credentials){
			//设置证书
			//使用证书：cert 与 key 分别属于两个.pem文件
			curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
			curl_setopt($ch,CURLOPT_SSLCERT, $curl_credentials['SSLCERT']);
			curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
			curl_setopt($ch,CURLOPT_SSLKEY, $curl_credentials['SSLKEY']);
                        curl_setopt($ch,CURLOPT_CAINFO,$curl_credentials['SSLCA_PATH']);
		}
		//post提交方式
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		//运行curl
		$data = curl_exec($ch);
		//返回结果
		if($data){
			curl_close($ch);
			return $data;
		} else { 
			$error = curl_errno($ch);
			curl_close($ch);
			throw new WxPayException("curl出错，错误码:$error");
		}
	}
	
	/**
	 * 获取毫秒级别的时间戳
	 */
	private static function getMillisecond()
	{
		//获取毫秒的时间戳
		$time = explode ( " ", microtime () );
		$time = $time[1] . ($time[0] * 1000);
		$time2 = explode( ".", $time );
		$time = $time2[0];
		return $time;
	}

	/**
	 * by xizb
	 * 活动订单
         * @param array $curl_credentials证书路径
	 * @param WxPayRedPackPay $inputObj
	 * @param int $timeOut
	 * @throws WxPayException
	 * @return 成功时返回，其他抛异常
	 */
	public static function redPack_Activity($inputObj,$timeOut = 6)
	{
		$compConf = ComponectConfig::get('COUPON');
        $compInfo = array(
            'comp_appid' => @$compConf['APP_ID'],
            'comp_app_secret' => @$compConf['APP_SECRET'],
            'authorizer_appid' => 'wx22737b782dbb0567'
        );
        $token= getAuthorizerAccessToken($compInfo);
        if(empty($token)){
        	$token=getToken('wx22737b782dbb0567', 'hk67f61de34122bcf65ff47c865880fe13');
        }
		$logo_url='http://test.hzc.socialjia.com/Admin/images/iphone.png';
		$url = 'https://api.weixin.qq.com/shakearound/lottery/addlotteryinfo?access_token='.$token.'&use_template=1&logo_url='.$logo_url;
		//检测必填参数
		if(!$inputObj->gtitle()) {
			throw new WxPayException("缺少必填参数title！");
		}else if(!$inputObj->gdesc()){
			throw new WxPayException("缺少必填参数desc！");
		}else if(!$inputObj->gonoff()) {
			throw new WxPayException("缺少必填参数onoff！");
		}else if(!$inputObj->gbegin_time()) {
			throw new WxPayException("缺少必填参数begin_time！");
		}else if(!$inputObj->gexpire_time()) {
			throw new WxPayException("缺少必填参数expire_time！");
		}else if(!$inputObj->gsponsor_appid()) {
			throw new WxPayException("缺少必填参数sponsor_appid！");
		}else if(!$inputObj->gtotal()) {
			throw new WxPayException("缺少必填参数total！");
		}else if(!$inputObj->gjump_url()) {
			throw new WxPayException("缺少必填参数jump_url！");
		}else if(!$inputObj->gsign()) {
			throw new WxPayException("缺少必填参数sign！");
		}

		$get_data=$inputObj->get_data();
		foreach ($get_data as $key => $value) {
            $data[]='"'.$key.'":"'.$value.'"';
        }
        $res="{".implode(',',$data)."}";
		$ch = curl_init();  
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,TRUE);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);//严格校验
		//curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $res);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1); //是否抓取跳转后的页面
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					    'Content-Type: application/json',
					    'Content-Length: ' . strlen($res),
					    'Content-Type: application/x-www-form-urlencoded; charset=UTF-8'
					    )
					);
		//运行curl，结果以jason形式返回
 		$result = curl_exec($ch);
 		$res = curl_getinfo($ch);
		curl_close($ch);
		return $result;
	}

	/**
	 * by xizb
	 * 添加红包订单
         * @param array $curl_credentials证书路径
	 * @param WxPayRedPackPay $inputObj
	 * @param int $timeOut
	 * @throws WxPayException
	 * @return 成功时返回，其他抛异常
	 */
	public static function red_Packet($inputObj,$timeOut = 6)
	{
		$compConf = ComponectConfig::get('COUPON');
        $compInfo = array(
            'comp_appid' => @$compConf['APP_ID'],
            'comp_app_secret' => @$compConf['APP_SECRET'],
            'authorizer_appid' => 'wx22737b782dbb0567'
        );
        $token= getAuthorizerAccessToken($compInfo);
        if(empty($token)){
        	$token=getToken('wx22737b782dbb0567', 'hk67f61de34122bcf65ff47c865880fe13');
        }

		$url = 'https://api.weixin.qq.com/shakearound/lottery/setprizebucket?access_token='.$token;
		//检测必填参数
		if(!$inputObj->glottery_id()) {
			throw new WxPayException("缺少必填参数lottery_id！");
		}else if(!$inputObj->gmchid()){
			throw new WxPayException("缺少必填参数mchid！");
		}else if(!$inputObj->gappid()) {
			throw new WxPayException("缺少必填参数appid！");
		}else if(!$inputObj->gprize_info_list()) {
			throw new WxPayException("缺少必填参数prize_info_list！");
		}

		$get_data=$inputObj->get_data();
		$data=json_encode($get_data);
		$ch = curl_init();  
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,TRUE);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);//严格校验
		//curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1); //是否抓取跳转后的页面
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					    'Content-Type: application/json',
					    'Content-Length: ' . strlen($data),
					    'Content-Type: application/x-www-form-urlencoded; charset=UTF-8'
					    )
					);
		//运行curl，结果以jason形式返回
 		$result = curl_exec($ch);
 		$res = curl_getinfo($ch);
		curl_close($ch);
		return $result;
    }

    /**
	 * by xizb
	 * 红包订单
         * @param array $curl_credentials证书路径
	 * @param WxPayRedPackPay $inputObj
	 * @param int $timeOut
	 * @throws WxPayException
	 * @return 成功时返回，其他抛异常
	 */
	public static function redPacks($inputObj,$curl_credentials, $timeOut = 6)
	{
		$url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/hbpreorder';
		//检测必填参数
		if(!$inputObj->gnonce_str()) {
			throw new WxPayException("缺少必填参数nonce_str！");
		}else if(!$inputObj->gmch_billno()){
			throw new WxPayException("缺少必填参数mch_billno！");
		}else if(!$inputObj->gmch_id()) {
			throw new WxPayException("缺少必填参数mch_id！");
		}else if(!$inputObj->gwxappid()) {
			throw new WxPayException("缺少必填参数wxappid！");
		}else if(!$inputObj->gsend_name()) {
			throw new WxPayException("缺少必填参数send_name！");
		}else if(!$inputObj->ghb_type()) {
			throw new WxPayException("缺少必填参数hb_type！");
		}else if(!$inputObj->gtotal_amount()) {
			throw new WxPayException("缺少必填参数total_amount！");
		}else if(!$inputObj->gtotal_num()) {
			throw new WxPayException("缺少必填参数total_num！");
		}else if(!$inputObj->gwishing()) {
			throw new WxPayException("缺少必填参数wishing！");
		}else if(!$inputObj->gact_name()) {
			throw new WxPayException("缺少必填参数act_name！");
		}else if(!$inputObj->gremark()) {
			throw new WxPayException("缺少必填参数remark！");
		}else if(!$inputObj->gauth_mchid()) {
			throw new WxPayException("缺少必填参数auth_mchid！");
		} else if(!$inputObj->gauth_appid()) {
			throw new WxPayException("缺少必填参数auth_appid！");
		} else if(!$inputObj->grisk_cntl()) {
			throw new WxPayException("缺少必填参数risk_cntl！");
		} else if(!$inputObj->gsign()) {
			throw new WxPayException("缺少必填参数sign！");
		}else if(!$inputObj->gamt_type()) {
			throw new WxPayException("缺少必填参数amt_type！");
		}
		$xml = $inputObj->ToXml();
		echo $xml;
		exit();
                Logger::info("==============================redPackOrder start===========================\n");
		Logger::debug('redpack post xml:',$xml);
		$startTimeStamp = self::getMillisecond();//请求开始时间
		$response = self::postXmlCurl($xml, $url, true, $timeOut,$curl_credentials);
                Logger::debug('redpack response:',$response);
                Logger::info("==============================redPackOrder end===========================\n");
		//$result = WxPayResults::Init($response);
                $result = json_decode(json_encode(simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
                
		self::reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间
		
		return $result;
	}
        
        /**
	 * by xizb
	 * 红包订单
         * @param array $curl_credentials证书路径
	 * @param WxPayRedPackPay $inputObj
	 * @param int $timeOut
	 * @throws WxPayException
	 * @return 成功时返回，其他抛异常
	 */
	public static function redPackOrder($inputObj,$curl_credentials, $timeOut = 6)
	{
		$url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';
		//检测必填参数
		if(!$inputObj->IsMch_billnoSet()) {
			throw new WxPayException("缺少必填参数mch_billno！");
		}else if(!$inputObj->IsOpenidSet()){
			throw new WxPayException("缺少必填参数openid！");
		}else if(!$inputObj->IsNick_nameSet()) {
			throw new WxPayException("缺少必填参数nick_name！");
		}else if(!$inputObj->IsSend_nameSet()) {
			throw new WxPayException("缺少必填参数send_name！");
		}else if(!$inputObj->IsTotal_amountSet()) {
			throw new WxPayException("缺少必填参数total_amount！");
		}else if(!$inputObj->IsMin_valueSet()) {
			throw new WxPayException("缺少必填参数min_value！");
		}else if(!$inputObj->IsMax_valueSet()) {
			throw new WxPayException("缺少必填参数max_value！");
		}else if(!$inputObj->IsTotal_numSet()) {
			throw new WxPayException("缺少必填参数total_num！");
		}else if(!$inputObj->IsWishingSet()) {
			throw new WxPayException("缺少必填参数wishing！");
		}else if(!$inputObj->IsAct_nameSet()) {
			throw new WxPayException("缺少必填参数act_name！");
		}else if(!$inputObj->IsRemarkSet()) {
			throw new WxPayException("缺少必填参数remark！");
		}else if(!$inputObj->IsAppidSet()) {
			throw new WxPayException("缺少必填参数appid！");
		} else if(!$inputObj->IsMch_idSet()) {
			throw new WxPayException("缺少必填参数mch_id！");
		} else if(!$inputObj->IsNonce_strSet()) {
			throw new WxPayException("缺少必填参数nonce_str！");
		} else if(!$inputObj->IsSignSet()) {
			throw new WxPayException("缺少必填参数sign！");
		} else if(!$inputObj->IsSpbill_create_ipSet()) {
			throw new WxPayException("缺少必填参数spbill_create_ip！");
		} 
		$xml = $inputObj->ToXml();
                Logger::info("==============================redPackOrder start===========================\n");
		Logger::debug('redpack post xml:',$xml);
		$startTimeStamp = self::getMillisecond();//请求开始时间
		$response = self::postXmlCurl($xml, $url, true, $timeOut,$curl_credentials);
                Logger::debug('redpack response:',$response);
                Logger::info("==============================redPackOrder end===========================\n");
		//$result = WxPayResults::Init($response);
                $result = json_decode(json_encode(simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
                
		self::reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间
		
		return $result;
	}
	/**
	 * by xizb
	 * 红包订单查询，
	 * @param WxPayRedPackPay $inputObj
	 * @param int $timeOut
         * @param array $curl_credentials 证书路径
	 * @throws WxPayException
	 * @return 成功时返回，其他抛异常
	 */
	public static function redPackOrderQuery($inputObj,$curl_credentials, $timeOut = 6)
	{
		$url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/gethbinfo ';
		//检测必填参数
		if(!$inputObj->IsMch_billnoSet()) {
			throw new WxPayException("缺少必填参数mch_billno！");
		}else if(!$inputObj->IsAppidSet()) {
			throw new WxPayException("缺少必填参数appid！");
		}else if(!$inputObj->IsMch_idSet()) {
			throw new WxPayException("缺少必填参数mch_id！");
		} else if(!$inputObj->IsNonce_strSet()) {
			throw new WxPayException("缺少必填参数nonce_str！");
		} else if(!$inputObj->IsSignSet()) {
			throw new WxPayException("缺少必填参数sign！");
		}  else if(!$inputObj->IsBill_typeSet()) {
			throw new WxPayException("缺少必填参数bill_type！");
		} 
			
		$xml = $inputObj->ToXml();
                Logger::info("==============================redPackOrderQuery start===========================\n");
		Logger::debug('red pack query xml:'.$xml);
		$startTimeStamp = self::getMillisecond();//请求开始时间
		$response = self::postXmlCurl($xml, $url, TRUE, $timeOut,$curl_credentials);
                Logger::debug('redpack query response:',$response);
                Logger::info("==============================redPackOrderQuery end===========================\n");
		//$result = WxPayResults::Init($response);
                $result = json_decode(json_encode(simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
                
		self::reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间
		
		return $result;
	}
        
        
        
        
        /**
	 * by xizb
	 * 红包订单
	 * @param WxPayRedPackPay $inputObj
	 * @param int $timeOut
	 * @throws WxPayException
         * @param array $curl_credentials 证书路径
	 * @return 成功时返回，其他抛异常
	 */
	public static function groupRedPackOrder($inputObj,$curl_credentials, $timeOut = 6)
	{
		$url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendgroupredpack';
		//检测必填参数
		if(!$inputObj->IsMch_billnoSet()) {
			throw new WxPayException("缺少必填参数mch_billno！");
		}else if(!$inputObj->IsOpenidSet()){
			throw new WxPayException("缺少必填参数openid！");
		}else if(!$inputObj->IsSend_nameSet()) {
			throw new WxPayException("缺少必填参数send_name！");
		}else if(!$inputObj->IsTotal_amountSet()) {
			throw new WxPayException("缺少必填参数total_amount！");
		}else if(!$inputObj->IsTotal_numSet()) {
			throw new WxPayException("缺少必填参数total_num！");
		}else if(!$inputObj->IsWishingSet()) {
			throw new WxPayException("缺少必填参数wishing！");
		}else if(!$inputObj->IsAct_nameSet()) {
			throw new WxPayException("缺少必填参数act_name！");
		}else if(!$inputObj->IsRemarkSet()) {
			throw new WxPayException("缺少必填参数remark！");
		}else if(!$inputObj->IsAppidSet()) {
			throw new WxPayException("缺少必填参数appid！");
		} else if(!$inputObj->IsMch_idSet()) {
			throw new WxPayException("缺少必填参数mch_id！");
		} else if(!$inputObj->IsNonce_strSet()) {
			throw new WxPayException("缺少必填参数nonce_str！");
		} else if(!$inputObj->IsSignSet()) {
			throw new WxPayException("缺少必填参数sign！");
		}  else if(!$inputObj->IsAmt_typeSet()) {
			throw new WxPayException("缺少必填参数amt_type！");
		} 
	
		
		
		$xml = $inputObj->ToXml();
                Logger::info("==============================groupRedPackOrder start===========================\n");
		Logger::debug('group  redpack post xml:',$xml);
		$startTimeStamp = self::getMillisecond();//请求开始时间
		$response = self::postXmlCurl($xml, $url, true, $timeOut,$curl_credentials);
                Logger::debug('group  redpack response:',$response);
                Logger::info("==============================groupRedPackOrder end===========================\n");
		//$result = WxPayResults::Init($response);
                $result = json_decode(json_encode(simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
         
		self::reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间
		
		return $result;
	}
        
        
        
        /**
	 * by xizb
	 * 企业支付订单，
	 * appid、mchid、spbill_create_ip、nonce_str不需要填入
	 * @param WxPayRedPackPay $inputObj
	 * @param int $timeOut
         * @param array $curl_credentials 证书路径
	 * @throws WxPayException
	 * @return 成功时返回，其他抛异常
	 */
	public static function companyPay($inputObj,$curl_credentials, $timeOut = 6)
	{
                $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
		//检测必填参数
		if(!$inputObj->IsAmountSet()) {
			throw new WxPayException("缺少必填参数amount！");
		}else if(!$inputObj->IsOpenidSet()){
			throw new WxPayException("缺少必填参数openid！");
		}else if(!$inputObj->IsCheck_nameSet()) {
			throw new WxPayException("缺少必填参数check_name！");
		}else if(!$inputObj->IsPartner_trade_noSet()) {
			throw new WxPayException("缺少必填参数partner_trade_no！");
		}else if(!$inputObj->IsDescSet()) {
			throw new WxPayException("缺少必填参数desc！");
		}else if(!$inputObj->IsMch_appidSet()) {
			throw new WxPayException("缺少必填参数mch_appid！");
		} else if(!$inputObj->IsMchidSet()) {
			throw new WxPayException("缺少必填参数mchid！");
		} else if(!$inputObj->IsSpbill_create_ipSet()) {
			throw new WxPayException("缺少必填参数spbill_create_ip！");
		} else if(!$inputObj->IsNonce_strSet()) {
			throw new WxPayException("缺少必填参数nonce_str！");
		} else if(!$inputObj->IsSignSet()) {
			throw new WxPayException("缺少必填参数sign！");
		} 
		
		
		$xml = $inputObj->ToXml();
		Logger::info("==============================companyPay start===========================\n");
		Logger::debug('company pay post xml:',$xml);
                $startTimeStamp = self::getMillisecond();//请求开始时间
		$response = self::postXmlCurl($xml, $url, true, $timeOut,$curl_credentials);
                Logger::debug('company pay response:',$response);
                Logger::info("==============================companyPay end===========================\n");
		//$result = WxPayResults::Init($response);
                $result = json_decode(json_encode(simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
                
		self::reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间
		
		return $result;
		
	}
        
        
        
        
        /**
         * 企业支付查询
         * @param WxPayCompanyOrderQuery $inputObj
         * @param int $timeOut
         * @param array $curl_credentials 证书路径
         * @return type
         * @throws WxPayException
         */
        public static function companyOrderQuery($inputObj,$curl_credentials, $timeOut = 6)
	{
		
                $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/gettransferinfo ';
		//检测必填参数
		if(!$inputObj->IsPartner_trade_noSet()) {
			throw new WxPayException("缺少必填参数partner_trade_no！");
		}else if(!$inputObj->IsAppidSet()) {
			throw new WxPayException("缺少必填参数appid！");
		} else if(!$inputObj->IsMch_idSet()) {
			throw new WxPayException("缺少必填参数mch_id！");
		} else if(!$inputObj->IsNonce_strSet()) {
			throw new WxPayException("缺少必填参数nonce_str！");
		} else if(!$inputObj->IsSignSet()) {
			throw new WxPayException("缺少必填参数sign！");
		} 
		
		$xml = $inputObj->ToXml();
                Logger::info("==============================companyOrderQuery start===========================\n");
		Logger::debug('companyOrderQuery post xml:',$xml);
		$startTimeStamp = self::getMillisecond();//请求开始时间
		$response = self::postXmlCurl($xml, $url, true, $timeOut,$curl_credentials);
                Logger::debug('companyOrderQuery  response:',$response);
                Logger::info("==============================companyOrderQuery end===========================\n");
		//$result = WxPayResults::Init($response);
                $result = json_decode(json_encode(simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
                
		self::reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间
		
		return $result;
	}
        
        
        
        /**
         * 查询代金券
         * @param WxPayCouponQuery $inputObj
         * @param int $timeOut
         */
        public static function couponQuery($inputObj,$timeOut = 6){
            
                $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/query_coupon_stock';
		//检测必填参数
		if(!$inputObj->IsCoupon_stock_idSet()) {
			throw new WxPayException("缺少必填参数coupon_stock_id！");
		}else if(!$inputObj->IsAppidSet()) {
			throw new WxPayException("缺少必填参数appid！");
		} else if(!$inputObj->IsMch_idSet()) {
			throw new WxPayException("缺少必填参数mch_id！");
		} else if(!$inputObj->IsNonce_strSet()) {
			throw new WxPayException("缺少必填参数nonce_str！");
		} else if(!$inputObj->IsSignSet()) {
			throw new WxPayException("缺少必填参数sign！");
		} 
			    
		
		$xml = $inputObj->ToXml();
                Logger::info("==============================couponQuery start===========================\n");
		Logger::debug('couponQuery post xml:',$xml);
		$startTimeStamp = self::getMillisecond();//请求开始时间
		$response = self::postXmlCurl($xml, $url, false, $timeOut);
                Logger::debug('couponQuery  response:',$response);
                Logger::info("==============================couponQuery end===========================\n");
		//$result = WxPayResults::Init($response);
                $result = json_decode(json_encode(simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
                
		self::reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间
		
		return $result;
        }
        
        
        
        
        
        
        /**
         * 发送代金券
         * @param WxPaySendCoupon $inputObj
         * @param int $timeOut
         * @param array $curl_credentials   证书参数路径
         */
        public static function sendCoupon($inputObj,$curl_credentials,$timeOut = 6){
            
                $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/send_coupon';
		//检测必填参数
		if(!$inputObj->IsCoupon_stock_idSet()) {
			throw new WxPayException("缺少必填参数从coupon_stock_id！");
		}else if(!$inputObj->IsAppidSet()) {
			throw new WxPayException("缺少必填参数appid！");
		} else if(!$inputObj->IsMch_idSet()) {
			throw new WxPayException("缺少必填参数mch_id！");
		} else if(!$inputObj->IsNonce_strSet()) {
			throw new WxPayException("缺少必填参数nonce_str！");
		} else if(!$inputObj->IsSignSet()) {
			throw new WxPayException("缺少必填参数sign！");
		} else if(!$inputObj->IsPartner_trade_noSet()){
                        throw new WxPayException("缺少必填参数partner_trade_no！");
                }else if(!$inputObj->IsOpenid_countSet()){
                        throw new WxPayException("缺少必填参数openid_count！");
                }else if(!$inputObj->IsOpenidSet()){
                        throw new WxPayException("缺少必填参数openid！");
                }
		
		$xml = $inputObj->ToXml();
                Logger::info("==============================sendCoupon start===========================\n");
		Logger::debug('sendCoupon post xml:',$xml);
		$startTimeStamp = self::getMillisecond();//请求开始时间
		$response = self::postXmlCurl($xml, $url, TRUE, $timeOut,$curl_credentials);
                Logger::debug('sendCoupon  response:',$response);
                Logger::info("==============================sendCoupon end===========================\n");
		//$result = WxPayResults::Init($response);
                $result = json_decode(json_encode(simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
                
		self::reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间
		
		return $result;
        }
        
        
        
        
}

