<?php
/**
 *该方法用网站的第三方接口调用 
**/
 namespace app\services;
 use Weibo\SaeTOAuthV2;
 use Weibo\SaeTClientV2;
 use think\Cache;
 use think\Log;
 class Third{
 	private $auth=null;
 	private $config=[
 	  'app_key'=>'1779560874',
 	  'app_secret'=>'512748b760c9c55507a97f871ffd9491',
 	  'weibo_back_url'=>'http://api.blogs.cn/index.php/api/callback',
 	];
 	public function __construct($config=array()){
 		if(empty($config)){
 			$this->config=array_merge($config,$this->config);
 		}else{
 			$this->config=array_merge($this->config,$config);
 		}
 		//初始化授权认证
 		$this->auth=new SaeTOAuthV2($this->config['app_key'],$this->config['app_secret']);	
 	}
 	/**
 	 * 微博登录处理
 	**/
 	public function weibologin(){
 		//请求用户授权Token
 		$url=$this->auth->getAuthorizeURL($this->config['weibo_back_url']);
 		echo "<a href='".$url."'>进入到授权页面</a>";
 	}
 	/**
 	 *微博授权回调 并将access_token保存到缓存中
 	 * @param $param=>code:授权
 	 * @param $url 授权回调地址
 	**/
 	public function callback($param,$url){
 		$keys=[];
 		 if(!empty($param)){
 		 	$keys['code']=$param;
 		 	$keys['redirect_uri']=$url;
 		 }
 		 //调用函数来获取授权过的Access Token
 		 $access_token=$this->auth->getAccessToken('code',$keys);
 		 if(! array_key_exists('access_token',$access_token)){
 		 	Log::write("微博授权失败:".json_encode($access_token));
 		 }
 		 if(array_key_exists('expires_in',$access_token)){
 		    Cache::set('weibo_'.$this->auth->client_id,$access_token['access_token'],$access_token['expires_in']);	
 		 }
 		 echo "<a href='http://api.blogs.cn/index.php/api/list'>授权成功</a>";
 	}
 	/**
 	 *授权成功，进行相关操作 :用户自己调用SaeTClientV2类中的方法
 	 * 
 	**/
 	public function weibolist(){
 		$token=Cache::get('weibo_1779560874');
 		if(!empty($token)){
 			//初始化新浪微博操作类
 			$client=new SaeTClientV2($this->config['app_key'],$this->config['app_secret'],$token);
 			$uid_get=$client->get_uid();
 			$uid=$uid_get['uid'];
 			//halt($res);
 		}
 	}
 }
