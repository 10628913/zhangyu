<?php
namespace Admin\Controller;
use Think\Auth;
class AgreementController extends BaseController {
    public function __construct(){
        parent::__construct();
        $this->agreementDb = M('Agreement');
    }
    /**
    * 协议列表
    */
    public function index(){
        $list = $this->agreementDb->order('id desc')->select();
        $this->assign('list',$list);
        $this->display("agreement_list");
    }
    // 协议增加
    public function agreementAdd(){
        if(IS_POST){
            $data = $_POST['info'];
            $data['addtime'] = time();
            $result = $this->agreementDb->data($data)->add();
            if($result){
                $this->success("操作成功");
            }else{
                $this->error("操作失败");
            }
        }else{
            $editor = new \Org\Editor\Editor;
            $this->assign('editor',$editor);
            $this->display("agreement_add");
        }
    }
    // 协议编辑
    public function agreementEdit(){
        if(IS_POST){
            $id = intval($_POST['id']);
            $data = $_POST['info'];
            $where['id'] = $id;
            $this->agreementDb->where($where)->save($data);
            $this->success("操作成功");
        }else{
            $id = $where['id'] = intval($_GET['id']);
            $info = $this->agreementDb->where($where)->find();
            $this->assign($info);
            $editor = new \Org\Editor\Editor;
            $this->assign('editor',$editor);
            $this->display("agreement_edit");
        }
    }
    // 协议删除
    public function agreeementDelete(){
        if(IS_POST){
            $id = $where['id'] = intval($_POST['id']);
            $result = $this->agreementDb->where($where)->delete();
            if($result){
                $this->success("操作成功");
            }else{
                $this->error("操作失败");
            }
        }
    }
}