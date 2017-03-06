<?php
namespace Admin\Controller;
//use Think\Controller;
class IndexController extends BaseController {
    public function __construct(){
        parent::__construct();
        $this->adminDb = M('Admin');
        $this->memberDb = M('Member');
        // $this->newsDb = M('News');
        // $this->buildingDb = M('Building');
    }
    //后台入口
    public function index(){
        $productInfo['copyright'] = "北京点萌科技有限公司";
        $productInfo['http'] = "http://www.dianm.cc";
        $this->assign("productInfo",$productInfo);
        layout(false);
        $this->display('index');
    }
    // 后台首页
    public function main(){
        //  会员数
        $memberCount = $this->memberDb->count();
        $this->assign('memberCount',$memberCount);
        // //  新闻数
        // $newsCount = $this->newsDb->count();
        // $this->assign('newsCount',$newsCount);
        // //  房源数
        // $buildingCount = $this->buildingDb->count();
        // $this->assign('buildingCount',$buildingCount);
        // // 最近10条新闻
        // $newsList = $this->newsDb->order("addtime desc")->limit(10)->select();
        // $this->assign("newsList",$newsList);
        // // 最近10条房源
        // $buildingList = $this->buildingDb->order("addtime desc")->limit(10)->select();
        // $this->assign("buildingList",$buildingList);
        $this->display('main');
    }
    //登录
    public function login(){
        if(IS_POST){
            $where['username'] = $username = $_POST['username'];
            $verify = $_POST['verify'];
            if(!$username || !$_POST['password'] || !$verify){
                $this->error("请输入完整信息");
            }
            if(!check_verify($verify)){
                $this->error("验证码输入错误");
            }
            //根据用户名获取用户信息
            $adminInfo = $this->adminDb->where($where)->find();
            if(!$adminInfo){
                $this->error('管理员不存在');
            }else{
                if($adminInfo['status'] == 0){
                    $this->error('禁止登录');
                }
                $password = password($_POST['password'],$adminInfo['encrypt']);
                if($password==$adminInfo['password']){
                    //验证成功
                    cookie('admin_uid',''.$adminInfo['uid'].'');
                    session('admin_uid',''.$adminInfo['uid'].'');
                    session('admin_username',''.$adminInfo['username'].'');
                    session('admin_realname',''.$adminInfo['realname'].'');
                    session('admin_avatar',''.$adminInfo['avatar'].'');
                    // 更新登录时间
                    $updateData['last_date'] = time();
                    $updateData['last_ip'] = ip();
                    $this->adminDb->where($where)->setField($updateData);
                    $this->success('登录成功',''.__MODULE__.'');
                }else{
                    $this->error('密码错误');
                }
            }
        }else{
            layout(false);
            $this->display('login');
        }
    }
    //退出
    public function logout(){
        cookie('admin_uid',null);
        session('admin_uid',null);
        session('admin_username',null);
        session('admin_realname',null);
        session('admin_avatar',null);
        session('ADMIN_MENU_LIST',null);
        layout(false);
        // $this->success('退出成功');
        $url = "http://".$_SERVER['HTTP_HOST']."/manager.php";
        header("location: ".$url);
    }
    public function verify(){
        $config =    array(
            'fontSize'    =>    30,    // 验证码字体大小
            'length'      =>    4,     // 验证码位数
            'useNoise'    =>    false, // 关闭验证码杂点
        );
        $verify = new \Think\Verify($config);
        $verify->codeSet = '0123456789';
        $verify->entry(1);
    }
    public function getCurrentPos(){
        $menuId = I('menuId');
        echo self::currentPos($menuId);
        exit;
    }
}
