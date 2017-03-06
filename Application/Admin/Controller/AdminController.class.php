<?php
namespace Admin\Controller;
use Think\Auth;
class AdminController extends BaseController {
    public function __construct(){
        parent::__construct();
        $this->adminDb = M('Admin');
        $this->siteSettingDb = M('Site_setting');
        $this->groupDb = M("Auth_group");
        $this->groupAccessDb = M("Auth_group_access");
    }
    /**
    * 管理员列表
    */
    public function index(){
        $count = $this->adminDb->count();
        $Page = new \Think\Page($count,20);
        $list = $this->adminDb->limit($Page->firstRow.','.$Page->listRows)->order('uid desc')->select();
        foreach ($list as $key => $v) {
            $whereGroup['id'] = $v['group_id'];
            $list[$key]['group_name'] = $this->groupDb->where($whereGroup)->getField("title");
        }
        $show = $Page->show();
        $this->assign('page',$show);
        $this->assign('list',$list);
        $this->display("admin_list");
    }
    /**
    * 管理员增加
    */
    public function adminAdd(){
        if(IS_POST){
            $data = $_POST['info'];
            $password = password($_POST['password']);
            $data['password'] = $password['password'];
            $data['encrypt'] = $password['encrypt'];
            $data['reg_date'] = $data['last_date'] = $data['update_time'] = time();
            $data['reg_ip'] = $data['last_ip'] = ip();
            //判断用户名是否存在
            $where['username'] = $data['username'];
            $isIn = $this->adminDb->where($where)->find();
            if($isIn){
                $this->error('账号已存在');
                return false;
            }
            if(!$data['username'] || !$data['password']){
                $this->error('操作失败');
            }else{
                $result = $this->adminDb->data($data)->add();
                //添加至管理组表
                $groupAccessData['uid'] = $result;
                $groupAccessData['group_id'] = $data['group_id'];
                $this->groupAccessDb->data($groupAccessData)->add();
                if($result){
                    $this->success('操作成功');
                }else{
                    $this->error('操作失败');
                }
            }
        }else{
            $whereData['status'] = 1;
            $groupInfo = $this->groupDb->where($whereData)->select();
            $this->assign("groupInfo",$groupInfo);
            layout(false);
            $this->display("admin_add");
        }
    }

    /**
    * 管理员编辑
    */
    public function adminEdit(){
        if(IS_POST){
            $uid = I("uid");
            $whereData['uid'] = $uid;
            if(!checkuserinfo($_POST['info'])){
                $this->error("提交信息不合法");
            }
            $data = $_POST['info'];
            if(isset($_POST['password']) && !empty($_POST['password'])){
                $password = password($_POST['password']);
                $passwordData['password'] = $password['password'];
                $passwordData['encrypt'] = $password['encrypt'];
                $this->adminDb->where($whereData)->save($passwordData);
            }
            //管理组判断更新
            if(get_auth_group($uid,1) !=  $data['group_id']){
                $this->groupAccessDb->where($whereData)->setField("group_id",$data['group_id']);
            }
            //更新管理员信息
            $data['update_date'] = time();
            $this->adminDb->where($whereData)->save($data);
            // 如果为当前管理员更新本地缓存
            if($uid == session('admin_uid')){
                session('admin_username',''.$data['username'].'');
                session('admin_realname',''.$data['realname'].'');
                session('admin_avatar',''.$data['avatar'].'');
            }
            $this->success("操作成功");
        }else{
            $uid = I("uid");
            //管理员信息读取
            $whereData['uid'] = $uid;
            $adminInfo = $this->adminDb->where($whereData)->find();
            $adminInfo['group_id'] = get_auth_group($uid,1);
            $this->assign($adminInfo);

            //全部用户组获取
            $whereData['status'] = 1;
            $groupInfo = $this->groupDb->where($whereData)->select();
            $this->assign("groupInfo",$groupInfo);
            layout(false);
            $this->display("admin_edit");
        }
    }
    //  当前管理员我的资料修改
    public function myAdminEdit(){
        if(IS_POST){
            $whereData['uid'] = $uid = session('admin_uid');
            if($uid != session('admin_uid')){
                $this->error("非法操作");
            }
            if(!checkuserinfo($_POST['info'])){
                $this->error("提交信息不合法");
            }
            $data = $_POST['info'];
            if(isset($_POST['password']) && !empty($_POST['password'])){
                $password = password($_POST['password']);
                $passwordData['password'] = $password['password'];
                $passwordData['encrypt'] = $password['encrypt'];
                $this->adminDb->where($whereData)->save($passwordData);
            }
            //管理组判断更新
            if(get_auth_group($uid,1) !=  $data['group_id']){
                $this->groupAccessDb->where($whereData)->setField("group_id",$data['group_id']);
            }
            //更新管理员信息
            $data['update_date'] = time();
            $this->adminDb->where($whereData)->save($data);
            // 如果为当前管理员更新本地缓存
            session('admin_username',''.$data['username'].'');
            session('admin_realname',''.$data['realname'].'');
            session('admin_avatar',''.$data['avatar'].'');
            $this->success("操作成功");
        }else{
            //管理员信息读取
            $whereData['uid'] = $uid = session('admin_uid');
            $adminInfo = $this->adminDb->where($whereData)->find();
            $adminInfo['group_id'] = get_auth_group($uid,1);
            $this->assign($adminInfo);

            //全部用户组获取
            $whereData['status'] = 1;
            $groupInfo = $this->groupDb->where($whereData)->select();
            $this->assign("groupInfo",$groupInfo);

            $this->display("admin_edit");
        }
    }
    // 管理员信息浏览
    public function adminInfo(){
        //管理员信息读取
        $uid = intval($_GET['uid']);
        $whereData['uid'] = $uid;
        $adminInfo = $this->adminDb->where($whereData)->find();
        $adminInfo['group_name'] = get_auth_group($adminInfo['uid'],0);
        $this->assign($adminInfo);

        //全部用户组获取
        $whereData['status'] = 1;
        $groupInfo = $this->groupDb->where($whereData)->select();
        $this->assign("groupInfo",$groupInfo);
        layout(false);
        $this->display("admin_info");
    }
    /**
    *管理员删除
    */
    public function adminDelete(){
        $uid = intval($_POST['uid']);
        if($uid == '1'){
            $this->error("当前管理员不允许删除");
        }
        if($uid == session("admin_uid")){
            $this->error("当前管理员不允许删除");
        }
        $where['uid'] = $uid;
        $this->adminDb->data($where)->delete();
        $this->groupAccessDb->data($where)->delete();
        $this->success('删除成功');
    }
    /**
    *批量删除管理员
    */
    public function adminsDelete(){
        if(IS_POST){
            $uids = $_POST['uids'];
            if(in_array('1',$uids) || $uids==1) $this->error("存在不允许禁止的管理员");
            foreach ($uids as $key => $v) {
                $this->adminDb->where(array("uid"=>$v))->delete();
            }
            $this->success('删除成功');
        }
    }
    /*
    *管理员禁止
    *status 1=正常 0=禁止
    */
    public function lock(){
        if(IS_POST){
            $uids = $_POST['uids'];
            if(in_array('1',$uids)) $this->error("存在不允许禁止的管理员");
            foreach ($uids as $key => $v) {
                $this->adminDb->where('uid='.$v)->setField('status','0');
            }
            $this->success('禁止成功');
        }
    }

    /**
    *管理员解除禁止
    */
    public function unlock(){
        if(IS_POST){
            $uids = $_POST['uids'];
            foreach ($uids as $key => $v) {
                $this->adminDb->where('uid='.$v)->setField('status','1');
            }
            $this->success('解除禁止成功');
        }
    }
    // 站点配置
    public function siteSetting(){
        if(IS_POST){
            $data = $_POST['info'];
            $info = $this->siteSettingDb->find();
            if($info){
                $where['id'] = $info['id'];
                $result = $this->siteSettingDb->where($where)->save($data);
            }else{
                $result = $this->siteSettingDb->add($data);
            }
            if($result){
                $this->success('操作成功');
            }else{
                $this->error('操作失败');
            }
        }else{
            $info = $this->siteSettingDb->find();
            $this->assign($info);
            $this->display("site_setting");
        }
    }
}