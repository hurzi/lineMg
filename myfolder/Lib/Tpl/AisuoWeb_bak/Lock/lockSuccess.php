<!doctype html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>爱锁</title>
    <meta content="爱锁" name="keywords" />
    <meta content="爱锁" name="description" />
    <link href="css/style.css" rel="stylesheet" type="text/css">
</head>
<body class="apply-body">
    <div class="bind-success">
        <h1>恭喜您！您与<?php echo $othername;?>已经绑定成功！</h1>
        <p>您可以点击右上角按钮分享到朋友圈!</p>
    </div>
                
    <div class="bind-head">
        <ul>
            <li>
            	<img src="<?php echo $selfObj['headimgurl'];?>" alt="" width="40" height="40">
                <p><?php echo $selfname;?></p><p><?php echo $selfsex;?></p>
            </li>
            <li>
                <img src="<?php echo $otherObj['headimgurl'];?>" alt="" width="40" height="40">
                <p><?php echo $othername;?></p><p><?php echo $othersex;?></p>
            </li>
        </ul>
    </div>
    <div class="btn pt10"><a href="<?php echo url("Message","index")?>" class="base-btn">寄信</a></div>
</body>
</html>
