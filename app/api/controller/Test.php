<?php
 namespace app\api\controller;
 use think\captcha\Captcha;
 use think\Request;
 use think\View;
 use app\services\Upload;
 use app\common\libs\Aes;
 use app\services\Third;
 use think\Cache;
 class Test{
 	public function login(){
 		return View::instance()->fetch("login");
 	}
 	public function verify(){
 		$config=[
 		  'imageW'=>200,
 		  'imageH'=>60,
 		  'length'=>4,
 		  'useZh'=>true
 		];
 		$captcha=new Captcha($config);
 		return $captcha->entry();
 	}
 	public function check(){
 		if(Request::instance()->isPost()){
 			$verify=input("post.verify");
 			$captcha=new Captcha();
 			$flag=$captcha->check($verify);
 			if($flag!=false){
 				return "验证码正确";
 			}else{
 				return "验证码错误";
 			}
 		}else{
 			return "访问页面有错误";
 		}
 	}
 	public function img(){
 		return View::instance()->fetch("upload");
 	}
 	public function upload_one(){
 		$file=Request::instance()->file("img");
 		$info=$file->getInfo();
 		$data=pathinfo($info['name']);
 		halt($_FILES['img']);
 		 $info = $file->move(ROOT_PATH . 'public' . DS . 'test');
 		 if($info){
 		 	$data=[
 		 	  'code'=>200,
 		 	  'msg'=>'图片上传成功',
 		 	  'data'=>$info->getSaveName()
 		 	];
 		 	return  json($data);
 		 }else{
 		 	$data=[
 		 	 'code'=>404,
 		 	  'msg'=>'图片上传失败',
 		 	  'data'=>$file->getError()
 		 	];
 		 	return  json($data);
 		 }
 	}
 	public function upload_two(){
 		$image=Upload::image();
 		 if($image){
 		 	$data=[
 		 	 'code'=>200,
 		 	  'msg'=>'图片上传成功',
 		 	  'data'=>$image
 		 	];
 		 	return json($data);exit;
 		 }
 		 $data=[
 		      'code'=>200,
 		 	  'msg'=>'图片上传成功',
 		 	  'data'=>$image
 		 ];
 		 return json($data);
 	}
 	public function aes_test(){
 		$head=request()->header();
 		return show(1,'ok',['name'=>'wanglei'],200);
 	}
 	public function send_sms(){
 		$phone="17854298392";
 		$config=[
	     'ak'=>config('app.ak'),//api中的accessKey
	     //api中的accessKeySecret
	     'aks'=>config('app.aks'),
	     //短信签名
	     'signName'=>config('app.signName'),
	     //短信模板ID
	     'templateCode'=>config('app.templateCode')
     ];
 		$sms=new \app\services\Sms($config);
 		//query_msm($phoneNumber,$send_time,$bizId=null,$size=10,$curr=1)
 		$query=$sms->query_sms($phone,'20171017');
 		 if($query){
 		 	return show(1,'查询短信成功',$query,200);
 		 }
 		//$sms->receive_sms(config('app.messageType'),config('app.queueName'));
 	}
 	public function weibo(){
 		$wb=new Third();
 		$wb->weibologin();
 	}
 	public function callback(){
 		$wb=new Third();
 		$code=request()->get('code');
 		$url=config('app.weibo_back_url');
 		$wb->callback($code,$url);	
 	}
 	public function weibolist(){
 		$wb=new Third();
 		$wb->weibolist();
 	}
 }
?>