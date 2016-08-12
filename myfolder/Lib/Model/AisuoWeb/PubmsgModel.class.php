<?php
/**
 * 爱锁表
 */
class PubmsgModel extends Model {
	private static $_db;
	private static $_tablename;
	private static $_tablename_user;
	private static $_tablename_reply;
	private static $_tablename_zan;
	private static $_tablename_store;
	private static $_tablename_notice;
	public function __construct() {
		self::$_db = Factory::getDb ();
		self::$_tablename = "as_pubmsg";
		self::$_tablename_user = "as_user";
		self::$_tablename_reply = "as_pubmsg_reply";
		self::$_tablename_zan = "as_pubmsg_zan";
		self::$_tablename_store = "as_pubmsg_store";
		self::$_tablename_notice = "as_pubmsg_notice";
	}
	
	/**
	 * 增加话题表
	 */
	public function savePubmsg($data) {
		try {
			self::$_db->insert ( self::$_tablename, $data );
			return true;
		} catch ( Exception $e ) {
			Logger::error ( "操作消息表失败" . $e->getMessage () );
			return false;
		}
	}
	
	/**
	 * 获取话题的总数
	 */
	public function getCountToMe($uid) {
		try {
			return self::$_db->getOne ( "select count(1) from " . self::$_tablename . " where uid = '{$uid}'" );
		} catch ( Exception $e ) {
			Logger::error ( "操作消息表失败" . $e->getMessage () );
			return false;
		}
	}
	
	/**
	 * 获取公众的列表
	 */
	public function getPublic($pagesize, $pageIndex, $needReply = false, $needZan = false,$timestamp=0) {
		try {
			if($timestamp==0){
				$timestamp = date('Y-m-d H:i:s');
			}else{
				$timestamp = date('Y-m-d H:i:s',$timestamp);
			}
			$start = ($pageIndex - 1) * $pagesize;
			$result = self::$_db->getAll ( "select *
					from " . self::$_tablename . " tmpa 
					where is_delete = 0 and tmpa.create_time<'".$timestamp."'
					order by tmpa.create_time desc 
					limit $start,$pagesize" );
			if($result){
				for($i = 0; $i < count ( $result ); $i ++) {
					// foreach ($arrData as $v){
					$v = $result [$i];
					$duration = floor((time()-strtotime($v['create_time']))/60);
					if($duration<60){
						$result [$i] ['create_time'] = $duration."分钟前";
					}else if($duration<60*24){
						$result [$i] ['create_time'] = floor($duration/60)."小时前";
					}else if($duration<60*24*3){
						$result [$i] ['create_time'] = floor($duration/(60*24))."天前";
					}					
				}
			}
			$result = $needReply ? $this->fillReply ( $result ) : $result;
			$result = $needZan ? $this->fillZan ( $result ) : $result;
			return $result;
		} catch ( Exception $e ) {
			Logger::error ( "操作消息表失败" . $e->getMessage () );
			return false;
		}
	}
	
	/**
	 * 获取一个信条的详情
	 */
	public function getPubmsgById($pubmsgid) {
		try {
			$result = self::$_db->getRow ( "select *
					from " . self::$_tablename . " tmpa
					where as_pubmsg_id='$pubmsgid'" );
			return $result;
		} catch ( Exception $e ) {
			Logger::error ( "操作消息表失败" . $e->getMessage () );
			return false;
		}
	}
	
	/**
	 * 获得未读消息数
	 */
	public function getUnReadyList($uid, $pagesize, $pageIndex) {
		try {
			$start = ($pageIndex - 1) * $pagesize;
			$sql = "select tmpa.*,tmpb.truthname, tmpb.headimgurl 
					from " . self::$_tablename_notice . " tmpa, " . self::$_tablename_user . " tmpb
					where tmpa.uid = tmpb.uid
					and tmpa.as_uid='{$uid}'
					and tmpa.is_ready=0
					order by tmpa.create_time desc
					limit $start,$pagesize
					";		
			$result = self::$_db->getAll ($sql);
			self::$_db->update(self::$_tablename_notice," as_uid=".$uid." and is_ready=0",array("is_ready"=>1));
			return $result;
		} catch ( Exception $e ) {
			Logger::error ( "操作消息表失败" . $e->getMessage () );
			return false;
		}
	}
	
	/**
	 * 获得未读消息数
	 */
	public function getUnReadyTip($uid) {
		try {
			$sql = "select count(1)
					from " . self::$_tablename_notice . " tmpa
					where as_uid='{$uid}'
					and tmpa.is_ready=0";
			$unReadCount = self::$_db->getOne($sql);
			if(!$unReadCount || $unReadCount == 0){
				return null;
			}			
			$sql = "select  tmpa.is_private, tmpb.headimgurl
					from " . self::$_tablename_notice . " tmpa, " . self::$_tablename_user . " tmpb
					where tmpa.uid = tmpb.uid
					and tmpa.as_uid='{$uid}'
					order by tmpa.create_time desc limit 1
					";
			$headimgurl = AbcConfig::DEFAULT_USER_HEADIMGURL;
			$lastOne = self::$_db->getRow ($sql);
			if($lastOne['is_private'] == 0){
				$headimgurl = $lastOne['headimgurl'];
			}
			$result = array("unReadyCount"=>$unReadCount,"headimgurl"=>$headimgurl);
			return $result;
		} catch ( Exception $e ) {
			Logger::error ( "操作消息表失败" . $e->getMessage () );
			return false;
		}
	}
	
	/**
	 * 获取我的收藏的列表
	 */
	public function getMyStore($uid, $pagesize, $pageIndex) {
		try {
			$start = ($pageIndex - 1) * $pagesize;
			$sql = "select tmpa.as_pubmsg_store_id,tmpa.as_pubmsg_id ,tmpc.uid,tmpc.truthname,tmpc.headimgurl
						,tmpb.content,tmpb.is_delete,tmpb.is_private,tmpa.create_time
					from " . self::$_tablename_store . " tmpa," . self::$_tablename . " tmpb," . self::$_tablename_user . " tmpc
					where tmpa.as_pubmsg_id = tmpb.as_pubmsg_id
						and tmpb.uid=tmpc.uid
						and tmpa.uid=$uid
					order by tmpa.create_time desc
					limit $start,$pagesize";
			$result = self::$_db->getAll ( $sql );
			
			return $result;
		} catch ( Exception $e ) {
			Logger::error ( "操作消息表失败" . $e->getMessage () );
			return false;
		}
	}
	
	/**
	 * 获取某一用户的列表
	 */
	public function getPublicByOpenid($openid, $pagesize, $pageIndex, $needReply = false, $needZan = false) {
		try {
			$start = ($pageIndex - 1) * $pagesize;
			$result = self::$_db->getAll ( "select *
					from " . self::$_tablename . " tmpa
					where openid='{$openid}' is_delete = 0
					order by tmpa.create_time desc
					limit $start,$pagesize" );
			$result = $needReply ? $this->fillReply ( $result ) : $result;
			$result = $needZan ? $this->fillZan ( $result ) : $result;
			return $result;
		} catch ( Exception $e ) {
			Logger::error ( "操作消息表失败" . $e->getMessage () );
			return false;
		}
	}
	
	/**
	 * 获取某一用户的列表
	 */
	public function getPublicByUid($uid, $pagesize, $pageIndex, $needReply = false, $needZan = false) {
		try {
			$start = ($pageIndex - 1) * $pagesize;
			$result = self::$_db->getAll ( "select *
					from " . self::$_tablename . " tmpa
					where uid='{$uid}' and is_delete = 0
					order by tmpa.create_time desc
					limit $start,$pagesize" );
			$result = $needReply ? $this->fillReply ( $result ) : $result;
			$result = $needZan ? $this->fillZan ( $result ) : $result;
			return $result;
		} catch ( Exception $e ) {
			Logger::error ( "操作消息表失败" . $e->getMessage () );
			return false;
		}
	}
	
	/**
	 * 填充回复
	 * 
	 * @param unknown $msgList        	
	 */
	public function fillReply($arrData) {
		try {
			if (! $arrData) {
				return $arrData;
			}
			for($i = 0; $i < count ( $arrData ); $i ++) {
				// foreach ($arrData as $v){
				$v = $arrData [$i];
				$replyList = $this->getReplysByPubmsgid ( $v ['as_pubmsg_id'], 3, 1 );
				if ($replyList) {
					$arrData [$i] ['replyList'] = $replyList;
				} else {
					$arrData [$i] ['replyList'] = array ();
				}
			}
			
			return $arrData;
		} catch ( Exception $e ) {
			Logger::error ( "信池表失败" . $e->getMessage () );
			return false;
		}
	}
	
	/**
	 * 填充回复
	 * 
	 * @param unknown $msgList        	
	 */
	public function fillZan($arrData) {
		try {
			if (! $arrData) {
				return $arrData;
			}
			for($i = 0; $i < count ( $arrData ); $i ++) {
				// foreach ($arrData as $v){
				$v = $arrData [$i];
				$zanList = $this->getZanByPubmsgid ( $v ['as_pubmsg_id'] );
				if ($zanList) {
					$arrData [$i] ['zanList'] = $zanList;
				} else {
					$arrData [$i] ['zanList'] = array ();
				}
			}
			return $arrData;
		} catch ( Exception $e ) {
			Logger::error ( "信池表失败" . $e->getMessage () );
			return false;
		}
	}
	
	/**
	 * 获取某一帖子的回复列表
	 */
	public function getReplysByPubmsgid($pubmsgid, $pagesize, $pageIndex) {
		try {
			$start = ($pageIndex - 1) * $pagesize;
			$result = self::$_db->getAll ( "select tmpa.*,tmpb.truthname,tmpb.headimgurl
					from " . self::$_tablename_reply . " tmpa, " . self::$_tablename_user . " tmpb
					where tmpa.uid = tmpb.uid
					and as_pubmsg_id='{$pubmsgid}' and is_delete = 0
					order by tmpa.create_time 
					limit $start,$pagesize" );
			if ($result) {
				for($i = 0; $i < count ( $result ); $i ++) {
					if ($result [$i] ['is_private'] == 1) {
						$result [$i] ['truthname'] = AbcConfig::DEFAULT_USER_TRUTHNAME;
						$result [$i] ['headimgurl'] = AbcConfig::DEFAULT_USER_HEADIMGURL;
					}
					$result [$i] ['create_time'] = date('m月d日 H:i',strtotime($result [$i] ['create_time']));
				}
			}
			
			return $result;
		} catch ( Exception $e ) {
			Logger::error ( "操作消息表失败" . $e->getMessage () );
			return false;
		}
	}
	
	/**
	 * 获取我还未阅读回复消息
	 */
	public function getMyUnReadyReplys($uid, $lastReadyTime) {
		try {
			$result = self::$_db->getAll ( "select tmpa.*,tmpb.truthname,tmpb.headimgurl
					from " . self::$_tablename_reply . " tmpa, " . self::$_tablename_user . " tmpb
						,".self::$_tablename." tc
					where tmpa.uid = tmpb.uid
						and ta.as_pubmsg_id = tc.as_pubmsg_id
					and tb.uid='{$uid}' and tmpa.create_time >'$lastReadyTime'
					order by tmpa.create_time desc" );
			if ($result) {
				for($i = 0; $i < count ( $result ); $i ++) {
					if ($result [$i] ['is_private'] == 1) {
						$result [$i] ['truthname'] = AbcConfig::DEFAULT_USER_TRUTHNAME;
						$result [$i] ['headimgurl'] = AbcConfig::DEFAULT_USER_HEADIMGURL;
					}
				}
			}
			
			return $result;
		} catch ( Exception $e ) {
			Logger::error ( "操作消息表失败" . $e->getMessage () );
			return false;
		}
	}
	
	/**
	 * 获取某一帖子的回复列表
	 */
	public function getZanByPubmsgid($pubmsgid) {
		try {
			$sql = "select tmpa.uid, tmpb.truthname,tmpb.headimgurl
					from " . self::$_tablename_zan . " tmpa, " . self::$_tablename_user . " tmpb
					where tmpa.uid = tmpb.uid
					and tmpa.as_pubmsg_id='{$pubmsgid}' 
					order by tmpa.create_time ";
			return self::$_db->getAll ( $sql );
		} catch ( Exception $e ) {
			Logger::error ( "操作消息表失败" . $e->getMessage () );
			return false;
		}
	}
	
	/**
	 * 判断是否可以有点赞的条件
	 * 
	 * @param unknown $pubmsgid        	
	 * @param unknown $uid        	
	 */
	public function checkZanCondition($pubmsgid, $openid) {
		try {
			$preTime = date ( 'Y-m-d H:i:s', time () - AbcConfig::ZAN_DURATION_TIME * 60 * 60 );
			
			$dbcount = self::$_db->getOne ( "select count(1) from " . self::$_tablename_zan . " where openid='$openid' and as_pubmsg_id=$pubmsgid and create_time>'$preTime'" );
			if ($dbcount >= AbcConfig::ZAN_MAX_COUNT) {
				return false;
			} else {
				return true;
			}
		} catch ( Exception $e ) {
			Logger::error ( "操作消息表失败" . $e->getMessage () );
			return false;
		}
	}
	
	/**
	 * 判断是否可以有点赞的条件
	 * 
	 * @param unknown $pubmsgid        	
	 * @param unknown $uid        	
	 */
	public function checkStoreCondition($pubmsgid, $openid) {
		try {
			
			$dbcount = self::$_db->getOne ( "select count(1) from " . self::$_tablename_store . " where as_pubmsg_id='$pubmsgid' and openid='$openid'" );
			if ($dbcount > 0) {
				return false;
			} else {
				return true;
			}
		} catch ( Exception $e ) {
			Logger::error ( "操作消息表失败" . $e->getMessage () );
			return false;
		}
	}
	
	/**
	 * 加赞
	 * 
	 * @param unknown $pubmsgid        	
	 * @param unknown $uid        	
	 * @param unknown $openid        	
	 * @return boolean
	 */
	public function addPubmsgZan($pubmsgid, $uid, $openid) {
		try {
			self::$_db->query ( "update " . self::$_tablename . " set zan_count=zan_count+1 where as_pubmsg_id=$pubmsgid" );
			
			$data ['uid'] = $uid;
			$data ['openid'] = $openid;
			$data ['as_pubmsg_id'] = $pubmsgid;
			$data ['create_time'] = date ( 'Y-m-d H:i:s' );
			
			$result = $this->savePubmsgZan ( $data );
			if($result){
				$pubmsgObj = $this->getPubmsgById($pubmsgid);
				if($uid != $pubmsgObj['uid']){
					$datanotic ['uid'] = $uid;
					$datanotic ['openid'] = $openid;
					$datanotic ['as_pubmsg_id'] = $pubmsgid;
					$datanotic ['create_time'] = date ( 'Y-m-d H:i:s' );
					$datanotic ['as_uid'] = $pubmsgObj['uid'];
					$datanotic ['as_openid'] = $pubmsgObj['openid'];
					$datanotic ['as_content'] = $pubmsgObj['content'];
					$datanotic ['notice_type'] = 1;
					$datanotic ['is_private'] = 0;
					$this->savePubmsgNotice($datanotic);
				}
			}
			return $result;
		} catch ( Exception $e ) {
			Logger::error ( "操作消息表失败" . $e->getMessage () );
			return false;
		}
	}
	
	/**
	 * 取消点赞
	 * 
	 * @param unknown $pubmsgid        	
	 * @param unknown $uid        	
	 * @param unknown $openid        	
	 * @return boolean
	 */
	public function deletePubmsgZan($pubmsgid, $uid, $openid) {
		try {
			self::$_db->query ( "update " . self::$_tablename . " set zan_count=zan_count-1 where as_pubmsg_id=$pubmsgid" );
			
			return self::$_db->query ( "delete " . self::$_tablename_zan . " where as_pubmsg_id='{$pubmsgid}' and uid='{$uid}'" );
		} catch ( Exception $e ) {
			Logger::error ( "操作消息表失败" . $e->getMessage () );
			return false;
		}
	}
	/**
	 * 收藏
	 * 
	 * @param unknown $pubmsgid        	
	 * @param unknown $uid        	
	 * @param unknown $openid        	
	 * @return boolean
	 */
	public function addPubmsgStore($pubmsgid, $uid, $openid) {
		try {
			self::$_db->query ( "update " . self::$_tablename . " set store_count=store_count+1 where as_pubmsg_id=$pubmsgid" );
			
			$data ['uid'] = $uid;
			$data ['openid'] = $openid;
			$data ['as_pubmsg_id'] = $pubmsgid;
			$data ['create_time'] = date ( 'Y-m-d H:i:s' );
			
			return $this->savePubmsgStore ( $data );
		} catch ( Exception $e ) {
			Logger::error ( "操作消息表失败" . $e->getMessage () );
			return false;
		}
	}
	
	/**
	 * 取消点赞
	 * 
	 * @param unknown $pubmsgid        	
	 * @param unknown $uid        	
	 * @param unknown $openid        	
	 * @return boolean
	 */
	public function deletePubmsgStore($pubmsgid, $uid, $openid) {
		try {
			self::$_db->query ( "update " . self::$_tablename . " set store_count=store_count-1 where as_pubmsg_id=$pubmsgid" );
			
			return self::$_db->query ( "delete from " . self::$_tablename_store . " where as_pubmsg_id='{$pubmsgid}' and uid='{$uid}'" );
		} catch ( Exception $e ) {
			Logger::error ( "操作消息表失败" . $e->getMessage () );
			return false;
		}
	}
	
	/**
	 * 删除贴子
	 * 
	 * @param unknown $pubmsgid        	
	 * @param unknown $uid        	
	 * @param unknown $openid        	
	 * @return boolean
	 */
	public function deletePubmsg($pubmsgid) {
		try {
			return self::$_db->query ( "update " . self::$_tablename . " set is_delete=1 where as_pubmsg_id=" . $pubmsgid );
		} catch ( Exception $e ) {
			Logger::error ( "操作消息表失败" . $e->getMessage () );
			return false;
		}
	}

	/**
	 * 删除贴子批量
	 *
	 * @param unknown $pubmsgid
	 * @param unknown $uid
	 * @param unknown $openid
	 * @return boolean
	 */
	public function deletePubmsgBatch($pubmsgidarr) {
		if(empty($pubmsgidarr)){
			return false;
		}
		try {
			return self::$_db->query ( "update " . self::$_tablename . " set is_delete=1 where as_pubmsg_id  in ('" . implode("','", $pubmsgidarr)."')" );
		} catch ( Exception $e ) {
			Logger::error ( "操作消息表失败" . $e->getMessage () );
			return false;
		}
	}
	/**
	 * 删除回复
	 * 
	 * @param unknown $pubmsgid        	
	 * @param unknown $uid        	
	 * @param unknown $openid        	
	 * @return boolean
	 */
	public function deletePubmsgReply($pubmsgReplyIdd) {
		try {
			$replyObj = $this->getPubmsgReplyById($pubmsgReplyIdd);
			//删除
			$result =  self::$_db->query ( "update " . self::$_tablename_reply . " set is_delete=1 where as_pubmsg_reply_id='" . $pubmsgReplyIdd."'" );
			//更新数据
			self::$_db->query ( "update " . self::$_tablename . " set reply_count=reply_count-1 where as_pubmsg_id=".$replyObj['as_pubmsg_id'] );

			return $result;
		} catch ( Exception $e ) {
			Logger::error ( "操作消息表失败" . $e->getMessage () );
			return false;
		}
	}
	
	/**
	 * 删除回复批量
	 *
	 * @param unknown $pubmsgid
	 * @param unknown $uid
	 * @param unknown $openid
	 * @return boolean
	 */
	public function deletePubmsgReplyBatch($pubmsgReplyIdArr) {
		if(empty($pubmsgReplyIdArr)){
			return false;
		}
		try {
			$replyObj = $this->getPubmsgReplyById($pubmsgReplyIdArr[0]);
				
			$result =  self::$_db->query ( "update " . self::$_tablename_reply . " set is_delete=1 where as_pubmsg_reply_id in ('" . implode("','", $pubmsgReplyIdArr)."')" );
			
			//更新数据
			self::$_db->query ( "update " . self::$_tablename . " set reply_count=reply_count-".count($pubmsgReplyIdArr)." where as_pubmsg_id=".$replyObj['as_pubmsg_id'] );
				
			return $result;
		} catch ( Exception $e ) {
			Logger::error ( "操作消息表失败" . $e->getMessage () );
			return false;
		}
	}
	
	/**
	 * 根据回复ID获得回复内容
	 * @param unknown $pubmsgReplyId
	 */
	public function getPubmsgReplyById($pubmsgReplyId){
		try {
			$result = self::$_db->getRow ( "select *
					from " . self::$_tablename_reply . " tmpa
					where as_pubmsg_reply_id='$pubmsgReplyId'" );
			return $result;
		} catch ( Exception $e ) {
			Logger::error ( "操作消息表失败" . $e->getMessage () );
			return false;
		}
	}
	
	/**
	 * 回复
	 * 
	 * @param unknown $pubmsgid        	
	 * @param unknown $uid        	
	 * @param unknown $openid        	
	 * @return boolean
	 */
	public function addPubmsgReply($pubmsgid, $content, $isprivate, $uid, $openid, $replyuid, $replyname) {
		try {
			self::$_db->query ( "update " . self::$_tablename . " set reply_count=reply_count+1 where as_pubmsg_id=$pubmsgid" );
			
			$data ['uid'] = $uid;
			$data ['openid'] = $openid;
			$data ['as_pubmsg_id'] = $pubmsgid;
			$data ['content'] = $content;
			$data ['is_private'] = $isprivate;
			$data ['reply_to_uid'] = $replyuid;
			$data ['reply_to_name'] = $replyname;
			$data ['create_time'] = date ( 'Y-m-d H:i:s' );
			$data ['is_delete'] = 0;
			
			$pubmsgObj = $this->getPubmsgById($pubmsgid);
			if($uid != $pubmsgObj['uid']){
				$datanotic ['uid'] = $uid;
				$datanotic ['openid'] = $openid;
				$datanotic ['as_pubmsg_id'] = $pubmsgid;
				$datanotic ['create_time'] = date ( 'Y-m-d H:i:s' );
				$datanotic ['as_uid'] = $pubmsgObj['uid'];
				$datanotic ['as_openid'] = $pubmsgObj['openid'];
				$datanotic ['as_content'] = $pubmsgObj['content'];
				$datanotic ['notice_type'] = 2;
				$datanotic ['is_private'] = $isprivate;
				$datanotic ['content'] = $content;
				$this->savePubmsgNotice($datanotic);
			}
				
			
			return $this->savePubmsgReply ( $data );
		} catch ( Exception $e ) {
			Logger::error ( "操作消息表失败" . $e->getMessage () );
			return false;
		}
	}
	
	/**
	 * 增加点赞表
	 */
	public function savePubmsgZan($data) {
		try {
			self::$_db->insert ( self::$_tablename_zan, $data );
			return true;
		} catch ( Exception $e ) {
			Logger::error ( "操作消息表失败" . $e->getMessage () );
			return false;
		}
	}
	
	/**
	 * 增加收藏表
	 */
	public function savePubmsgStore($data) {
		try {
			self::$_db->insert ( self::$_tablename_store, $data );
			return true;
		} catch ( Exception $e ) {
			Logger::error ( "操作消息表失败" . $e->getMessage () );
			return false;
		}
	}
	
	/**
	 * 增加回复表
	 */
	public function savePubmsgReply($data) {
		try {
			self::$_db->insert ( self::$_tablename_reply, $data );
			return true;
		} catch ( Exception $e ) {
			Logger::error ( "操作消息表失败" . $e->getMessage () );
			return false;
		}
	}
	
	/**
	 * 增加通知表
	 */
	public function savePubmsgNotice($data) {
		try {
			self::$_db->insert ( self::$_tablename_notice, $data );
			return true;
		} catch ( Exception $e ) {
			Logger::error ( "操作消息表失败" . $e->getMessage () );
			return false;
		}
	}
	
	/**
	 * 获取客户首页显示数据
	 * @param array $args
	 * @return array
	 */
	public function getPubmsg($args,$needReply = false, $needZan = false,$orderby="create_time",$ordertype="desc")
	{
		$where = "WHERE is_delete=0";
		$groupby = "";
		if($args['openid']){
			$where .=" AND u.`openid`='{$args['openid']}'";
		}
		if($args['uid']){
			$where .=" AND u.`uid`='{$args['uid']}'";
		}
		
		$joinTable = '';
	
		if ($args['truthname'] || $args['is_majia']) {
			$joinTable .= " LEFT JOIN `as_user` dia on dia.uid = u.uid";
			if($args['truthname']){
				$where .= " AND dia.truthname LIKE '%{$args['truthname']}%'";
			}
			if($args['is_majia']){
				$where .= " AND dia.is_majia = '{$args['is_majia']}'";
			}
		}
	
		$limit = '';
		if ($args['paged'] && $args['pagesize']) {
			$limit = " LIMIT ".($args['paged'] -1) * $args['pagesize'].",".$args['pagesize'];
		}
	
		$order = " ORDER BY u.".$orderby." ".$ordertype;
	
		$sql = "SELECT SQL_CALC_FOUND_ROWS u.*  FROM `as_pubmsg` u"
				." {$joinTable} {$where} {$groupby} {$order} {$limit}";
	
		$list = array();
	
		Logger::error("sql:".$sql);
		try {
			$list = $this->getDb()->getAll($sql);
			$count = $this->getDb()->getOne('SELECT FOUND_ROWS()');
		} catch (Exception $e) {
			Logger::error(__FILE__.' '.__CLASS__.' '.__METHOD__,'查询出错');
			return false;
		}
	
		if($list){
			for($i = 0; $i < count ( $list ); $i ++) {
				// foreach ($arrData as $v){
				$v = $list [$i];
				$duration = floor((time()-strtotime($v['create_time']))/60);
				if($duration<60){
					$result [$i] ['create_time'] = $duration."分钟前";
				}else if($duration<60*24){
					$result [$i] ['create_time'] = floor($duration/60)."小时前";
				}else if($duration<60*24*3){
					$result [$i] ['create_time'] = floor($duration/(60*24))."天前";
				}					
			}
		}
		$list = $needReply ? $this->fillReply ( $list ) : $list;
		$list = $needZan ? $this->fillZan ( $list ) : $list;
		return array('list' => $list, 'count' => $count);
		//return $list;
	}
	
	/**
	 * 获取回复显示数据
	 * @param array $args
	 * @return array
	 */
	public function getReplayData($args,$needReply = false, $needZan = false,$orderby="create_time",$ordertype="desc")
	{
		$where = "WHERE is_delete=0";
		$groupby = "";
		if($args['as_pubmsg_id']){
			$where .=" AND u.`as_pubmsg_id`='{$args['as_pubmsg_id']}'";
		}
		if($args['content']){
			$where .=" AND u.`content` like '%{$args['content']}$'";
		}
		
		$joinTable = '';
	
		
	
		$limit = '';
		if ($args['paged'] && $args['pagesize']) {
			$limit = " LIMIT ".($args['paged'] -1) * $args['pagesize'].",".$args['pagesize'];
		}
	
		$order = " ORDER BY u.".$orderby." ".$ordertype;
	
		$sql = "SELECT SQL_CALC_FOUND_ROWS u.*  FROM `as_pubmsg_reply` u"
				." {$joinTable} {$where} {$groupby} {$order} {$limit}";
	
				$list = array();
	
				Logger::error("sql:".$sql);
				try {
					//echo $sql;exit;
					$list = $this->getDb()->getAll($sql);
					$count = $this->getDb()->getOne('SELECT FOUND_ROWS()');
				} catch (Exception $e) {
					Logger::error(__FILE__.' '.__CLASS__.' '.__METHOD__,'查询出错');
					return false;
				}
	
				if($list){
					for($i = 0; $i < count ( $list ); $i ++) {
						// foreach ($arrData as $v){
						$v = $list [$i];
						$duration = floor((time()-strtotime($v['create_time']))/60);
						if($duration<60){
							$result [$i] ['create_time'] = $duration."分钟前";
						}else if($duration<60*24){
							$result [$i] ['create_time'] = floor($duration/60)."小时前";
						}else if($duration<60*24*3){
							$result [$i] ['create_time'] = floor($duration/(60*24))."天前";
						}
					}
				}
				$list = $needReply ? $this->fillReply ( $list ) : $list;
				$list = $needZan ? $this->fillZan ( $list ) : $list;
				return array('list' => $list, 'count' => $count);
				//return $list;
	}
}