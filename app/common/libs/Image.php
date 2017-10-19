<?php
 namespace app\common\libs;
 use app\service\Upload;
 class Image{
 	public static function image(){
 		$image=Upload::upload();
 		$url_image=config('qiniu.image_url')."/".$image;
 		$url=[
 		  'url_image'=>$url_image
 		];
 		return show(1,'上传图片成功',$url_image,200);
 	}
 }
