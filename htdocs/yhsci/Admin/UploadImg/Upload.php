<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script src="../Public_1/js/jquery.min.js" type="text/javascript"></script>
<script src="../Public_1/js/jquery.windows-engine.js" type="text/javascript"></script>
<title>图片上传</title>
<style type="text/css">

body, #uploadForm, #uploadFile {
    height: 26px;
    margin: 0;
    overflow: hidden;
    padding: 0;
    position: absolute;
    right: 0;
    top: 0;
    width: 100%;
    z-index: 1000;
}
</style>
</head>

<body>
<?php
 function getUri () {
		if (isset($_SERVER['HTTP_HOST'])) {
			$uri = dirname($_SERVER['SCRIPT_NAME']);
			if ('/' == $uri || '\\' == $uri) {
				return 'http://' . $_SERVER['HTTP_HOST'] ;
			}
			return 'http://' . $_SERVER['HTTP_HOST'] . $uri ;
		}
		return '';
	}
	$type = isset($_GET['type'])?urldecode($_GET['type']):'.jpg';
	$max_size = isset($_GET['max_size']) ? $_GET['max_size'] : 0;
	
?>
<!-- 上传图片弹出层 -->
		<form id="uploadForm" method="post" target="hidden_frames" enctype="multipart/form-data" action="http://wx.hysci.com.cn/yhsci/Common/UploadImage.php">
             <input type="file" onchange="upload()" class="uploadFile" name="upfile" id="uploadFile" />
             <input type="hidden" name="printFormat" value="proxy" />
             <input type="hidden" name="max_size" value="<?php echo $max_size;?>" />
             <input type="hidden" name="upTypes" value="<?php echo $type;?>" />
			 <input type="hidden" name="callback" value="parent.imgUpload_callback" />
			<?php
				if(isset($_GET['max_size'])){
			?>
			 <input type="hidden" name="max_size" value="<?php echo (int)$_GET['max_size']; ?>" />
			 <?php
				}
			 ?>
			
			 <input type="hidden" name="proxy_url" value="<?php echo getUri().'/imgCallback.php';?>" />
		</form>
    	<iframe name="hidden_frames" style="display: none;"></iframe>
    <!-- 上传图片弹出层end -->
</body>
<?php $callback = isset($_GET['callback'])?$_GET['callback']:'imgUpload_callback';?>
<script type="text/javascript">
    function upload(){
    	$('#uploadForm').submit();
    }
	//上传图片回调地址
	function imgUpload_callback(msg,type){
		$('#uploadFile').val('');
		if(type== false){
			alert(msg);
			return;
		}else{
			parent.<?php echo $callback;?>(msg);
		}
	}
</script>
</html>
