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
<body class="write-body">
<div class="tab"><p><a href="<?php echo url("Message","index");?>">寄信</a><a href="<?php echo url("Message","inbox")?>" class="current">收信</a></p></div>   
<div class="message-total" style="padding-top: 172px;">恭喜您！<br>已经收到<?php echo $inboxCount?>封<br>爱锁信件</div>
<div class="reserivd-tips">
    <h2>尊敬的：<?php echo $regObj['truthname'];?></h2>
    <p>当前为锁定状态，无法查看爱锁信件</p>
</div>

</body>
</html>
