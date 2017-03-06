<?php
namespace Member\Controller;
use Think\Controller;
class BaseController extends Controller {
    public function __construct() {
        parent::__construct();
        self::check_login();
        self::check_ip();

    }
    //验证登录
    final public function check_login() {
        if(MODULE_NAME == 'Member' && CONTROLLER_NAME == 'Index' && in_array(ACTION_NAME, array('login','logout'))) {
            //
            return true;
        } else {
            //验证管理员
            $userid = session('userid');
            if(!$userid){
                $this->error('操作超时，重新登录','/Member/Index/login',1);
            }
        }
    }
    /**
     *
     * IP禁止判断 ...
     */
    final private function check_ip(){

    }
}