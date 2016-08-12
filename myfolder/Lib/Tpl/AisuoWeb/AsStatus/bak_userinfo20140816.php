  <?php tpl('Common.header');?>
</head>
<body>

  <div class="M_content">
        
       <div class="user_infoN">
            <div class="user_img">
                 <img src="<?php echo $regObj['headimgurl'];?>" width="70" height="70" />
            </div>
            <div class="user_info_rightN">
              <h3 style="padding-left:15px">头像</h3>
            </div>
       </div>

       <div style="margin:0 0 10px 0;">
            <ul class="base_listN">
               <li style=" border-top: 1px solid #A9A9A9;">
                    <h3>昵称</h3>
                    <div class="bl_right" style="font-size:16px;font-weight: normal;">
                       <a href="<?php echo url("AsStatus","username"); ?>"><?php echo $regObj['truthname'];?> ></a>
                    </div>
               </li>
               <li>
                    <h3>性别</h3>
                    <div class="bl_right" style="font-size:16px;font-weight: normal;">
                         <?php echo $regObj['sex'] == 1?'男':'女';?>
                    </div>
               </li>

               <li>
                    <h3>手机</h3>
                    <div class="bl_right" style="font-size:16px;font-weight: normal;">
                         <span class="color_t_Green"><?php echo $regObj['mobile'];?></span>
                    </div>
               </li>
               
				 <li>
                    <h3>地区</h3>
                    <div class="bl_right" style="font-size:16px;font-weight: normal;">
                        <?php echo trim($asZone)?trim($asZone):"火星";?>
                    </div>
               </li>
               <?php if($regObj['as_status'] != 1) {?>
                <li>
                    <h3>爱锁状态</h3>
                    <div class="bl_right" style="font-size:16px;font-weight: normal;">
                         单身  <a href="<?php echo $isself?(url("Lock","index")):"#";?>"><span class="color_t_Green">申请锁定</span></a>
                    </div>
               </li>
                <?php }else {?>
               <li>
                    <h3>爱锁状态</h3>
                    <div class="bl_right" style="font-size:16px;font-weight: normal;">
                         你与<a href="<?php echo url("AsStatus","index",array("showopenid"=>$otherObj['openid']));?>"><span class="color_t_Green" ><?php echo $otherObj['truthname']?></span></a> 已锁定
                    </div>
               </li>
				<li>
                    <h3>锁定日期</h3>
                    <div class="bl_right" style="font-size:16px;font-weight: normal;">
                        <?php echo date("Y.m.d",strtotime($regObj['as_lock_time']))?>,共<?php echo $durtionDay;?>天
                    </div>
               </li> 
                <?php }?>
				<li>
                    <h3>爱锁邮编</h3>
                    <div class="bl_right" style="font-size:16px;font-weight: normal;">
                        <?php echo $regObj['as_code'];?>
                    </div>
               </li> 
				<li>
                    <h3>爱锁积分</h3>
                    <div class="bl_right" style="font-size:16px;font-weight: normal;">
                        <a href="<?php echo AbcConfig::BASE_WEB_DOMAIN_PATH;?>News/show.php?mid=4&index=1"> <?php echo $durtionDay;?>积分 ></a>
                    </div>
               </li> 
               </ul>
       </div>
       <a href="<?php echo url("AsStatus","userinfo",array("isUpdate"=>1));?>"><div class="button WJ_Green">更新用户信息</div></a>

  </div>
 <?php tpl("Common.foot");?>