<?php
/**
 * 爱锁配置
 * @author
 */
class AbcConfig
{
	
	const COOKIE_UID_TOKEN = 'uidtoken';//cookie的key
	const OPENID_VALID_DURTION = 3600; //openid在缓存的有效期(单位s)
	const VERSION = '1.1.1';
	
	
	//短信参数
	const SMS_WG_URL = 'http://sdk4report.eucp.b2m.cn:8080/sdk/SDKService?wsdl';//短信网关地址
	const SMS_SERIAL_NUMBER = '6SDK-EMY-6688-KDXLQ';//序列号,请通过亿美销售人员获取
	const SMS_PW = '342465';//密码,请通过亿美销售人员获取
	const SMS_SESSION_KEY = '219309';//登录后所持有的SESSION KEY，即可通过login方法时创建
	const SMS_MAXCOUNT_DAY = 3; //一个手机号一天只能发送几条短信
	const SMS_IP_MAXCOUNT_DAY = 3; //一个IP一天只能发送几条短信
	
	//绑定参数
	const APPLY_TIME_DURTION = 48 ;  //申请爱锁的时间间隔(单位小时)
	
	//分页参数
	const PAGE_SIZE = 5 ;//每页的大小
	
	//恋爱提醒参数
	const MAX_WARN_COUNT = 2; //每个人可设置几条提醒
	
	//信池参数
	const ZAN_DURATION_TIME =24; //多长时间可以点赞几次
	const ZAN_MAX_COUNT =1; //多长时间可以点赞几次
	const DEFAULT_USER_HEADIMGURL = './images/niming.jpg';//默认头像
	const DEFAULT_USER_TRUTHNAME = '匿名';//默认名称
		
	//马甲
	const MAJIA_OPENID = 'oImZwt000000000000000001';
	
}
