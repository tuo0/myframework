<?php
namespace admin\ctrl;

class userCtrl extends \core\lib\baseCtrl  {
	
	/**
	 * 用户退出登陆
	 */
	public function actionLoginout(){
		session_destroy();
		redirect( url('default','login') ); //'/admin'.
	}
	
}