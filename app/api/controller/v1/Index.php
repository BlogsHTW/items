<?php
	namespace app\api\controller\v1;
	use think\View;
	class Index{
		private $view=null;
		public function __construct(){
			$this->view=View::instance();
		}
		public function index(){
			//return show(1,'ok',['name'=>'wnaglei'],200);
			return $this->view->fetch("/index");
		}
	}
