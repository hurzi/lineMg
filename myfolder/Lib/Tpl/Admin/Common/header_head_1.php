<?php
if ($action == 'Shop') { ?>
	<link type="text/css" rel="stylesheet" href="./Public_1/css/style.css?v=<?php echo VERSION;?>"/>
<?php }else if ($action == 'CustomMenu' ){?>
	<link type="text/css" rel="stylesheet" href="./Public_1/css/sessionHistoryDetail.css?v=<?php echo VERSION;?>" />
	<script type="text/javascript" src="./Public_1/cj/showMsg/showSendInfo.js?v=<?php echo VERSION;?>"></script>
	<script type="text/javascript" src="./Public_1/js/materialSelecter.js?v=<?php echo VERSION;?>"></script>
	<script type="text/javascript" src="./Public_1/cj/ckplayer/ckplayer.js?v=<?php echo VERSION;?>"></script>
	<link type="text/css" rel="stylesheet" href="./Public_1/css/Amass.css?v=<?php echo VERSION;?>" />
	<script type="text/javascript" src="./Public_1/js/customMenu.js?v=<?php echo VERSION;?>"></script>
	<script type="text/javascript" src="./Public_1/cj/MsgEditor/messageSelector.js?v=<?php echo VERSION;?>"></script>
<?php } ?>