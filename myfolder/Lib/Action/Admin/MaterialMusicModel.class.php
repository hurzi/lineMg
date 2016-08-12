<?php
class MaterialMusicModel extends Model
{
	public $tableName = "wx_material";

	/**
	 * 获取音乐素材总数
	 */
	public function getCount()
	{
		$total = 0;
		$sql = "SELECT COUNT(*) FROM {$this->tableName} WHERE type = 'Music'";
		try {
			$total = $this->dbEnt->getOne($sql);
		} catch ( Exception $e ) {
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
		}
		return $total;
	}

	/**
	 * 获取音乐素材列表数据
	 * @author zp
	 */
	public function getList($where = '', $limit = '')
	{
		$list = array ();
		$sql = "SELECT * FROM {$this->tableName} WHERE 1 AND type='Music' {$where}  ORDER BY `id` DESC {$limit}";
		try {
			$list = $this->dbEnt->getAll($sql);
		} catch ( Exception $e ) {
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
		}
		return $list;
	}

	/**
	 * @name 验证音乐参数
	 */
	public function checkMusicData($data)
	{
		if ($data['music_url'] == '') {
			$this->setError(1, '普通音乐地址不能为空');
			return false;
		}
		if (! $this->checkUrl($data['thumb_url'])) {
			$this->setError(1, '图片链接地址不正确');
			return false;
		}
		if (! $this->checkUrl($data['music_url'])) {
			$this->setError(1, '普通音乐链接地址不正确');
			return false;
		}
		if ($data['hq_music_url'] != '' && ! $this->checkUrl($data['hq_music_url'])) {
			$this->setError(1, '高清音乐链接地址不正确');
			return false;
		}
		return $data;
	}

	/**
	 * 验证url格式
	 * @param string $url
	 * @return string
	 */
	public function checkUrl($url)
	{
		$url_format = '/^(http:\/\/)?(https:\/\/)?([\w\d-]+\.)+[\w-]+(\/.*)?$/';
		preg_match($url_format, $url, $output);
		return $output;
	}

	/**
	 * 添加音乐素材
	 * @author
	 */
	public function insertData($param = array())
	{
		$flag = false;
		if ($param) {
			try {
				$flag = $this->dbEnt->insert($this->tableName, $param);
			} catch ( Exception $e ) {
				Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
			}
		}
		return $flag;
	}

	/**
	 * 获取单条音乐素材
	 */
	public function getMaterialMusicById($id)
	{
		$music = array();
		$sql = "SELECT * FROM {$this->tableName} WHERE id = %d AND type = 'music'";
		try {
			$music = $this->dbEnt->getRow(sprintf($sql, $id));
		} catch ( Exception $e ) {
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
		}
		return $music;
	}

	/**
	 * 更新音乐素材
	 * @author zp
	 */
	public function updateMusic($id, $param)
	{
		$flag = false;
		if ($param) {
			try {
				$where = " id = {$id}";
				$flag = $this->dbEnt->update($this->tableName, $where, $param);
			} catch ( Exception $e ) {
				Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
			}
		}
		return $flag;
	}

	/**
	 * 删除音乐素材
	 */
	public function deleteMusic($id)
	{
		$flag = false;
		if (! $id) {
			return $flag;
		}
		try {
			if (loadModel('Admin.Material')->checkMaterialIsUse($id)) {
				$this->setError(1, "当前音乐已经被使用，无法删除！");
				return false;
			}
			$where = "id={$id} ";
			$flag = $this->dbEnt->delete($this->tableName, $where);
		} catch ( Exception $e ) {
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
		}
		return $flag;
	}
}