<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

 /**
   * api开发过程中数据接口
   * @param  integer $status   业务逻辑状态码
   * @param  string  $msg      错误提示信息
   * @param  array   $data     数据
   * @param  integer  $httpCode http状态码
   * @return json     json数据  
   */
  function show($status=0,$msg="",$data=[],$httpCode=200){
    $result=[
      'status'=>$status,
      'message'=>$msg,
      'data'=>$data
    ];
    return json($result,$httpCode);
}
