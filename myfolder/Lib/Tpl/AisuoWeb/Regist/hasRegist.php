  <?php tpl('Common.header');?>
  
  
</head>
<body>

  <div class="M_content">
        
       <div class="user_info" >
            <div class="user_img" style="padding-top: 10px">
                 <img src="<?php echo $regObj['headimgurl'];?>" width="62" height="62" />
            </div>
            <div class="user_info_right" >
                 <h2><?php echo $regObj['truthname'];?></h2>
                 <p>爱锁邮编：<span><?php echo $regObj['as_code'];?></span></p>
                 <p>爱锁状态：<span><?php echo $regObj['as_status']==1?'已锁定':'未锁定';?></span></p>
            </div>
       </div>

       <div style="margin:0 0 80px 0;">
            <ul class="base_list">

               <li>
                    <h3>性别</h3>
                    <div class="bl_right">
                         <?php echo $regObj['sex'] == 1?'男':'女';?>
                    </div>
               </li>

               <li>
                    <h3>手机</h3>
                    <div class="bl_right">
                         <?php echo $regObj['mobile'];?>
                    </div>
               </li>
			
               <li>
                    <h3>爱锁邮箱</h3>
                    <div class="bl_right">
                         连接入口,将来连接到专享
                    </div>
               </li> 
               <li>
                    <h3>爱锁状态</h3>
                    <div class="bl_right">
                    	<?php if($regObj['as_status'] != 1) {?>
                         <span class="color_t_Green">单身</span> 发送邀请,锁定心仪的TA吧！
                         <?php }else {?>
                         您与<span class="color_t_Green"><?php echo $otherObj['truthname']?></span>于<?php echo date('Y年m月d日',strtotime($regObj['as_lock_time']))?>进行了锁定，至今已经<?php echo $durtionDay;?>天
                         <?php }?>
                    </div>
               </li>			
            </ul>
       </div>
        
		<?php if($regObj['as_status']!=1){?>
       <a href="<?php echo url("Lock","index")?>"><div class="button WJ_Green">申请锁定</div></a>
		<?php }?>
  </div>
 
 <?php tpl("Common.foot");?>