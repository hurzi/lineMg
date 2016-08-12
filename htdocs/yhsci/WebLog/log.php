<?php
define ( 'APP_GROUP', 'WebLog' );

include_once dirname ( __FILE__ ) . '/../../../myfolder/Lib/Init.php';

$logPath = dirname ( __FILE__ ) . "/../../../myfolder/log/";

$searchKey =  @$_GET ['s'] ;
$logfile = urldecode ( @$_GET ['logfile'] );
$line = isset($_GET ['line'])?$_GET ['line']:50;
$flushTime = @$_GET ['flushTime'];
if (! $logfile || ! file_exists ( $logfile )) {
	$ite = new RecursiveDirectoryIterator ( $logPath );
	foreach ( new RecursiveIteratorIterator ( $ite ) as $filename => $cur ) {
		$fn = $cur->getBasename ();
		if ($cur->isDir () || ($fn == '.' || $fn == '..'))
			continue;
		if($searchKey && strpos($filename, $searchKey) ===false){
			continue;
		}
		echo "<a href='log.php?logfile=" . urlencode ( $filename ) . "'>查看最新日志</a>" . $filename . "</br>";
	}
	exit ();
}

echo "<a href='log.php'>返回目录</a>|<a href='log.php?logfile=".$_GET ['logfile']."&flushTime=2'>2秒自动刷新</a></br>";
echo tail ( $logfile, $line );
if($flushTime){
	echo '<meta http-equiv="refresh" content="'.$flushTime.'">';
}
function tail($logfile, $line = 10) {
	$fp = fopen ( $logfile, "r" );
	$pos = - 2;
	$t = " ";
	$data = "";
	while ( $line > 0 ) {
		while ( $t != "\n" ) {
			$flag = fseek ( $fp, $pos, SEEK_END );
			if (fseek ( $fp, $pos, SEEK_END ) == - 1) {
				// fseek($fp, 0);
				rewind ( $fp );
				$t = "\n";
				$line = 0;
			} else {
				$t = fgetc ( $fp );
				$pos --;
			}
		}
		$t = " ";
		$line --;
	}
	while ( ! feof ( $fp ) ) {
		$data .= htmlspecialchars(fgets ( $fp ));
		$data .= '</br>';
	}
	fclose ( $fp );
	return $data;
}