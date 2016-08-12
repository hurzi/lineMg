<?php
return array(
	//调试及日志配置
	'LOCAL_RUNING' => false,//是否本地运行,本地运行采用测试地址
	'PUBLIC_SERVICE' => false,//是否为正式服务，否：cache不会启用redis
	'DEBUGGING' => true,//debug 模式
	'ENABLE_RUN_LOG' => TRUE,//是否开启运行日志
	'ENABLE_SQL_LOG' => TRUE,//是否开启sql日志
	'ENABLE_SYSTEM_LOG' => true,//是否开启system日志
	'RUN_SHELL' => false, //运行方式是否为脚本方式
	'RUN_LOG_LEVEL' => LOG_E_ALL,//运行日志级别LOG_E_ALL,LOG_E_ERROR
	'LOG_PATH' => '../../../myfolder/log/',//日志目录，以“/”结束  //在init文件中可以动态修改
	'PHP_CLI_PATH' => '/usr/bin/php',//php脚本命令
	
	//开关配置
	'NEED_WXPAY' => true,  //是否需要微信支付
	
	//基础程序配置(基本不变)
	'DEFAULT_ACTION' => 'Index',//默认ACTION
	'DEFAULT_METHOD' => 'index',//默认METHOD
	'APP_GROUP'  => '',//App GROUP
	'VAR_AJAX_SUBMIT' => 'ajax',//ajax请求标识
	'ON_INIT_AFTER' => NULL,//ABCPHP 初始化后调用callback
	'DEFAULT_CACHER' => 'file', //默认cache方式,redis|file|remote
	//redis配置(DEFAULT_CACHER=redis有效)
	'REDIS_HOST' => '',
	'REDIS_PORT' => '',
	//远程cache方式配置(DEFAULT_CACHER=remote有效)
	'REMOTE_CACHE_HOST' => '',//URL
	'REMOTE_CACHE_PORT' => '80',
	//file cache (DEFAULT_CACHER=file有效)
	'FILE_CACHE_PATH' => '',//文件缓存的目录

	//微信应用配置(188微信号测试信息)
	'APP_ID' => 'wx2d373f6d7988cbfa',
	'APP_SECRET' => '8532ad3d96f3b9491f74d685e7f44c0e',
	'APP_NAME' => '小何测试',
	'APP_WEIXIN_USER' => 'gh_1ff1a7020e03',
	'APP_WEIXIN_API_TOKEN' => 'dfwetawere',
		
	//图文监测
	'NEWS_TEXT_URL' => WEB_PATH.'/News/show.php',  //text
	'NEWS_ORIGINAL_URL' =>  WEB_PATH.'/News/show.php', //original
	//oauth参数：授权地址
	'OAUTH_URI' => WEB_PATH."/Oauth/index.php", 
	//oauth参数：授权回调地址
	'OAUTH_REDIRET_URI' => WEB_PATH."/Oauth/index.php?a=Index&m=callback",
	//oauth参数：微信OAuth2.0授权接口地址
	'OAUTH_WX_AUTH_PATH' => "https://open.weixin.qq.com/connect/oauth2/authorize?appid=APP_ID&redirect_uri=REDIRET_URI&response_type=code&scope=SCOPE&state=STATE#wechat_redirect",
	//oauth参数：授权state参数
	'OAUTH_STATE' => 'abc',
		
	//微信号
	'WECHAT' => '',
	//插件目录
	'PLUGIN_PATH' => LIB_PATH . '/Plugin/',
	'WX_APP' => array(
				'EXEC_TYPE' => 'local',//local｜http
				'FILE_PATH' => LIB_PATH . '/WXApp/WXApp.class.php',//文件路径｜url
				'CLASS_NAME' => 'WXApp',//类名
				'METHOD_NAME' => 'main',//方法名
				'CLASS_TYPE' => 'static',//类执行方式instance｜static
	),
	//插件配置文件
	'PLUGINS' => include(LIB_PATH.'/Config/Plugin.Config.php'),
		
	//数据库配置(默认数据库) DB CONFIG'
	'DB_HOST' => 'qdm216841497.my3w.com',
	'DB_USER' => 'qdm216841497',
	'DB_PASSWORD' => 'yhsciroot',
	'DB_NAME' => 'qdm216841497_db',
	//数据库配置(其它辅助数据库)
	'DB_CONFIGS' => array(
			'tanxingdb' => array(
				'DB_HOST' => 'rdsl4w179a0jj12tt06n.mysql.rds.aliyuncs.com',
				'DB_USER' => 'ry42wn65tuuwl725',
				'DB_PASSWORD' => 'yhsciroot',
				'DB_NAME' => 'ry42wn65tuuwl725',
			),
	),
);

