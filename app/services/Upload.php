<?php
 namespace app\services;
 use Qiniu\Auth;
 use Qiniu\Storage\UploadManager;
 use think\Request;
 class Upload{
 	public static function upload(){
 		$file=Request::instance()->file("img");
 		 $config=config("qiniu");
 		 $AccessKey=$config['AccessKey'];
 		 $SecretKey=$config['SecretKey'];
 		 //初始化签权对象
 		 $auth=new Auth($AccessKey,$SecretKey);
 		 //生成上传Token
 		 $token=$auth->uploadToken($config['bucket']);
 		 //构建 UploadManager 对象
 		 $uploadMgr = new UploadManager();
 		 //调用上传的方法
 		  //文件名
 		  $info=$file->getInfo();
 		 $data=pathinfo($info['name']); 
 		 $filename=date("Y")."/".date("m")."/".date("d")."/".substr(md5($AccessKey),0,9).'.'.$ext['extension'];
 		 list($res,$err)=$uploadMgr->putFile($token,$filename,$info['tmp_name']);
 		  if($err!==null){
 		  	return null;
 		  }else{
 		  	return $filename;
 		  }
 	}
 }
?>