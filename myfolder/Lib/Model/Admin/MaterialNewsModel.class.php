<?php
class MaterialNewsModel extends Model{
	private $tableName = null;
	private  $db = null;
	public function __construct()
	{
		parent::__construct();
		$this->db = $this->getDb();
		$this->tableName = "wx_material";
	}
	
	
	/**
     * 启动事务
     * @return void
     */
    public function startTrans() {
    	return $this->_execute('startTrans',array(), true);
    }

    /**
     * 用于非自动提交状态下面的查询提交
     * @return boolen
     */
    public function commit()
    {
        return $this->_execute('commit',array(), true);
    }

    /**
     * 事务回滚
     * @return boolen
     */
    public function rollback()
    {
         return $this->_execute('rollback',array(), true);
    }

	/**
	 * 获取图文素材列表数据
	 * @author zp
	 */
	public function getList($where = '', $limit = '')
	{
		$list = array ();
		$sql = "SELECT * FROM {$this->tableName} WHERE 1 AND type = 'news' {$where}  ORDER BY `create_time` DESC, `id` DESC {$limit}";
		try {
			$list = $this->db->getAll($sql);
		} catch ( Exception $e ) {
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
		}
		if ($list) {
			foreach ($list as &$v) {
				$v['articles'] = $this->getNewsDetailById($v['id']);
				$v['create_time'] = date('Y-m-d', strtotime($v['create_time']));
			}
		}
		return $list;
	}

	/**
	 * 获取图文素材库总记录数
	 * @author zp
	 */
	public function getCount()
	{
		$rtnInt = 0;
		$sql = "SELECT COUNT(*) FROM {$this->tableName} WHERE type = 'news'";
		try {
			$rtnInt = $this->db->getOne($sql);
		} catch ( Exception $e ) {
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage());
		}
		return $rtnInt;
	}

	/**
	 * 验证图文消息数据
	 * @param array $data
	 * @return array
	 */
	public function checkNewsData($data)
	{
		if (! is_array($data)) {
			$this->setError(ErrCode::NEWS_FORMAT_ERROR, ErrCode::getError(ErrCode::NEWS_FORMAT_ERROR));
			return false;
		}
		if (count($data) > Config::NEWS_MAC_COUNT) {
			$this->setError(ErrCode::NEWS_COUNT_LIMIT, ErrCode::getError(ErrCode::NEWS_COUNT_LIMIT));
			return false;
		}
		$params = array ();
		foreach ($data as $key => $val) {
			$len_title = mb_strlen(trim($val['news_title']), 'GBK');
			$len_author = mb_strlen(trim($val['news_author']), 'GBK');
			$len_desc = mb_strlen(trim($val['news_description']), 'GBK');
			$len_text = mb_strlen($val['news_content'], 'GBK');
			$no_html_text_len = mb_strlen(strip_tags($val['news_content'], 'GBK'));
			$img_eregi = "<img[^a][^<>]*>";
			//验证是否存在图片
			$check_content_img_eregi = preg_match($img_eregi, $val['news_content']);
			$check_content_array = array ('&nbsp;', "\n");
			$check_content = trim(str_replace($check_content_array, '', strip_tags($val['news_content'])));
			if ($len_title < 1) {
				$this->setError(ErrCode::NEWS_TITLE_EMPTY, ErrCode::getError(ErrCode::NEWS_TITLE_EMPTY));
				return false;
			}
			if ($len_title > Config::NEWS_MAX_TITLE_LENGTH) {
				$this->setError(ErrCode::NEWS_TITLE_ERROR, ErrCode::getError(ErrCode::NEWS_TITLE_ERROR));
				return false;
			}
			if ($len_author > Config::NEWS_MAX_AUTHOR_LENGTH) {
				$this->setError(ErrCode::NEWS_AUTHOR_ERROR, ErrCode::getError(ErrCode::NEWS_AUTHOR_ERROR));
				return false;
			}
			if ($check_content == '' && empty($val['news_url']) && ! $check_content_img_eregi) {
				$this->setError(ErrCode::NEWS_CONTENT_AND_URL_EMPTY, ErrCode::getError(ErrCode::NEWS_CONTENT_AND_URL_EMPTY));
				return false;
			}
			if (! empty($no_html_text_len) && $no_html_text_len > 20000) {
				$this->setError(ErrCode::NEWS_TITLE_EMPTY, ErrCode::getError(ErrCode::NEWS_CONTENT_LIMIT));
				return false;
			}
			/* if($len_desc < 1){
				$this->setError(ErrCode::NEWS_DISCRIPTION_EMPTY, ErrCode::getError(ErrCode::NEWS_DISCRIPTION_EMPTY)) ;
				return false;
			} */
			if ($len_title > Config::NEWS_MAX_DESCRIPTION_LENGTH) {
				$this->setError(ErrCode::NEWS_DISCRIPTION_ERROR, ErrCode::getError(ErrCode::NEWS_DISCRIPTION_ERROR));
				return false;
			}
			if (! empty($val['news_url']) && ! $this->checkUrl($val['news_url'])) {
				$this->setError(ErrCode::NEWS_ORIGINAL_LINK_FORMAT_ERROR, ErrCode::getError(ErrCode::NEWS_ORIGINAL_LINK_FORMAT_ERROR));
				return false;
			}
			if (! $val['news_img_url']) {
				$this->setError(ErrCode::NEWS_PIC_LINK_IS_NULL, ErrCode::getError(ErrCode::NEWS_PIC_LINK_IS_NULL));
				return false;
			}
			if (! $this->checkUrl($val['news_img_url'])) {
				$this->setError(ErrCode::NEWS_PIC_LINK_FORMAT_ERROR, ErrCode::NEWS_PIC_LINK_FORMAT_ERROR);
				return false;
			}
			$arr = array ();
			$arr['title'] = trim($val['news_title']);
			$arr['author'] = trim($val['news_author']);
			$arr['description'] = trim($val['news_description']);
			$arr['url'] = trim($val['news_url']);
			$arr['show_cover_pic'] = trim($val['news_show_cover_pic']);
			$arr['picurl'] = trim($val['news_img_url']);
			$arr['content'] = $val['news_content'];
			$params[] = $arr;
		}
		return $params;
	}

	/**
	 * 添加数据
	 */
	public function insert($news)
	{
		
		$data = $this->makeNewsData($news);
		$materialData = $data['materialData'];
		$materialNewsData = $data['materialNewsData'];
		try {
			$this->db->startTrans();
			$materialData['create_time'] = date('Y-m-d H:i:s');
			$id = $this->db->insert($this->tableName, $materialData);
			if (! $id) {
				throw new Exception("error");
			}
			$values = '';
			foreach ($materialNewsData as $v){
				if (empty($values)) {
					$values .= "({$id}, '{$v['news_index']}', '{$v['title']}','{$v['author']}', '{$v['description']}',"
							." '{$v['picurl']}', {$v['show_cover_pic']}, '{$v['url']}', '{$v['news_text']}', '".date('Y-m-d H:i:s')."')";
				} else {
					$values .= ", ({$id}, '{$v['news_index']}', '{$v['title']}','{$v['author']}', '{$v['description']}',"
							." '{$v['picurl']}', {$v['show_cover_pic']}, '{$v['url']}', '{$v['news_text']}', '".date('Y-m-d H:i:s')."')";
				}
			}

			$sql = "INSERT INTO `wx_material_news` (`material_id`, `news_index`, `title`,`author`,"
					." `description`, `picurl`,`show_cover_pic`, `url`, `news_text`, `create_time`)"
					." VALUES {$values}";

			$result = $this->db->query($sql);
			#添加关系
			$last_id = $this->db->getOne('SELECT LAST_INSERT_ID()');
			if (! $result) {
				throw new Exception("error");
			}
			$this->db->commit();
		} catch (Exception $e) {
			$this->db->rollback();
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
			return false;
		}
		
		return $id;
	}

	/**
	 * 图文数据生成
	 * @param $news array  checkNewsData()验证后的数据
	 */
	public function makeNewsData($news)
	{
		//$news = tripslashes($news);
		$articles = array ();
		$no_error = true;
		$i = 0;
		foreach ($news as $k => $v) {
			++$i;
			//如果摘要为空,那么取正文前15字
			if (empty($v['description'])) {
				$description = trim(mb_substr(str_replace(' ', '', strip_tags($v['content'])), 0, Config::MATERIAL_DESC_NUM, 'UTF-8'));
				if (empty($description)) {
					$description = $v['title'];
				}
			} else {
				$description = $v['description'];
			}
			$articles[] = array (
					'title' => tripslashes($v['title']),
					'author' => tripslashes($v['author']),
					'description' => tripslashes($description),
					'picurl' => tripslashes($v['picurl']),
					'show_cover_pic' => tripslashes($v['show_cover_pic']),
					'url' => tripslashes($v['url'])
			);

			$text_array[] = array (
				'news_index' => $i,
				'title' => $v['title'],
				'author' => $v['author'],
				'description' => $description,
				'picurl' => $v['picurl'],
				'show_cover_pic' => $v['show_cover_pic'],
				'url' => $v['url'],
				'news_text' => $v['content']
			);
		}
		$data = array (
				'materialData' => array(
					'articles' => faddslashes(serialize($articles)),
					'type' => 'news'
				),
				'materialNewsData' => $text_array
		);

		return $data;
	}

	/**
	 * 通过ID获取图文信息
	 * @param int $id
	 * @return Ambigous <NULL, multitype:>
	 */
	public function getNewsById($id)
	{
		if (! $id) {
			return null;
		}
		$news = array();
		$sql = "SELECT * FROM `wx_material` WHERE 1 AND id = %d ";
		try {
			$news = $this->db->getRow(sprintf($sql, $id));
		} catch ( Exception $e ) {
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
		}
		if ($news) {
			$news['detail'] = $this->getNewsDetailById($id);
		}

		return $news;
	}

	/**
	 * 获取图文明细
	 * @param int $id
	 * @return Ambigous <NULL, multitype:multitype: >
	 */
	public function getNewsDetailById($id)
	{
		$list = array();
		$sql = "SELECT * FROM `wx_material_news` WHERE 1 AND material_id = %d ";
		try {
			$list = $this->db->getAll(sprintf($sql, $id));
		} catch ( Exception $e ) {
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
		}
		return $list;
	}

	/**
	 * 解析图为明细
	 * @param array $newsDetail
	 * @return NULL|Ambigous <multitype:, unknown>
	 */
	public function parseNewsDetail($newsDetail)
	{
		$data = array();
		if (! $newsDetail) {
			return null;
		}
		foreach ($newsDetail as $k => $v) {
			$data[$k]['news_title'] = $v['title'];
			$data[$k]['news_author'] = $v['author'];
			$data[$k]['news_img_url'] = $v['picurl'];
			$data[$k]['news_show_cover_pic'] = $v['show_cover_pic'];
			$data[$k]['news_description'] = trim($v['description']);
			$data[$k]['news_content'] = trim($v['news_text']);
			$data[$k]['news_url'] = $v['url'];
		}
		return $data;
	}

	/**
	 * 修改图文数据
	 * @param int $id
	 * @param array $news
	 * @return boolean
	 */
	public function update($id, $news)
	{
		$data = $this->makeNewsData($news);

		$materialData = $data['materialData'];
		$materialNewsData = $data['materialNewsData'];
		try {
			$this->db->startTrans();
			$where = " id = %d";
			$result = $this->db->update($this->tableName, sprintf($where, $id), $materialData);
			if (! $result) {
				throw new Exception("error");
			}
			//删除原明细
			if (! $this->removeNewsDetailById($id)) {
				throw new Exception("error");
			}
			//添加新明细
			$values = '';
			foreach ($materialNewsData as $v){
				if (empty($values)) {
					$values .= "({$id}, '{$v['news_index']}', '{$v['title']}', '{$v['author']}', '{$v['description']}',"
					." '{$v['picurl']}',{$v['show_cover_pic']}, '{$v['url']}', '{$v['news_text']}', '".date('Y-m-d H:i:s')."')";
				} else {
				$values .= ", ({$id}, '{$v['news_index']}', '{$v['title']}', '{$v['author']}','{$v['description']}',"
						." '{$v['picurl']}',{$v['show_cover_pic']}, '{$v['url']}', '{$v['news_text']}', '".date('Y-m-d H:i:s')."')";
				}
			}
			$sql = "INSERT INTO `wx_material_news` (`material_id`, `news_index`, `title`,`author`,"
				." `description`, `picurl`,`show_cover_pic`, `url`, `news_text`, `create_time`)"
				." VALUES {$values}";

			$result = $this->db->query($sql);
			if (! $result) {
				throw new Exception("error");
			}
			$this->db->commit();			
		} catch (Exception $e) {
			$this->db->rollback();
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
			return false;
		}
		return true;
	}

	/**
	 * 删除图文明细数据
	 * 编辑时使用真实删除数据
	 * @param int $id
	 * @return boolean
	 */
	public function removeNewsDetailById($id)
	{
		try {
			$where = "material_id = %d";
			return $this->db->delete('wx_material_news', sprintf($where, $id));
		} catch ( Exception $e ) {
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
		}
		return false;
	}

	/**
	 * 标识删除图文明细数据
	 * 删除素材时使用
	 * @param int $id
	 * @return boolean
	 */
	public function deleteNewsDetailById($id)
	{
		try {
			$where = "material_id = %d";
			$set = array(
					'is_deleted' => 1
			);
			return $this->db->update('wx_material_news', sprintf($where, $id), $set);
		} catch ( Exception $e ) {
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
		}
		return false;
	}

	/**
	 * 删除图文
	 * @param int $id
	 * @return boolean|unknown
	 */
	public function delete($id)
	{
/*		if (loadModel('Admin.Material')->checkMaterialIsUse($id)) {
    		$this->setError(1, "当前图文已经被使用，无法删除！");
    		return false;
    	}*/

		try {
			$this->db->startTrans();
			$where = "id = %d";
			$result = $this->db->delete($this->tableName, sprintf($where, $id));
			if (! $result) {
				throw new Exception("error");
			}
			if (! $this->deleteNewsDetailById($id)) {
				throw new Exception("error");
			}
			$this->db->commit();
		} catch ( Exception $e ) {
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
			return false;
		}
		return true;
	}

	/**
	 * 发送预览
	 */
	public function sendPreview($nickname, $news)
	{
		if (empty($nickname)) {
			$this->setError(100, '发送失败，微信号不能为空');
			return false;
		}
		$weixinId = $this->getUserId($nickname);
		if (! $weixinId) {
			$this->setError(101, '发送失败，该用户不存在');
			return false;
		}

		$news = $this->checkNewsData($news);
		$news = $this->makeNewsData ( $news );
		$news = $news['materialNewsData'];
		if (! $news) {
			return false;
		}

		$id = $this->insertPreview($news);
		if (! $id) {
			$this->setError(103, '发送失败，保存预览消息错误');
			return false;
		}

		$articles = array();
		foreach ($news as $k => $v) {
			$articles[$k]['title'] = $v['title'];
			$articles[$k]['author'] = $v['author'];
			$articles[$k]['description'] = $v['description'];
			$articles[$k]['picurl'] = $v['picurl'];
			$articles[$k]['show_cover_pic'] = $v['show_cover_pic'];
			if ($v['news_text']) {
				$queryData = array(
						MonitorHttpParams::MATERIAL_ID => $id,
						MonitorHttpParams::INDEX => $v['news_index'],
						MonitorHttpParams::ENT_ID => UHome::getEntId(),
						MonitorHttpParams::OPEN_ID => $weixinId
				);
				$articles[$k]['url'] = Config::NEWS_TEXT_URL_REVIEW.'?'.http_build_query($queryData);
			} else {
				$articles[$k]['url'] = resetUrl($v['url'], array(MonitorHttpParams::OPEN_ID => $weixinId));
			}
		}

		$apiClient = AbcUtilTools::getApiClient();

		$sendBody = new WX_Message_Body();
		$sendBody->to_users = $weixinId;
		$sendBody->type = 'news';
		$sendBody->articles = $articles;

		$sendReturn = $apiClient->sendMessage($sendBody);
		if (! $sendReturn) {
			Logger::error(' 素材发送预览 发送失败:code:' . $apiClient->getErrorCode()
				. '  error:' . $apiClient->getErrorMessage(), $sendBody);
			switch ($apiClient->getErrorCode()) {
				case WX_Error::API_FREQ_OUT_ERROR :
					$error = '发送频次超出上限，请稍后重试！';
					break;
				case WX_Error::REQUIRE_SUBSCRIBE :
					$error = '请关注公众帐号后重试！';
					break;
				case WX_Error::RESPONSE_OUT_TIME :
					$error = '用户会话超时，请向公众帐号发送消息后重试！';
					break;
				default :
					$error = '发送失败,请重试！';
			}
			$this->setError(105, $error);
			return false;
		} else {
			return $id;
		}
	}
	
	/**
	 * 添加预览数据
	 */
	public function insertPreview($news) {
		try {
			$this->db->startTrans ();
			if (@$_SERVER ['SERVER_ADDR']) {
				$serIp = substr ( $_SERVER ['SERVER_ADDR'], strrpos ( $_SERVER ['SERVER_ADDR'], '.' ) );
			} else {
				$serIp = rand ( 10, 99 );
			}
			$id = uniqid () . $serIp;
	
			$values = '';
			foreach ( $news as $v ) {
				if (empty($values)) {
					$values .= "('{$id}', '{$v['news_index']}', '{$v['title']}', '{$v['author']}', '{$v['description']}',"
							." '{$v['picurl']}', {$v['show_cover_pic']}, '{$v['url']}', '{$v['news_text']}', '".date('Y-m-d H:i:s')."')";
				} else {
					$values .= ", ('{$id}', '{$v['news_index']}', '{$v['title']}', '{$v['author']}', '{$v['description']}',"
							." '{$v['picurl']}', {$v['show_cover_pic']}, '{$v['url']}', '{$v['news_text']}', '".date('Y-m-d H:i:s')."')";
				}
			}
	
			$sql = "INSERT INTO `wx_material_news_preview` (`material_id`, `news_index`, `title`,`author`,"
					." `description`, `picurl`,`show_cover_pic`, `url`, `news_text`, `create_time`)"
					." VALUES {$values}";
			$result = $this->db->query ( $sql );
			if (! $result) {
				throw new Exception("error");
			}
			$this->db->commit();
		} catch ( Exception $e ) {
			$this->db->rollback ();
			Logger::error ( 'MaterialNewsModel::insertPreview db error:' . $e->getMessage (), $e->getTraceAsString () );
			return false;
		}
		return $id;
	}

	/**
	 * 验证url格式
	 * @param string $url
	 * @return string
	 */
	public function checkUrl($url)
	{
		$url_format = '/^[A-Za-z]+:\/\/[A-Za-z0-9-_.]+/is';
		preg_match($url_format, $url, $output);
		return $output;
	}

	/**
	 * @name 获取微信发送ID
	 * @param string $nickname
	 * @return boolean | string;
	 */
	public function getUserId($nickname)
	{
		try {
			$sql = " SELECT `openid` FROM wx_user WHERE `remark` = '{$nickname}' ";
			$user_id = $this->db->getOne($sql);
			if (!$user_id) {
				$sql = " SELECT `openid` FROM wx_user WHERE `nickname` = '{$nickname}' ";
				$user_id = $this->db->getOne($sql);
			}
		} catch ( Exception $e ) {
			Logger::error(__FILE__ . ' . ' . __METHOD__ . '  ' . __LINE__ . '  ', $e->getMessage() . "\n" . $e->getTraceAsString());
			return false;
		}
		if (! $user_id) {
			return false;
		}
		return $user_id;
	}
}
/**
 * 错误信息处理文件
 * @author zp
 *
 */
class ErrCode
{
	const PARAM_MISSING = 10001;
	const ADD_DATA_SUCC = 10002;
	const ADD_DATA_FAIL = 10003;
	const EDIT_DATA_SUCC = 10004;
	const EDIT_DATA_FAIL = 10005;
	const DELETE_DATA_SUCC = 10006;
	const DELETE_DATA_FAIL = 10007;
	const PARAM_ERROR = 10008;
	const NEWS_FORMAT_ERROR = 10010;
	const NEWS_COUNT_LIMIT = 10011;
	const NEWS_TITLE_ERROR = 10012;
	const NEWS_DISCRIPTION_ERROR = 10013;
	const NEWS_ORIGINAL_LINK_FORMAT_ERROR = 10014;
	const NEWS_PIC_LINK_IS_NULL = 10015;
	const NEWS_PIC_LINK_FORMAT_ERROR = 10016;
	const NEWS_AUTHOR_ERROR = 10017;
	const NEWS_NOT_EXISTS_ERROR = 10020;
	const NEWS_TITLE_EMPTY = 10021;
	const NEWS_DISCRIPTION_EMPTY = 10022;
	const NEWS_CONTENT_EMPTY = 10023;
	const NEWS_CONTENT_LIMIT = 10024;
	const NEWS_CONTENT_AND_URL_EMPTY = 100025;

	public static function getError($code)
	{
		switch ($code) {
			case self::NEWS_CONTENT_LIMIT :
				return '正文不能为空且长度不能超过20000字';
			case self::NEWS_CONTENT_EMPTY :
				return '正文不能为空且长度不能超过20000字';
			case self::PARAM_MISSING :
				return '请求参数不完整';
			case self::ADD_DATA_SUCC :
				return '添加成功';
			case self::ADD_DATA_FAIL :
				return '添加失败';
			case self::EDIT_DATA_SUCC :
				return '更新成功';
			case self::EDIT_DATA_FAIL :
				return '更新失败';
			case self::DELETE_DATA_SUCC :
				return '删除成功';
			case self::DELETE_DATA_FAIL :
				return '删除失败';
			case self::PARAM_ERROR :
				return '请求参数错误';
			case self::NEWS_FORMAT_ERROR :
				return '图文信息发送格式有误！';
			case self::NEWS_COUNT_LIMIT :
				return '一次最多添加十条图文';
			case self::NEWS_TITLE_EMPTY :
				return '图文标题为空！';
			case self::NEWS_TITLE_ERROR :
				return '图文标题过长，只能为' . Config::NEWS_MAX_TITLE_LENGTH . '个字';
			case self::NEWS_AUTHOR_ERROR :
				return '作者过长，只能为' . Config::NEWS_MAX_TITLE_LENGTH . '个字';
			case self::NEWS_DISCRIPTION_EMPTY :
				return '图文摘要为空！';
			case self::NEWS_DISCRIPTION_ERROR :
				return '图文摘要过长，只能为' . Config::NEWS_MAX_DESCRIPTION_LENGTH . '个字';
			case self::NEWS_ORIGINAL_LINK_FORMAT_ERROR :
				return '原文格式不正确！';
			case self::NEWS_PIC_LINK_IS_NULL :
				return '图片地址不能为空！';
			case self::NEWS_PIC_LINK_FORMAT_ERROR :
				return '图片地址格式不正确！';
			case self::NEWS_NOT_EXISTS_ERROR :
				return '此条图文信息不存在';
			case self::NEWS_CONTENT_AND_URL_EMPTY :
				return '图文正文和链接地址不能全部为空';
			default :
				return '未知错误';
		}
	}
}
