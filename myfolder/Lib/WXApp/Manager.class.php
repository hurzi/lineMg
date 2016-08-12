<?php
/**
 * 管理器的基类
 */
abstract class Manager
{
	/**
     * 单条消息信息
     * @var WX_Message
     */
	protected $message;
	/**
	 * 微信消息原格式XML
	 * @var string
	 */
	protected $msgStr;
	/**
     * base库db类
     * @var DB
     */
	protected $db = null;
	/**
     * 实例化数量
     * @var int
     */
	private static $instanceNum = 0;
	/**
     * 最后执行sql是否出现错误
     * @var bool
     */
	protected $sqlError = false;

	public function __construct($message, $msgStr)
	{
		self::$instanceNum ++;
		$this->message = $message;
		$this->messageStr = $msgStr;
		$this->db = Factory::getDb();
	}

}