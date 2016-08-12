<?php
set_time_limit(0);

	header("Content-Type: text/html; charset=GBK");
	
	/**
	 * ���������·��
	 */
	define('SCRIPT_ROOT',  dirname(__FILE__).'/');
	require_once SCRIPT_ROOT.'include/Client.php';
	
	
/**
 * ��ص�ַ
 */	
$gwUrl = 'http://sdkhttp.eucp.b2m.cn/sdk/SDKService?wsdl';


/**
 * ���к�,��ͨ������������Ա��ȡ
 */
$serialNumber = '0SDK-EBB-0130-NEWRN';

/**
 * ����,��ͨ������������Ա��ȡ
 */
$password = '879635';

/**
 * ��¼������е�SESSION KEY������ͨ��login����ʱ����
 */
$sessionKey = '879635';

/**
 * ���ӳ�ʱʱ�䣬��λΪ��
 */
$connectTimeOut = 2;

/**
 * Զ����Ϣ��ȡ��ʱʱ�䣬��λΪ��
 */ 
$readTimeOut = 10;

/**
	$proxyhost		��ѡ�������������ַ��Ĭ��Ϊ false ,��ʹ�ô��������
	$proxyport		��ѡ������������˿ڣ�Ĭ��Ϊ false
	$proxyusername	��ѡ������������û���Ĭ��Ϊ false
	$proxypassword	��ѡ��������������룬Ĭ��Ϊ false
*/	
	$proxyhost = false;
	$proxyport = false;
	$proxyusername = false;
	$proxypassword = false; 

$client = new Client($gwUrl,$serialNumber,$password,$sessionKey,$proxyhost,$proxyport,$proxyusername,$proxypassword,$connectTimeOut,$readTimeOut);
/**
 * ���������˵ı��룬���ҳ��ı���ΪGBK����ʹ��GBK
 */
$client->setOutgoingEncoding("GBK");

 login();   //�������к�
// updatePassword();  //�޸�����
// logout();          //ע�����к� 
// registDetailInfo();//ע����ҵ��Ϣ
// getEachFee();      //�õ����� 
// getMO();           //���ն���
// getVersion();      //�õ��汾��   
// sendSMS();         //���Ͷ���
// getBalance();      //�õ����
// chargeUp();        //��ֵ


//----------------------------------------------------------------------
// ע: 
// 1. �����Ǹ��ӿڵ�ʹ������Client.php ����ÿһ���ӿڸ���ϸ�Ĳ���˵��
// 2. ���Ƿ��� $statusCode ��, ������ز�����״̬��
// 3. ����php�����������ԣ��������û����ʱ��Ҳ���ͬ��Ϊ $statusCode=='0', �������ж�ʱӦ��ʹ�� if ($statusCode!=null && $statusCode==0) 
//----------------------------------------------------------------------







/**
 * �ӿڵ��ô���鿴 ����
 */
function chkError()
{
	global $client;
	
	$err = $client->getError();
	if ($err)
	{
		/**
		 * ���ó��?����������ԭ�򣬽ӿڰ汾ԭ�� �ȷ�ҵ���ϴ�������⵼�µĴ���
		 * ����ÿ���������ú�鿴�����ڿ�����Ա����
		 */
		
		echo $err;
	}
	
}

/**
 * ��¼ ����
 */
function login()
{
	global $client;
	
	/**
	 * ����Ĳ����ǲ������6λ�� session key
	 * ע��: ���Ҫ���µ�session key�������Ҫ���ȳɹ�ִ�� logout(ע�����)����ܸ�
	 * ���ǽ��� sesson key���ó���
	 */
	//$sessionKey = $client->generateKey();
	//$statusCode = $client->login($sessionKey);
	
	$statusCode = $client->login();
	
	echo "����״̬��:".$statusCode."<br/>";
	if ($statusCode!=null && $statusCode=="0")
	{
		//��¼�ɹ������������� $sessionKey �Ĳ����������Ժ���ز�����ʹ��
		echo "��¼�ɹ�, session key:".$client->getSessionKey()."<br/>";
	}else{
		//��¼ʧ�ܴ���
		echo "��¼ʧ��";
	}
	 
}

/**
 * ע���¼ ����
 */
function logout()
{
	global $client;

	$statusCode = $client->logout();
	echo "����״̬��:".$statusCode;
}

/**
 * ��ȡ�汾�� ����
 */
function getVersion()
{
	global $client;
	
	echo "�汾:". $client->getVersion();
	
}
	
	
/**
 * ȡ�����ת�� ����
 */	
function cancelMOForward()
{
	global $client;
	

	$statusCode = $client->cancelMOForward();
	echo "����״̬��:".$statusCode;
}

/**
 * ���ų�ֵ ����
 */
function chargeUp()
{
	global $client;
	
	/**
	 * $cardId [��ֵ������]
	 * $cardPass [����]
	 * 
	 * ��ͨ������������Ա��ȡ [��ֵ������]����Ϊ20�� [����]����Ϊ6
	 * 
	 */
	 
	$cardId = 'EMY00000000000000000';
	$cardPass = '000000';
	$statusCode = $client->chargeUp($cardId,$cardPass);
	echo "����״̬��:".$statusCode;
}


/**
 * ��ѯ�������� ����
 */
function getEachFee()
{
	global $client;
	$fee = $client->getEachFee();
	echo "����:".$fee;
}


/**
 * ��ҵע�� ����
 */
function registDetailInfo()
{
	global $client;
	
	$eName = "xx��˾";
	$linkMan = "��xx";
	$phoneNum = "010-1111111";
	$mobile = "159xxxxxxxx";
	$email = "xx@yy.com";
	$fax = "010-1111111";
	$address = "xx·";
	$postcode = "111111";
	
	/**
	 * ��ҵע��  [��������]����Ϊ6 ��������Ϊ20����
	 * 
	 * @param string $eName 	��ҵ���
	 * @param string $linkMan 	��ϵ������
	 * @param string $phoneNum 	��ϵ�绰
	 * @param string $mobile 	��ϵ�ֻ����
	 * @param string $email 	��ϵ�����ʼ�
	 * @param string $fax 		�������
	 * @param string $address 	��ϵ��ַ
	 * @param string $postcode  ��������
	 * 
	 * @return int �������״̬��
	 * 
	 */
	$statusCode = $client->registDetailInfo($eName,$linkMan,$phoneNum,$mobile,$email,$fax,$address,$postcode);
	echo "����״̬��:".$statusCode;
	
}

/**
 * �������� ����
 */
function updatePassword()
{
	global $client;
	
	/**
	 * [����]����Ϊ6
	 * 
	 * ������������ǽ������޸ĳ�: 654321
	 */
	$statusCode = $client->updatePassword('654321');
	echo "����״̬��:".$statusCode;
}

/**
 * ����ת�� ����
 */
function setMOForward()
{
	
	global $client;

	/**
	 * �� 159xxxxxxxx ����ת������
	 */	
	$statusCode = $client->setMOForward('159xxxxxxxx');
	echo "����״̬��:".$statusCode;
}

/**
 * �õ����ж��� ����
 */
function getMO()
{
	global $client;
	$moResult = $client->getMO();
	echo "��������:".count($moResult);
	foreach($moResult as $mo)
	{
		//$mo ��λ�� Client.php ��� Mo ����
		// ʵ�����Ϊֱ�����
	 	echo "�����߸�����:".$mo->getAddSerial();
	 	echo "�����߸�����:".$mo->getAddSerialRev();
	 	echo "ͨ����:".$mo->getChannelnumber();
	 	echo "�ֻ��:".$mo->getMobileNumber();
	 	echo "����ʱ��:".$mo->getSentTime();
	 	
	 	/**
	 	 * ���ڷ���˷��صı�����UTF-8,������Ҫ���б���ת��
	 	 */
	 	echo "��������:".iconv("UTF-8","GBK",$mo->getSmsContent());
	 	
	 	// ���ж������Ҫ����,����ҵ���߼�����,�磺������ݿ⣬д�ļ��ȵ�
	}
		
}

/**
 * ���ŷ��� ����
 */
function sendSMS()
{
	global $client;
	/**
	 * ����Ĵ��뽫��������Ϊ test �� 159xxxxxxxx �� 159xxxxxxxx
	 * $client->sendSMS���и����ò�����ο� Client.php
	 */
	$statusCode = $client->sendSMS(array('15101019215',"tetdsdwtwe:213412"));
	echo "����״̬��:".$statusCode;
}

/**
 * ����ѯ ����
 */
function getBalance()
{
	global $client;
	$balance = $client->getBalance();
	echo "���:".$balance;
}

/**
 * ����ת����չ ����
 */
function setMOForwardEx()
{
	global $client;

	/**
	 * �����������ת������
	 * 
	 * ��������ʽ��д�ֻ����
	 */	
	$statusCode = $client->setMOForwardEx(
		array('159xxxxxxxx','159xxxxxxxx','159xxxxxxxx')
	);
	echo "����״̬��:".$statusCode;
}

?>
