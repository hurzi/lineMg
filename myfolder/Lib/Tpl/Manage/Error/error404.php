<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>404错误页面-<?= TITLE ?></title>
    <?php tpl('Common.public'); ?>
    <link rel="stylesheet" type="text/css" href="<?= URL; ?>css/stylex.css">
</head>
<body>
<div id="wrap">
    <!--start left menu-->
    <?php tpl('Common.left'); ?>
    <!--end left menu-->
    <div class="cont-right">
        <!--start header-->
        <?php tpl('Common.header'); ?>
        <!--end header-->
        <!--start right content-->
        <!--=====切换====-->
        <div class="cont-right-wrap">
            <div class="error-wrap" style="text-align: center;margin:0 auto;">
                <div class=""><img src="images/management/error.png"></div>
                <div class="" >
                    <h2 style="font-size: 18px;margin:20px 0;"><?php echo @$title;?></h2>
                    <p class="p1">
                    	<?php echo empty($message)?'':$message;?>
                    	<a href="javascript:void(0);" id="go_back">返回</a>
                    	<?php if (!empty($refreshUrl)) {?>
                    	&nbsp;&nbsp;<a href="<?php echo $refreshUrl;?>" >刷新</a>
                    	<?php }?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php tpl('Common.footer'); ?>
    <script>
        $(document).ready(function(){
            $('#go_back').click(function(){window.history.go( -1 );})
        });
    </script>
</body>
</html>
