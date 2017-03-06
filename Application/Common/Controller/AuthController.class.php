<?php
/*
 * @thinkphp3.2.2  auth认证   php5.3以上
 * @Created on 2015/08/18
 * @Author  slackck   876902658@qq.com
 * @如果需要公共控制器，就不要继承AuthController，直接继承Controller
 */
namespace Common\Controller;
use Think\Controller;
use Think\Auth;
use Think\Model;

//权限认证
class AuthController extends Controller {
	protected function _initialize(){
	
		//自动运行，为了判断左侧导航、右侧导航的选中状态,S为导航ID
		cookie('s',I('s'),86400);
		//session不存在时，不允许直接访问
		if(!cookie('aid')){
			$this->error('还没有登录，正在跳转到登录页',U('Admin/Login/login'));
		}

		//session存在时，不需要验证的权限
		$not_check = array('Index/index','Login/login',//后台首页和登录页
				'Sys/admin_list_add',//管理员设置：添加
				'Sys/ruleorder','Sys/admin_rule_add','Sys/admin_rule_del','Sys/admin_rule_edit','Sys/admin_rule_runedit','Sys/admin_rule_state',//权限设置：排序、添加、删除、修改、修改控制器，状态
				'Sys/admin_group_state','Sys/admin_group_access','Sys/admin_group_del','Sys/admin_group_edit','Sys/admin_group_runaccess');//用户组设置：状态、配置、删除、修改
		
		
		
		//当前操作的请求                 模块名/方法名
		if(in_array(CONTROLLER_NAME.'/'.ACTION_NAME, $not_check)){
			return true;
		}
		
		//下面代码动态判断权限
		$auth = new Auth();
		if(!$auth->check(CONTROLLER_NAME.'/'.ACTION_NAME,cookie('aid')) && cookie('aid')!= 1){
			$this->error('没有权限');
		}
	}
}