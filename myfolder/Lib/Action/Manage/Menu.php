<?php
//后台管理菜单
return array(
    array(
        'name' => '微信管理',
        'icon' => 'fa fa-briefcase',
        'id' => 'shanghuguanli',
        'child' => array(
            array(
                'url' => url("WxMaterialNews", "index"),
                'name' => '图文素材管理',
            	'icon' => 'fa fa-briefcase',
                'key' => array("EnterpriseFunc"),
            	'method' => array('WxMaterialNews.index', 'WxMaterialNews.edit')
            ),
            array(
                'url' => url('WxCustomMenu', 'index'),
                'name' => '自定义菜单',
            	'icon' => 'glyphicon glyphicon-plus',
                'method' => array('WxCustomMenu.index')
            ),
        )
    ),
    array(
        'name' => '其它管理',
        'icon' => 'glyphicon glyphicon-folder-close',
        'id' => 'dingdanguanli',
        'child' => array(
            array(
                'url' => url('ZgykdxEvaluating', 'index'),
                'name' => '评教管理',
            	'icon' => 'fa fa-credit-card',
                'method' => array("ZgykdxEvaluating.index","ZgykdxEvaluating.add","ZgykdxEvaluating.update"),
            	'ignore_method' => array()
            ),array(
                'url' => url('SeUser', 'index'),
                'name' => '管理员管理',
            	'icon' => 'fa fa-credit-card',
                'method' => array("SeUser.index","SeUser.add","SeUser.update"),
            	'ignore_method' => array()
            ),
        )
    )
);
