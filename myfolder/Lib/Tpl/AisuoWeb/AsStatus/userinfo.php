  <?php tpl('Common.header');?>
  </head>
<body  style="background-color:#f0eff4">

  <div class="M_content">
       <div style="margin:0 0 10px 0;">
            <ul class="base_listN1">
             <div style="margin:0 0 30px 0;">
               <div class="user_infoN">
                    <div class="user_img">
                         <img src="<?php echo $regObj['headimgurl'];?>" width="65" height="65" />
                    </div>
                    <div class="user_info_rightN1">
                      <h3 style="padding-left:12px;padding-top:5px;color: #000;">头像</h3>
                    </div>
               </div>
            </div>
               <li style=" border-top: 1px solid #cfcfcf;">
                    <h3>昵称</h3>
                    <div class="bl_right">
                   		<a href="<?php echo url("AsStatus","username"); ?>"><span style="color:#2C6582"><?php echo $regObj['truthname'];?></span> 
						<span style="color:#737373">></span></a>
                    </div>
               </li>
				<li>
                    <h3>爱锁邮编</h3>
                    <div class="bl_right">
                        <?php echo $regObj['as_code'];?>
                    </div>
               </li> 
             </ul>
               <br>
              <ul class="base_listN1">
               <li>
                    <h3>性别</h3>
                    <div class="bl_right">
                         <?php echo $regObj['sex'] == 1?'男':'女';?>
                    </div>
               </li>
				 <li>
                    <h3>地区</h3>
                    <div class="bl_right">
                        <?php echo trim($asZone)?trim($asZone):"火星";?>
                    </div>
               </li>
               <li>
                    <h3>手机</h3>
                    <div class="bl_right">
                          <span class="color_t_Blue"  style="color:#2C6582"><?php echo $regObj['mobile'];?></span>
                    </div>
               </li>
               </ul>
                <br>
               <ul class="base_listN1">
               		<?php if($regObj['as_status'] != 1) {?>
                <li>
                    <h3>爱锁状态</h3>
                    <div class="bl_right" >
                         单身  <a href="<?php echo $isself?(url("Lock","index")):"#";?>"><span class="color_t_Green">申请锁定</span></a>
                    </div>
               </li>
                <?php }else {?>
               <li>
                    <h3>爱锁状态</h3>
                    <div class="bl_right">
                         你与<a href="<?php echo url("AsStatus","index",array("showopenid"=>$otherObj['openid']));?>"><span class="color_t_Blue" style="color:#2C6582"><?php echo $otherObj['truthname']?></span></a>已锁定
                    </div>
               </li>
				<li>
                    <h3>锁定日期</h3>
                    <div class="bl_right" >
                        <?php echo date("Y m d",strtotime($regObj['as_lock_time']))?> 至今<?php echo $durtionDay;?>天
                    </div>
               </li> 
                <?php }?>
                   
                    <li>
                        <h3>爱锁积分</h3>
                        <div class="bl_right">
                         	<!--
							<a href="<?php echo AbcConfig::BASE_WEB_DOMAIN_PATH;?>News/show.php?mid=4&index=1">可用积分&nbsp;&nbsp; <?php echo $durtionDay;?></a>
                            -->
							<a href="<?php echo WEB_PATH;?>/News/show.php?mid=34&index=1">可用积分&nbsp;&nbsp; <?php echo $durtionDay;?></a>
                             
                        </div>
                   </li> 
               </ul>
       </div>
        <a href="<?php echo url("AsStatus","userinfo",array("isUpdate"=>1));?>"><div class="button WJ_Green">更新用户信息</div></a>

  </div>
  
 <?php tpl("Common.foot");?>