<?php
namespace Admin\Controller;
class ClientController extends BaseController {
    public function __construct(){
        parent::__construct();
        $this->clientDb = M('Client');
        $this->clientAddrDb = M('Client_address');
        $this->adminDb = M('Admin');
        $this->memberDb = M('Member');
    }
    // 客户列表
    public function index(){
        // if(isset($_GET['search'])){
            if(isset($_GET['keyword']) && !empty($_GET['keyword'])){
                $whereSearch['client_name'] = array('like',"%".I('keyword')."%");
                $searchInfo['keyword'] = I("keyword");
            }
        // }

        $uid = session('admin_uid');
        if($uid != 1){
            $whereSearch['parent_id'] = $uid;
        }
        $this->assign('searchInfo',$searchInfo);
        $count = $this->clientDb->where($whereSearch)->count();

        $Page = new \Think\Page($count,20);
        $list = $this->clientDb->limit($Page->firstRow.','.$Page->listRows)->where($whereSearch)->order('client_id desc')->select();
        $show = $Page->show();
        $this->assign('page',$show);
        $this->assign('list',$list);
        $this->display('client_list');
    }
    // 客户增加
    public function clientAdd(){
        
    	if(IS_POST){
    		$data = $_POST['info'];
            $data['parent_id'] = session("admin_uid");
            // 上级类型 0是boss  1是代理
            $data['parent_type'] = 0;
            $data['client_addtime'] = time();
            if($data['client_name'] && !$data['client_remark']){
                $data['initial'] = getFirstCharter($data['client_name']);
            }
            if($data['client_remark'] && !$data['client_name']){
                $data['initial'] = getFirstCharter($data['client_remark']);
            }
            if($data['client_name'] && $data['client_remark']){
                $data['initial'] = getFirstCharter($data['client_remark']);
            }
            if(!$data['client_name'] && !$data['client_remark']){
                return $data['initial'] = '#';
            }
            //判断账号是否存在
            // $where['username'] = $data['username'];
            $where['client_mobile'] = $data['client_mobile'];
            // $where['_logic'] = 'OR';
            $isIn = $this->clientDb->where($where)->find();
            if($isIn){
                $this->error('客户已存在');
            }
            if(!$data['client_mobile']){
                $this->error('操作失败');
            }else{
                $result = $this->clientDb->data($data)->add();
                if($result){
                    // checkMemberGrade($result);
                    $this->success('操作成功');
                }else{
                    $this->error('操作失败');
                }
            }
    	}else{
    		$this->display("client_add");
    	}
    }

    // 客户编辑
    public function clientEdit(){
        if(IS_POST){
            $data = $_POST['info'];
            if($data['client_name'] && !$data['client_remark']){
                $data['initial'] = getFirstCharter($data['client_name']);
            }
            if($data['client_remark'] && !$data['client_name']){
                $data['initial'] = getFirstCharter($data['client_remark']);
            }
            if($data['client_name'] && $data['client_remark']){
                $data['initial'] = getFirstCharter($data['client_remark']);
            }
            if(!$data['client_name'] && !$data['client_remark']){
                return $data['initial'] = '#';
            }
            $where['client_id'] = intval($_POST['client_id']);          
            $this->clientDb->where($where)->save($data);
            $this->success("操作成功");
        }else{
            $client_id = $where['client_id'] = I('client_id');
            $clientInfo = $this->clientDb->where($where)->find();
            $this->assign($clientInfo);
            $this->display("client_edit");
        }
    }
    // 浏览客户信息
    public function clientInfo(){
        $client_id = $where['client_id'] = I("client_id");
        $clientInfo = $this->clientDb->where($where)->find();
        // 上级为boss
        if($clientInfo['parent_type'] == 0){
            $whereAdmin['uid'] = $clientInfo['client_id'];
            $clientInfo['parent_id'] = $this->adminDb->where($where)->getField("username");
            $clientInfo['parent_type'] = 'Boss';
        }else{
            $wheremember['userid'] = $clientInfo['client_id'];
            $clientInfo['parent_id'] = $this->memberDb->where($where)->getField("nickname");
            $clientInfo['parent_type'] = '代理';
        }        
        $this->assign($clientInfo);
        layout(false);
        $this->display("client_info");
    }
    // 客户删除操作
    public function clientDelete(){
        $client_id = $where['client_id'] = I("client_id");
        $clientInfo = $this->clientDb->where($where)->find();
        if($clientInfo){
            $result = $this->clientDb->where($where)->delete();
        }
        if($result){
            $this->success("操作成功");
        }else{
            $this->error("操作失败");
        }
    }
    /**
    *批量操作删除
    */
    public function clientsDelete(){
        if(IS_POST){
            $clientids = explode(",",$_POST['clientids']);
            foreach ($clientids as $key => $v) {
                $this->clientDb->where(array("client_id"=>$v))->delete();
            }
            $this->success('操作成功');
        }
    }

}