 <?php tpl('Common.header');?>
 <style type="text/css"> 
body{ 
background-image: url(images/unlockok.jpg);
   background-repeat:no-repeat;
   background-size:cover;
} 
.write-msg-btn {
  width: 100%;
  padding: 16px;
}
.write-msg-btn a {
  display: block;
  background: #06be04;
  border-radius: 3px;
  text-align: center;
  height: 36px;
  line-height: 36px;
  color: #ffffff;
  font-size: 1.5rem;
}
.write-msg-btn a i.icon-write {
  display: inline-block;
  width: 17px;
  height: 15px;
  vertical-align: top;
  background: url("../images/icon2@2.png") no-repeat;
  -webkit-background-size: 50px 100px;
  background-size: 50px 100px;
  height: 18px;
  vertical-align: middle;
  background-position: -36px 0;
}
</style>
 </head>
<body>
<!-- 
<div class="M_content zaiWidth">
        
 <div class="M_content" style="padding-top:60px;">
        <div class="BigClock"></div>

        <h3 class="user_xy color_t_Green">双方解锁成功</h3>
        
  </div>
  <a href="<?php echo url("Message","inbox")?>"  >
 	<div class="button WJ_Green" >查阅私信</div>
  </a>
</div>
  -->
     <div class="write-msg-btn" style="padding-bottom:6;position:fixed;bottom:0;left:0;">
        <a href="<?php echo url("Message","inbox")?>" style="font-size: 18px">查阅收信</a>
    </div>
 <?php tpl("Common.foot");?>

