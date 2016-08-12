<?php
class MaterialModel extends Model
{
	
	var  $db = null;
	function __construct(){
		$this->db = $this->getDb();
	}
	
	/**
	 * 获取素材信息根据ID
	 * @param int $id
	 * @return NULL|Ambigous <multitype:, unknown>
	 */
	public function getMaterialById($id)
	{
		if (! $id) {
			return array();
		}
		$material = array();
		$sql = "SELECT * FROM wx_material WHERE id = %d";
		try {
			$material = $this->db->getRow(sprintf($sql, $id));
		} catch ( Exception $e ) {
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
		}
		return $this->parseMaterial($material);
	}

	public function parseMaterial($material)
	{
		if (! $material) {
			return array();
		}
		$material['material_id'] = $material['id'];
		switch ($material['type']) {
			case 'news' :
				$material['articles'] = $this->getNewsDetailById($material['id']);
				break;
			case 'music' :
				$tmp = unserialize($material['articles']);
				$material['title'] = @$tmp['title'];
				$material['description'] = @$tmp['description'];
				$material['thumb_url'] = @$tmp['thumb_url'];
				$material['music_url'] = @$tmp['music_url'];
				$material['hq_music_url'] = @$tmp['hq_music_url'];
				break;
			case 'image' :
			case 'voice' :
			case 'video' :
				$material['title'] = (string) $material['title'];
				$material['description'] = (string) $material['description'];
				$material['media_url'] = $material['media_url'];
				break;
		}
		unset($material['id']);
		unset($material['type']);
		unset($material['content']);
		unset($material['create_time']);
		return $material;
	}

	/**
	 * 获取图文明细
	 * @param int $id
	 * @return Ambigous <NULL, multitype:multitype: >
	 */
	public function getNewsDetailById($id)
	{
		$list = array();
		$sql = "SELECT * FROM `wx_material_news` WHERE `is_deleted` = 0 AND material_id = %d ";
		try {
			$list = $this->db->getAll(sprintf($sql, $id));
		} catch ( Exception $e ) {
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
		}
		if ($list) {
			foreach ($list as &$v) {
				$v['text_url'] = '';
				if ($v['news_text']) {
					$queryData = array(
							MonitorHttpParams::MATERIAL_ID => $v['material_id'],
							MonitorHttpParams::INDEX => $v['news_index']
					);
					$v['text_url'] = Config::NEWS_TEXT_URL_SHOW.'?'.http_build_query($queryData);
				}
			}
		}
		return $list;
	}

	/**
	 * 检测素材是否被使用
	 * @param int $id
	 * @return bool
	 */
	public function checkMaterialIsUse($id)
	{
		try {
			//检查faq
			$sql = "SELECT COUNT(*) FROM `wx_faq_material` WHERE material_id = {$id}";
			if ($this->db->getOne($sql)) {
				return true;
			}
			//检查自定义菜单
			$sql = "SELECT COUNT(*) FROM `wx_custom_menu` WHERE material_id = {$id}";
			if ($this->db->getOne($sql)) {
				return true;
			}
			//检查二维码
			$sql = "SELECT COUNT(*) FROM `wx_qr_code_app_msg` WHERE material_id = {$id}";
			if ($this->db->getOne($sql)) {
				return true;
			}

			$dbPlugin = Factory::getDb(DBConfig::WX_PLUGINS_DB);
			$entId = Config::ENT_ID;
			//检查关键词插件
			$sql = "SELECT COUNT(*) FROM `wx_keyword_reply_message` WHERE material_id = {$id} AND ent_id = {$entId}";
			if ($dbPlugin->getOne($sql)) {
				return true;
			}
			//检查地理位置插件
			$sql = "SELECT COUNT(*) FROM `wx_location_push` WHERE material_id = {$id} AND ent_id = {$entId}";
			if ($dbPlugin->getOne($sql)) {
				return true;
			}

		} catch (Exception $e) {
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
			return true;
		}
		return false;
	}
}
