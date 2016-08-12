  
     <?php tpl('Common.header');?>
</head>
<body>

	<header class="header">
		<ul class="header_nav">
			<li><a href="<?php echo url("Message","index");?>">寄信</a></li>
			<li class="nav_active"><a href="<?php echo url("Message","inbox")?>">收信</a></li>
		</ul>

	</header>
  <div class="M_content" style="padding-top:40px;">
        <div class="bg_inbox"></div>

        <h3 class="user_xy color_t_Green" ><font color="#737373">当前为锁定状态，无法查看<br/>已收到“</font><a href="<?php echo url("AsStatus","index",array("showopenid"=>$otherObj['openid'])); ?>" style="color: #06be04"><?php echo $otherObj['truthname']?></a><font color="#737373">”</font> <font color="#737373"><?php echo $inboxCount?> 封信</font></h3>
        
     <!--   <p class="user_xy" >如48小时后未锁定成功，请重新申请</p> --> 

  </div>
 <?php tpl("Common.foot_co");?>

