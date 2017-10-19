<?php
 namespace app\exception;
 use think\exception\Handle;
 class ApiHandleException extends Handle{
 	private $httpCode=500;
 	public function render(\Exception $e){
 		if(config['app_debung']){
 			parent::render($e);
 		}
 		if($e instanceof ApiException){
 			$this->httpCode=$e->httpCode;
 		}
 		return show(0,$e->getMessage,[],$this->httpCode);
 	}
 }
