<?php
/**
 * 系统配置文件
 */
define("VERSION", "0.1");

class Config
{
	const ENT_ID = 1;
	//微信api版本
	const WX_API_VERSION = 2;
	const REQUEST_LOG = true;
	//次变量是区分日志目录到标识
	private static $MODULE = '';

	const CDN_MATERIAL_CACHE_CSS  = 'http://wx.hysci.com.cn/yhsci/Common/css/phone.css';
	
	//分页参数
	const VAR_PAGE = 'p';
	const PAGE_LISTROWS = 10;
	//群发搜索用户上行时间限制TODO:注意添加服务器上
	const MASS_USER_UP_TIME = 40;//单位 小时

	//图文素材相关配置
	const NEWS_MAX_TITLE_LENGTH = 128;//按字符定义 2个英文字符 = 1汉字
	const NEWS_MAX_AUTHOR_LENGTH = 24;//按字符定义 2个英文字符 = 1汉字
	const NEWS_MAX_DESCRIPTION_LENGTH = 240; //按字符定义
	const NEWS_MAC_COUNT = 10;
	const NEWS_TEXT_URL = 'http://wx.hysci.com.cn/yhsci/News/show.php'; //text
	const NEWS_ORIGINAL_URL = 'http://wx.hysci.com.cn/yhsci/News/original.php';
	const NEWS_MONITOR_JS_PATH = 'http://wx.hysci.com.cn/yhsci/News/track.js';
	const NEWS_TEXT_URL_REVIEW = 'http://wx.hysci.com.cn/yhsci/News/preview.php';
	const NEWS_TEXT_URL_SHOW = 'http://wx.hysci.com.cn/yhsci/News/show.php';

	//带参数二维码
	const QR_LIMIT_SCENE_MAX = 100000;//永久有效二维码scene最大值



	const TIMING_MASS_INTERVAL_MIN = 10 ;//单位：分钟
	const TASK_USER_STORE_MAX = 10000;//务用户最大存储量
	//php程序路径
	const PHP_CLI_PATH = '/usr/bin/php';
	

	
}

/**
 * 企业配置项key
 * @author paizhang
 *
 */
class EntSettingKey
{
	const PLUGIN = 'plugins'; //企业插件配置
	const SESSION_MAX = 'session_max'; //每个客服最大同时会话数
	const ESSION_MAX = 'session_max';
	const OPERATOR_MAX = 'operator_max'; //客服最大数
	const BRANCH_ADMIN_MAX = 'branch_admin_max'; //门店管理员最大数

	//手动绑定短链token
	const MANUAL_BIND_TOKEN = 'manual_bind_token';
	//手动绑定时验证用户填写信息有效性的url，由企业提供
	const MANUAL_BIND_CHECK_URL = 'manual_bind_check_url';
	//扫描二维码绑定短链token
	const SCANNEN_BIND_TOKEN = 'scannen_bind_token';
	//绑定后同步到企业的url，由企业提供
	const BIND_NOTICE_URL = 'bind_notice_url';
	//绑定完成后转向url,由企业配置
	const BIND_CALLBACK_URL = 'bind_callback_url';

	//微信会员注册短链token
	const MEMBER_REGIST_TOKEN = 'member_regist_token';
	//微信会员注册后企业同步数据地址，由企业提供
	const MEMBER_REGIST_NOTICE_URL = 'member_regist_notice_url';
	//自定义菜单最后一次同步到微信时间
	const CUSTOM_MENU_LAST_SYNCHRONOUS_TIME = 'custom_menu_last_synchronous_time';
	//自定义菜单最后一次修改时间
	const CUSTOM_MENU_LAST_UPDATE_TIME = 'custom_menu_last_update_time';

	//群发消息总下行数
	const MASS_SEND_TOTAL = 'mass_send_total';
	//群发消息每日最大下行数
	const MASS_SEND_DAY_MAX = 'mass_send_day_max';
	//群发消息总剩余下行数
	const MASS_SEND_SURPLUS_TOTAL = 'mass_send_surplus_total';
	//需要向企业推送消息，setting 表中value值为由PushToThirdpartyKey中的值组成的数组后序列化
	const PUSH_TO_THIRDPARTY_SET = 'push_to_thirdparty_set';
	//需要向企业推送消息，url 表中value值为由PushToThirdpartyUrl
	const PUSH_TO_THIRDPARTY_URL = 'push_to_thirdparty_url';
	//客服服务时间
	const OPERATOR_ONLINE_TIME = 'operator_online_time';
	//客服非服务时间自动回复消息内容
	const OPERATOR_UNLINE_CONTENT = 'operator_unline_con';

	//OAuth 授权权限
	const OAUTH_SCOPE = 'oauth_scope';

	//是否自动进入人工客服设置
	const OPERATOR_IS_AUTO = 'operator_is_auto';
	//关键词
	const OPERATOR_AUTO_KEYWORD = 'operator_auto_keyword';
	//自动回复内容
	const OPERATOR_AUTO_CONTENT = 'operator_auto_content';
	//进入企业会员系统url
	const OPERATOR_INTO_ENT_SYSTEM_URL = 'operator_into_ent_system_url';

	//进入队列提示
	const OPERATOR_USER_INTO_QUEUE_CONTENT = 'operator_user_into_queue_content';
	//超时退出队列提示
	const OPERATOR_USER_TIMEOUT_QUEUE_CONTENT = 'operator_user_timeout_queue_content';
	//进入一线客服提示
	const OPERATOR_USER_INTO_LEVEL_ONE_CONTENT = 'operator_user_into_level_one_content';
	//退出人工客服提示
	const OPERATOR_USER_OUT_OPERATOR_CONTENT = 'operator_user_out_operator_content';
	//人工客服会话转菜单递进式应答会话提示
	const OPERATOR_USER_SHIFT_TO_PROCESS_CONTENT = 'operator_user_shift_to_process_content';
	//队列中排队最大时间（分钟）
	const USER_QUEUE_TIME_LINIT = 'user_queue_time_limit';
}


/**
 * 插件key定义常量类
 * @author paizhang
 */
class PluginKey
{
	const WELCOME = 'Welcome';//欢迎词
	const LOCATION = 'Location'; //地理位置推送
	const KEYWORD = 'Keyword'; //关键词回复
	const CONSULT = 'Consult'; //自定义咨询服务
	const CUSTOM_MENU = 'CustomMenu'; //自定义菜单
	const QRC_PARAM = 'QrcParam'; //带参数二维码
	//const BRANCH = 'Branch'; //门店地理位置用户自动分配
}

/**
 * 插件被命中信息,dialog中存储
 * @author paizhang
 *
 */
class PluginDialogInfo
{
	public $key; //插件key
	public $locationId; //地理位置回复id
	public $keyword; //关键词
	public $ruleId; //关键词规则
	public $processId; //流程id
	public $processDetailId; //流程步骤id
	public $id;
}

/**
 * 第三方通讯认证参数
 *
 */
class AuthQueryData
{
	const REQUEST_AUTH_API_KEY = 'apiKey'; //api key
	const REQUEST_AUTH_API_SECRET = 'apiSecret'; //api secret
	const REQUEST_AUTH_TIMESTAMP = 'timestamp'; //时间戳
	const REQUEST_AUTH_SIGNATURE = 'sig'; //密钥
}

/**
 * 请求第三方时参数名定义
 */
final class ThirdPartyReqParams
{
	const OPEN_ID = 'openid';
	//自定义查询插件
	const QUERY   = 'q';
	//地理位置坐标
	const LOCATION_X = 'x';
	const LOCATION_Y = 'y';
	//二维码
	const UID = 'uid';
	const CID = 'cid';
	const SUBSCRIBE = 'subscribe';
	//后置推送类型
	const PUSH_TYPE = 'push_type';
	//客服id
	const OPERATOR_ID = 'operatorid';
	//当前登录到企业系统的哦用户id，在嵌入第三方页面时使用
	const USER_ID = 'user_id';
	//转向第三方地址时来源系统类型；callcenter or manager
	const SOURCE = 'source';
	
	
	const APP_ID = 'app_id';
	const APP_SECRET = 'app_secret';
	const TIMESTAMP = 'timestamp';
	const SIG = 'sig';

}

/**
 * 监测数据
 */
class MonitorParams
{
	const DELIMITER = ';';//分割符$formatArray
	const WECHAT = 0;//微信号
	const MATERIAL_ID = 1;//素材（消息）id
	const MATERIAL_INDEX = 2;//消息index
	const SOURCE_ID = 3;//贡献者openid
	const MOUDEL  = 4;//消息来源功能模块
	const MODUEL_ID = 5;//不同模块中关联的信息id
	const RULE_ID = 6;//关键词规则id
	const OPERATOR_ID = 7;//客服id
	const USE_OAUTH = 8;//是否使用oauth 0|1
	const MSG_SOURCE = 9;//消息来源(1:系统预设，2:动态回复)
	const QRC_APP_ID = 10;//二维码应用id
	const EVENT_KEY = 11;//带参数二维码扫描event key
	const MATERIAL_SOURCE = 12;//素材来源模块 MOUDEL
	const EVENT_TYPE = 13;//上行消息event类型
	//----MOUDEL定义--------
	const MOUDEL_WELCOME = 1;//欢迎词,MODUEL_ID = qrc_app_img id
	const MOUDEL_QRCODE_PARAM = 2;//带参数二维码 MODUEL_ID = qrc_app_img id
	const MOUDEL_KEYWORD = 3;//关键词 MODUEL_ID = keyword_id
	const MOUDEL_CUNSTOM_MENU = 4;//自定义菜单 MODUEL_ID = menu_id
	const MOUDEL_CONSULT = 5;//自定义回复插件 MODUEL_ID = wx_plug_consult_set.id
	const MOUDEL_LOCATION = 6;//地理位置插件 MODUEL_ID = wx_plug_location_set.id
	const MOUDEL_API = 7;//api发送  MODUEL_ID = wx_api_msg_fast.task_id
	const MOUDEL_MASS = 8;//群发 MODUEL_ID = wx_api_msg_task.task_id
	const MOUDEL_FAQ = 9;//FAQ  MODUEL_ID = faq_id
	const MOUDEL_PROCESS = 10;//IVR  MODUEL_ID = wx_process.p_id
	const MOUDEL_QRCODE_CUSTOM = 11;//自定义二维码 MODUEL_ID = qrc_app_img id
	const MOUDEL_FRONT_PLUGIN = 12;//前置插件
	const MOUDEL_OTHER = 50;//其他

}
/**
 * 监测http请求参数
 *
 */
final class MonitorHttpParams
{
	const ENT_ID = 'entid';
	//《正文和原文》------- -------
	//正文中签名参数[mid,index,MONITOR_DATA,openid]
	//原文中签名参数[MONITOR_DATA,openid,TARGET]
	//素材（图文消息）id
	const MATERIAL_ID = 'mid';
	//图文index
	const INDEX = 'index';
	const WECHAT = '';//企业微信号，预览使用
	//检测
	const MONITOR_DATA = 'MONITOR_DATA';
	//目标url地址
	const TARGET = 'TARGET';
	const CALLBACK_URL = 'CALLBACK_URL';
	const M_FROM = 'MFROM';//请求来源 msg|share|forward
	const OAUTHED = 'OAUTHED';//是否通过oauth转换 0|1
	const FACK_ID = 'FACK_ID';//请求id
	const OPEN_ID = 'openid';//
	const TYPE = 'type';//检测类型.pv,timeline,friend,pagetime 等
	const VIEW = 'view';//当前页面类型,text or original
	const START_TIME = 'start_time';//页面载入时间
	const END_TIME = 'end_time';//页面销毁时间
	//《正文和原文》--------------
}

/**
 * 全局信息catch id 前缀
 */
class GlobalCatchId
{
	// + appid
	const WX_API_TOKEN = 'g_app_token_';
	//+auth_token
	const WX_WEB_AUTH_TOKEN = 'g_auth_token_';
	//企业用户访问权限 + wechat + '-' + adminuser_id
	const REQUEST_PERMISSION_ADMIN = 'request_permission_admin_';
	//随视API_info + api_key
	const ABC_API_INFO = 'sh_api_info_';
	//随视api权限 + wechat
	const ABC_API_PERMISSION = 'sh_api_permission_';
	//素材消息缓存
	const MATERIAL_MESSAGE_INFO = 'sh_material_info_';
	//素材正文缓存 + wechat + _ + materialid + _ + index
	const MATERIAL_TEXT_INFO = 'sh_material_text_';
	//关键词规则在有效期列表 + wechat
	const KEYWORD_RULES = 'sh_keyword_rules_';
	//关键词规则下对应关键词列表 + wechat + '_' + rule_id
	const KEYWORD_RULE_KEYWORDS = 'sh_keyword_rule_keyword_';
	//自定义回复插件规则列表 + wechat
	const CONSULT_RULES = 'sh_consult_rules_';
	//基础的key
	const ABC_BASE_KEY = 'a_base_';
}

/**
 * 全局信息catch 有效期(秒)
 */
class GlobalCatchExpired
{
	const ENT_APP_INFO = 3600;//1小时
	const ENT_SETTING = 3600;//1小时
	const WX_API_TOKEN = 6600;//1小时50分
	const WX_WEB_AUTH_TOKEN = 360000;//100小时
	const REQUEST_PERMISSION_ADMIN = 3600; //请求权限一个小时
	const ABC_API_INFO = 3600; //1小时
	const ABC_API_PERMISSION = 3600; //1小时
	const MATERIAL_MESSAGE_INFO = 3600; //1小时
	const MATERIAL_TEXT_INFO = 3600; //1小时
	const KEYWORD_RULES = 3600; //1小时
	const KEYWORD_RULE_KEYWORDS = 3600; //1小时
	const CONSULT_RULES = 3600; //1小时
	const ENT_PLUGINS = 3600; //1小时
	const ABC_BASE_DURTION = 3600; //1小时
}







/**
 * 需要向企业推送消息配置key
 * @author paizhang
 */
class PushToThirdpartyKey
{
	const TEXT = 'text'; // 文本类型消息
	const IMAGE = 'image'; //图片类型消息
	const SUBSCRIBE = 'event_subscribe'; //关注事件
	const UNSUBSCRIBE = 'event_unsubscribe'; //取消关注
	const LOCATION = 'location';  //客户主动上行地理位置信息

	const CLOSE_SESSION = 'close_session'; //客服关闭完成会话


	/**
	 * 获取配置key和描述列表数据
	 * array('text','image')
	 * @return array
	 */
	public static function get () {
		//type : 1 为 按消息类型， 2 为按功能
		return array(
				'1'=>array(
						array ('id'=>self::TEXT, 'name'=>'文本类型消息', 'type'=>1),
						array ('id'=>self::IMAGE, 'name'=>'图片类型消息', 'type'=>1),
						array ('id'=>self::SUBSCRIBE, 'name'=>'用户关注帐号', 'type'=>1),
						array ('id'=>self::UNSUBSCRIBE, 'name'=>'用户取消关注', 'type'=>1),
						array ('id'=>self::LOCATION, 'name'=>'地理位置消息', 'type'=>1),
				),
				'2'=>array(
						array ('id'=>self::CLOSE_SESSION, 'name'=>'客服完成会话', 'type'=>2),
				)
		);
	}

	public static function getMsgTypeKeys () {
		return array(
				'text' => self::TEXT,
				'image' => self::IMAGE,
				'event_subscribe' => self::SUBSCRIBE,
				'event_unsubscribe' => self::UNSUBSCRIBE,
				'location' => self::LOCATION,

		);
	}
}




/**
 * 二维码相关基础参数配置，本类将会被WxWeb下二维码应用中继承
 * @author paizhang
 */
class QrCodeParamter
{
	const CODE = 'code';//微信传递参数key名
	const STATE = 'state';//微信与我们共同拥有的参数，key名
	const STATE_VALUE = 'STK';//我们定义state对应值
}




