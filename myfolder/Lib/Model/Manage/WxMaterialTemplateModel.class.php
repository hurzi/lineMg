<?php
class WxMaterialTemplateModel extends Model
{
	public $dbEnt;
	public function __construct() {
		parent::__construct ();
		$this->dbEnt =  $this->getDb();
	}
	
	/**
	 * 获取模板素材列表db原始数据
	 * @return array
	 */
	public function getAll()
	{
		$sql = "SELECT id, title, template_id, content FROM `wx_material` WHERE type = 'template'";
		try {
			$list = $this->dbEnt->getAll($sql);
		} catch ( Exception $e ) {
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
			return false;
		}

		return $list;
	}
	/**
	 * 获取模板素材列表
	 * @return array
	 */
	public function getList()
	{
		$list = $this->getAll();

		return $this->parseTemplateData($list);
	}

	/**
	 * 解析模板数据
	 * @param array $list
	 * @return array
	 */
	public function parseTemplateData($list)
	{
		if (! $list || ! is_array($list)) return array();

		if ($list) {
			foreach ($list as &$v) {
				$v['content'] = str_replace("\n", "<br />", $v['content']);
				$v['edit_content'] = preg_replace('/\{\{(\w+)\.DATA\}\}/',
						'<input type="text" id="$1" name="$1" class="template_edit_input" value="" />',
						$v['content']);
				$v['show_content'] = preg_replace('/\{\{(\w+)\.DATA\}\}/', '<span id="$1"></span>', $v['content']);
			}
		}
		return $list;
	}
}