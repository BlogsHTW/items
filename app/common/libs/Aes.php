<?php
  //Aes加密与解密
  namespace app\common\libs;
  class Aes{
  	protected static $instance=null;
  	//密钥
  	private $aesKey=null;
  	public function __construct(){
  		$this->aesKey=config("app.aeskey");
  	}
  	protected static function getInstance(){
  		 if(is_null(self::$instance)){
  		 	  self::$instance=new static();
  		 }
  		 return self::$instance;
  	} 
  	/**
    * Aes加密
    * @param   $data 加密的数据
    * @return  
    */
    public function enctypt($data){
    	//获取打开的算法分组大小
    	$size=mcrypt_get_block_size(MCRYPT_RIJNDAEl_128,MCRYPT_MODE_ECB);
  		//打开加密算法与模式
  		$td=mcrypt_module_open(MCRYPT_RIJNDAEl_128,'',MCRYPT_MODE_ECB,'');
  		 //初始向量大小
  		 $ini_size=mcrypt_enc_get_iv_size($td);
  		//从随机源中创建初始向量 
  		$iv=mcrypt_create_iv($ini_size,MCRYPT_RAND);
  		//初始化加密所需的缓冲区
  		mcrypt_generic_init($td,$this->aesKey,$iv);
  		//获取加密pkcs5模式下的数据
  		$input=$this->pkcs5_pad($data,$size);
  		//开始加密数据
  		$encrypted=mcrypt_generic($td,$input);
  		//对加密模块进行清理
  		mcrypt_generic_deinit($td);
  		//关闭加密所需的缓冲区
  		mcrypt_module_close($td);
  		$aes_encode=base64_encode($encrypted);
  		return $aes_encode;
  	}
    /**
    * Aes解密
    * @param   $data 解密的数据
    * @return  
    */
    public function dectypt($data){
    	//对加密的数据进行解密
  	    $decrypted=mcrypt_decrypt(MCRYPT_RIJNDAEl_128,$this->aesKey,base64_decode($data),MCRYPT_MODE_ECB);
  	    $dec_len=strlen($decrypted);
  	    $padding=ord($decrypted[$dec_len-1]);
  	    //获取解密的数据并输出
  	    $dec_decode=strsub($dec_decode,0,-$padding);
  	    return $des_decode;
    }
    /**
     * 加密模式
     * @param  [type] $data 加密数据
     * @param  [type] $size 加密初始化分组向量大小
     * @return 
     */
  private function  pkcs5_pad($data,$size){
     $pad=$size-(strlen($data)%$size);
     return $data.str_repeat(chr($pad),$pad);
  }
} 