<?php
/**
 * 图文素材Action类
 * @author zp,zox*
 */
class MaterialNewsAction extends AdminAction
{

	private $_model = null;

	public function __construct()
	{
		parent::__construct();
		$this->_model = loadModel('Admin.MaterialNews');
		$this->assign('title_max', Config::NEWS_MAX_TITLE_LENGTH);
		$this->assign('desc_max', Config::NEWS_MAX_DESCRIPTION_LENGTH);
	}

	/**
	 * 图文素材列表
	 * @author zp
	 */
	public function index()
	{
		$listRows = Config::PAGE_LISTROWS; //每页显示记录数
		//得到总记录数
		$totalRows = $this->_model->getCount();
		$list = array ();
		$pageHtml = null;
		$where = $limit = null;
		if ($totalRows > 0) {
			$pageObj = new Page($totalRows, $listRows);
			$pageHtml = $pageObj->show();
			$limit = " limit {$pageObj->firstRow},{$pageObj->listRows} ";
			$list = $this->_model->getList($where, $limit);
		}

		$this->assign('list', $list);
		$this->assign('page', $pageHtml);
		$this->display();
	}

	/**
	 * 图文素材添加模板
	 * @author zp
	 */
	public function add()
	{
		$news_type = (int) $this->getParam('news_type');
		if ($news_type != 1 && $news_type != 2) {
			$news_type = 1;
		}
		$this->assign('do_type', 'add');
		$this->assign('news_type', $news_type);
		$this->assign('news', '[]');
		$this->display();
	}

	/**
	 * 图文素材添加数据接口
	 * @author zp
	 */
	public function insert()
	{
		$shopId= $this->shop_id;
		$newsId = (int) $this->getParam('id');
		$news = $this->getParam('news', null, false, 'all');
		if (! $news) {
			printJson(0, ErrCode::PARAM_MISSING, ErrCode::getError(ErrCode::PARAM_MISSING));
		}

		$news = $this->_model->checkNewsData($news);
		if (! $news) {
			printJson(0, - 1, $this->_model->getError());
		}
		$newsInfo = $this->_model->getNewsById($newsId);
		if ($newsInfo) {
			$result = $this->_model->update($newsId, $news);
		} else {
			$result = $this->_model->insert($news,$shopId);
		}

		if (! $result) {
			printJson(0, ErrCode::ADD_DATA_FAIL, ErrCode::getError(ErrCode::ADD_DATA_FAIL));
		}
		printJson(0, 0, ErrCode::getError(ErrCode::ADD_DATA_SUCC));
	}

	/**
	 * 图文素材编辑模板
	 * @author zp
	 */
	public function edit()
	{
		$newsId = (int) $this->getParam('id');
		if ($newsId < 0) {
			printJson(0, ErrCode::PARAM_ERROR, ErrCode::getError(ErrCode::PARAM_ERROR));
		}
		$news = $this->_model->getNewsById($newsId);
		$data = array();
		if ($news) {
			$data = $this->_model->parseNewsDetail($news['detail']);
		}
		empty($data) && $data = array();
		$this->assign('do_type', 'edit');
		$this->assign('id', @$news['id']);
		$this->assign('news', json_encode($data));
		$this->display('MaterialNews.add');
	}

	/**
	 * 图文素材更新数据接口
	 * @author zp
	 */
	public function update()
	{
		$newsId = (int) $this->getParam('id');
		$news = $this->getParam('news', null, false, 'all');

		if (! $news || $newsId < 1) {
			printJson(0, ErrCode::PARAM_ERROR, ErrCode::getError(ErrCode::PARAM_ERROR));
		}
		$newsInfo = $this->_model->getNewsById($newsId);

		if (! $newsInfo) {
			printJson(0, ErrCode::NEWS_NOT_EXISTS_ERROR, ErrCode::getError(ErrCode::NEWS_NOT_EXISTS_ERROR));
		}
		$news = $this->_model->checkNewsData($news);
		if (! $news) {
			printJson(0, - 1, $this->_model->getError());
		}
		$result = $this->_model->update($newsId, $news);
		if ($result === false) {
			printJson(0, ErrCode::EDIT_DATA_FAIL, ErrCode::getError(ErrCode::EDIT_DATA_FAIL));
		}
		$this->clearCache(Config::ENT_ID,$newsId, count($news));
		printJson(0, 0, ErrCode::getError(ErrCode::EDIT_DATA_SUCC));
	}

	/**
	 * 图文素材删除数据接口
	 * @author zp
	 */
	public function delete()
	{
		$newsId = (int) $this->getParam('id');
		if ($newsId < 1) {
			printJson(0, ErrCode::PARAM_ERROR, ErrCode::getError(ErrCode::PARAM_ERROR));
		}
		$dbNews = $this->_model->getNewsById($newsId);
		if (! $dbNews) {
			printJson(0, ErrCode::NEWS_NOT_EXISTS_ERROR, ErrCode::getError(ErrCode::NEWS_NOT_EXISTS_ERROR));
		}
		$flag = $this->_model->delete($newsId);
		$error = $this->_model->getError() ? $this->_model->getError() : ErrCode::getError(ErrCode::DELETE_DATA_FAIL);
		if ($flag == false) {
			printJson(0, ErrCode::DELETE_DATA_FAIL, $error);
		}
		printJson(0, 0, ErrCode::getError(ErrCode::DELETE_DATA_SUCC));
	}

	//oxIuPjhC4pw9pJB7xWXKpK2Tfl2s
	//发微信
	public function send()
	{
		$nickname = trim($this->getParam('nickname'));
		$news = $this->getParam('send_data', null, false, 'add');

		$result = $this->_model->sendPreview($nickname, $news);
		if (! $result) {
			printJson(0, 1, $this->_model->getError());
		} else {
			printJson($result, 0, '发送成功');
		}
	}

	/**
	 * 群发图文弹出层
	 */
	public function showMaterial()
	{
		$listRows = Config::PAGE_LISTROWS; //每页显示记录数
		$callback = trim($this->getParam('callback'));
		//得到总记录数
		$totalRows = $this->_model->getCount();
		$list = array ();
		$pageHtml = null;
		$where = $limit = null;
		if ($totalRows > 0) {
			$pageObj = new Page($totalRows, $listRows);
			$pageHtml = $pageObj->show($callback);
			$limit = " LIMIT {$pageObj->firstRow},{$pageObj->listRows} ";
			$list = $this->_model->getList($where, $limit);
			if ($list) {
				foreach ($list as $key => &$val) {
					$val['articles'] = $val['articles'];
					foreach ($val['articles'] as &$v) {
						$v['title'] = htmlspecialchars($v['title']);
						$v['description'] = htmlspecialchars($v['description']);
					}
				}
			}
		}
		echo json_encode(array (
				'content' => $list,
				'page' => $pageHtml,
				'error'=>0
		));
		exit();
	}

	protected function clearCache ($entId, $mid, $count) {
		$catcher = Factory::getCacher();
		for ($i = 1; $i <= $count; $i++) {
			$catcherId = GlobalCatchId::MATERIAL_TEXT_INFO.implode('_',	array($entId, $mid, $i));
			$catcher->clear($catcherId);
		}
	}
}
