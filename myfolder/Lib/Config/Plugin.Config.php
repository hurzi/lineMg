<?php
return array(
		0 => array(
			'plugin_id' => 1,
			'key' => 'Welcome',
			'exec_type' => 'local',//local｜http
			'file_path' => 'Welcome/Filter/WelcomeFilter.class.php',//文件路径｜url
			'class_name' => 'WelcomeFilter',//类名
			'method_name' => 'main',//方法名
			'class_type' => 'static',//类执行方式instance｜static
		),
		1 => array(
			'plugin_id' => 2,
			'key' => 'CustomMenu',
			'exec_type' => 'local',//local｜http
			'file_path' => 'CustomMenu/Filter/CustomMenuFilter.class.php',//文件路径｜url
			'class_name' => 'CustomMenuFilter',//类名
			'method_name' => 'main',//方法名
			'class_type' => 'static',//类执行方式instance｜static
		),
		2 => array(
			'plugin_id' => 3,
			'key' => 'QrcParam',
			'exec_type' => 'local',//local｜http
			'file_path' => 'QrcParam/Filter/QrcParamFilter.class.php',//文件路径｜url
			'class_name' => 'QrcParamFilter',//类名
			'method_name' => 'main',//方法名
			'class_type' => 'static',//类执行方式instance｜static
		),
		3 => array(
			'plugin_id' => 4,
			'key' => 'Keyword',
			'exec_type' => 'local',//local｜http
			'file_path' => 'Keyword/Filter/KeywordFilter.class.php',//文件路径｜url
			'class_name' => 'KeywordFilter',//类名
			'method_name' => 'main',//方法名
			'class_type' => 'static',//类执行方式instance｜static
		),
		/*3 => array(
			'plugin_id' => 4,
			'key' => 'Authentication',
			'exec_type' => 'local',//local｜http
			'file_path' => 'Verify/Filter/AuthenticationFilter.class.php',//文件路径｜url
			'class_name' => 'AuthenticationFilter',//类名
			'method_name' => 'main',//方法名
			'class_type' => 'static',//类执行方式instance｜static
		), */
	);