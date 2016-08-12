<?php
/**
* 2015-06-29 修复签名问题
**/
//require_once "WxPay.Config.php";
//require_once "WxPay.Exception.php";

/**
 * 
 * 数据对象基础类，该类中定义数据类最基本的行为，包括：
 * 计算/设置/获取签名、输出xml格式的参数、从xml读取数据对象等
 * @author widyhu
 *
 */
class WxPayDataBase
{
	protected $values = array();
	
	/**
	* 设置签名，详见签名生成算法
	* @param string $value 
	**/
	public function SetSign($key)
	{
		$sign = $this->MakeSign($key);
		$this->values['sign'] = $sign;
		return $sign;
	}
	public function unset_values($param){
            unset($this->values[$param]);
        }

        /**
	* 获取签名，详见签名生成算法的值
	* @return 值
	**/
	public function GetSign()
	{
		return $this->values['sign'];
	}
	
	/**
	* 判断签名，详见签名生成算法是否存在
	* @return true 或 false
	**/
	public function IsSignSet()
	{
		return array_key_exists('sign', $this->values);
	}

	/**
	 * 输出xml字符
	 * @throws WxPayException
	**/
	public function ToXml()
	{
		if(!is_array($this->values) 
			|| count($this->values) <= 0)
		{
    		throw new WxPayException("数组数据异常！");
    	}
    	
    	$xml = "<xml>";
    	foreach ($this->values as $key=>$val)
    	{
    		if (is_numeric($val)){
    			$xml.="<".$key.">".$val."</".$key.">";
    		}else{
    			$xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
    		}
        }
        $xml.="</xml>";
        return $xml; 
	}
	
    /**
     * 将xml转为array
     * @param string $xml
     * @throws WxPayException
     */
	public function FromXml($xml)
	{	
		if(!$xml){
			throw new WxPayException("xml数据异常！");
		}
        //将XML转为array
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $this->values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);		
		return $this->values;
	}
	
	/**
	 * 格式化参数格式化成url参数
	 */
	public function ToUrlParams()
	{
		$buff = "";
		foreach ($this->values as $k => $v)
		{
			if($k != "sign" && $v != "" && !is_array($v)){
				$buff .= $k . "=" . $v . "&";
			}
		}
		
		$buff = trim($buff, "&");
		return $buff;
	}
	
	/**
	 * 生成签名
	 * @return 签名，本函数不覆盖sign成员变量，如要设置签名需要调用SetSign方法赋值
	 */
	public function MakeSign($key)
	{
		//签名步骤一：按字典序排序参数
		ksort($this->values);
		$string = $this->ToUrlParams();
		//签名步骤二：在string后加入KEY
		$string = $string . "&key=".$key;
		//签名步骤三：MD5加密
		$string = md5($string);
		//签名步骤四：所有字符转为大写
		$result = strtoupper($string);
		return $result;
	}
	
	/**
	 * 获取设置的值
	 */
	public function GetValues()
	{
		return $this->values;
	}
        
}

/**
 * 
 * 接口调用结果类
 * @author widyhu
 *
 */
class WxPayResults extends WxPayDataBase
{
	/**
	 * 
	 * 检测签名
	 */
	public function CheckSign($key)
	{
		//fix异常
		if(!$this->IsSignSet()){
			throw new WxPayException("签名错误！");
		}
		
		$sign = $this->MakeSign($key);
		if($this->GetSign() == $sign){
			return true;
		}
		throw new WxPayException("签名错误！");
	}
	
	/**
	 * 
	 * 使用数组初始化
	 * @param array $array
	 */
	public function FromArray($array)
	{
		$this->values = $array;
	}
	
	/**
	 * 
	 * 使用数组初始化对象
	 * @param array $array
	 * @param 是否检测签名 $noCheckSign
	 */
	public static function InitFromArray($array, $noCheckSign = false)
	{
		$obj = new self();
		$obj->FromArray($array);
		if($noCheckSign == false){
			$obj->CheckSign();
		}
        return $obj;
	}
	
	/**
	 * 
	 * 设置参数
	 * @param string $key
	 * @param string $value
	 */
	public function SetData($key, $value)
	{
		$this->values[$key] = $value;
	}
	
    /**
     * 将xml转为array
     * @param string $xml
     * @throws WxPayException
     */
	public static function Init($xml,$key)
	{	
		$obj = new self();
		$obj->FromXml($xml);
		//fix bug 2015-06-29
		if($obj->values['return_code'] != 'SUCCESS'){
			 return $obj->GetValues();
		}
		$obj->CheckSign($key);
        return $obj->GetValues();
	}
}

/**
 * 
 * 回调基础类
 * @author widyhu
 *
 */
class WxPayNotifyReply extends  WxPayDataBase
{
	/**
	 * 
	 * 设置错误码 FAIL 或者 SUCCESS
	 * @param string
	 */
	public function SetReturn_code($return_code)
	{
		$this->values['return_code'] = $return_code;
	}
	
	/**
	 * 
	 * 获取错误码 FAIL 或者 SUCCESS
	 * @return string $return_code
	 */
	public function GetReturn_code()
	{
		return $this->values['return_code'];
	}

	/**
	 * 
	 * 设置错误信息
	 * @param string $return_code
	 */
	public function SetReturn_msg($return_msg)
	{
		$this->values['return_msg'] = $return_msg;
	}
	
	/**
	 * 
	 * 获取错误信息
	 * @return string
	 */
	public function GetReturn_msg()
	{
		return $this->values['return_msg'];
	}
	
	/**
	 * 
	 * 设置返回参数
	 * @param string $key
	 * @param string $value
	 */
	public function SetData($key, $value)
	{
		$this->values[$key] = $value;
	}

}

/**
 * 
 * 统一下单输入对象
 * @author widyhu
 *
 */
class WxPayUnifiedOrder extends WxPayDataBase
{	
	/**
	* 设置微信分配的公众账号ID
	* @param string $value 
	**/
	public function SetAppid($value)
	{
		$this->values['appid'] = $value;
	}
	/**
	* 获取微信分配的公众账号ID的值
	* @return 值
	**/
	public function GetAppid()
	{
		return $this->values['appid'];
	}
	/**
	* 判断微信分配的公众账号ID是否存在
	* @return true 或 false
	**/
	public function IsAppidSet()
	{
		return array_key_exists('appid', $this->values);
	}


	/**
	* 设置微信支付分配的商户号
	* @param string $value 
	**/
	public function SetMch_id($value)
	{
		$this->values['mch_id'] = $value;
	}
	/**
	* 获取微信支付分配的商户号的值
	* @return 值
	**/
	public function GetMch_id()
	{
		return $this->values['mch_id'];
	}
	/**
	* 判断微信支付分配的商户号是否存在
	* @return true 或 false
	**/
	public function IsMch_idSet()
	{
		return array_key_exists('mch_id', $this->values);
	}


	/**
	* 设置微信支付分配的终端设备号，商户自定义
	* @param string $value 
	**/
	public function SetDevice_info($value)
	{
		$this->values['device_info'] = $value;
	}
	/**
	* 获取微信支付分配的终端设备号，商户自定义的值
	* @return 值
	**/
	public function GetDevice_info()
	{
		return $this->values['device_info'];
	}
	/**
	* 判断微信支付分配的终端设备号，商户自定义是否存在
	* @return true 或 false
	**/
	public function IsDevice_infoSet()
	{
		return array_key_exists('device_info', $this->values);
	}


	/**
	* 设置随机字符串，不长于32位。推荐随机数生成算法
	* @param string $value 
	**/
	public function SetNonce_str($value)
	{
		$this->values['nonce_str'] = $value;
	}
	/**
	* 获取随机字符串，不长于32位。推荐随机数生成算法的值
	* @return 值
	**/
	public function GetNonce_str()
	{
		return $this->values['nonce_str'];
	}
	/**
	* 判断随机字符串，不长于32位。推荐随机数生成算法是否存在
	* @return true 或 false
	**/
	public function IsNonce_strSet()
	{
		return array_key_exists('nonce_str', $this->values);
	}

	/**
	* 设置商品或支付单简要描述
	* @param string $value 
	**/
	public function SetBody($value)
	{
		$this->values['body'] = $value;
	}
	/**
	* 获取商品或支付单简要描述的值
	* @return 值
	**/
	public function GetBody()
	{
		return $this->values['body'];
	}
	/**
	* 判断商品或支付单简要描述是否存在
	* @return true 或 false
	**/
	public function IsBodySet()
	{
		return array_key_exists('body', $this->values);
	}


	/**
	* 设置商品名称明细列表
	* @param string $value 
	**/
	public function SetDetail($value)
	{
		$this->values['detail'] = $value;
	}
	/**
	* 获取商品名称明细列表的值
	* @return 值
	**/
	public function GetDetail()
	{
		return $this->values['detail'];
	}
	/**
	* 判断商品名称明细列表是否存在
	* @return true 或 false
	**/
	public function IsDetailSet()
	{
		return array_key_exists('detail', $this->values);
	}


	/**
	* 设置附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据
	* @param string $value 
	**/
	public function SetAttach($value)
	{
		$this->values['attach'] = $value;
	}
	/**
	* 获取附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据的值
	* @return 值
	**/
	public function GetAttach()
	{
		return $this->values['attach'];
	}
	/**
	* 判断附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据是否存在
	* @return true 或 false
	**/
	public function IsAttachSet()
	{
		return array_key_exists('attach', $this->values);
	}


	/**
	* 设置商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号
	* @param string $value 
	**/
	public function SetOut_trade_no($value)
	{
		$this->values['out_trade_no'] = $value;
	}
	/**
	* 获取商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号的值
	* @return 值
	**/
	public function GetOut_trade_no()
	{
		return $this->values['out_trade_no'];
	}
	/**
	* 判断商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号是否存在
	* @return true 或 false
	**/
	public function IsOut_trade_noSet()
	{
		return array_key_exists('out_trade_no', $this->values);
	}


	/**
	* 设置符合ISO 4217标准的三位字母代码，默认人民币：CNY，其他值列表详见货币类型
	* @param string $value 
	**/
	public function SetFee_type($value)
	{
		$this->values['fee_type'] = $value;
	}
	/**
	* 获取符合ISO 4217标准的三位字母代码，默认人民币：CNY，其他值列表详见货币类型的值
	* @return 值
	**/
	public function GetFee_type()
	{
		return $this->values['fee_type'];
	}
	/**
	* 判断符合ISO 4217标准的三位字母代码，默认人民币：CNY，其他值列表详见货币类型是否存在
	* @return true 或 false
	**/
	public function IsFee_typeSet()
	{
		return array_key_exists('fee_type', $this->values);
	}


	/**
	* 设置订单总金额，只能为整数，详见支付金额
	* @param string $value 
	**/
	public function SetTotal_fee($value)
	{
		$this->values['total_fee'] = $value;
	}
	/**
	* 获取订单总金额，只能为整数，详见支付金额的值
	* @return 值
	**/
	public function GetTotal_fee()
	{
		return $this->values['total_fee'];
	}
	/**
	* 判断订单总金额，只能为整数，详见支付金额是否存在
	* @return true 或 false
	**/
	public function IsTotal_feeSet()
	{
		return array_key_exists('total_fee', $this->values);
	}


	/**
	* 设置APP和网页支付提交用户端ip，Native支付填调用微信支付API的机器IP。
	* @param string $value 
	**/
	public function SetSpbill_create_ip($value)
	{
		$this->values['spbill_create_ip'] = $value;
	}
	/**
	* 获取APP和网页支付提交用户端ip，Native支付填调用微信支付API的机器IP。的值
	* @return 值
	**/
	public function GetSpbill_create_ip()
	{
		return $this->values['spbill_create_ip'];
	}
	/**
	* 判断APP和网页支付提交用户端ip，Native支付填调用微信支付API的机器IP。是否存在
	* @return true 或 false
	**/
	public function IsSpbill_create_ipSet()
	{
		return array_key_exists('spbill_create_ip', $this->values);
	}


	/**
	* 设置订单生成时间，格式为yyyyMMddHHmmss，如2009年12月25日9点10分10秒表示为20091225091010。其他详见时间规则
	* @param string $value 
	**/
	public function SetTime_start($value)
	{
		$this->values['time_start'] = $value;
	}
	/**
	* 获取订单生成时间，格式为yyyyMMddHHmmss，如2009年12月25日9点10分10秒表示为20091225091010。其他详见时间规则的值
	* @return 值
	**/
	public function GetTime_start()
	{
		return $this->values['time_start'];
	}
	/**
	* 判断订单生成时间，格式为yyyyMMddHHmmss，如2009年12月25日9点10分10秒表示为20091225091010。其他详见时间规则是否存在
	* @return true 或 false
	**/
	public function IsTime_startSet()
	{
		return array_key_exists('time_start', $this->values);
	}


	/**
	* 设置订单失效时间，格式为yyyyMMddHHmmss，如2009年12月27日9点10分10秒表示为20091227091010。其他详见时间规则
	* @param string $value 
	**/
	public function SetTime_expire($value)
	{
		$this->values['time_expire'] = $value;
	}
	/**
	* 获取订单失效时间，格式为yyyyMMddHHmmss，如2009年12月27日9点10分10秒表示为20091227091010。其他详见时间规则的值
	* @return 值
	**/
	public function GetTime_expire()
	{
		return $this->values['time_expire'];
	}
	/**
	* 判断订单失效时间，格式为yyyyMMddHHmmss，如2009年12月27日9点10分10秒表示为20091227091010。其他详见时间规则是否存在
	* @return true 或 false
	**/
	public function IsTime_expireSet()
	{
		return array_key_exists('time_expire', $this->values);
	}


	/**
	* 设置商品标记，代金券或立减优惠功能的参数，说明详见代金券或立减优惠
	* @param string $value 
	**/
	public function SetGoods_tag($value)
	{
		$this->values['goods_tag'] = $value;
	}
	/**
	* 获取商品标记，代金券或立减优惠功能的参数，说明详见代金券或立减优惠的值
	* @return 值
	**/
	public function GetGoods_tag()
	{
		return $this->values['goods_tag'];
	}
	/**
	* 判断商品标记，代金券或立减优惠功能的参数，说明详见代金券或立减优惠是否存在
	* @return true 或 false
	**/
	public function IsGoods_tagSet()
	{
		return array_key_exists('goods_tag', $this->values);
	}


	/**
	* 设置接收微信支付异步通知回调地址
	* @param string $value 
	**/
	public function SetNotify_url($value)
	{
		$this->values['notify_url'] = $value;
	}
	/**
	* 获取接收微信支付异步通知回调地址的值
	* @return 值
	**/
	public function GetNotify_url()
	{
		return $this->values['notify_url'];
	}
	/**
	* 判断接收微信支付异步通知回调地址是否存在
	* @return true 或 false
	**/
	public function IsNotify_urlSet()
	{
		return array_key_exists('notify_url', $this->values);
	}


	/**
	* 设置取值如下：JSAPI，NATIVE，APP，详细说明见参数规定
	* @param string $value 
	**/
	public function SetTrade_type($value)
	{
		$this->values['trade_type'] = $value;
	}
	/**
	* 获取取值如下：JSAPI，NATIVE，APP，详细说明见参数规定的值
	* @return 值
	**/
	public function GetTrade_type()
	{
		return $this->values['trade_type'];
	}
	/**
	* 判断取值如下：JSAPI，NATIVE，APP，详细说明见参数规定是否存在
	* @return true 或 false
	**/
	public function IsTrade_typeSet()
	{
		return array_key_exists('trade_type', $this->values);
	}


	/**
	* 设置trade_type=NATIVE，此参数必传。此id为二维码中包含的商品ID，商户自行定义。
	* @param string $value 
	**/
	public function SetProduct_id($value)
	{
		$this->values['product_id'] = $value;
	}
	/**
	* 获取trade_type=NATIVE，此参数必传。此id为二维码中包含的商品ID，商户自行定义。的值
	* @return 值
	**/
	public function GetProduct_id()
	{
		return $this->values['product_id'];
	}
	/**
	* 判断trade_type=NATIVE，此参数必传。此id为二维码中包含的商品ID，商户自行定义。是否存在
	* @return true 或 false
	**/
	public function IsProduct_idSet()
	{
		return array_key_exists('product_id', $this->values);
	}


	/**
	* 设置trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识。下单前需要调用【网页授权获取用户信息】接口获取到用户的Openid。 
	* @param string $value 
	**/
	public function SetOpenid($value)
	{
		$this->values['openid'] = $value;
	}
	/**
	* 获取trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识。下单前需要调用【网页授权获取用户信息】接口获取到用户的Openid。 的值
	* @return 值
	**/
	public function GetOpenid()
	{
		return $this->values['openid'];
	}
	/**
	* 判断trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识。下单前需要调用【网页授权获取用户信息】接口获取到用户的Openid。 是否存在
	* @return true 或 false
	**/
	public function IsOpenidSet()
	{
		return array_key_exists('openid', $this->values);
	}
}

/**
 * 
 * 订单查询输入对象
 * @author widyhu
 *
 */
class WxPayOrderQuery extends WxPayDataBase
{
	/**
	* 设置微信分配的公众账号ID
	* @param string $value 
	**/
	public function SetAppid($value)
	{
		$this->values['appid'] = $value;
	}
	/**
	* 获取微信分配的公众账号ID的值
	* @return 值
	**/
	public function GetAppid()
	{
		return $this->values['appid'];
	}
	/**
	* 判断微信分配的公众账号ID是否存在
	* @return true 或 false
	**/
	public function IsAppidSet()
	{
		return array_key_exists('appid', $this->values);
	}


	/**
	* 设置微信支付分配的商户号
	* @param string $value 
	**/
	public function SetMch_id($value)
	{
		$this->values['mch_id'] = $value;
	}
	/**
	* 获取微信支付分配的商户号的值
	* @return 值
	**/
	public function GetMch_id()
	{
		return $this->values['mch_id'];
	}
	/**
	* 判断微信支付分配的商户号是否存在
	* @return true 或 false
	**/
	public function IsMch_idSet()
	{
		return array_key_exists('mch_id', $this->values);
	}


	/**
	* 设置微信的订单号，优先使用
	* @param string $value 
	**/
	public function SetTransaction_id($value)
	{
		$this->values['transaction_id'] = $value;
	}
	/**
	* 获取微信的订单号，优先使用的值
	* @return 值
	**/
	public function GetTransaction_id()
	{
		return $this->values['transaction_id'];
	}
	/**
	* 判断微信的订单号，优先使用是否存在
	* @return true 或 false
	**/
	public function IsTransaction_idSet()
	{
		return array_key_exists('transaction_id', $this->values);
	}


	/**
	* 设置商户系统内部的订单号，当没提供transaction_id时需要传这个。
	* @param string $value 
	**/
	public function SetOut_trade_no($value)
	{
		$this->values['out_trade_no'] = $value;
	}
	/**
	* 获取商户系统内部的订单号，当没提供transaction_id时需要传这个。的值
	* @return 值
	**/
	public function GetOut_trade_no()
	{
		return $this->values['out_trade_no'];
	}
	/**
	* 判断商户系统内部的订单号，当没提供transaction_id时需要传这个。是否存在
	* @return true 或 false
	**/
	public function IsOut_trade_noSet()
	{
		return array_key_exists('out_trade_no', $this->values);
	}


	/**
	* 设置随机字符串，不长于32位。推荐随机数生成算法
	* @param string $value 
	**/
	public function SetNonce_str($value)
	{
		$this->values['nonce_str'] = $value;
	}
	/**
	* 获取随机字符串，不长于32位。推荐随机数生成算法的值
	* @return 值
	**/
	public function GetNonce_str()
	{
		return $this->values['nonce_str'];
	}
	/**
	* 判断随机字符串，不长于32位。推荐随机数生成算法是否存在
	* @return true 或 false
	**/
	public function IsNonce_strSet()
	{
		return array_key_exists('nonce_str', $this->values);
	}
}

/**
 * 
 * 关闭订单输入对象
 * @author widyhu
 *
 */
class WxPayCloseOrder extends WxPayDataBase
{
	/**
	* 设置微信分配的公众账号ID
	* @param string $value 
	**/
	public function SetAppid($value)
	{
		$this->values['appid'] = $value;
	}
	/**
	* 获取微信分配的公众账号ID的值
	* @return 值
	**/
	public function GetAppid()
	{
		return $this->values['appid'];
	}
	/**
	* 判断微信分配的公众账号ID是否存在
	* @return true 或 false
	**/
	public function IsAppidSet()
	{
		return array_key_exists('appid', $this->values);
	}


	/**
	* 设置微信支付分配的商户号
	* @param string $value 
	**/
	public function SetMch_id($value)
	{
		$this->values['mch_id'] = $value;
	}
	/**
	* 获取微信支付分配的商户号的值
	* @return 值
	**/
	public function GetMch_id()
	{
		return $this->values['mch_id'];
	}
	/**
	* 判断微信支付分配的商户号是否存在
	* @return true 或 false
	**/
	public function IsMch_idSet()
	{
		return array_key_exists('mch_id', $this->values);
	}


	/**
	* 设置商户系统内部的订单号
	* @param string $value 
	**/
	public function SetOut_trade_no($value)
	{
		$this->values['out_trade_no'] = $value;
	}
	/**
	* 获取商户系统内部的订单号的值
	* @return 值
	**/
	public function GetOut_trade_no()
	{
		return $this->values['out_trade_no'];
	}
	/**
	* 判断商户系统内部的订单号是否存在
	* @return true 或 false
	**/
	public function IsOut_trade_noSet()
	{
		return array_key_exists('out_trade_no', $this->values);
	}


	/**
	* 设置商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号
	* @param string $value 
	**/
	public function SetNonce_str($value)
	{
		$this->values['nonce_str'] = $value;
	}
	/**
	* 获取商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号的值
	* @return 值
	**/
	public function GetNonce_str()
	{
		return $this->values['nonce_str'];
	}
	/**
	* 判断商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号是否存在
	* @return true 或 false
	**/
	public function IsNonce_strSet()
	{
		return array_key_exists('nonce_str', $this->values);
	}
}

/**
 * 
 * 提交退款输入对象
 * @author widyhu
 *
 */
class WxPayRefund extends WxPayDataBase
{
	/**
	* 设置微信分配的公众账号ID
	* @param string $value 
	**/
	public function SetAppid($value)
	{
		$this->values['appid'] = $value;
	}
	/**
	* 获取微信分配的公众账号ID的值
	* @return 值
	**/
	public function GetAppid()
	{
		return $this->values['appid'];
	}
	/**
	* 判断微信分配的公众账号ID是否存在
	* @return true 或 false
	**/
	public function IsAppidSet()
	{
		return array_key_exists('appid', $this->values);
	}


	/**
	* 设置微信支付分配的商户号
	* @param string $value 
	**/
	public function SetMch_id($value)
	{
		$this->values['mch_id'] = $value;
	}
	/**
	* 获取微信支付分配的商户号的值
	* @return 值
	**/
	public function GetMch_id()
	{
		return $this->values['mch_id'];
	}
	/**
	* 判断微信支付分配的商户号是否存在
	* @return true 或 false
	**/
	public function IsMch_idSet()
	{
		return array_key_exists('mch_id', $this->values);
	}


	/**
	* 设置微信支付分配的终端设备号，与下单一致
	* @param string $value 
	**/
	public function SetDevice_info($value)
	{
		$this->values['device_info'] = $value;
	}
	/**
	* 获取微信支付分配的终端设备号，与下单一致的值
	* @return 值
	**/
	public function GetDevice_info()
	{
		return $this->values['device_info'];
	}
	/**
	* 判断微信支付分配的终端设备号，与下单一致是否存在
	* @return true 或 false
	**/
	public function IsDevice_infoSet()
	{
		return array_key_exists('device_info', $this->values);
	}


	/**
	* 设置随机字符串，不长于32位。推荐随机数生成算法
	* @param string $value 
	**/
	public function SetNonce_str($value)
	{
		$this->values['nonce_str'] = $value;
	}
	/**
	* 获取随机字符串，不长于32位。推荐随机数生成算法的值
	* @return 值
	**/
	public function GetNonce_str()
	{
		return $this->values['nonce_str'];
	}
	/**
	* 判断随机字符串，不长于32位。推荐随机数生成算法是否存在
	* @return true 或 false
	**/
	public function IsNonce_strSet()
	{
		return array_key_exists('nonce_str', $this->values);
	}

	/**
	* 设置微信订单号
	* @param string $value 
	**/
	public function SetTransaction_id($value)
	{
		$this->values['transaction_id'] = $value;
	}
	/**
	* 获取微信订单号的值
	* @return 值
	**/
	public function GetTransaction_id()
	{
		return $this->values['transaction_id'];
	}
	/**
	* 判断微信订单号是否存在
	* @return true 或 false
	**/
	public function IsTransaction_idSet()
	{
		return array_key_exists('transaction_id', $this->values);
	}


	/**
	* 设置商户系统内部的订单号,transaction_id、out_trade_no二选一，如果同时存在优先级：transaction_id> out_trade_no
	* @param string $value 
	**/
	public function SetOut_trade_no($value)
	{
		$this->values['out_trade_no'] = $value;
	}
	/**
	* 获取商户系统内部的订单号,transaction_id、out_trade_no二选一，如果同时存在优先级：transaction_id> out_trade_no的值
	* @return 值
	**/
	public function GetOut_trade_no()
	{
		return $this->values['out_trade_no'];
	}
	/**
	* 判断商户系统内部的订单号,transaction_id、out_trade_no二选一，如果同时存在优先级：transaction_id> out_trade_no是否存在
	* @return true 或 false
	**/
	public function IsOut_trade_noSet()
	{
		return array_key_exists('out_trade_no', $this->values);
	}


	/**
	* 设置商户系统内部的退款单号，商户系统内部唯一，同一退款单号多次请求只退一笔
	* @param string $value 
	**/
	public function SetOut_refund_no($value)
	{
		$this->values['out_refund_no'] = $value;
	}
	/**
	* 获取商户系统内部的退款单号，商户系统内部唯一，同一退款单号多次请求只退一笔的值
	* @return 值
	**/
	public function GetOut_refund_no()
	{
		return $this->values['out_refund_no'];
	}
	/**
	* 判断商户系统内部的退款单号，商户系统内部唯一，同一退款单号多次请求只退一笔是否存在
	* @return true 或 false
	**/
	public function IsOut_refund_noSet()
	{
		return array_key_exists('out_refund_no', $this->values);
	}


	/**
	* 设置订单总金额，单位为分，只能为整数，详见支付金额
	* @param string $value 
	**/
	public function SetTotal_fee($value)
	{
		$this->values['total_fee'] = $value;
	}
	/**
	* 获取订单总金额，单位为分，只能为整数，详见支付金额的值
	* @return 值
	**/
	public function GetTotal_fee()
	{
		return $this->values['total_fee'];
	}
	/**
	* 判断订单总金额，单位为分，只能为整数，详见支付金额是否存在
	* @return true 或 false
	**/
	public function IsTotal_feeSet()
	{
		return array_key_exists('total_fee', $this->values);
	}


	/**
	* 设置退款总金额，订单总金额，单位为分，只能为整数，详见支付金额
	* @param string $value 
	**/
	public function SetRefund_fee($value)
	{
		$this->values['refund_fee'] = $value;
	}
	/**
	* 获取退款总金额，订单总金额，单位为分，只能为整数，详见支付金额的值
	* @return 值
	**/
	public function GetRefund_fee()
	{
		return $this->values['refund_fee'];
	}
	/**
	* 判断退款总金额，订单总金额，单位为分，只能为整数，详见支付金额是否存在
	* @return true 或 false
	**/
	public function IsRefund_feeSet()
	{
		return array_key_exists('refund_fee', $this->values);
	}


	/**
	* 设置货币类型，符合ISO 4217标准的三位字母代码，默认人民币：CNY，其他值列表详见货币类型
	* @param string $value 
	**/
	public function SetRefund_fee_type($value)
	{
		$this->values['refund_fee_type'] = $value;
	}
	/**
	* 获取货币类型，符合ISO 4217标准的三位字母代码，默认人民币：CNY，其他值列表详见货币类型的值
	* @return 值
	**/
	public function GetRefund_fee_type()
	{
		return $this->values['refund_fee_type'];
	}
	/**
	* 判断货币类型，符合ISO 4217标准的三位字母代码，默认人民币：CNY，其他值列表详见货币类型是否存在
	* @return true 或 false
	**/
	public function IsRefund_fee_typeSet()
	{
		return array_key_exists('refund_fee_type', $this->values);
	}


	/**
	* 设置操作员帐号, 默认为商户号
	* @param string $value 
	**/
	public function SetOp_user_id($value)
	{
		$this->values['op_user_id'] = $value;
	}
	/**
	* 获取操作员帐号, 默认为商户号的值
	* @return 值
	**/
	public function GetOp_user_id()
	{
		return $this->values['op_user_id'];
	}
	/**
	* 判断操作员帐号, 默认为商户号是否存在
	* @return true 或 false
	**/
	public function IsOp_user_idSet()
	{
		return array_key_exists('op_user_id', $this->values);
	}
}

/**
 * 
 * 退款查询输入对象
 * @author widyhu
 *
 */
class WxPayRefundQuery extends WxPayDataBase
{
	/**
	* 设置微信分配的公众账号ID
	* @param string $value 
	**/
	public function SetAppid($value)
	{
		$this->values['appid'] = $value;
	}
	/**
	* 获取微信分配的公众账号ID的值
	* @return 值
	**/
	public function GetAppid()
	{
		return $this->values['appid'];
	}
	/**
	* 判断微信分配的公众账号ID是否存在
	* @return true 或 false
	**/
	public function IsAppidSet()
	{
		return array_key_exists('appid', $this->values);
	}


	/**
	* 设置微信支付分配的商户号
	* @param string $value 
	**/
	public function SetMch_id($value)
	{
		$this->values['mch_id'] = $value;
	}
	/**
	* 获取微信支付分配的商户号的值
	* @return 值
	**/
	public function GetMch_id()
	{
		return $this->values['mch_id'];
	}
	/**
	* 判断微信支付分配的商户号是否存在
	* @return true 或 false
	**/
	public function IsMch_idSet()
	{
		return array_key_exists('mch_id', $this->values);
	}


	/**
	* 设置微信支付分配的终端设备号
	* @param string $value 
	**/
	public function SetDevice_info($value)
	{
		$this->values['device_info'] = $value;
	}
	/**
	* 获取微信支付分配的终端设备号的值
	* @return 值
	**/
	public function GetDevice_info()
	{
		return $this->values['device_info'];
	}
	/**
	* 判断微信支付分配的终端设备号是否存在
	* @return true 或 false
	**/
	public function IsDevice_infoSet()
	{
		return array_key_exists('device_info', $this->values);
	}


	/**
	* 设置随机字符串，不长于32位。推荐随机数生成算法
	* @param string $value 
	**/
	public function SetNonce_str($value)
	{
		$this->values['nonce_str'] = $value;
	}
	/**
	* 获取随机字符串，不长于32位。推荐随机数生成算法的值
	* @return 值
	**/
	public function GetNonce_str()
	{
		return $this->values['nonce_str'];
	}
	/**
	* 判断随机字符串，不长于32位。推荐随机数生成算法是否存在
	* @return true 或 false
	**/
	public function IsNonce_strSet()
	{
		return array_key_exists('nonce_str', $this->values);
	}

	/**
	* 设置微信订单号
	* @param string $value 
	**/
	public function SetTransaction_id($value)
	{
		$this->values['transaction_id'] = $value;
	}
	/**
	* 获取微信订单号的值
	* @return 值
	**/
	public function GetTransaction_id()
	{
		return $this->values['transaction_id'];
	}
	/**
	* 判断微信订单号是否存在
	* @return true 或 false
	**/
	public function IsTransaction_idSet()
	{
		return array_key_exists('transaction_id', $this->values);
	}


	/**
	* 设置商户系统内部的订单号
	* @param string $value 
	**/
	public function SetOut_trade_no($value)
	{
		$this->values['out_trade_no'] = $value;
	}
	/**
	* 获取商户系统内部的订单号的值
	* @return 值
	**/
	public function GetOut_trade_no()
	{
		return $this->values['out_trade_no'];
	}
	/**
	* 判断商户系统内部的订单号是否存在
	* @return true 或 false
	**/
	public function IsOut_trade_noSet()
	{
		return array_key_exists('out_trade_no', $this->values);
	}


	/**
	* 设置商户退款单号
	* @param string $value 
	**/
	public function SetOut_refund_no($value)
	{
		$this->values['out_refund_no'] = $value;
	}
	/**
	* 获取商户退款单号的值
	* @return 值
	**/
	public function GetOut_refund_no()
	{
		return $this->values['out_refund_no'];
	}
	/**
	* 判断商户退款单号是否存在
	* @return true 或 false
	**/
	public function IsOut_refund_noSet()
	{
		return array_key_exists('out_refund_no', $this->values);
	}


	/**
	* 设置微信退款单号refund_id、out_refund_no、out_trade_no、transaction_id四个参数必填一个，如果同时存在优先级为：refund_id>out_refund_no>transaction_id>out_trade_no
	* @param string $value 
	**/
	public function SetRefund_id($value)
	{
		$this->values['refund_id'] = $value;
	}
	/**
	* 获取微信退款单号refund_id、out_refund_no、out_trade_no、transaction_id四个参数必填一个，如果同时存在优先级为：refund_id>out_refund_no>transaction_id>out_trade_no的值
	* @return 值
	**/
	public function GetRefund_id()
	{
		return $this->values['refund_id'];
	}
	/**
	* 判断微信退款单号refund_id、out_refund_no、out_trade_no、transaction_id四个参数必填一个，如果同时存在优先级为：refund_id>out_refund_no>transaction_id>out_trade_no是否存在
	* @return true 或 false
	**/
	public function IsRefund_idSet()
	{
		return array_key_exists('refund_id', $this->values);
	}
}

/**
 * 
 * 下载对账单输入对象
 * @author widyhu
 *
 */
class WxPayDownloadBill extends WxPayDataBase
{
	/**
	* 设置微信分配的公众账号ID
	* @param string $value 
	**/
	public function SetAppid($value)
	{
		$this->values['appid'] = $value;
	}
	/**
	* 获取微信分配的公众账号ID的值
	* @return 值
	**/
	public function GetAppid()
	{
		return $this->values['appid'];
	}
	/**
	* 判断微信分配的公众账号ID是否存在
	* @return true 或 false
	**/
	public function IsAppidSet()
	{
		return array_key_exists('appid', $this->values);
	}


	/**
	* 设置微信支付分配的商户号
	* @param string $value 
	**/
	public function SetMch_id($value)
	{
		$this->values['mch_id'] = $value;
	}
	/**
	* 获取微信支付分配的商户号的值
	* @return 值
	**/
	public function GetMch_id()
	{
		return $this->values['mch_id'];
	}
	/**
	* 判断微信支付分配的商户号是否存在
	* @return true 或 false
	**/
	public function IsMch_idSet()
	{
		return array_key_exists('mch_id', $this->values);
	}


	/**
	* 设置微信支付分配的终端设备号，填写此字段，只下载该设备号的对账单
	* @param string $value 
	**/
	public function SetDevice_info($value)
	{
		$this->values['device_info'] = $value;
	}
	/**
	* 获取微信支付分配的终端设备号，填写此字段，只下载该设备号的对账单的值
	* @return 值
	**/
	public function GetDevice_info()
	{
		return $this->values['device_info'];
	}
	/**
	* 判断微信支付分配的终端设备号，填写此字段，只下载该设备号的对账单是否存在
	* @return true 或 false
	**/
	public function IsDevice_infoSet()
	{
		return array_key_exists('device_info', $this->values);
	}


	/**
	* 设置随机字符串，不长于32位。推荐随机数生成算法
	* @param string $value 
	**/
	public function SetNonce_str($value)
	{
		$this->values['nonce_str'] = $value;
	}
	/**
	* 获取随机字符串，不长于32位。推荐随机数生成算法的值
	* @return 值
	**/
	public function GetNonce_str()
	{
		return $this->values['nonce_str'];
	}
	/**
	* 判断随机字符串，不长于32位。推荐随机数生成算法是否存在
	* @return true 或 false
	**/
	public function IsNonce_strSet()
	{
		return array_key_exists('nonce_str', $this->values);
	}

	/**
	* 设置下载对账单的日期，格式：20140603
	* @param string $value 
	**/
	public function SetBill_date($value)
	{
		$this->values['bill_date'] = $value;
	}
	/**
	* 获取下载对账单的日期，格式：20140603的值
	* @return 值
	**/
	public function GetBill_date()
	{
		return $this->values['bill_date'];
	}
	/**
	* 判断下载对账单的日期，格式：20140603是否存在
	* @return true 或 false
	**/
	public function IsBill_dateSet()
	{
		return array_key_exists('bill_date', $this->values);
	}


	/**
	* 设置ALL，返回当日所有订单信息，默认值SUCCESS，返回当日成功支付的订单REFUND，返回当日退款订单REVOKED，已撤销的订单
	* @param string $value 
	**/
	public function SetBill_type($value)
	{
		$this->values['bill_type'] = $value;
	}
	/**
	* 获取ALL，返回当日所有订单信息，默认值SUCCESS，返回当日成功支付的订单REFUND，返回当日退款订单REVOKED，已撤销的订单的值
	* @return 值
	**/
	public function GetBill_type()
	{
		return $this->values['bill_type'];
	}
	/**
	* 判断ALL，返回当日所有订单信息，默认值SUCCESS，返回当日成功支付的订单REFUND，返回当日退款订单REVOKED，已撤销的订单是否存在
	* @return true 或 false
	**/
	public function IsBill_typeSet()
	{
		return array_key_exists('bill_type', $this->values);
	}
}

/**
 * 
 * 测速上报输入对象
 * @author widyhu
 *
 */
class WxPayReport extends WxPayDataBase
{
	/**
	* 设置微信分配的公众账号ID
	* @param string $value 
	**/
	public function SetAppid($value)
	{
		$this->values['appid'] = $value;
	}
	/**
	* 获取微信分配的公众账号ID的值
	* @return 值
	**/
	public function GetAppid()
	{
		return $this->values['appid'];
	}
	/**
	* 判断微信分配的公众账号ID是否存在
	* @return true 或 false
	**/
	public function IsAppidSet()
	{
		return array_key_exists('appid', $this->values);
	}


	/**
	* 设置微信支付分配的商户号
	* @param string $value 
	**/
	public function SetMch_id($value)
	{
		$this->values['mch_id'] = $value;
	}
	/**
	* 获取微信支付分配的商户号的值
	* @return 值
	**/
	public function GetMch_id()
	{
		return $this->values['mch_id'];
	}
	/**
	* 判断微信支付分配的商户号是否存在
	* @return true 或 false
	**/
	public function IsMch_idSet()
	{
		return array_key_exists('mch_id', $this->values);
	}


	/**
	* 设置微信支付分配的终端设备号，商户自定义
	* @param string $value 
	**/
	public function SetDevice_info($value)
	{
		$this->values['device_info'] = $value;
	}
	/**
	* 获取微信支付分配的终端设备号，商户自定义的值
	* @return 值
	**/
	public function GetDevice_info()
	{
		return $this->values['device_info'];
	}
	/**
	* 判断微信支付分配的终端设备号，商户自定义是否存在
	* @return true 或 false
	**/
	public function IsDevice_infoSet()
	{
		return array_key_exists('device_info', $this->values);
	}


	/**
	* 设置随机字符串，不长于32位。推荐随机数生成算法
	* @param string $value 
	**/
	public function SetNonce_str($value)
	{
		$this->values['nonce_str'] = $value;
	}
	/**
	* 获取随机字符串，不长于32位。推荐随机数生成算法的值
	* @return 值
	**/
	public function GetNonce_str()
	{
		return $this->values['nonce_str'];
	}
	/**
	* 判断随机字符串，不长于32位。推荐随机数生成算法是否存在
	* @return true 或 false
	**/
	public function IsNonce_strSet()
	{
		return array_key_exists('nonce_str', $this->values);
	}


	/**
	* 设置上报对应的接口的完整URL，类似：https://api.mch.weixin.qq.com/pay/unifiedorder对于被扫支付，为更好的和商户共同分析一次业务行为的整体耗时情况，对于两种接入模式，请都在门店侧对一次被扫行为进行一次单独的整体上报，上报URL指定为：https://api.mch.weixin.qq.com/pay/micropay/total关于两种接入模式具体可参考本文档章节：被扫支付商户接入模式其它接口调用仍然按照调用一次，上报一次来进行。
	* @param string $value 
	**/
	public function SetInterface_url($value)
	{
		$this->values['interface_url'] = $value;
	}
	/**
	* 获取上报对应的接口的完整URL，类似：https://api.mch.weixin.qq.com/pay/unifiedorder对于被扫支付，为更好的和商户共同分析一次业务行为的整体耗时情况，对于两种接入模式，请都在门店侧对一次被扫行为进行一次单独的整体上报，上报URL指定为：https://api.mch.weixin.qq.com/pay/micropay/total关于两种接入模式具体可参考本文档章节：被扫支付商户接入模式其它接口调用仍然按照调用一次，上报一次来进行。的值
	* @return 值
	**/
	public function GetInterface_url()
	{
		return $this->values['interface_url'];
	}
	/**
	* 判断上报对应的接口的完整URL，类似：https://api.mch.weixin.qq.com/pay/unifiedorder对于被扫支付，为更好的和商户共同分析一次业务行为的整体耗时情况，对于两种接入模式，请都在门店侧对一次被扫行为进行一次单独的整体上报，上报URL指定为：https://api.mch.weixin.qq.com/pay/micropay/total关于两种接入模式具体可参考本文档章节：被扫支付商户接入模式其它接口调用仍然按照调用一次，上报一次来进行。是否存在
	* @return true 或 false
	**/
	public function IsInterface_urlSet()
	{
		return array_key_exists('interface_url', $this->values);
	}


	/**
	* 设置接口耗时情况，单位为毫秒
	* @param string $value 
	**/
	public function SetExecute_time_($value)
	{
		$this->values['execute_time_'] = $value;
	}
	/**
	* 获取接口耗时情况，单位为毫秒的值
	* @return 值
	**/
	public function GetExecute_time_()
	{
		return $this->values['execute_time_'];
	}
	/**
	* 判断接口耗时情况，单位为毫秒是否存在
	* @return true 或 false
	**/
	public function IsExecute_time_Set()
	{
		return array_key_exists('execute_time_', $this->values);
	}


	/**
	* 设置SUCCESS/FAIL此字段是通信标识，非交易标识，交易是否成功需要查看trade_state来判断
	* @param string $value 
	**/
	public function SetReturn_code($value)
	{
		$this->values['return_code'] = $value;
	}
	/**
	* 获取SUCCESS/FAIL此字段是通信标识，非交易标识，交易是否成功需要查看trade_state来判断的值
	* @return 值
	**/
	public function GetReturn_code()
	{
		return $this->values['return_code'];
	}
	/**
	* 判断SUCCESS/FAIL此字段是通信标识，非交易标识，交易是否成功需要查看trade_state来判断是否存在
	* @return true 或 false
	**/
	public function IsReturn_codeSet()
	{
		return array_key_exists('return_code', $this->values);
	}


	/**
	* 设置返回信息，如非空，为错误原因签名失败参数格式校验错误
	* @param string $value 
	**/
	public function SetReturn_msg($value)
	{
		$this->values['return_msg'] = $value;
	}
	/**
	* 获取返回信息，如非空，为错误原因签名失败参数格式校验错误的值
	* @return 值
	**/
	public function GetReturn_msg()
	{
		return $this->values['return_msg'];
	}
	/**
	* 判断返回信息，如非空，为错误原因签名失败参数格式校验错误是否存在
	* @return true 或 false
	**/
	public function IsReturn_msgSet()
	{
		return array_key_exists('return_msg', $this->values);
	}


	/**
	* 设置SUCCESS/FAIL
	* @param string $value 
	**/
	public function SetResult_code($value)
	{
		$this->values['result_code'] = $value;
	}
	/**
	* 获取SUCCESS/FAIL的值
	* @return 值
	**/
	public function GetResult_code()
	{
		return $this->values['result_code'];
	}
	/**
	* 判断SUCCESS/FAIL是否存在
	* @return true 或 false
	**/
	public function IsResult_codeSet()
	{
		return array_key_exists('result_code', $this->values);
	}


	/**
	* 设置ORDERNOTEXIST—订单不存在SYSTEMERROR—系统错误
	* @param string $value 
	**/
	public function SetErr_code($value)
	{
		$this->values['err_code'] = $value;
	}
	/**
	* 获取ORDERNOTEXIST—订单不存在SYSTEMERROR—系统错误的值
	* @return 值
	**/
	public function GetErr_code()
	{
		return $this->values['err_code'];
	}
	/**
	* 判断ORDERNOTEXIST—订单不存在SYSTEMERROR—系统错误是否存在
	* @return true 或 false
	**/
	public function IsErr_codeSet()
	{
		return array_key_exists('err_code', $this->values);
	}


	/**
	* 设置结果信息描述
	* @param string $value 
	**/
	public function SetErr_code_des($value)
	{
		$this->values['err_code_des'] = $value;
	}
	/**
	* 获取结果信息描述的值
	* @return 值
	**/
	public function GetErr_code_des()
	{
		return $this->values['err_code_des'];
	}
	/**
	* 判断结果信息描述是否存在
	* @return true 或 false
	**/
	public function IsErr_code_desSet()
	{
		return array_key_exists('err_code_des', $this->values);
	}


	/**
	* 设置商户系统内部的订单号,商户可以在上报时提供相关商户订单号方便微信支付更好的提高服务质量。 
	* @param string $value 
	**/
	public function SetOut_trade_no($value)
	{
		$this->values['out_trade_no'] = $value;
	}
	/**
	* 获取商户系统内部的订单号,商户可以在上报时提供相关商户订单号方便微信支付更好的提高服务质量。 的值
	* @return 值
	**/
	public function GetOut_trade_no()
	{
		return $this->values['out_trade_no'];
	}
	/**
	* 判断商户系统内部的订单号,商户可以在上报时提供相关商户订单号方便微信支付更好的提高服务质量。 是否存在
	* @return true 或 false
	**/
	public function IsOut_trade_noSet()
	{
		return array_key_exists('out_trade_no', $this->values);
	}


	/**
	* 设置发起接口调用时的机器IP 
	* @param string $value 
	**/
	public function SetUser_ip($value)
	{
		$this->values['user_ip'] = $value;
	}
	/**
	* 获取发起接口调用时的机器IP 的值
	* @return 值
	**/
	public function GetUser_ip()
	{
		return $this->values['user_ip'];
	}
	/**
	* 判断发起接口调用时的机器IP 是否存在
	* @return true 或 false
	**/
	public function IsUser_ipSet()
	{
		return array_key_exists('user_ip', $this->values);
	}


	/**
	* 设置系统时间，格式为yyyyMMddHHmmss，如2009年12月27日9点10分10秒表示为20091227091010。其他详见时间规则
	* @param string $value 
	**/
	public function SetTime($value)
	{
		$this->values['time'] = $value;
	}
	/**
	* 获取系统时间，格式为yyyyMMddHHmmss，如2009年12月27日9点10分10秒表示为20091227091010。其他详见时间规则的值
	* @return 值
	**/
	public function GetTime()
	{
		return $this->values['time'];
	}
	/**
	* 判断系统时间，格式为yyyyMMddHHmmss，如2009年12月27日9点10分10秒表示为20091227091010。其他详见时间规则是否存在
	* @return true 或 false
	**/
	public function IsTimeSet()
	{
		return array_key_exists('time', $this->values);
	}
}

/**
 * 
 * 短链转换输入对象
 * @author widyhu
 *
 */
class WxPayShortUrl extends WxPayDataBase
{
	/**
	* 设置微信分配的公众账号ID
	* @param string $value 
	**/
	public function SetAppid($value)
	{
		$this->values['appid'] = $value;
	}
	/**
	* 获取微信分配的公众账号ID的值
	* @return 值
	**/
	public function GetAppid()
	{
		return $this->values['appid'];
	}
	/**
	* 判断微信分配的公众账号ID是否存在
	* @return true 或 false
	**/
	public function IsAppidSet()
	{
		return array_key_exists('appid', $this->values);
	}


	/**
	* 设置微信支付分配的商户号
	* @param string $value 
	**/
	public function SetMch_id($value)
	{
		$this->values['mch_id'] = $value;
	}
	/**
	* 获取微信支付分配的商户号的值
	* @return 值
	**/
	public function GetMch_id()
	{
		return $this->values['mch_id'];
	}
	/**
	* 判断微信支付分配的商户号是否存在
	* @return true 或 false
	**/
	public function IsMch_idSet()
	{
		return array_key_exists('mch_id', $this->values);
	}


	/**
	* 设置需要转换的URL，签名用原串，传输需URL encode
	* @param string $value 
	**/
	public function SetLong_url($value)
	{
		$this->values['long_url'] = $value;
	}
	/**
	* 获取需要转换的URL，签名用原串，传输需URL encode的值
	* @return 值
	**/
	public function GetLong_url()
	{
		return $this->values['long_url'];
	}
	/**
	* 判断需要转换的URL，签名用原串，传输需URL encode是否存在
	* @return true 或 false
	**/
	public function IsLong_urlSet()
	{
		return array_key_exists('long_url', $this->values);
	}


	/**
	* 设置随机字符串，不长于32位。推荐随机数生成算法
	* @param string $value 
	**/
	public function SetNonce_str($value)
	{
		$this->values['nonce_str'] = $value;
	}
	/**
	* 获取随机字符串，不长于32位。推荐随机数生成算法的值
	* @return 值
	**/
	public function GetNonce_str()
	{
		return $this->values['nonce_str'];
	}
	/**
	* 判断随机字符串，不长于32位。推荐随机数生成算法是否存在
	* @return true 或 false
	**/
	public function IsNonce_strSet()
	{
		return array_key_exists('nonce_str', $this->values);
	}
}

/**
 * 
 * 提交被扫输入对象
 * @author widyhu
 *
 */
class WxPayMicroPay extends WxPayDataBase
{
	/**
	* 设置微信分配的公众账号ID
	* @param string $value 
	**/
	public function SetAppid($value)
	{
		$this->values['appid'] = $value;
	}
	/**
	* 获取微信分配的公众账号ID的值
	* @return 值
	**/
	public function GetAppid()
	{
		return $this->values['appid'];
	}
	/**
	* 判断微信分配的公众账号ID是否存在
	* @return true 或 false
	**/
	public function IsAppidSet()
	{
		return array_key_exists('appid', $this->values);
	}


	/**
	* 设置微信支付分配的商户号
	* @param string $value 
	**/
	public function SetMch_id($value)
	{
		$this->values['mch_id'] = $value;
	}
	/**
	* 获取微信支付分配的商户号的值
	* @return 值
	**/
	public function GetMch_id()
	{
		return $this->values['mch_id'];
	}
	/**
	* 判断微信支付分配的商户号是否存在
	* @return true 或 false
	**/
	public function IsMch_idSet()
	{
		return array_key_exists('mch_id', $this->values);
	}


	/**
	* 设置终端设备号(商户自定义，如门店编号)
	* @param string $value 
	**/
	public function SetDevice_info($value)
	{
		$this->values['device_info'] = $value;
	}
	/**
	* 获取终端设备号(商户自定义，如门店编号)的值
	* @return 值
	**/
	public function GetDevice_info()
	{
		return $this->values['device_info'];
	}
	/**
	* 判断终端设备号(商户自定义，如门店编号)是否存在
	* @return true 或 false
	**/
	public function IsDevice_infoSet()
	{
		return array_key_exists('device_info', $this->values);
	}


	/**
	* 设置随机字符串，不长于32位。推荐随机数生成算法
	* @param string $value 
	**/
	public function SetNonce_str($value)
	{
		$this->values['nonce_str'] = $value;
	}
	/**
	* 获取随机字符串，不长于32位。推荐随机数生成算法的值
	* @return 值
	**/
	public function GetNonce_str()
	{
		return $this->values['nonce_str'];
	}
	/**
	* 判断随机字符串，不长于32位。推荐随机数生成算法是否存在
	* @return true 或 false
	**/
	public function IsNonce_strSet()
	{
		return array_key_exists('nonce_str', $this->values);
	}

	/**
	* 设置商品或支付单简要描述
	* @param string $value 
	**/
	public function SetBody($value)
	{
		$this->values['body'] = $value;
	}
	/**
	* 获取商品或支付单简要描述的值
	* @return 值
	**/
	public function GetBody()
	{
		return $this->values['body'];
	}
	/**
	* 判断商品或支付单简要描述是否存在
	* @return true 或 false
	**/
	public function IsBodySet()
	{
		return array_key_exists('body', $this->values);
	}


	/**
	* 设置商品名称明细列表
	* @param string $value 
	**/
	public function SetDetail($value)
	{
		$this->values['detail'] = $value;
	}
	/**
	* 获取商品名称明细列表的值
	* @return 值
	**/
	public function GetDetail()
	{
		return $this->values['detail'];
	}
	/**
	* 判断商品名称明细列表是否存在
	* @return true 或 false
	**/
	public function IsDetailSet()
	{
		return array_key_exists('detail', $this->values);
	}


	/**
	* 设置附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据
	* @param string $value 
	**/
	public function SetAttach($value)
	{
		$this->values['attach'] = $value;
	}
	/**
	* 获取附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据的值
	* @return 值
	**/
	public function GetAttach()
	{
		return $this->values['attach'];
	}
	/**
	* 判断附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据是否存在
	* @return true 或 false
	**/
	public function IsAttachSet()
	{
		return array_key_exists('attach', $this->values);
	}


	/**
	* 设置商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号
	* @param string $value 
	**/
	public function SetOut_trade_no($value)
	{
		$this->values['out_trade_no'] = $value;
	}
	/**
	* 获取商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号的值
	* @return 值
	**/
	public function GetOut_trade_no()
	{
		return $this->values['out_trade_no'];
	}
	/**
	* 判断商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号是否存在
	* @return true 或 false
	**/
	public function IsOut_trade_noSet()
	{
		return array_key_exists('out_trade_no', $this->values);
	}


	/**
	* 设置订单总金额，单位为分，只能为整数，详见支付金额
	* @param string $value 
	**/
	public function SetTotal_fee($value)
	{
		$this->values['total_fee'] = $value;
	}
	/**
	* 获取订单总金额，单位为分，只能为整数，详见支付金额的值
	* @return 值
	**/
	public function GetTotal_fee()
	{
		return $this->values['total_fee'];
	}
	/**
	* 判断订单总金额，单位为分，只能为整数，详见支付金额是否存在
	* @return true 或 false
	**/
	public function IsTotal_feeSet()
	{
		return array_key_exists('total_fee', $this->values);
	}


	/**
	* 设置符合ISO 4217标准的三位字母代码，默认人民币：CNY，其他值列表详见货币类型
	* @param string $value 
	**/
	public function SetFee_type($value)
	{
		$this->values['fee_type'] = $value;
	}
	/**
	* 获取符合ISO 4217标准的三位字母代码，默认人民币：CNY，其他值列表详见货币类型的值
	* @return 值
	**/
	public function GetFee_type()
	{
		return $this->values['fee_type'];
	}
	/**
	* 判断符合ISO 4217标准的三位字母代码，默认人民币：CNY，其他值列表详见货币类型是否存在
	* @return true 或 false
	**/
	public function IsFee_typeSet()
	{
		return array_key_exists('fee_type', $this->values);
	}


	/**
	* 设置调用微信支付API的机器IP 
	* @param string $value 
	**/
	public function SetSpbill_create_ip($value)
	{
		$this->values['spbill_create_ip'] = $value;
	}
	/**
	* 获取调用微信支付API的机器IP 的值
	* @return 值
	**/
	public function GetSpbill_create_ip()
	{
		return $this->values['spbill_create_ip'];
	}
	/**
	* 判断调用微信支付API的机器IP 是否存在
	* @return true 或 false
	**/
	public function IsSpbill_create_ipSet()
	{
		return array_key_exists('spbill_create_ip', $this->values);
	}


	/**
	* 设置订单生成时间，格式为yyyyMMddHHmmss，如2009年12月25日9点10分10秒表示为20091225091010。详见时间规则
	* @param string $value 
	**/
	public function SetTime_start($value)
	{
		$this->values['time_start'] = $value;
	}
	/**
	* 获取订单生成时间，格式为yyyyMMddHHmmss，如2009年12月25日9点10分10秒表示为20091225091010。详见时间规则的值
	* @return 值
	**/
	public function GetTime_start()
	{
		return $this->values['time_start'];
	}
	/**
	* 判断订单生成时间，格式为yyyyMMddHHmmss，如2009年12月25日9点10分10秒表示为20091225091010。详见时间规则是否存在
	* @return true 或 false
	**/
	public function IsTime_startSet()
	{
		return array_key_exists('time_start', $this->values);
	}


	/**
	* 设置订单失效时间，格式为yyyyMMddHHmmss，如2009年12月27日9点10分10秒表示为20091227091010。详见时间规则
	* @param string $value 
	**/
	public function SetTime_expire($value)
	{
		$this->values['time_expire'] = $value;
	}
	/**
	* 获取订单失效时间，格式为yyyyMMddHHmmss，如2009年12月27日9点10分10秒表示为20091227091010。详见时间规则的值
	* @return 值
	**/
	public function GetTime_expire()
	{
		return $this->values['time_expire'];
	}
	/**
	* 判断订单失效时间，格式为yyyyMMddHHmmss，如2009年12月27日9点10分10秒表示为20091227091010。详见时间规则是否存在
	* @return true 或 false
	**/
	public function IsTime_expireSet()
	{
		return array_key_exists('time_expire', $this->values);
	}


	/**
	* 设置商品标记，代金券或立减优惠功能的参数，说明详见代金券或立减优惠
	* @param string $value 
	**/
	public function SetGoods_tag($value)
	{
		$this->values['goods_tag'] = $value;
	}
	/**
	* 获取商品标记，代金券或立减优惠功能的参数，说明详见代金券或立减优惠的值
	* @return 值
	**/
	public function GetGoods_tag()
	{
		return $this->values['goods_tag'];
	}
	/**
	* 判断商品标记，代金券或立减优惠功能的参数，说明详见代金券或立减优惠是否存在
	* @return true 或 false
	**/
	public function IsGoods_tagSet()
	{
		return array_key_exists('goods_tag', $this->values);
	}


	/**
	* 设置扫码支付授权码，设备读取用户微信中的条码或者二维码信息
	* @param string $value 
	**/
	public function SetAuth_code($value)
	{
		$this->values['auth_code'] = $value;
	}
	/**
	* 获取扫码支付授权码，设备读取用户微信中的条码或者二维码信息的值
	* @return 值
	**/
	public function GetAuth_code()
	{
		return $this->values['auth_code'];
	}
	/**
	* 判断扫码支付授权码，设备读取用户微信中的条码或者二维码信息是否存在
	* @return true 或 false
	**/
	public function IsAuth_codeSet()
	{
		return array_key_exists('auth_code', $this->values);
	}
}

/**
 * 
 * 撤销输入对象
 * @author widyhu
 *
 */
class WxPayReverse extends WxPayDataBase
{
	/**
	* 设置微信分配的公众账号ID
	* @param string $value 
	**/
	public function SetAppid($value)
	{
		$this->values['appid'] = $value;
	}
	/**
	* 获取微信分配的公众账号ID的值
	* @return 值
	**/
	public function GetAppid()
	{
		return $this->values['appid'];
	}
	/**
	* 判断微信分配的公众账号ID是否存在
	* @return true 或 false
	**/
	public function IsAppidSet()
	{
		return array_key_exists('appid', $this->values);
	}


	/**
	* 设置微信支付分配的商户号
	* @param string $value 
	**/
	public function SetMch_id($value)
	{
		$this->values['mch_id'] = $value;
	}
	/**
	* 获取微信支付分配的商户号的值
	* @return 值
	**/
	public function GetMch_id()
	{
		return $this->values['mch_id'];
	}
	/**
	* 判断微信支付分配的商户号是否存在
	* @return true 或 false
	**/
	public function IsMch_idSet()
	{
		return array_key_exists('mch_id', $this->values);
	}


	/**
	* 设置微信的订单号，优先使用
	* @param string $value 
	**/
	public function SetTransaction_id($value)
	{
		$this->values['transaction_id'] = $value;
	}
	/**
	* 获取微信的订单号，优先使用的值
	* @return 值
	**/
	public function GetTransaction_id()
	{
		return $this->values['transaction_id'];
	}
	/**
	* 判断微信的订单号，优先使用是否存在
	* @return true 或 false
	**/
	public function IsTransaction_idSet()
	{
		return array_key_exists('transaction_id', $this->values);
	}


	/**
	* 设置商户系统内部的订单号,transaction_id、out_trade_no二选一，如果同时存在优先级：transaction_id> out_trade_no
	* @param string $value 
	**/
	public function SetOut_trade_no($value)
	{
		$this->values['out_trade_no'] = $value;
	}
	/**
	* 获取商户系统内部的订单号,transaction_id、out_trade_no二选一，如果同时存在优先级：transaction_id> out_trade_no的值
	* @return 值
	**/
	public function GetOut_trade_no()
	{
		return $this->values['out_trade_no'];
	}
	/**
	* 判断商户系统内部的订单号,transaction_id、out_trade_no二选一，如果同时存在优先级：transaction_id> out_trade_no是否存在
	* @return true 或 false
	**/
	public function IsOut_trade_noSet()
	{
		return array_key_exists('out_trade_no', $this->values);
	}


	/**
	* 设置随机字符串，不长于32位。推荐随机数生成算法
	* @param string $value 
	**/
	public function SetNonce_str($value)
	{
		$this->values['nonce_str'] = $value;
	}
	/**
	* 获取随机字符串，不长于32位。推荐随机数生成算法的值
	* @return 值
	**/
	public function GetNonce_str()
	{
		return $this->values['nonce_str'];
	}
	/**
	* 判断随机字符串，不长于32位。推荐随机数生成算法是否存在
	* @return true 或 false
	**/
	public function IsNonce_strSet()
	{
		return array_key_exists('nonce_str', $this->values);
	}
}

/**
 * 
 * 提交JSAPI输入对象
 * @author widyhu
 *
 */
class WxPayJsApiPay extends WxPayDataBase
{
	/**
	* 设置微信分配的公众账号ID
	* @param string $value 
	**/
	public function SetAppid($value)
	{
		$this->values['appId'] = $value;
	}
	/**
	* 获取微信分配的公众账号ID的值
	* @return 值
	**/
	public function GetAppid()
	{
		return $this->values['appId'];
	}
	/**
	* 判断微信分配的公众账号ID是否存在
	* @return true 或 false
	**/
	public function IsAppidSet()
	{
		return array_key_exists('appId', $this->values);
	}


	/**
	* 设置支付时间戳
	* @param string $value 
	**/
	public function SetTimeStamp($value)
	{
		$this->values['timeStamp'] = $value;
	}
	/**
	* 获取支付时间戳的值
	* @return 值
	**/
	public function GetTimeStamp()
	{
		return $this->values['timeStamp'];
	}
	/**
	* 判断支付时间戳是否存在
	* @return true 或 false
	**/
	public function IsTimeStampSet()
	{
		return array_key_exists('timeStamp', $this->values);
	}
	
	/**
	* 随机字符串
	* @param string $value 
	**/
	public function SetNonceStr($value)
	{
		$this->values['nonceStr'] = $value;
	}
	/**
	* 获取notify随机字符串值
	* @return 值
	**/
	public function GetReturn_code()
	{
		return $this->values['nonceStr'];
	}
	/**
	* 判断随机字符串是否存在
	* @return true 或 false
	**/
	public function IsReturn_codeSet()
	{
		return array_key_exists('nonceStr', $this->values);
	}


	/**
	* 设置订单详情扩展字符串
	* @param string $value 
	**/
	public function SetPackage($value)
	{
		$this->values['package'] = $value;
	}
	/**
	* 获取订单详情扩展字符串的值
	* @return 值
	**/
	public function GetPackage()
	{
		return $this->values['package'];
	}
	/**
	* 判断订单详情扩展字符串是否存在
	* @return true 或 false
	**/
	public function IsPackageSet()
	{
		return array_key_exists('package', $this->values);
	}
	
	/**
	* 设置签名方式
	* @param string $value 
	**/
	public function SetSignType($value)
	{
		$this->values['signType'] = $value;
	}
	/**
	* 获取签名方式
	* @return 值
	**/
	public function GetSignType()
	{
		return $this->values['signType'];
	}
	/**
	* 判断签名方式是否存在
	* @return true 或 false
	**/
	public function IsSignTypeSet()
	{
		return array_key_exists('signType', $this->values);
	}
	
	/**
	* 设置签名方式
	* @param string $value 
	**/
	public function SetPaySign($value)
	{
		$this->values['paySign'] = $value;
	}
	/**
	* 获取签名方式
	* @return 值
	**/
	public function GetPaySign()
	{
		return $this->values['paySign'];
	}
	/**
	* 判断签名方式是否存在
	* @return true 或 false
	**/
	public function IsPaySignSet()
	{
		return array_key_exists('paySign', $this->values);
	}
}

/**
 * 
 * 扫码支付模式一生成二维码参数
 * @author widyhu
 *
 */
class WxPayBizPayUrl extends WxPayDataBase
{
		/**
	* 设置微信分配的公众账号ID
	* @param string $value 
	**/
	public function SetAppid($value)
	{
		$this->values['appid'] = $value;
	}
	/**
	* 获取微信分配的公众账号ID的值
	* @return 值
	**/
	public function GetAppid()
	{
		return $this->values['appid'];
	}
	/**
	* 判断微信分配的公众账号ID是否存在
	* @return true 或 false
	**/
	public function IsAppidSet()
	{
		return array_key_exists('appid', $this->values);
	}


	/**
	* 设置微信支付分配的商户号
	* @param string $value 
	**/
	public function SetMch_id($value)
	{
		$this->values['mch_id'] = $value;
	}
	/**
	* 获取微信支付分配的商户号的值
	* @return 值
	**/
	public function GetMch_id()
	{
		return $this->values['mch_id'];
	}
	/**
	* 判断微信支付分配的商户号是否存在
	* @return true 或 false
	**/
	public function IsMch_idSet()
	{
		return array_key_exists('mch_id', $this->values);
	}
	
	/**
	* 设置支付时间戳
	* @param string $value 
	**/
	public function SetTime_stamp($value)
	{
		$this->values['time_stamp'] = $value;
	}
	/**
	* 获取支付时间戳的值
	* @return 值
	**/
	public function GetTime_stamp()
	{
		return $this->values['time_stamp'];
	}
	/**
	* 判断支付时间戳是否存在
	* @return true 或 false
	**/
	public function IsTime_stampSet()
	{
		return array_key_exists('time_stamp', $this->values);
	}
	
	/**
	* 设置随机字符串
	* @param string $value 
	**/
	public function SetNonce_str($value)
	{
		$this->values['nonce_str'] = $value;
	}
	/**
	* 获取随机字符串的值
	* @return 值
	**/
	public function GetNonce_str()
	{
		return $this->values['nonce_str'];
	}
	/**
	* 判断随机字符串是否存在
	* @return true 或 false
	**/
	public function IsNonce_strSet()
	{
		return array_key_exists('nonce_str', $this->values);
	}
	
	/**
	* 设置商品ID
	* @param string $value 
	**/
	public function SetProduct_id($value)
	{
		$this->values['product_id'] = $value;
	}
	/**
	* 获取商品ID的值
	* @return 值
	**/
	public function GetProduct_id()
	{
		return $this->values['product_id'];
	}
	/**
	* 判断商品ID是否存在
	* @return true 或 false
	**/
	public function IsProduct_idSet()
	{
		return array_key_exists('product_id', $this->values);
	}
}

/*
*摇一摇微信红包支付
*/
class WxPayReds extends WxPayDataBase{

	//生成32位随机字符
	public function nonce_str($value){
		$this->values['nonce_str']=$value;
	}
	public function gnonce_str()
	{
		return array_key_exists('nonce_str', $this->values);
	}

	//商户订单号
	public function mch_billno($value){
		$this->values['mch_billno']=$value;
	}
	public function gmch_billno()
	{
		return array_key_exists('mch_billno', $this->values);
	}

	//商户号
	public function mch_id($value){
		$this->values['mch_id'] = $value;
	}
	public function gmch_id()
	{
		return array_key_exists('mch_id', $this->values);
	}

	//公众账号appid
	public function wxappid($value){
		$this->values['wxappid']=$value;
	}
	public function gwxappid()
	{
		return array_key_exists('wxappid', $this->values);
	}

	//商户名称
	public function send_name($value){
		$this->values['send_name']=$value;
	}
	public function gsend_name()
	{
		return array_key_exists('send_name', $this->values);
	}

	//红包类型
	public function hb_type($value){
		$this->values['hb_type']=$value;
	}
	public function ghb_type()
	{
		return array_key_exists('hb_type', $this->values);
	}

	//总金额
	public function total_amount($value){
		$this->values['total_amount']=$value;
	}
	public function gtotal_amount()
	{
		return array_key_exists('total_amount', $this->values);
	}

	//发放总人数
	public function total_num($value){
		$this->values['total_num']=$value;
	}
	public function gtotal_num()
	{
		return array_key_exists('total_num', $this->values);
	}

	//发放全额设置方式
	public function amt_type($value){
		$this->values['amt_type']=$value;
	}
	public function gamt_type()
	{
		return array_key_exists('amt_type', $this->values);
	}

	//祝福语
	public function wishing($value){
		$this->values['wishing']=$value;
	}
	public function gwishing()
	{
		return array_key_exists('wishing', $this->values);
	}

	//活动名称
	public function act_name($value){
		$this->values['act_name']=$value;
	}
	public function gact_name()
	{
		return array_key_exists('act_name', $this->values);
	}

	//备注
	public function remark($value){
		$this->values['remark']=$value;
	}
	public function gremark()
	{
		return array_key_exists('remark', $this->values);
	}

	//授权商户号
	public function auth_mchid($value){
		$this->values['auth_mchid']=$value;
	}
	public function gauth_mchid()
	{
		return array_key_exists('auth_mchid', $this->values);
	}

	//授权商户APPID
	public function auth_appid($value){
		$this->values['auth_appid']=$value;
	}
	public function gauth_appid()
	{
		return array_key_exists('auth_appid', $this->values);
	}

	//风控设施
	public function risk_cntl($value){
		return $this->values['risk_cntl']=$value;
	}
	public function grisk_cntl()
	{
		return array_key_exists('risk_cntl', $this->values);
	}

	public function gsign()
	{
		return array_key_exists('sign', $this->values);
	}
}

/**
*添加红包内容
*/
class Wxred_Packet extends WxPayDataBase{

	//lottery_id
	public function lottery_id($value){
		$this->values['lottery_id']=$value;
	}
	public function glottery_id()
	{
		return array_key_exists('lottery_id', $this->values);
	}

	//提供者商户号
	public function mchid($value){
		$this->values['mchid']=$value;
	}
	public function gmchid()
	{
		return array_key_exists('mchid', $this->values);
	}

	//提供者APPID
	public function appid($value){
		$this->values['appid']=$value;
	}
	public function gappid()
	{
		return array_key_exists('appid', $this->values);
	}

	//提供者APPID
	public function prize_info_list($value){
		$this->values['prize_info_list']=$value;
	}
	public function gprize_info_list()
	{
		return array_key_exists('prize_info_list', $this->values);
	}

	public function get_data(){
		return $this->values;
	}
}

/**
*创建活动
*/
class Wxred_Activity extends WxPayDataBase{

	//抽奖活劢名称
	public function title($value){
		$this->values['title']=$value;
	}
	public function gtitle()
	{
		return array_key_exists('title', $this->values);
	}

	//抽奖活劢描述
	public function desc($value){
		$this->values['desc']=$value;
	}
	public function gdesc()
	{
		return array_key_exists('desc', $this->values);
	}

	//抽奖开关
	public function onoff($value){
		$this->values['onoff']=$value;
	}
	public function gonoff()
	{
		return array_key_exists('onoff', $this->values);
	}

	//开始时间
	public function begin_time($value){
		$this->values['begin_time']=$value;
	}
	public function gbegin_time()
	{
		return array_key_exists('begin_time', $this->values);
	}

	//结束时间
	public function expire_time($value){
		$this->values['expire_time']=$value;
	}
	public function gexpire_time()
	{
		return array_key_exists('expire_time', $this->values);
	}

	//红包提供商户公众号的appid
	public function sponsor_appid($value){
		$this->values['sponsor_appid']=$value;
	}
	public function gsponsor_appid()
	{
		return array_key_exists('sponsor_appid', $this->values);
	}

	//红包提供商户公众号的appid
	public function total($value){
		$this->values['total']=$value;
	}
	public function gtotal()
	{
		return array_key_exists('total', $this->values);
	}

	//领取完红包跳转的位置
	public function jump_url($value){
		$this->values['jump_url']=$value;
	}
	public function gjump_url()
	{
		return array_key_exists('jump_url', $this->values);
	}

	//key
	public function key($value){
		$this->values['key']=$value;
	}
	public function gkey()
	{
		return array_key_exists('key', $this->values);
	}


	public function gsign()
	{
		return array_key_exists('sign', $this->values);
	}

	public function get_data(){
		return $this->values;
	}
}

/**
 * 微信红包支付  xizb
 */
class WxPayRedPackPay extends WxPayDataBase{
    /**
	* 设置微信分配的公众账号ID
	* @param string $value 
	**/
	public function SetAppid($value)
	{
		$this->values['wxappid'] = $value;
	}
	/**
	* 获取微信分配的公众账号ID的值
	* @return 值
	**/
	public function GetAppid()
	{
		return $this->values['wxappid'];
	}
	/**
	* 判断微信分配的公众账号ID是否存在
	* @return true 或 false
	**/
	public function IsAppidSet()
	{
		return array_key_exists('wxappid', $this->values);
	}


	/**
	* 设置微信支付分配的商户号
	* @param string $value 
	**/
	public function SetMch_id($value)
	{
		$this->values['mch_id'] = $value;
	}
	/**
	* 获取微信支付分配的商户号的值
	* @return 值
	**/
	public function GetMch_id()
	{
		return $this->values['mch_id'];
	}
	/**
	* 判断微信支付分配的商户号是否存在
	* @return true 或 false
	**/
	public function IsMch_idSet()
	{
		return array_key_exists('mch_id', $this->values);
	}


	


	/**
	* 设置随机字符串，不长于32位。推荐随机数生成算法
	* @param string $value 
	**/
	public function SetNonce_str($value)
	{
		$this->values['nonce_str'] = $value;
	}
	/**
	* 获取随机字符串，不长于32位。推荐随机数生成算法的值
	* @return 值
	**/
	public function GetNonce_str()
	{
		return $this->values['nonce_str'];
	}
	/**
	* 判断随机字符串，不长于32位。推荐随机数生成算法是否存在
	* @return true 或 false
	**/
	public function IsNonce_strSet()
	{
		return array_key_exists('nonce_str', $this->values);
	}

	
	/**
	* 设置订单号
	* @param string $value 
	**/
	public function SetMch_billno($value)
	{
		$this->values['mch_billno'] = $value;
	}
	/**
	* 获取订单号
	* @return 值
	**/
	public function GetMch_billno()
	{
		return $this->values['mch_billno'];
	}
	/**
	* 判断订单号是否存在
	* @return true 或 false
	**/
	public function IsMch_billnoSet()
	{
		return array_key_exists('mch_billno', $this->values);
	}



	/**
	* 设置APP和网页支付提交用户端ip，Native支付填调用微信支付API的机器IP。
	* @param string $value 
	**/
	public function SetSpbill_create_ip($value)
	{
		$this->values['client_ip'] = $value;
	}
	/**
	* 获取APP和网页支付提交用户端ip，Native支付填调用微信支付API的机器IP。的值
	* @return 值
	**/
	public function GetSpbill_create_ip()
	{
		return $this->values['client_ip'];
	}
	/**
	* 判断APP和网页支付提交用户端ip，Native支付填调用微信支付API的机器IP。是否存在
	* @return true 或 false
	**/
	public function IsSpbill_create_ipSet()
	{
		return array_key_exists('client_ip', $this->values);
	}






	/**
	* 设置接收微信支付异步通知回调地址
	* @param string $value 
	**/
	public function SetNotify_url($value)
	{
		$this->values['notify_url'] = $value;
	}
	/**
	* 获取接收微信支付异步通知回调地址的值
	* @return 值
	**/
	public function GetNotify_url()
	{
		return $this->values['notify_url'];
	}
	/**
	* 判断接收微信支付异步通知回调地址是否存在
	* @return true 或 false
	**/
	public function IsNotify_urlSet()
	{
		return array_key_exists('notify_url', $this->values);
	}




	//Openid
	public function SetOpenid($value)
	{
		$this->values['re_openid'] = $value;
	}
	
	public function GetOpenid()
	{
		return $this->values['re_openid'];
	}
	
	public function IsOpenidSet()
	{
		return array_key_exists('re_openid', $this->values);
	}
        //提供方名称
        public function SetNick_name($value)
	{
		$this->values['nick_name'] = $value;
	}
        public function GetNick_name()
	{
		return $this->values['nick_name'];
	}
	
	public function IsNick_nameSet()
	{
		return array_key_exists('nick_name', $this->values);
	}
        //红包发送者名称
        public function SetSend_name($value)
	{
		$this->values['send_name'] = $value;
	}
        public function GetSend_name()
	{
		return $this->values['send_name'];
	}
	
	public function IsSend_nameSet()
	{
		return array_key_exists('send_name', $this->values);
	}
        //付款金额，单位分
        public function SetTotal_amount($value)
	{
		$this->values['total_amount'] = $value;
	}
        public function GetTotal_amount()
	{
		return $this->values['total_amount'];
	}
	
	public function IsTotal_amountSet()
	{
		return array_key_exists('total_amount', $this->values);
	}
        //最小红包金额，单位分
         public function SetMin_value($value)
	{
		$this->values['min_value'] = $value;
	}
        public function GetMin_value()
	{
		return $this->values['min_value'];
	}
	
	public function IsMin_valueSet()
	{
		return array_key_exists('min_value', $this->values);
	}
        //最大红包金额，单位分
         public function SetMax_value($value)
	{
		$this->values['max_value'] = $value;
	}
        public function GetMax_value()
	{
		return $this->values['max_value'];
	}
	
	public function IsMax_valueSet()
	{
		return array_key_exists('max_value', $this->values);
	}
        //红包収放总人数
         public function SetTotal_num($value)
	{
		$this->values['total_num'] = $value;
	}
        public function GetTotal_num()
	{
		return $this->values['total_num'];
	}
	
	public function IsTotal_numSet()
	{
		return array_key_exists('total_num', $this->values);
	}
        //红包祝福诧
         public function SetWishing($value)
	{
		$this->values['wishing'] = $value;
	}
        public function GetWishing()
	{
		return $this->values['wishing'];
	}
	
	public function IsWishingSet()
	{
		return array_key_exists('wishing', $this->values);
	}

        
        //活劢名称
         public function SetAct_name($value)
	{
		$this->values['act_name'] = $value;
	}
        public function GetAct_name()
	{
		return $this->values['act_name'];
	}
	
	public function IsAct_nameSet()
	{
		return array_key_exists('act_name', $this->values);
	}
        //备注信息
  
        public function SetRemark($value)
	{
		$this->values['remark'] = $value;
	}
        public function GetRemark()
	{
		return $this->values['remark'];
	}
	
	public function IsRemarkSet()
	{
		return array_key_exists('remark', $this->values);
	}
}

class WxPayRedPackQuery extends WxPayDataBase
{
    protected $values;
    
        /**
	* 设置微信分配的公众账号ID
	* @param string $value 
	**/
	public function SetAppid($value)
	{
		$this->values['appid'] = $value;
	}
	/**
	* 获取微信分配的公众账号ID的值
	* @return 值
	**/
	public function GetAppid()
	{
		return $this->values['appid'];
	}
	/**
	* 判断微信分配的公众账号ID是否存在
	* @return true 或 false
	**/
	public function IsAppidSet()
	{
		return array_key_exists('appid', $this->values);
	}


	/**
	* 设置微信支付分配的商户号
	* @param string $value 
	**/
	public function SetMch_id($value)
	{
		$this->values['mch_id'] = $value;
	}
	/**
	* 获取微信支付分配的商户号的值
	* @return 值
	**/
	public function GetMch_id()
	{
		return $this->values['mch_id'];
	}
	/**
	* 判断微信支付分配的商户号是否存在
	* @return true 或 false
	**/
	public function IsMch_idSet()
	{
		return array_key_exists('mch_id', $this->values);
	}


	

	/**
	* 设置随机字符串，不长于32位。推荐随机数生成算法
	* @param string $value 
	**/
	public function SetNonce_str($value)
	{
		$this->values['nonce_str'] = $value;
	}
	/**
	* 获取随机字符串，不长于32位。推荐随机数生成算法的值
	* @return 值
	**/
	public function GetNonce_str()
	{
		return $this->values['nonce_str'];
	}
	/**
	* 判断随机字符串，不长于32位。推荐随机数生成算法是否存在
	* @return true 或 false
	**/
	public function IsNonce_strSet()
	{
		return array_key_exists('nonce_str', $this->values);
	}

	


	/**
	* 设置商户系统内部的订单号
	* @param string $value 
	**/
	public function SetMch_billno ($value)
	{
		$this->values['mch_billno'] = $value;
	}
	/**
	* 获取商户系统内部的订单号的值
	* @return 值
	**/
	public function GetMch_billno()
	{
		return $this->values['mch_billno'];
	}
	/**
	* 判断商户系统内部的订单号是否存在
	* @return true 或 false
	**/
	public function IsMch_billnoSet()
	{
		return array_key_exists('mch_billno', $this->values);
	}
        /**
         * bill_type 固定值“MCHT”
         */
        public function SetBill_type(){
            $this->values['bill_type'] = "MCHT";
        }
        public function GetBill_type(){
            return $this->values['bill_type'];
        }
        public function IsBill_typeSet()
	{
		return array_key_exists('bill_type', $this->values);
	}
}

class WxPayGroupRedPackPay extends WxPayDataBase{
    /**
	* 设置微信分配的公众账号ID
	* @param string $value 
	**/
	public function SetAppid($value)
	{
		$this->values['wxappid'] = $value;
	}
	/**
	* 获取微信分配的公众账号ID的值
	* @return 值
	**/
	public function GetAppid()
	{
		return $this->values['wxappid'];
	}
	/**
	* 判断微信分配的公众账号ID是否存在
	* @return true 或 false
	**/
	public function IsAppidSet()
	{
		return array_key_exists('wxappid', $this->values);
	}


	/**
	* 设置微信支付分配的商户号
	* @param string $value 
	**/
	public function SetMch_id($value)
	{
		$this->values['mch_id'] = $value;
	}
	/**
	* 获取微信支付分配的商户号的值
	* @return 值
	**/
	public function GetMch_id()
	{
		return $this->values['mch_id'];
	}
	/**
	* 判断微信支付分配的商户号是否存在
	* @return true 或 false
	**/
	public function IsMch_idSet()
	{
		return array_key_exists('mch_id', $this->values);
	}


	


	/**
	* 设置随机字符串，不长于32位。推荐随机数生成算法
	* @param string $value 
	**/
	public function SetNonce_str($value)
	{
		$this->values['nonce_str'] = $value;
	}
	/**
	* 获取随机字符串，不长于32位。推荐随机数生成算法的值
	* @return 值
	**/
	public function GetNonce_str()
	{
		return $this->values['nonce_str'];
	}
	/**
	* 判断随机字符串，不长于32位。推荐随机数生成算法是否存在
	* @return true 或 false
	**/
	public function IsNonce_strSet()
	{
		return array_key_exists('nonce_str', $this->values);
	}

	
	/**
	* 设置订单号
	* @param string $value 
	**/
	public function SetMch_billno($value)
	{
		$this->values['mch_billno'] = $value;
	}
	/**
	* 获取订单号
	* @return 值
	**/
	public function GetMch_billno()
	{
		return $this->values['mch_billno'];
	}
	/**
	* 判断订单号是否存在
	* @return true 或 false
	**/
	public function IsMch_billnoSet()
	{
		return array_key_exists('mch_billno', $this->values);
	}

	/**
	* 设置接收微信支付异步通知回调地址
	* @param string $value 
	**/
	public function SetNotify_url($value)
	{
		$this->values['notify_url'] = $value;
	}
	/**
	* 获取接收微信支付异步通知回调地址的值
	* @return 值
	**/
	public function GetNotify_url()
	{
		return $this->values['notify_url'];
	}
	/**
	* 判断接收微信支付异步通知回调地址是否存在
	* @return true 或 false
	**/
	public function IsNotify_urlSet()
	{
		return array_key_exists('notify_url', $this->values);
	}




	//Openid
	public function SetOpenid($value)
	{
		$this->values['re_openid'] = $value;
	}
	
	public function GetOpenid()
	{
		return $this->values['re_openid'];
	}
	
	public function IsOpenidSet()
	{
		return array_key_exists('re_openid', $this->values);
	}
        
        //红包发送者名称
        public function SetSend_name($value)
	{
		$this->values['send_name'] = $value;
	}
        public function GetSend_name()
	{
		return $this->values['send_name'];
	}
	
	public function IsSend_nameSet()
	{
		return array_key_exists('send_name', $this->values);
	}
        //付款金额，单位分
        public function SetTotal_amount($value)
	{
		$this->values['total_amount'] = $value;
	}
        public function GetTotal_amount()
	{
		return $this->values['total_amount'];
	}
	
	public function IsTotal_amountSet()
	{
		return array_key_exists('total_amount', $this->values);
	}
        
        //红包収放总人数
         public function SetTotal_num($value)
	{
		$this->values['total_num'] = $value;
	}
        public function GetTotal_num()
	{
		return $this->values['total_num'];
	}
	
	public function IsTotal_numSet()
	{
		return array_key_exists('total_num', $this->values);
	}
        //红包祝福诧
         public function SetWishing($value)
	{
		$this->values['wishing'] = $value;
	}
        public function GetWishing()
	{
		return $this->values['wishing'];
	}
	
	public function IsWishingSet()
	{
		return array_key_exists('wishing', $this->values);
	}

        
        //活劢名称
         public function SetAct_name($value)
	{
		$this->values['act_name'] = $value;
	}
        public function GetAct_name()
	{
		return $this->values['act_name'];
	}
	
	public function IsAct_nameSet()
	{
		return array_key_exists('act_name', $this->values);
	}
        //备注信息
  
        public function SetRemark($value)
	{
		$this->values['remark'] = $value;
	}
        public function GetRemark()
	{
		return $this->values['remark'];
	}
	
	public function IsRemarkSet()
	{
		return array_key_exists('remark', $this->values);
	}
        
        
        
        //红包金额设置方式 
  
        public function SetAmt_type()
	{
		$this->values['amt_type'] = 'ALL_RAND';
	}
        public function GetAmt_type()
	{
		return $this->values['amt_type'];
	}
	
	public function IsAmt_typeSet()
	{
		return array_key_exists('amt_type', $this->values);
	}
        
        
         //各红包金额  例:200|100|100 非必须参数 
  
        public function SetAmt_list($value)
	{
		$this->values['amt_list'] = $value;
	}
        public function GetAmt_list()
	{
		return $this->values['amt_list'];
	}
	
	public function IsAmt_listSet()
	{
		return array_key_exists('amt_list', $this->values);
	}
}

class WxPayCompanyPay extends WxPayDataBase{
    /**
	* 设置微信分配的公众账号ID
	* @param string $value 
	**/
	public function SetMch_appid($value)
	{
		$this->values['mch_appid'] = $value;
	}
	/**
	* 获取微信分配的公众账号ID的值
	* @return 值
	**/
	public function GetMch_appid()
	{
		return $this->values['mch_appid'];
	}
	/**
	* 判断微信分配的公众账号ID是否存在
	* @return true 或 false
	**/
	public function IsMch_appidSet()
	{
		return array_key_exists('mch_appid', $this->values);
	}


	/**
	* 设置微信支付分配的商户号
	* @param string $value 
	**/
	public function SetMchid($value)
	{
		$this->values['mchid'] = $value;
	}
	/**
	* 获取微信支付分配的商户号的值
	* @return 值
	**/
	public function GetMchid()
	{
		return $this->values['mchid'];
	}
	/**
	* 判断微信支付分配的商户号是否存在
	* @return true 或 false
	**/
	public function IsMchidSet()
	{
		return array_key_exists('mchid', $this->values);
	}


	


	/**
	* 设置随机字符串，不长于32位。推荐随机数生成算法
	* @param string $value 
	**/
	public function SetNonce_str($value)
	{
		$this->values['nonce_str'] = $value;
	}
	/**
	* 获取随机字符串，不长于32位。推荐随机数生成算法的值
	* @return 值
	**/
	public function GetNonce_str()
	{
		return $this->values['nonce_str'];
	}
	/**
	* 判断随机字符串，不长于32位。推荐随机数生成算法是否存在
	* @return true 或 false
	**/
	public function IsNonce_strSet()
	{
		return array_key_exists('nonce_str', $this->values);
	}

	
	/**
	* 设置订单号
	* @param string $value 
	**/
	public function SetPartner_trade_no($value)
	{
		$this->values['partner_trade_no'] = $value;
	}
	/**
	* 获取订单号
	* @return 值
	**/
	public function GetPartner_trade_no()
	{
		return $this->values['partner_trade_no'];
	}
	/**
	* 判断订单号是否存在
	* @return true 或 false
	**/
	public function IsPartner_trade_noSet()
	{
		return array_key_exists('partner_trade_no', $this->values);
	}

	/**
	* 设置接收微信支付异步通知回调地址
	* @param string $value 
	**/
	public function SetNotify_url($value)
	{
		$this->values['notify_url'] = $value;
	}
	/**
	* 获取接收微信支付异步通知回调地址的值
	* @return 值
	**/
	public function GetNotify_url()
	{
		return $this->values['notify_url'];
	}
	/**
	* 判断接收微信支付异步通知回调地址是否存在
	* @return true 或 false
	**/
	public function IsNotify_urlSet()
	{
		return array_key_exists('notify_url', $this->values);
	}




	//Openid
	public function SetOpenid($value)
	{
		$this->values['openid'] = $value;
	}
	
	public function GetOpenid()
	{
		return $this->values['openid'];
	}
	
	public function IsOpenidSet()
	{
		return array_key_exists('openid', $this->values);
	}
        
        //红包发送者名称
        public function SetCheck_name($value)
	{
		$this->values['check_name'] = $value;
	}
        public function GetCheck_name()
	{
		return $this->values['check_name'];
	}
	
	public function IsCheck_nameSet()
	{
		return array_key_exists('check_name', $this->values);
	}
        //付款金额，单位分
        public function SetAmount($value)
	{
		$this->values['amount'] = $value;
	}
        public function GetAmount()
	{
		return $this->values['amount'];
	}
	
	public function IsAmountSet()
	{
		return array_key_exists('amount', $this->values);
	}
        
        //红包収放总人数
         public function SetDesc($value)
	{
		$this->values['desc'] = $value;
	}
        public function GetDesc()
	{
		return $this->values['desc'];
	}
	
	public function IsDescSet()
	{
		return array_key_exists('desc', $this->values);
	}
      
         public function SetSpbill_create_ip($value)
	{
		$this->values['spbill_create_ip'] = $value;
	}
        public function GetSpbill_create_ip()
	{
		return $this->values['spbill_create_ip'];
	}
	
	public function IsSpbill_create_ipSet()
	{
		return array_key_exists('spbill_create_ip', $this->values);
	}

         public function SetRe_user_name($value)
	{
		$this->values['re_user_name'] = $value;
	}
        public function GetRe_user_name()
	{
		return $this->values['re_user_name'];
	}
	
	public function IsRe_user_nameSet()
	{
		return array_key_exists('re_user_name', $this->values);
	}
       
}




class WxPayCompanyOrderQuery extends WxPayDataBase{
    /**
	* 设置微信分配的公众账号ID
	* @param string $value 
	**/
	public function SetAppid($value)
	{
		$this->values['appid'] = $value;
	}
	/**
	* 获取微信分配的公众账号ID的值
	* @return 值
	**/
	public function GetAppid()
	{
		return $this->values['appid'];
	}
	/**
	* 判断微信分配的公众账号ID是否存在
	* @return true 或 false
	**/
	public function IsAppidSet()
	{
		return array_key_exists('appid', $this->values);
	}


	/**
	* 设置微信支付分配的商户号
	* @param string $value 
	**/
	public function SetMch_id($value)
	{
		$this->values['mch_id'] = $value;
	}
	/**
	* 获取微信支付分配的商户号的值
	* @return 值
	**/
	public function GetMch_id()
	{
		return $this->values['mch_id'];
	}
	/**
	* 判断微信支付分配的商户号是否存在
	* @return true 或 false
	**/
	public function IsMch_idSet()
	{
		return array_key_exists('mch_id', $this->values);
	}


	


	/**
	* 设置随机字符串，不长于32位。推荐随机数生成算法
	* @param string $value 
	**/
	public function SetNonce_str($value)
	{
		$this->values['nonce_str'] = $value;
	}
	/**
	* 获取随机字符串，不长于32位。推荐随机数生成算法的值
	* @return 值
	**/
	public function GetNonce_str()
	{
		return $this->values['nonce_str'];
	}
	/**
	* 判断随机字符串，不长于32位。推荐随机数生成算法是否存在
	* @return true 或 false
	**/
	public function IsNonce_strSet()
	{
		return array_key_exists('nonce_str', $this->values);
	}

	
	/**
	* 设置订单号
	* @param string $value 
	**/
	public function SetPartner_trade_no($value)
	{
		$this->values['partner_trade_no'] = $value;
	}
	/**
	* 获取订单号
	* @return 值
	**/
	public function GetPartner_trade_no()
	{
		return $this->values['partner_trade_no'];
	}
	/**
	* 判断订单号是否存在
	* @return true 或 false
	**/
	public function IsPartner_trade_noSet()
	{
		return array_key_exists('partner_trade_no', $this->values);
	}

	/**
	* 设置接收微信支付异步通知回调地址
	* @param string $value 
	**/
	public function SetNotify_url($value)
	{
		$this->values['notify_url'] = $value;
	}
	/**
	* 获取接收微信支付异步通知回调地址的值
	* @return 值
	**/
	public function GetNotify_url()
	{
		return $this->values['notify_url'];
	}
	/**
	* 判断接收微信支付异步通知回调地址是否存在
	* @return true 或 false
	**/
	public function IsNotify_urlSet()
	{
		return array_key_exists('notify_url', $this->values);
	}


}

class WxPayCouponQuery extends WxPayDataBase{
    /**
	* 设置微信分配的公众账号ID
	* @param string $value 
	**/
	public function SetAppid($value)
	{
		$this->values['appid'] = $value;
	}
	/**
	* 获取微信分配的公众账号ID的值
	* @return 值
	**/
	public function GetAppid()
	{
		return $this->values['appid'];
	}
	/**
	* 判断微信分配的公众账号ID是否存在
	* @return true 或 false
	**/
	public function IsAppidSet()
	{
		return array_key_exists('appid', $this->values);
	}


	/**
	* 设置微信支付分配的商户号
	* @param string $value 
	**/
	public function SetMch_id($value)
	{
		$this->values['mch_id'] = $value;
	}
	/**
	* 获取微信支付分配的商户号的值
	* @return 值
	**/
	public function GetMch_id()
	{
		return $this->values['mch_id'];
	}
	/**
	* 判断微信支付分配的商户号是否存在
	* @return true 或 false
	**/
	public function IsMch_idSet()
	{
		return array_key_exists('mch_id', $this->values);
	}


	


	/**
	* 设置随机字符串，不长于32位。推荐随机数生成算法
	* @param string $value 
	**/
	public function SetNonce_str($value)
	{
		$this->values['nonce_str'] = $value;
	}
	/**
	* 获取随机字符串，不长于32位。推荐随机数生成算法的值
	* @return 值
	**/
	public function GetNonce_str()
	{
		return $this->values['nonce_str'];
	}
	/**
	* 判断随机字符串，不长于32位。推荐随机数生成算法是否存在
	* @return true 或 false
	**/
	public function IsNonce_strSet()
	{
		return array_key_exists('nonce_str', $this->values);
	}

	

	/**
	* 代金券批次id
	* @param string $value 
	**/
	public function SetCoupon_stock_id($value)
	{
		$this->values['coupon_stock_id'] = $value;
	}
	/**
	* 获取代金券批次id
	* @return 值
	**/
	public function GetCoupon_stock_id()
	{
		return $this->values['coupon_stock_id'];
	}
	/**
	* 判断代金券批次id
	* @return true 或 false
	**/
	public function IsCoupon_stock_idSet()
	{
		return array_key_exists('coupon_stock_id', $this->values);
	}


}



class WxPaySendCoupon extends WxPayDataBase{
    /**
	* 设置微信分配的公众账号ID
	* @param string $value 
	**/
	public function SetAppid($value)
	{
		$this->values['appid'] = $value;
	}
	/**
	* 获取微信分配的公众账号ID的值
	* @return 值
	**/
	public function GetAppid()
	{
		return $this->values['appid'];
	}
	/**
	* 判断微信分配的公众账号ID是否存在
	* @return true 或 false
	**/
	public function IsAppidSet()
	{
		return array_key_exists('appid', $this->values);
	}


	/**
	* 设置微信支付分配的商户号
	* @param string $value 
	**/
	public function SetMch_id($value)
	{
		$this->values['mch_id'] = $value;
	}
	/**
	* 获取微信支付分配的商户号的值
	* @return 值
	**/
	public function GetMch_id()
	{
		return $this->values['mch_id'];
	}
	/**
	* 判断微信支付分配的商户号是否存在
	* @return true 或 false
	**/
	public function IsMch_idSet()
	{
		return array_key_exists('mch_id', $this->values);
	}


	


	/**
	* 设置随机字符串，不长于32位。推荐随机数生成算法
	* @param string $value 
	**/
	public function SetNonce_str($value)
	{
		$this->values['nonce_str'] = $value;
	}
	/**
	* 获取随机字符串，不长于32位。推荐随机数生成算法的值
	* @return 值
	**/
	public function GetNonce_str()
	{
		return $this->values['nonce_str'];
	}
	/**
	* 判断随机字符串，不长于32位。推荐随机数生成算法是否存在
	* @return true 或 false
	**/
	public function IsNonce_strSet()
	{
		return array_key_exists('nonce_str', $this->values);
	}

	

	/**
	* 代金券批次id
	* @param string $value 
	**/
	public function SetCoupon_stock_id($value)
	{
		$this->values['coupon_stock_id'] = $value;
	}
	/**
	* 获取代金券批次id
	* @return 值
	**/
	public function GetCoupon_stock_id()
	{
		return $this->values['coupon_stock_id'];
	}
	/**
	* 判断代金券批次id
	* @return true 或 false
	**/
	public function IsCoupon_stock_idSet()
	{
		return array_key_exists('coupon_stock_id', $this->values);
	}

        /**
	* 设置订单号
	* @param string $value 
	**/
	public function SetPartner_trade_no($value)
	{
		$this->values['partner_trade_no'] = $value;
	}
	/**
	* 获取订单号
	* @return 值
	**/
	public function GetPartner_trade_no()
	{
		return $this->values['partner_trade_no'];
	}
	/**
	* 判断订单号是否存在
	* @return true 或 false
	**/
	public function IsPartner_trade_noSet()
	{
		return array_key_exists('partner_trade_no', $this->values);
	}
        
        
        
        /**
	* openid记录数
	* @param string $value 
	**/
	public function SetOpenid_count($value)
	{
		$this->values['openid_count'] = $value;
	}
	/**
	* openid记录数
	* @return 值
	**/
	public function GetOpenid_count()
	{
		return $this->values['openid_count'];
	}
	/**
	* openid记录数
	* @return true 或 false
	**/
	public function IsOpenid_countSet()
	{
		return array_key_exists('openid_count', $this->values);
	}
        
        
        
        
        public function SetOpenid($value)
	{
		$this->values['openid'] = $value;
	}
	
	public function GetOpenid()
	{
		return $this->values['openid'];
	}
	
	public function IsOpenidSet()
	{
		return array_key_exists('openid', $this->values);
	}
}


