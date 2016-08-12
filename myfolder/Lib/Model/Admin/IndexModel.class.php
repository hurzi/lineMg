<?php
class IndexModel extends Model
{
	
	public function __construct(){
		
	}
	
	/**
	 * 执行sql
	 * @param array $args
	 * @return array
	 */
	public function execSql($sql)
	{
		
		try {
			$result = $this->getDb()->query($sql);
			return $result;
		} catch (Exception $e) {
			Logger::error(__FILE__.' '.__CLASS__.' '.__METHOD__,'查询出错');
			return false;
		}
	}

}
