<?php
  namespace app\services;
  use Aliyun\Core\Config;
  use Aliyun\Core\Profile\DefaultProfile;
  use Aliyun\Core\DefaultAcsClient;
  use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;
  use Aliyun\Api\Sms\Request\V20170525\QuerySendDetailsRequest;
  use Aliyun\Api\TokenGetterForAlicom;
  use think\Log;
  use think\Cache;
  class Sms{
  	private $iClientProfile;
  	private $itokenGetterForAlicom;
  	protected static $instance=null;
  	private $sms=[
       'ak'=>config('app.ak'),//api中的accessKey
	     //api中的accessKeySecret
	     'aks'=>config('app.aks'),
	     //短信签名
	     'signName'=>config('app.signName'),
	     //短信模板ID
	     'templateCode'=>config('app.templateCode'),
	     'outId'=>config('app.outId')
   ];
    // 短信API产品名,产品名固定
    private $product = "Dysmsapi";
    // 短信API产品域名固定
    private $domain = "dysmsapi.aliyuncs.com";
    // 暂时不支持多Region，仅支持cn-hangzhou不需要修改
    private $region = "cn-hangzhou";
    // 服务结点
    private $endPointName = "cn-hangzhou";
    //短信信息类别:SmsReport:短信回执;SmsUp:短息上行;VoiceReport:语音呼叫
    private $messageType=[
      'SmsReport',
      'SmsUp',
      'VoiceReport',
    ];
  	public function __construct($config=array()){
  		  Config::load();
  		  $this->sms=array_merge($config,$this->sms);
        // 初始化用户Profile实例
        $profile= DefaultProfile::getProfile($this->region,$this->sms['ak'],$this->sms['aks']);
        //增加服务节点
        DefaultProfile::addEndpoint($this->endPointName,$this->region,$this->product,$this->domain);
        //初始化访问的acsCleint，用于发送请求
        $this->iClientProfile=new DefaultAcsClient($profile);
        //初始化短信接收
        $this->itokenGetterForAlicom=new TokenGetterForAlicom('1943695596114318',$this->sms['ak'],$this->sms['aks']);
  	}
   /**
   * 发送短信
   * @param  [type] $phoneNumber 电话号码
   * @param  [type] $outId       短信流水号
   * @return [type]              
   */
  	public function send_sms($phoneNumber,$outId = null){
  		try{
  		//初始化SendSmsRequest类并设置发送短信的参数
  		$request=new SendSmsRequest();
  		//设置发送短信的电话号码
  		$request->setPhoneNumbers($phoneNumber);
  		//设置短信签名
  		$request->setSignName($this->sms['signName']);
  		//设置模板code
  		$request->setTemplateCode($this->sms['templateCode']);
  		//设置模板参数
  		$request->setTemplateParam("{\"number\":\"".$this->sms_verify_code()."\"}");
  		 if($outId){
  		 	//发送短信流水号
  		 	$request->setOutId($outId);
  		 }else{
  		 	$request->setOutId($this->sms['outId']);
  		 }
  		 //发起短信访问请求
  		 $acsResponse=$this->iClientProfile->getAcsResponse($request);
  		}catch(\Exception $e){
  			Log::write("sms(短信)".$e->getMessage());
  			return false;
  		}
       if($acsResponse->Code=="OK"){
       	 Cache::set($phoneNumber,$this->sms_verify_code(),config('app.invalidTime'));
       	 return true;
       }else{
       	 Log::write("sms(短信)".json_encode($acsResponse));
       	 return false;
       }
  	}
   /**
   * 查询短信
   * @param  [type] $phoneNumber 电话号码
   * @param  [type] $outId       短信流水号
   * @return [type]              
   */
  	public function query_sms($phoneNumber,$send_time,$bizId=null,$size=10,$curr=1){
  		try{
  		//初始化QuerySendDetailsRequest实例用于设置短信查询的参数
  		$request=new QuerySendDetailsRequest();
  		//设置查询的电话号码
  		$request->setPhoneNumber($phoneNumber);
  		//设置短信发送日期
  		$request->setSendDate($send_time);
  		//设置短信流水号
  		if($bizId){
  			$request->setBizId($bizId);
  		}
  		//设置分页大小
  		$request->setPageSize($size);
  		//设置当前页
  		$request->setCurrentPage($curr);
  		$acsResponse=$this->iClientProfile->getAcsResponse($request);
  		}catch(\Exception $e){
  			Log::write("查询短信信息:".$e->getMessage());
  			return false;
  		} 
  		if($acsResponse->Code=="OK"){
  			return $acsResponse->SmsSendDetailDTOs->SmsSendDetailDTO;
  		}else{
  			Log::write("查询短信信息:".json_encode($acsResponse));
  			return false;
  		}
  	}
  	/**
  	 * 接收返回的短信信息
  	 * @param string $messageType 消息订阅类型 SmsReport | SmsUp | VoiceReport
     * @param string $queueName 在云通信页面开通相应业务消息后，就能在页面上获得对应的queueName<br/>
  	 **/
  	public function receive_sms($messageType,$queueName){
  		//设置取回消息次数,默认为0
  		$i=0;
  		//取回消息次数大于3停止接收
  		/*while($i<=3){
  			// 取临时token存入存入tokenMap
  			 $tokenForAlicom=$this->tokenGetterForAlicom->tokenGetterForAlicom();
  			 //使用MNSClient得到队列
  			 //$queue=$tokenForAlicom->
  			$i++;
  		}*/
  		$tokenForAlicom=$this->itokenGetterForAlicom->getTokenByMessageType($messageType,$queueName);
  		halt($tokenForAlicom);
  	}
   /**
   * 核对缓存中是否有发送短信的验证码
   * @param  [type] $phoneNumber 手机号
   * @return [type]              
   */
  	public function check_sms_code($phoneNumber){
  		 if(!Cache::has($phoneNumber)){
  		 	  return false;
  		 } 
  		 return Cache::get($phoneNumber);
  	} 
  	private function sms_verify_code(){
  		$codeNum=range(0,9);
  		$randNum=array_rand(array_flip($codeNum),6);
  		$smsCode=join("",$randNum);
  		return $smsCode;
  	}
  }
