<?php
 namespace app\common\libs;
 class Auth{
 	protected static $instance=null;
 	protected static function getInstance(){
 		if(is_null(self::$instance)){
 			self::$instance=new static();
 		}
 		return self::$instance;
 	}
 	/**
     * 设置登录的token
     * @param [type] $phoneNum [description]
     * @return string $token
    */
 	public function set_login_token($phoneNum){
 		$token=md5(md5(uniqid(microtime(),true).$phoneNum));
 		$token=substr($token,0,30);
 		return $token;
 	}
 	/**
 	 * 设置第一次登录随机用户名
 	 * @return 
 	**/
 	public function set_rand_user(){
 	    //获取所有的大写字母
 		$arr_up=range('A','Z');
 		//货物所有的小写字母
 		$arr_low=range('a','z');
 		$str_up=join("",array_rand(array_flip($arr_up),3));
 		$str_low=join("",array_rand(array_flip($arr_low),4));
 		$str=$str_up.$str_low;
 		return $str;
 	}
 	/**
 	 * 对密码进行加密
 	 * @param string $password
 	 * @return 
 	**/
 	public function set_encrypt_pwd($password){
 		$encrypt=md5(md5($password).config('app.password_key'));
 		return $encrypt;
 	}
 }
