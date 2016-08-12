<?php
class MaterialImageAction extends AdminAction
{

	private $model = null;
	private $maxSize = 131072;

	public function __construct()
	{
		parent::__construct();
		$this->model = loadModel('Admin.MaterialImage');
	}

	/**
	 * 图片列表
	 */
	public function index()
	{
		$listRows = Config::PAGE_LISTROWS; //每页显示记录数
		//得到总记录数
		$totalRows = $this->model->getCount($this->shop_id);
		$list = array ();
		$pageHtml = null;
		$where = $limit = null;
		if ($totalRows > 0) {
			$pageObj = new Page($totalRows, $listRows);
			$pageHtml = $pageObj->show();
			$limit = " limit {$pageObj->firstRow},{$pageObj->listRows} ";
			$list = $this->model->getList($this->shop_id,$where, $limit);
		}
		$this->assign('list', $list);
		$this->assign('page', $pageHtml);
		$this->display();
	}

	//图片素材添加页面
	public function add()
	{
		$this->assign('maxSize', $this->maxSize);
		$this->display();
	}

	//图片素材添加
	public function insert()
	{
		$title = trim($this->getParam('title'));
		$img_url = trim($this->getParam('img_url'));
		if (! $title || ! $img_url) {
			printJson(0, ErrCode::PARAM_ERROR, ErrCode::getError(ErrCode::PARAM_ERROR));
		}
		$data = array (
				'title' => $title,
				'path' => $img_url,
				'inputtime' => time(),
				'shop_id'=>$this->shop_id
		);
		$return = $this->model->insertData($data);
		if (false === $return) {
			printJson(0, 1, '添加失败');
		}
		printJson(1);
	}

	/**
	 * @name 图片素材修改页面
	 */
	public function edit()
	{
		$img_id = (int) $this->getParam('id');
		$where = " and id={$img_id}";
		$data = $this->model->getOneMaterialImage($where);
		$this->assign('maxSize', $this->maxSize);
		$this->assign('data', $data);
		$this->display();
	}

	/**
	  * @name 图片素材修改
	  */
	public function update()
	{
		$img_id = (int) $this->getParam('img_id');
		$img_url = trim($this->getParam('img_url'));
		$title = trim($this->getParam('title'));
		if ($img_id < 1 || ! $img_url || ! $title) {
			printJson(0, 1, '缺少参数');
		}
		$data = array (
				'title' => $title,
				'path' => $img_url
		);
		$flag = $this->model->updateImage($img_id, $data);
		if ($flag === false) {
			printJson(0, 1, '修改失败');
		}
		printJson(0, 0, '修改成功');
	}

	/**
	 * 图片素材删除
	 * @author zp
	 */
	public function delete()
	{
		$img_id = (int) $this->getParam('id');
		if ($img_id < 1) {
			printJson(0, 1, '缺少参数');
		}
		#检测该文件是否被占用
		$nmodel = M('Admin.Shop');
	 	$shopViewINfo  = $nmodel->getView($this->shop_id);
	 	if($shopViewINfo){
	 		$allids = explode(',',$shopViewINfo['imgUrlIds']);
	 		$i=0;
 			if(in_array($img_id,$allids)){
 				$i=1;
 			}
	 	}
		if($i>0){
			printJson('','1','提交审核或被展示的素材不能被删除!!');
		}
		$flag = $this->model->deleteImage($img_id);
		$error = $this->model->getError() ? $this->model->getError() : '删除失败';
		if ($flag == false) {
			printJson(0, 1, $error);
		}
		printJson(0, 0, '删除成功');
	}

	/**
	 * 获取图片列表
	 */
	public function showMaterial()
	{
		$listRows = Config::PAGE_LISTROWS; //每页显示记录数
		$callback = trim($this->getParam('callback'));
		$materialModel = loadModel('Admin.MaterialImage');
		//得到总记录数
		$totalRows = $materialModel->getCount();
		$list = array ();
		$pageHtml = null;
		$where = $limit = null;
		if ($totalRows > 0) {
			$pageObj = new Page($totalRows, $listRows);
			$pageHtml = $pageObj->show($callback);
			$limit = " limit {$pageObj->firstRow},{$pageObj->listRows} ";
			$list = $materialModel->getList($where, $limit);
		}
		echo json_encode(array (
				'content' => $list,
				'page' => $pageHtml
		));
		exit();
	}
	
	
		/**
		 * 
		 * 设置首页
		 */
		function ajaxIsShow(){
			$state = trim($this->getParam('state'))==1? 0:1;
			$data = array(
		 		'id'=>trim($this->getParam('id')),
		 		'is_show'=>$state,
				'shop_id'=>$this->shop_id
	 		);
	 		$result = $this->model->setIsShow($data);
	 		if(is_numeric($result)){
				if($result<1){
					printJson('','1','设置失败!');
				}					 			
	 		}else{
	 			if($result=='max'){
	 				printJson('','1','设置超过最大值!');
	 			}
	 		}
	 		printJson(1);
		}
	
}