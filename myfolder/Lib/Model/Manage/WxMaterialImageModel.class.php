<?php
class WxMaterialImageModel extends Model{
		var  $db = null;
		var $tableName = 'haier_images';
		function __construct(){
			$this->db = $this->getDb();
		}

	//获取图片素材总数
	public function getCount($sid='')
	{
		$rtnInt = 0;
		$sql = "SELECT count(*) from {$this->tableName} WHERE shop_id=$sid AND is_del=0";
		try {
			$rtnInt = $this->db->getOne($sql);
		} catch ( Exception $e ) {
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
		}
		return $rtnInt;
	}

	/**
	 * 获取图片素材列表数据
	 * @author zp
	 */
	public function getList($sid = '',$where = '', $limit = '')
	{
		$rtnArr = array ();
		$sql = "select * from {$this->tableName} WHERE shop_id=$sid AND is_del=0 ORDER BY inputtime DESC {$limit}";
		try {
			$rtnArr = $this->db->getAll($sql);
		} catch ( Exception $e ) {
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
		}
		return $rtnArr;
	}

	/**
	 * 添加图片素材
	 * @author
	 */
	public function insertData($param = array())
	{
		$flag = false;
		if ($param) {
			try {
				$flag = $this->db->insert($this->tableName, $param);
			} catch ( Exception $e ) {
				Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
			}
		}
		return $flag;
	}

	/**
	 * 获取单条图片素材
	 * @author zp
	 */
	public function getOneMaterialImage($where = '')
	{
		$rtnArr = array ();
		$sql = "select * from {$this->tableName} where 1  {$where}";
		try {
			$rtnArr = $this->db->getRow($sql);
		} catch ( Exception $e ) {
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
		}
		return $rtnArr;
	}

	/**
	 * 更新图片素材
	 * @author zp
	 */
	public function updateImage($id, $param)
	{
		$flag = false;
		if ($param) {
			try {
				$where = " id= {$id}";
				$flag = $this->db->update($this->tableName, $where, $param);
			} catch ( Exception $e ) {
				Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
			}
		}
		return $flag;
	}

	/**
	 * 删除图片素材
	 */
	public function deleteImage($id)
	{
		$flag = false;
		if (! $id) {
			return $flag;
		}
		try {
			$where = " id = {$id} ";
			$flag = $this->db->delete($this->tableName, $where);
		} catch ( Exception $e ) {
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
		}
		return $flag;
	}
	
	
	/**
	 * 
	 *  图片首页显示
	 * @param unknown_type $data
	 */
		function setIsShow($data){
			if(!is_array($data)){
				return 0;
			}
			try {
				$count = $this->db->getRow('SELECT COUNT(id) as sum FROM haier_images  WHERE shop_id='.$data['shop_id'].' AND is_show=1 AND is_del=0');
				if($count['sum']>2 && $data['is_show']==1){
					return 'max';
				}
				$where  = 'id='.$data['id'];
				$arr['is_show']=$data['is_show']; 
				$this->db->update('haier_images',$where,$arr);
				return $this->db->affectedRows();
			} catch (Exception $e) {
				Logger::error('设置图片状态报错:',$e->getMessage());
				return false;
			}
			
		}
	
}