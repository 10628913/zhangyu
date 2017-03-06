<?php
namespace Member\Controller;
class IndexController extends BaseController {
    public function __construct(){
        parent::__construct();
        $this->memberDb = M('Member');
    }
    public function index(){
        $this->display('Member/index');
    }
    public function login(){
        if(IS_POST){
            $username = I('username');
            $password = I('password');
            if(!$username || !$password){
                $this->error("请输入完整信息");
            }
            //根据用户名检查用户是否存在
            $where['username'] = $username;
            $data = $this->memberDb->where($where)->find();
            if(!$data){
                //不存在
                $this->error('用户不存在');
            }else{
                //存在、验证密码是否正确
                $password = password($password,$data['encrypt']);
                if($password==$data['password']){
                    session('userid',null);
                    session('nickname',null);
                    session('avatar',null);
                    //验证成功
                    session('userid',''.$data['userid'].'');
                    session('nickname',''.$data['nickname'].'');
                    session('avatar',''.$data['avatar'].'');
                    //更新用户登录时间和ip
                    $updateData['last_date'] = time();
                    $updateData['last_ip'] = ip();
                    $this->memberDb->where($where)->save($updateData);
                    $this->success('登录成功','/Member/Index');
                }else{
                    $this->error('密码错误');
                }
            }
        }else{
            $this->display("Member/login");
        }
    }
    public function logout(){

    }
}