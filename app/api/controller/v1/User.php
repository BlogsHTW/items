<?php
 namespace app\api\controller\v1;
 use app\services\Sms;
 use app\validate\User as UserValidate;
 use app/exception\ApiException;
 use think\Cache;
 use app\model\User as UserModel;
 use app\common\libs\Auth;
 class User{
 	//短信对象
 	private $sms=null;
 	private $valid=null;
 	private $user=null;
 	private $default_time=60;
 	public func _initialize(){
 		$this->sms=new Sms();
 		$this->valid=new UserValidate();
 		$this->user=new UserModel();
 	}
 	/**
 	 *登录操作 
 	**/
 	public function login(){
 	  //获取参数:m_phone,m_code	
 	  $param=input('post.*');
 	  if(request()->isPost()){
 	  	 new ApiException(config('apicode.method__error'),'请求非法',401); 
 	  }
 	  //验证手机号和验证码:$param,'login'=>验证场景
 	  $result=$this->valid->check($param,'login');
		 if($result!==true){
		 	//异常处理
		 	new ApiException(config('apicode.param_error'),$result,403);
		 }
		 //判断输入的验证码与短信所给的验证码是否一致
		 $code=$this->sms->check_sms_code($param['m_phone']);
		  if($param['m_code']!=$code){
		  	new ApiException(config('apicode.param_error'),'验证码不存在',403);
		  }
		  //判断手机号是否在数据库
		  //'phone':数据库的字段,第二个参数是字段所对应的值
		  $message=$this->user->get('phone',$param['m_phone']);
		   if(!in_array($param['m_phone'],$message)){
		   	 //新用户第一次登录,数据入库
		   	 $data=[
		   	   'username'=>Auth::getInstance()->set_rand_user(),
		   	   'phone'=>$param['m_phone'],
		   	   'token'=>Auth::getInstance()->set_login_token(),
		   	   'token_time'=>strtotime('+'.config('app.login_invalid_time').'days'),
		   	   'status'=>1,
		   	   'create_time'=>time()
		   	 ];
		   	 $insId=$this->insert($data);
		   	  if($insId){
		   	  	return show();
		   	  }else{
		   	  	return show();
		   	  }
		   }else{
		   	 //数据库存在该电话号码,则需要更新时间
		   	 $data=[
		   	   'update_time'=>time()
		   	 ]
		   	 $where=[
		   	   'phone'=>$param['m_phone']
		   	 ];
		   	 $flag=$this->user->update($where,$data);
		   }
 	} 
 	/**
 	 *通过手机号获取验证码 
 	 **/
 	public function code(){
		//获取手机号
		$param=input('post.m_phone',0,'intval');
		//验证手机号$param,'code'=>验证场景
		$result=$this->valid->check($param,'code');
		 if($result!==true){
		 	//异常处理
		 	new ApiException(config('apicode.param_error'),$result,500);
		 }
		//调用短信接口
		$result=$this->sms->send_sms($param);
		  if($result){
		  	 //短信发送成功
		  	 return show(config('apicode.third_success'),'短信发送成功',[],200);
		  } else{
		  	 //短信发送失败
		  	 return show(config('apicode.third_error'),'短信发送失败,请重新发送',[],500);
		  }
 	}
 	private function insert($data){
 		$insId=$this->user->add($data);
 		return $insId;
 	}
 }
