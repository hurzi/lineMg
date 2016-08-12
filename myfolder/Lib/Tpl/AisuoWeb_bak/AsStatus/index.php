<!doctype html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>爱锁状态</title>
    <meta content="爱锁" name="keywords" />
    <meta content="爱锁" name="description" />
    <link href="css/style.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="article" style="background: #e7e7e7;height: 500px">
    <address class="atitle">
        <h1>爱锁状态</h1>
        <p><?php echo date('Y年m月')?></p>
    </address>
    <div class="article-cot">
        <img src="images/temp3.png" alt="">
        <h2 class="article-title">您的爱锁状态：<?php echo $asStatusStr;?></h2>
        <?php if($asStatus == 1){?>
        <p>您与<?php echo $otherTruthName;?>于<?php echo $lock_time?>进行了爱锁绑定，至今已有<?php echo $as_durtion_day?>天</p>  
        <?php }else{?>   
        <p>您可以申请爱锁，绑定您想绑定的TA,给TA写信</p>   
        <?php }?>
    </div>
</div>
 <?php if($asStatus != 1){?>
<!-- 解锁弹层 -->
<div class="unlock-div" style="height: 116px;">
    <div class="unlock-conform">
        <!-- <div class="unlock-title">您是否申请爱锁？</div> -->
        <a class="unlock-btn" style="border-bottom: 1px solid #c1c1c6;" href="<?php echo url("Lock","index")?>">申请锁定</a>
        <a class="unlock-btn" href="<?php echo url("Index","introduce")?>">先不申请锁定</a>
    </div>
    <!-- 
    <div class="unlock-conform"><a href="javascript::void" class="unlock-btn">先不申请锁定了</a></div>
     -->
</div>
<?php }?>
</body>
</html>
