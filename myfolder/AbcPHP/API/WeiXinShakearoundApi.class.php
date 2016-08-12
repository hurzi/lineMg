<?php
/**
 * @modify      更新了参数类型检测，已测试，但未使用
 * PHP SDK for weixin 摇周边
 * @copyright  2012-2013 北京随视传媒
 * @version
 * @since      File available since Release 1.0
 * @time       15-4-15 下午12:30
 * @author     xizb
 */
include_once ABC_PHP_PATH . '/API/WeiXinError.class.php';
include_once ABC_PHP_PATH . '/API/WeiXinApiRequest.class.php';
include_once ABC_PHP_PATH . '/Common/Json.class.php';
class WeiXinShakearoundApi
{
    /**
     * @var $host 摇周边host
     */
    protected $host='https://api.weixin.qq.com/shakearound/';
    protected $json;
    protected $appId;
    protected $token;
    /**
     * 最后错误代码
     */
    protected $_error_code = 0;

    /**
     * 最后错误信息
     */
    protected $_error_message = '';
    public function __construct($appId,$token){
        $this->json=new json();
        $this->appId=$appId;
        $this->_setToken($token);
    }

    /**
     * 设置access_token
     * @param $access_token
     */
    private function _setToken($token){
        $this->token=$token;
    }
    /**
     * 申请设备id
     * @param Int    $quantity     申请设备数量          必须参数    小于500
     * @param String $apply_reason 申请原因              必须参数
     * @param String $comment      备注                  可选参数
     * @param Int    $poi_id       微信门店id            可选参数
     * @return array
     */
    public function applyDeviceId($quantity,$apply_reason,$comment=null,$poi_id=null){
        if(empty($quantity)){
            return array(
                'errcode'=>-1,
                'errmsg'=> "申请设备数量不能为空"
            );
        }elseif(!is_int($quantity)){
            return array(
                'errcode'=>-1,
                'errmsg'=> "申请设备数量必须为整数"
            );
        }
        if(empty($apply_reason)){
            return array(
                'errcode'=>-1,
                'errmsg'=> "申请原因不能为空"
            );
        }
        $param=array(
            'quantity'=>$quantity,
            'apply_reason'=>$apply_reason,
            'comment'=>$comment,
            'poi_id'=>$poi_id
        );
        $url=$this->host.'device/applyid?access_token='.$this->token;

        return $this->CreateHttpPost($url,$param);
    }

    /**
    * 更新设备信息通过设备id
    *  编辑设备的备注信息。可用设备ID或完整的UUID、Major、Minor指定设备，二者选其一。
    * @param Int    $device_id    微信设备id，
    * @param String $uuid
    * @param Int    $major
    * @param Int    $minor
    * @param String $comment      设备备注，必须参数
    * @return array
    */
    public function updateDevice ($comment,$device_id=null,$uuid=null,$major=null,$minor=null){
        if(empty($comment)){
            return array(
                'errcode'=>-1,
                'errmsg'=> "备注不能为空"
            );
        }
        $param=array();
        if(!empty($device_id)){
            if(!is_int($device_id)){
                return array(
                    'errcode'=>-1,
                    'errmsg'=> "设备号必须为整数"
                );
            }
            $param=array(
                'device_identifier'=>array(
                    'device_id'=>$device_id
                ),
                'comment'=>$comment
            );
        }elseif(!empty($uuid)||!empty($major)||!empty($minor)){
            if(!is_int($major)){
                return array(
                    'errcode'=>-1,
                    'errmsg'=> "major必须为整数"
                );
            }
            if(!is_int($minor)){
                return array(
                    'errcode'=>-1,
                    'errmsg'=> "minor必须为整数"
                );
            }
            $param=array(
                'device_identifier'=>array(
                    'uuid'=>$uuid,
                    'major'=>$major,
                    'minor'=>$minor
                ),
                'comment'=>$comment
            );
        }else{
            return array(
                'errcode'=>-1,
                'errmsg'=> "可用设备ID或完整的UUID、Major、Minor指定设备，二者选其一"
            );
        }
        $url=$this->host.'device/update?access_token='.$this->token;
        return $this->CreateHttpPost($url,$param);
    }

    /**
     * 绑定门店
     * @param Int       $device_id    微信设备id，与完整的$uuid、$major、$minor，二者人选其一
     * @param String    $uuid
     * @param Int       $major
     * @param Int       $minor
     * @param Int       $poi_id 微信门店id，必传
     * @return array
     */
    public function bindLocation($poi_id,$device_id=null,$uuid=null,$major=null,$minor=null){
        if(empty($poi_id)){
            return array(
                'errcode'=>-1,
                'errmsg'=> "微信门店id不能为空"
            );
        }elseif(!is_int($poi_id)){
            return array(
                'errcode'=>-1,
                'errmsg'=> "微信门店id必须为整数"
            );
        }
        $param=array();
        if(!empty($device_id)){
            if(!is_int($device_id)){
                return array(
                    'errcode'=>-1,
                    'errmsg'=> "设备号必须为整数"
                );
            }
            $param=array(
                'device_identifier'=>array(
                    'device_id'=>$device_id
                ),
                'poi_id'=>$poi_id
            );
        }elseif(!empty($uuid)||!empty($major)||!empty($minor)){
            if(!is_int($major)){
                return array(
                    'errcode'=>-1,
                    'errmsg'=> "major必须为整数"
                );
            }
            if(!is_int($minor)){
                return array(
                    'errcode'=>-1,
                    'errmsg'=> "minor必须为整数"
                );
            }
            $param=array(
                'device_identifier'=>array(
                    'uuid'=>$uuid,
                    'major'=>$major,
                    'minor'=>$minor
                ),
                'poi_id'=>$poi_id
            );
        }else{
            return array(
                'errcode'=>-1,
                'errmsg'=> "可用设备ID或完整的UUID、Major、Minor指定设备，二者选其一"
            );
        }

        $url=$this->host.'device/bindlocation?access_token='.$this->token;
        return $this->CreateHttpPost($url,$param);
    }
    /**
     * 查询指定门店信息
     * @param Int       $device_id    微信设备id，与完整的$uuid、$major、$minor，二者人选其一
     * @param $uuid
     * @param Int       $major
     * @param Int       $minor
     * @return array
     */
    public function getDeviceInfoByIdentifiers($device_id=null,$uuid=null,$major=null,$minor=null){
        $param=array();
        if(!empty($device_id)){
            if(!is_int($device_id)){
                return array(
                    'errcode'=>-1,
                    'errmsg'=> "设备号必须为整数"
                );
            }
            $param=array(
                'device_identifiers'=>array( array('device_id'=>$device_id))
            );
        }elseif(!empty($uuid)||!empty($major)||!empty($minor)){
            if(!is_int($major)){
                return array(
                    'errcode'=>-1,
                    'errmsg'=> "major必须为整数"
                );
            }
            if(!is_int($minor)){
                return array(
                    'errcode'=>-1,
                    'errmsg'=> "minor必须为整数"
                );
            }
            $param=array(
                'device_identifiers'=>array(
                    array(
                        'uuid'=>$uuid,
                        'major'=>$major,
                        'minor'=>$minor,
                    ),
                ),

            );
        }else{
            return array(
                'errcode'=>-1,
                'errmsg'=> "可用设备ID或完整的UUID、Major、Minor指定设备，二者选其一"
            );
        }
        //https://api.weixin.qq.com/shakearound/device/search?access_token=ACCESS_TOKEN
        $url=$this->host.'device/search?access_token='.$this->token;
        return $this->CreateHttpPost($url,$param);
    }

    /**
     * 按照查询条件获取设备信息列表
     * @param Int $apply_id 批次ID，申请设备ID超出500个时所返回批次ID
     * @param Int $begin    设备列表的起始索引值
     * @param Int $count    待查询的设备个数
     * @return array
     */
    public function getDeviceInfoListByConditions($begin,$count,$apply_id=null){
        if(!is_int($begin)){
            return array(
                'errcode'=>-1,
                'errmsg'=> "begin必须为整数"
            );
        }
        if(!is_int($count)){
            return array(
                'errcode'=>-1,
                'errmsg'=> "count必须为整数"
            );
        }
        $param=array(
            'begin'=>$begin,
            'count'=>$count,
        );
        if(!empty($apply_id)){
            $param['apply_id']=$apply_id;
        }

        //https://api.weixin.qq.com/shakearound/device/search?access_token=ACCESS_TOKEN
        $url=$this->host.'device/search?access_token='.$this->token;
        return $this->CreateHttpPost($url,$param);
    }

    /**
     * 增加页面
     * @param String $title        标题      必须参数
     * @param String $description  描述      必须参数
     * @param String $page_url     页面url   必须参数
     * @param String $comment      备注15字
     * @param String $icon_url     图片url   必须参数
     * @return array
     */
    public function addPage($title,$description,$page_url,$icon_url,$comment=null){
        if(empty($title)){
            return array(
                'errcode'=>-1,
                'errmsg'=> "title标题不能为空"
            );
        }elseif(empty($description)){
            return array(
                'errcode'=>-1,
                'errmsg'=> "description描述不能为空"
            );
        }elseif(empty($page_url)){
            return array(
                'errcode'=>-1,
                'errmsg'=> "page_url页面url不能为空"
            );
        }
        elseif(empty($icon_url)){
            return array(
                'errcode'=>-1,
                'errmsg'=> "icon_url图片url不能为空"
            );
        }
        $param=array(
            'title'=>$title,
            'description'=>$description,
            'page_url'=>$page_url,
            'icon_url'=>$icon_url,
            'comment'=>$comment,
        );
        $url=$this->host.'page/add?access_token='.$this->token;
        return $this->CreateHttpPost($url,$param);
    }
    /**
     * 编辑页面
     * @param Int               $page_id      页面id
     * @param String            $title        描述
     * @param String            $description  描述
     * @param String            $page_url     页面url
     * @param String            $comment      备注15字
     * @param String            $icon_url     图片url
     * @return array
     */
    public function updatePage($page_id,$title,$description,$page_url,$icon_url,$comment=null){
        if(empty($page_id)){
            return array(
                'errcode'=>-1,
                'errmsg'=> "page_id不能为空"
            );
        }elseif(!is_int($page_id)){
            return array(
                'errcode'=>-1,
                'errmsg'=> "page_id必须为整数"
            );
        }
        if(empty($title)){
            return array(
                'errcode'=>-1,
                'errmsg'=> "title标题不能为空"
            );
        }elseif(empty($description)){
            return array(
                'errcode'=>-1,
                'errmsg'=> "description描述不能为空"
            );
        }
        elseif(empty($page_url)){
            return array(
                'errcode'=>-1,
                'errmsg'=> "page_url页面url不能为空"
            );
        }
        elseif(empty($icon_url)){
            return array(
                'errcode'=>-1,
                'errmsg'=> "icon_url图片url不能为空"
            );
        }
        $param=array(
            'page_id'=>$page_id,
            'title'=>$title,
            'description'=>$description,
            'page_url'=>$page_url,
            'icon_url'=>$icon_url,
            'comment'=>$comment,
        );
        $url=$this->host.'page/update?access_token='.$this->token;
        return $this->CreateHttpPost($url,$param);
    }

    /**
     * 通过指定page_id的方式查询页面列表
     * @param Array(Int)    $page_ids page_id数组(整数类型)
     * @return array
     */

    public function getPageInfoListByPage_ids($arr_page_ids){
        if(empty($arr_page_ids)){
            return array(
                'errcode'=>-1,
                'errmsg'=> "page_id数组不能为空"
            );
        }else{
            foreach($arr_page_ids as $v){
                if(!is_int($v)){
                    return array(
                        'errcode'=>-1,
                        'errmsg'=> "page_id数组中参数必须为整数"
                    );
                }
            }
        }
        $param=array(
            'page_ids'=>$arr_page_ids,
			'type'=>1
        );
        $url=$this->host.'page/search?access_token='.$this->token;
        return $this->CreateHttpPost($url,$param);
    }

    /**
     * 通过指定范围的查询页面信息列表
     * @param Int $begin    页面列表的起始索引值
     * @param Int $count    待查询的页面个数
     * @return array
     */
    public function getPageInfoListByRange($begin,$count){
        if(!is_int($begin)){
            return array(
                'errcode'=>-1,
                'errmsg'=> "begin必须为整数"
            );
        }
        if(!is_int($count)){
            return array(
                'errcode'=>-1,
                'errmsg'=> "count必须为整数"
            );
        }
        $param=array(
            'begin'=>$begin,
            'count'=>$count,
			'type'=>2
        );
        $url=$this->host.'page/search?access_token='.$this->token;
        return $this->CreateHttpPost($url,$param);
    }

    /**
     * 删除指定数组的页面
     * @param Array(Int) $arr_page_ids page_id数组
     * @return array
     */
    public function deletePages($arr_page_ids){
        if(empty($arr_page_ids)){
            return array(
                'errcode'=>-1,
                'errmsg'=> "page_id数组不能为空"
            );
        }else{
            foreach($arr_page_ids as $v){
                if(!is_int($v)){
                    return array(
                        'errcode'=>-1,
                        'errmsg'=> "page_id数组中参数必须为整数"
                    );
                }
            }
        }
        $param=array(
            'page_ids'=>$arr_page_ids,
        );
        $url=$this->host.'page/delete?access_token='.$this->token;
        return $this->CreateHttpPost($url,$param);
    }

    /**
     * @param String $image_name 上传图片素材
     * @return array
     */
    public function addMaterial($image_name){
        if(empty($image_name)){
            return array(
                'errcode'=>-1,
                'errmsg'=> "必须上传图片"
            );
        }
        $param=array(
            'media'=>'@'.$image_name,
            'msgtype'=>'image'
        );
        $url=$this->host.'material/add?access_token='.$this->token;
        Logger::debug(__METHOD__.' add_file:'.$image_name.'  file_size:'.filesize($image_name));
        return $this->CreateHttpPost($url,$param,true,false);
    }

    /**
     * 绑定页面
     * @param Int       $arr_page_ids page_id数组
     * @param Int       $device_id    微信设备id，与完整的$uuid、$major、$minor，二者人选其一
     * @param String    $uuid
     * @param Int       $major
     * @param Int       $minor
     * @param Int       $bind         关联操作标志位， 0为解除关联关系，1为建立关联关系
     * @param Int       $append       新增操作标志位， 0为覆盖，1为新增
     * @return array
     */

    public function bindPage($arr_page_ids,$device_id=null,$uuid=null,$major=null,$minor=null,$bind=1,$append=1){
        if(empty($arr_page_ids)){
            return array(
                'errcode'=>-1,
                'errmsg'=> "page_id数组不能为空"
            );
        }else{
            foreach($arr_page_ids as $v){
                if(!is_int($v)){
                    return array(
                        'errcode'=>-1,
                        'errmsg'=> "page_id数组中参数必须为整数"
                    );
                }
            }
        }
        if(!empty($device_id)){
            if(!is_int($device_id)){
                return array(
                    'errcode'=>-1,
                    'errmsg'=> "设备号必须为整数"
                );
            }
            $param=array(
                'device_identifiers'=>array( array('device_id'=>$device_id))
            );
        }elseif(!empty($uuid)||!empty($major)||!empty($minor)){
            if(!is_int($major)){
                return array(
                    'errcode'=>-1,
                    'errmsg'=> "major必须为整数"
                );
            }
            if(!is_int($minor)){
                return array(
                    'errcode'=>-1,
                    'errmsg'=> "minor必须为整数"
                );
            }
            $param=array(
                'device_identifiers'=>array(
                    array(
                        'uuid'=>$uuid,
                        'major'=>$major,
                        'minor'=>$minor,
                    ),
                ),

            );
        }else{
            return array(
                'errcode'=>-1,
                'errmsg'=> "可用设备ID或完整的UUID、Major、Minor指定设备，二者选其一"
            );
        }
        $param=array(
            'device_identifier'=>array(
                'device_id'=>$device_id,
                'uuid'=>$uuid,
                'major'=>$major,
                'minor'=>$minor,
            ),
            'page_ids'=>$arr_page_ids,
            'bind'=>$bind,
            'append'=>$append,
        );

        $url=$this->host.'device/bindpage?access_token='.$this->token;
        return $this->CreateHttpPost($url,$param);
    }

    /**
     * 获取摇周边的设备及用户信息
     * @param $ticket   摇周边业务的ticket，可在摇到的URL中得到，ticket生效时间为30分钟
     * @return array
     */
    public function getShakeInfo($ticket,$need_poi=0){
        if(empty($ticket)){
            return array(
                'errcode'=>-1,
                'errmsg'=> "ticket为空"
            );
        }
        $param=array(
            'ticket'=>$ticket,
            'need_poi'=>$need_poi
        );
        $url=$this->host.'user/getshakeinfo?access_token='.$this->token;
        return $this->CreateHttpPost($url,$param);

    }

    /**
     *以设备为维度的数据统计接口
     * @param Int $device_id    设备编号，若填了UUID、major、minor，即可不填设备编号，二者选其一
     * @param $uuid
     * @param Int $major
     * @param Int $minor
     * @param Int $begin_date   起始日期时间戳，最长时间跨度为30天
     * @param Int $end_date     结束日期时间戳，最长时间跨度为30天 ,不能大于当前时间
     * @return array
     */
    public function getStatisticsByDdevice($begin_date,$end_date,$device_id=null,$uuid=null,$major=null,$minor=null){

        if(empty($begin_date)){
            return array(
                'errcode'=>-1,
                'errmsg'=> "开始时间为空"
            );
        }
        if(empty($end_date)){
            return array(
                'errcode'=>-1,
                'errmsg'=> "结束时间为空"
            );
        }

        if(!empty($device_id)){
            if(!is_int($device_id)){
                return array(
                    'errcode'=>-1,
                    'errmsg'=> "设备号必须为整数"
                );
            }
            $param=array(
                'device_identifiers'=>array( array('device_id'=>$device_id))
            );
        }elseif(!empty($uuid)||!empty($major)||!empty($minor)){
            if(!is_int($major)){
                return array(
                    'errcode'=>-1,
                    'errmsg'=> "major必须为整数"
                );
            }
            if(!is_int($minor)){
                return array(
                    'errcode'=>-1,
                    'errmsg'=> "minor必须为整数"
                );
            }
            $param=array(
                'device_identifiers'=>array(
                    array(
                        'uuid'=>$uuid,
                        'major'=>$major,
                        'minor'=>$minor,
                    ),
                ),

            );
        }else{
            return array(
                'errcode'=>-1,
                'errmsg'=> "可用设备ID或完整的UUID、Major、Minor指定设备，二者选其一"
            );
        }
        $param=array(
            'device_identifier'=>array(
                'device_id'=>$device_id,
                'uuid'=>$uuid,
                'major'=>$major,
                'minor'=>$minor
            ),
            'begin_date'=>$begin_date,
            'end_date'=>$end_date
        );
        //https://api.weixin.qq.com/shakearound/statistics/device?access_token=ACCESS_TOKEN
        $url=$this->host.'statistics/device?access_token='.$this->token;
        return $this->CreateHttpPost($url,$param);
    }

    /**
     * 以页面为维度的数据统计接口
     * @param Int $page_id      指定设备的页面ID
     * @param Int $begin_date   起始日期时间戳，最长时间跨度为30天
     * @param Int $end_date     结束日期时间戳，最长时间跨度为30天 ,不能大于当前时间
     * @return array
     */

    public function getStatisticsByPage_id($page_id,$begin_date,$end_date){
        if(empty($begin_date)){
            return array(
                'errcode'=>-1,
                'errmsg'=> "开始时间为空"
            );
        }
        if(empty($end_date)){
            return array(
                'errcode'=>-1,
                'errmsg'=> "结束时间为空"
            );
        }

        if(empty($page_id)||!is_int($page_id)){
            return array(
                'errcode'=>-1,
                'errmsg'=> "page_id指定设备的页面ID不能为空,且为整数"
            );
        }
        $param=array(
            'page_id'=>$page_id,
            'begin_date'=>$begin_date,
            'end_date'=>$end_date

        );
        //https://api.weixin.qq.com/shakearound/statistics/page?access_token=ACCESS_TOKEN
        $url=$this->host.'statistics/page?access_token='.$this->token;
        return $this->CreateHttpPost($url,$param);
    }
    /**
     * 获取错误code
     * @return int
     */
    public function getErrorCode() {
        return $this->_error_code;
    }

    /**
     * 获取错误信息
     * function_description
     * @return string
     */
    public function getErrorMessage() {
        return $this->_error_message;
    }
    /**
     * 设置错误信息
     * @param unknown $errorcode
     * @param unknown $message
     */
    private function _setErrorMsg($errorcode, $message) {
        $this->_error_code = $errorcode;
        $this->_error_message = $message;

    }
    /**
     * 记录错误日志
     *
     * @author mxg
     * @return void
     */
    protected function _log() {
        $params = array();
        $params['data'] = WeiXinApiRequest::$params;
        $params['response'] = WeiXinApiRequest::$response;
        $params['app_id'] = $this->appId;
        $params['http_code'] = WeiXinApiRequest::$http_code;
        Logger::error('WeiXinCardApi last erorr, url: ' . WeiXinApiRequest::$url, $params);
    }
    /**
     * 获取api请求错误
     *
     * @param  int $code
     * @return void
     */
    protected function _setError($code, $log_enabled = true) {
        //记录错误日志
        if ($log_enabled) $this->_log();

        $this->_error_code = $code;
        $this->_error_message = WX_Error::getMessage($code);
    }
    /**
     * 分析错误
     *
     * @param  int $code
     * @param  array $response
     * @return bool
     */
    protected function _parseError($code, $response) {
        $this->_error_code = WX_Error::NO_ERROR;
        if (200 == $code && (isset($response['errcode']) && $response['errcode'])) {
            $code = $response['errcode'];
        }
        //与微信链接失败
        if (0 == $code) {
            $code = 5100;
        }

        switch ($code) {
            case 200:
                return true;
            //http code
            case 404:
                $error_code = WX_Error::HTTP_FORBIDDEN_ERROR;
                break;
            case 503:
                $error_code = WX_Error::HTTP_SERVICE_UNAVAILABLE_ERROR;
                break;
            case 40013:
                $error_code = WX_Error::INVALID_APP_ID_ERROR;
                break;
            case 41001:
                $error_code = WX_Error::KOTEN_MISSING_ERROR;
                break;
            case 40073: //invalid card id
                $error_code = WX_Error::INVALID_CARD_ID;
                break;
            case 40056: //invalid serial code
                $error_code = WX_Error::INVALID_CARD_CODE;
                break;
            case 40001 :
                $error_code = WX_Error::INVALID_CREDENTIAL_ERROR;
                //$this->clearTokenCache();
                break;
            case 40003 :
                $error_code = WX_Error::INVALID_USER_ERROR;
                break;
            case 42001 :
                $error_code = WX_Error::TOKEN_EXPIRED_ERROR;
                //$this->clearTokenCache();
                break;
            default:
                $error_code = $code;
        }
        $this->_setError($error_code);
        return false;
    }
    /**
     * 分析数据结果
     *
     * @param int $code curl发送请求的状态码
     * @param array $response 得到的结果
     * @param string $fun_name
     * @return mixed
     */
    protected function _parse($code, $response, $func_name = '') {
        $error = $this->_parseError($code, $response);
        /*if ($error == false) {
            return false;
        }*/
        if ($func_name) {
            return call_user_func_array(array($this, $func_name), array($response));
        }
        return true;
    }
    /**
     * 返回成功后的数据
     * @param $response
     * @return array
     */
    protected function _parseResult($response) {
        if (!isset($response['errcode']) || !isset($response['errmsg'])) {
            $this->_setError(WX_Error::RESPONSE_FOMAT_ERROR);
            return false;
        }
        return $response;
    }

    private function CreateHttpPost($url,$param,$isPic=false,$json=true){
        if(!$isPic){
            $param=$this->json->encode($param, false);
        }
        $response=WeiXinApiRequest::post($url, $param,$isPic,$json);
        return call_user_func_array(array($this, '_parse'),
            array(WeiXinApiRequest::$http_code, $response, '_parseResult'));
    }


    /**
     * by xizb
     * 活动订单
         * @param array $curl_credentials证书路径
     * @param WxPayRedPackPay $inputObj
     * @param int $timeOut
     * @throws WxPayException
     * @return 成功时返回，其他抛异常
     */
    public static function redPack_Activity($inputObj,$logo_url,$token,$timeOut = 6)
    {
        $url = 'https://api.weixin.qq.com/shakearound/lottery/addlotteryinfo?access_token='.$token.'&use_template=1&logo_url='.$logo_url;
        foreach ($inputObj as $key => $value) {
            $data[]='"'.$key.'":"'.$value.'"';
        }
        $res="{".implode(',',$data)."}";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,TRUE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);//严格校验
        //curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $res);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1); //是否抓取跳转后的页面
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen($res),
                        'Content-Type: application/x-www-form-urlencoded; charset=UTF-8'
                        )
                    );
        //运行curl，结果以json形式返回
        $result = curl_exec($ch);
        $res = curl_getinfo($ch);
        curl_close($ch);
        return $result;
    }

    /**
     * by xizb
     * 添加红包订单
         * @param array $curl_credentials证书路径
     * @param WxPayRedPackPay $inputObj
     * @param int $timeOut
     * @throws WxPayException
     * @return 成功时返回，其他抛异常
     */
    public static function Pre_pact($inputObj,$token,$timeOut = 6)
    {

        $url = 'https://api.weixin.qq.com/shakearound/lottery/setprizebucket?access_token='.$token;

        $data=json_encode($inputObj);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,TRUE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);//严格校验
        //curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1); //是否抓取跳转后的页面
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen($data),
                        'Content-Type: application/x-www-form-urlencoded; charset=UTF-8'
                        )
                    );
        //运行curl，结果以jason形式返回
        $result = curl_exec($ch);
        $res = curl_getinfo($ch);
        curl_close($ch);
        return $result;
    }

    /**
     * by xizb
     * 红包订单
         * @param array $curl_credentials证书路径
     * @param WxPayRedPackPay $inputObj
     * @param int $timeOut
     * @throws WxPayException
     * @return 成功时返回，其他抛异常
     */
    public static function redPacks($inputObj,$curl_credentials, $timeOut = 6)
    {
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/hbpreorder';
        //检测必填参数
        $xml = self::ToXml($inputObj);
        $startTimeStamp = self::getMillisecond();
        $response = self::postXmlCurl($xml, $url, true, $timeOut,$curl_credentials);
        $result = json_decode(json_encode(simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $result;
    }

    /**
     * 输出xml字符
     * @throws WxPayException
    **/
    private static function ToXml($array)
    {
        $xml = "<xml>";
        foreach ($array as $key=>$val)
        {
            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }

    /**
     * 以post方式提交xml到对应的接口url
     *
     * @param string $xml  需要post的xml数据
     * @param string $url  url
         * @param array $curl_credentials 证书路径 当$useCert为ture时必须指定
     * @param bool $useCert 是否需要证书，默认不需要
     * @param int $second   url执行超时时间，默认30s
     * @throws WxPayException
     */
    private static function postXmlCurl($xml, $url, $useCert = false, $second = 30,$curl_credentials=null)
    {
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,TRUE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);//严格校验
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    
        if($useCert&&$curl_credentials){
            //设置证书
            //使用证书：cert 与 key 分别属于两个.pem文件
            curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLCERT, $curl_credentials['SSLCERT']);
            curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLKEY, $curl_credentials['SSLKEY']);
            curl_setopt($ch,CURLOPT_CAINFO,$curl_credentials['SSLCA_PATH']);
        }
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行curl
        $data = curl_exec($ch);
        //返回结果
        if($data){
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
        }
    }

    /**
     * 获取毫秒级别的时间戳
     */
    private static function getMillisecond()
    {
        //获取毫秒的时间戳
        $time = explode ( " ", microtime () );
        $time = $time[1] . ($time[0] * 1000);
        $time2 = explode( ".", $time );
        $time = $time2[0];
        return $time;
    }

}