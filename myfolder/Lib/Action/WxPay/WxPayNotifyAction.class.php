<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of NotifyAction
 *
 * @author Caojing
 */
class WxPayNotifyAction extends WxPayNotify{
    public function index(){
        
        
        Logger::info('==============================NotifyAction==================================');
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        $this->Handle(WxPayConfig::KEY,$xml);
    }

    //查询订单
	public function Queryorder($data)
	{
            $transaction_id = $data["transaction_id"];
            $appid = $data['appid'];
            $mch_id = $data['mch_id'];
		$input = new WxPayOrderQuery();
		$input->SetTransaction_id($transaction_id);
                $input->SetAppid($appid);
                $input->SetMch_id($mch_id);
                $input->SetNonce_str(WxPayApi::getNonceStr());
                $input->SetSign(WxPayConfig::KEY);
                
		$result = WxPayApi::orderQuery($input);
                Logger::debug('Hezq NotifyAction query order result:',$result);
		if(array_key_exists("return_code", $result)
			&& array_key_exists("result_code", $result)
			&& $result["return_code"] == "SUCCESS"
			&& $result["result_code"] == "SUCCESS")
		{
			return true;
		}
		return false;
	}
	
	//重写回调处理函数
	public function NotifyProcess($data, &$msg)
	{
//		Log::DEBUG("call back:" . json_encode($data));
		$notfiyOutput = array();
		Logger::debug('Hezq NotifyAction NotifyProcess data:',$data);
		if(!array_key_exists("transaction_id", $data)){
			$msg = "输入参数不正确";
			return false;
		}
// 		//查询订单，判断订单真实性
// 		if(!$this->Queryorder($data)){
// 			$msg = "订单查询失败";
// 			return false;
// 		}
// 		if($data['result_code']!='SUCCESS'){
// 			$msg = "订单支付失败";
// 			return false;
// 		}
		Logger::info("-----------------收到开始请求糯米测试端数据");
		
		//获得特定的参数
		$attach = json_decode($data['attach'],true);
		$param['trade_no'] = $data['transaction_id'];  	//订单流水
		$param['trade_time'] = $data['time_end'];		//订单完成时间
		$param['shopId'] = $attach['shopId'];			//商户号
		$param['extId'] = $attach['extId'];				//扩展ID(员工编号)
		$param['isFirstTrake'] = true;  				//是否新用户首单
		$param['uid'] = $data['openid'];  				//用户ID
		$param['trade_fee'] = $data['total_fee'];  		//总金额
		
// 		if(isset($attach['ywType']) && $attach['ywType'] == 1){
// 			$noticeUrl = 'http://test.hzc.socialjia.com/Gratuity/Notice/index.php?a=Cofco&m=index';
// 		}else{
// 			$noticeUrl = 'http://test.hzc.socialjia.com/Gratuity/Notice/index.php?a=Index&m=index';
// 		}
		
// 		$reqResult = RequestClient::request($noticeUrl,"POST",$param);
// 		Logger::debug('-----------------请求结束['.$noticeUrl.']  Hezq request Gratuity result:'.$reqResult,$param);
		return true;
	}
	
	public function test(){
		$data = array (
  'appid' => 'wx764712b34332b20a',
  'attach' => '{"shopId"=>1,"extId"=>34}',
  'bank_type' => 'CFT',
  'cash_fee' => '1',
  'fee_type' => 'CNY',
  'is_subscribe' => 'Y',
  'mch_id' => '1218639501',
  'nonce_str' => 'yqltxs19hxbmnwvw7x1zbbo0su3ji69v',
  'openid' => 'o2u2VjnIzSHuKOkZuEQyqwaENS8k',
  'out_trade_no' => '121863950120160122163806',
  'result_code' => 'SUCCESS',
  'return_code' => 'SUCCESS',
  'sign' => 'ACB597B5DD9FD977C1AB4904A7B86210',
  'time_end' => '20160122164422',
  'total_fee' => '1',
  'trade_type' => 'JSAPI',
  'transaction_id' => '1000880692201601222840680315',
);
		//获得特定的参数
		$attach = json_decode($data['attach'],true);
		$param['trade_no'] = $data['transaction_id'];  	//订单流水
		$param['trade_time'] = $data['time_end'];		//订单完成时间
		$param['shopId'] = $attach['shopId'];			//商户号
		$param['extId'] = $attach['extId'];				//扩展ID(员工编号)
		$param['isFirstTrake'] = true;  				//是否新用户首单
		$param['uid'] = $data['openid'];  				//用户ID
		$param['trade_fee'] = $data['total_fee'];  		//总金额
		
		$noticeUrl = 'http://test.hzc.socialjia.com/Gratuity/Notice/index.php?a=Index&m=index';
		
		$reqResult = RequestClient::request($noticeUrl,"POST",$param);
		Logger::debug('Hezq request Gratuity result:'.$reqResult,$param);
		var_dump($reqResult,$param);
	}
}
