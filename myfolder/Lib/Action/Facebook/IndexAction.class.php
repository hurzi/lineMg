<?php

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
    	
    	echo "FB test index";exit;
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
