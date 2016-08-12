 <?php tpl('Common.header');?>

</head>
<body>

  <div class="M_content" style="padding-top:60px;">
        

        <div class="Ta_c bm10">解锁申请已提交，请等待</div>

        <div class="BigClock"></div>

        <h3 class="user_xy color_t_Green"  style="margin-bottom: 10px;">距离最终确认还有<font id="durtionStr"><?php echo $durtionStr;?></font></h3>
        
        <p class="user_xy" >解锁后，再次锁定成功的一方，原私信收信箱数据全部清空，请谨慎操作！</p>

       <a href="<?php echo url("Pubmsg","index")?>" id="sub_btn" class="base-btn" >	
      <div style="margin-top:20px;" class="button WJ_Green">公共信池</div>
		</a>
  </div>

<?php tpl("Common.foot_co");?>    
    
