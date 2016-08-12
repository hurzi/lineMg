
 
  <?php tpl('Common.header');?>
   </head>
<body>
  
  <div class="M_content">
        <div style="background:url(images/dsws.jpg); width:290px;height:284px;margin: 0 auto;margin-top:70px"></div>

     <!-- 
      <div class="TXimg HGreen"></div>

      <h3 class="user_xy">您还未锁定，请先锁定</h3>
       -->
	   <a href="<?php echo url("Lock","index")?>" id="sub_btn" class="base-btn" >	
      <div style="margin-top:80px;" class="button WJ_Green">申请锁定</div>
		</a>
  </div>
   <?php tpl("Common.foot");?>