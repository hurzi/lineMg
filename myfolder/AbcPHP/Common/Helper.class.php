<?php
include_once ABC_PHP_PATH . '/Common/AbcUtilTools.class.php';
/**
 * 这里是一些各种功能的封装
 */
class Helper {
    /**
     * MD5签名
     * @param array $param 参加签名的参数
     * @param boolean $urlencode
     * @param boolean $lower
     * @param string|array $suffix 后缀
     * @return string signd string
     */
    public static function md5Sign($param,$urlencode = false,$lower = false,$suffix = ''){
        $unSignStr = self::formatQueryParaMap($param,$urlencode,$lower);
        return md5($unSignStr.(is_string($suffix) ? $suffix : ''));
    }
    /**
     * 格式化参数列表
     * @param array $paraMap 要格式化的数据
     * @param boolean $urlencode 是否将值urlencode
     * @return string
     */
    public static function formatQueryParaMap($paraMap, $urlencode = false,$lower = false){
        $buff = "";
        if($lower){
            $tmp = array();
            foreach ($paraMap as $k => $v){
                $tmp[strtolower($k)] = $v;
            }
            $paraMap = $tmp;
        }
        ksort($paraMap,SORT_STRING);
        foreach ($paraMap as $k => $v){
            if (null != $v && "null" != $v && "sign" != $k) {
                if($urlencode) $v = str_replace('+', '%20', urlencode($v));
                $buff .= $k.'='.$v.'&';
            }
        }
        if (strlen($buff) > 0) return substr($buff, 0, -1);
        return false;
    }
    public static function createMessageBody($material,$openid){
        $messageBody = new WX_Message_Body();
        $messageBody->type = $material['msgtype'];
        $messageBody->to_users = $openid;
        switch ($messageBody->type) {
            case 'text' :
                $messageBody->content = trim($material['content']);
                break;
            case 'news' :
                if (! $material['articles'] || !is_array($material['articles'])) {
                    Logger::error(__METHOD__ . " 图文类型数据为空");
                    return null;
                }
                foreach ($material['articles'] as $key => $value) {
                    if (! is_array($value) || ! $value || ! @$value['title']
                    || ! @$value['description'] || ! @$value['url'] || ! @$value['picurl']) {
                        Logger::error(__METHOD__ . " 图文类型数据格式错误", $material['articles']);
                        return null;
                    }
                }
                $messageBody->articles = $material['articles'];
                break;
            case 'music' :
                if (! $material['title'] || ! $material['description'] ||
                ! $material['url'] || ! $material['thumb_url']) {
                    Logger::error(__METHOD__ . " 音乐类型数据格式错误");
                    return null;
                }
                $messageBody->title = $material['title'];
                $messageBody->description = $material['description'];
                $messageBody->music_url = $material['url'];
                $messageBody->thumb_path = $material['thumb_url'];
                $messageBody->hq_music_url = @$material['hq_url'];
                $messageBody->thumb_media_id = @$material['thumb_media_id'];
                break;
            case 'template' :
                $tempData = json_decode($material['data'], true);
                if (!$tempData) {
                    return false;
                }
                $messageBody -> template_id =  $material['template_id'];
                $messageBody -> data = $tempData;
                $messageBody -> url = $material['url'];
                $messageBody -> topcolor = $material['topcolor'];
                break;
            case 'image' :
                if (! $material['media_id'] ) {
                    Logger::error(__METHOD__ . "图片类型数据格式错误");
                    return null;
                }
                $messageBody->media_id = $material['media_id'];
                break;
            case 'voice' :
                if (! $material['media_id'] ) {
                    Logger::error(__METHOD__ . "语音类型数据格式错误");
                    return null;
                }
                $messageBody->media_id = $material['media_id'];
                break;
            case 'video' :
                if (! $material['media_id'] || ! $material['thumb_media_id']) {
                    Logger::error(__METHOD__ . " 媒体类型数据不存在");
                    return null;
                }
                $messageBody->media_id = $material['media_id'];
                $messageBody->thumb_media_id = $material['thumb_media_id'];
                break;
            default :
                Logger::error(__METHOD__ . " 消息类型不存在");
                return null;
        }
        return $messageBody;
    }

    /**
     * 请使用createMessgeid
     * @return string
     * @deprecated
     */
    public static function getMessageId(){
        $ip = @$_SERVER['SERVER_ADDR'];
        $start = strrpos($ip, '.');
        $prefix = substr($ip, $start+1);
        $diff = 3 - strlen($prefix);
        if($diff > 0){
            $prefix .= getRandStr($diff);
        }
        return uniqid($prefix) . getRandStr(3);
    }
    /**
     * 生成消息id （message time + openid）
     * @param WX_Message $message
     * @return string
     */
    public static function createMessageId ($message) {
    	if ($message && $message->message_id) {
    		return $message->message_id;
    	}
    	if ($message) {
    		if (strtolower($message->type) == 'event' && $message->event) {
    			//如果是卡券相关事件，以openid+card_id+code 做message id
    			if ($message->event->card_id && $message->event->user_card_code) {
    				$id = md5($message->fromUser.$message->event->card_id.$message->event->user_card_code);
    				return substr($id, 8, 18);
    			}
    		}
    		$id = md5($message->fromUser.$message->created_at);
    		return substr($id, 8, 18);
    	}
    	return uniqid() . getRandStr(3);
    }
    /**
     * 根据素材生成图文消息url
     * @param string $openid
     * @param string $apiKey
     * @param string $apiSecret
     * @param array $material
     * @return string the url
     */
    public static function getNewsUrl ($openid, $apiKey, $apiSecret, $material) {
    	if ($material['news_text']) {
	    	$param = array( ThirdPartyReqParams::API_KEY=>$apiKey
	    				  , ThirdPartyReqParams::API_SECRET => $apiSecret
	    				  , ThirdPartyReqParams::OPEN_ID => $openid
	    				  , 'mid'=>$material['m_id']
	    				  , 'index'=>$material['index']
	    				  , ThirdPartyReqParams::TIMESTAMP => time()
	    			);
	    	$param[ThirdPartyReqParams::SIG] = Helper::md5Sign($param);
	    	unset($param[ThirdPartyReqParams::API_SECRET]);
	    	$url = C('NEWS_TEXT_URL').'?'.http_build_query($param);//TODO
    	} else {
    		$url = @$material['url'];
    	}
    	return $url;
    }
}
