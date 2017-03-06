<?php
namespace App\Controller;
use Think\Controller;
use Think\Auth;
define('IN_MOBILE',true);
class BaseController extends Controller {
	public function _initialize() {
		vendor('Ucpaas.Ucpaas');
        vendor('RongCloud.ServerAPI');
        vendor('Pingpp.Init');
        vendor('JPush.JPush');
		self::check_token();
	}
	final public function check_token(){
		$token = I('token');
		if(in_array(ACTION_NAME, array('login','register','logout','checkSmsCode','sendSms','demo'))){ //过滤方法
			// echo 2;
			return;
		}else{
			//检查token是否存在
			$memberDb = M('Member');
			$where['token'] = $token;
			$info = $memberDb->where($where)->find();
			if($info){
				return;
			}else{
				// $retrunData['msg'] = "请重新登录";
				// $retrunData['code'] = '-10000';
				// $this->ajaxReturn($retrunData);
				// exit;
			}
		}

	}
}