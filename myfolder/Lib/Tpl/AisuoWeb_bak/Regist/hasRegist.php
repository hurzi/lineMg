<!doctype html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>爱锁</title>
    <meta content="爱锁" name="keywords" />
    <meta content="爱锁" name="description" />
    <link href="css/style.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
</head>
<body class="reg-body">
<div class="reg-article reged">
    <div class="reg-atitle">您已注册爱锁</div>
    <div class="reg-acontent">
        <p>姓名：<?php echo $regObj['truthname'];?></p>
        <p>性别：<?php echo $regObj['sex'] == 1?'男':'女';?></p>
        <p>手机：<?php echo $regObj['mobile'];?></p>
        <p>爱锁状态：<?php echo $regObj['as_status']==1?'已锁定':'未锁定';?></p>
        <p>爱锁邮编：<?php echo $regObj['as_code'];?></p>
    </div>
</div>
</body>
</html>