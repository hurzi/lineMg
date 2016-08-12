 <?php tpl('Common.header');?>
 
</head>
<body>

<div class="M_content" style="padding-top:20px;">
        
        
        <div class="heti">
           
           <div class="heti_c">
                <div class="ChrysanthemumLock_1"></div>
                <div class="ChrysanthemumLock_2"></div>
           </div>
           

           <div class="" style="margin-top:-60px; height:100px;position:relative;;z-index:999">
                <div class="heti_r" style="margin-right:0px;">
                  <a href="<?php echo url("AsStatus","index",array("showopenid"=>$selfObj['openid']));?>"><img src="<?php echo $selfObj['headimgurl'];?>" width="50" height="50" /></a>
                  <p><?php echo $selfname;?></p>
                </div>
                <div class="heti_r">
                  <a href="<?php echo url("AsStatus","index",array("showopenid"=>$otherObj['openid']));?>"><img src="<?php echo $otherObj['headimgurl'];?>" width="50" height="50" /></a>
                  <p><?php echo $othername;?></p>
                </div>
           </div>

        </div>


        <a href="<?php echo url("Message","index")?>" class="base-btn">
       	 <div class="button WJ_Green">寄信</div>
		</a>

  </div>
<!-- 

  <div class="M_content" style="padding-top:60px;">
        
        
        <div class="heti">
           <div class="heti_l">
                <img src="<?php echo $selfObj['headimgurl'];?>" width="60" height="60" />
                <p style="padding-left: 13px"><?php echo $selfname;?></p>
                <p style="padding-left: 13px"><?php echo $selfsex;?></p>
           </div>
           <div class="heti_c">
                <div class="ChrysanthemumLock" style="width: 100px;height:150px"></div>
           </div>
           <div class="heti_r">
                <img src="<?php echo $otherObj['headimgurl'];?>" width="60" height="60" />
                <p style="padding-left: 13px"><?php echo $othername;?></p>
                <p style="padding-left: 13px"><?php echo $othersex;?></p>
           </div>
        </div>
        

        <h3 class="user_xy color_t_Green">恭喜！你与<?php echo $othername;?>已锁定成功！</h3>
        
        <p class="user_xy" >点击右上角到朋友圈,分享你的幸福吧！</p>
       <a href="<?php echo url("Message","index")?>" class="base-btn">
       	 <div class="button WJ_Green">寄信</div>
		</a>
  </div>
   -->
 <?php tpl("Common.foot");?>
 
 
