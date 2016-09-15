<?php

use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\LineHTTPClient;
/**
 * 首页
 */
class IndexAction extends BaseAction{
	private $_model;
	public function __construct()
	{
		//parent::__construct();
		//$this->_model = loadModel('Index');
	}
	/**
     *  主管理界面
     */
    public function index(){    	 
    	$this->display();    
    }
    
 	public function welcome(){
    		$this->display();    
    }
    
    public function test(){
    	echo "aaabb";
    	$botApi = new LINEBot(LineConfig::$base, new LineHTTPClient(LineConfig::$base));
    	$result = $botApi->sendText(['u82a358394d656c974a04e5d78d444af5'], 'hello!');
    	var_dump($result);
    	echo "Line test index";
    	echo "test  xiaoi bot";
    	include_once LIB_PATH . '/../AbcPHP/Org/iBotCloud/XiaoiBot.php';
    	$bot = new XiaoiBot( [ 'app_key' => 'QCrCl92wojmX', 'app_secret' => 'HX8klwdrbOJTPYaQukbj' ] );
    	//自支应答
		$askResult = $bot->ask('您是谁');
		$botResult = "我暂时还无法回答您";
		if($askResult && $askResult[0]==200){
			$botResult = "回复:".$askResult[1];
		}
		echo $botResult;
    	exit;
    }
    
    /**
     * 修复数据
     */
    public function fixData(){
    	    	
    }
    
    /**
     * 
     */
    private function getFileSql($filename){
    	$file_handle = fopen($filename, "r");
    	$lastindex = true;
    	$sqls = array();
    	$sql = "";
    	while (!feof($file_handle)) {
    		$line = fgets($file_handle);
    		$indexpos = strrpos($line,']');
    		if($indexpos===false){
    			$sql = $sql." ".$line;
    			$lastindex = false;
    		}else{
    			if(!$lastindex){
    				$sql = $sql." ".substr($line, $indexpos+1);
    				$sqls[] = $sql;
    				$lastindex = true;
    				$sql = "";
    			}else{
    				$sqls[] = $sql;
    				$lastindex = true;
    				$sql = "";
    			}
    		}
    	}
    	if($sql){
    		$sqls[] = $sql;
    	}
    	fclose($file_handle);
    }
}
