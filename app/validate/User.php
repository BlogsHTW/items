<?php
 namespace app\validate;
 use think\Validate;
 class User{
 	private $valid=null;
 	protected static $instance=null;
 	protected $rule=[
 	  'm_phone'=>'require|length:11',
 	  'm_code'=>'require|length:6|number'
 	];
 	protected $msg=[
 	  'm_phone.require'=>'电话号码不能为空',
 	  'm_phone.length'=>'电话号码长度必须为11',
 	  'm_code.require'=>'验证码不能为空',
 	  'm_code.length'=>'验证码长度必须为6',
 	  'm_code.number'=>'验证码必须为数字'
 	];
 	protected $scene=[
 	 'code'=>['m_phone'],
 	 'login'=>['m_phone','m_code']
 	];
 	public function _initiazile(){
 		$this->valid=new Validate($rules,$msg);
 	}
 	public function check($data,$scene){
 		//判断验证场景的key是否存在
 		if(array_key_exits($scene,$this->scene)){
 			return true;
 		}
 		$result=$valid->scene($scene[$scene])->check($data);
 		 if($result!==false){
 		 	return true;
 		 }
 		 return $valid->getError();
 		 
 	}
 }
