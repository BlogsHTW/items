<?php
	/**
	 * 该类处理内部错误
	 **/
 namespace app/exception;
 use think\Exception;
 class ApiException extends Exception{
 	/**
 	 *httpCode http状态码
 	 * 默认状态码为500 
 	 **/
 	private $httpCode;
 	/**
 	 *错误信息 
 	 **/
 	/**
 	 *业务上状态码 
 	 **/
 	private $status=0;
 	private $message="";
 	public function __construct($status,$msg,$httpCode){
 		$this->status=$status;
 		$this->message=$msg;
 		$this->httpCode=$httpCode;
 		return show($this->status,$this->message,[],$this->httpCode);
 	}
 }
