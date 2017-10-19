<?php
 namespace app\controller;
 use think\View;
 use app\validate\User as UserValidate;
 use app\services\Sms;
 use app\common\libs\Auth;
 use think\Cache;
 use app\common\libs\Auth;
 class User{
 	public function _initialize(){
 		$sms=new Sms();
 	}
   /**
   * 登录操作
   * @return [type] [description]
   */
   public function save(){
     $param=input("post.*");
     $valid=UserValidate::getInstance->check($data);
      if($valid!=true){
      	return return show(0,$valid,[]);
      }
      if(empty($param['m_code'])){
      	return show(0,'验证码不能为空',[],403);
      }
      if(empty($param['m_password'])){
      	return show(0,'密码不能为空',[],403);
      }
      $sms_code=$sms->check_sms_code($param['m_phone']); 
      if($sms_code!=$param['m_code']){
      	return show(0,'短信验证码不存在',[],404);
      }
      //判断输入的手机号是否在数据库存在
      $user=model('User');
      $message=$user->get('phone',$param['m_phone']);
      if(empty($message)){
       $token=Auth::getInstance()->set_login_token();
       //开始对第一次登录的用户信息入库
       $data=[
        'username'=>Auth::getInstance()->set_rand_user(),
        'phone'=>$param['m_phone'],
        'token'=>$token,
        'token_time'=>strtotime("+".config('app.login_invalid_time')."days"),
        'status'=>1
       ];
       //开始对数据$data入库
       $insertId=$user->add($data);
       if($insertId){
       	 $result=[
       	   'token'=>Aes::getInstance()->enctypt($token);
       	 ];
       	 return show(1,'登录成功',$result,200);
       }else{
       	 return show(0,'登录失败',[],500);
       }
    }else{
    	if(!empty($param['m_password'])){
    		if($message['password']==Auth::getInstance()->set_encrypt_pwd($param['m_password'])){
    			return show(1,'登录成功',[],200);
    		}else{
    			return show(0,'密码错误',[],403);
    		}
    	}
    }
   }
 }
