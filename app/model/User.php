<?php
 namespace app\model;
 use think\Db;
 class User{
 	public function add($data){
 		$id=Db::name('user')->getLastInsID($data);
 		return $id;
 	}
 	public function get($field,$value){
 		$message=Db::name('user')->where($field,'eq',$value)->find();
 		return $message;
 	}
 	public function update($where,$data){
 		$message=Db::name('user')->where($where)->update($data);
 		return $message;
 	}
 }
