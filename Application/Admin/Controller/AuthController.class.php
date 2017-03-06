<?php
namespace Admin\Controller;
class AuthController extends BaseController {
    public function __construct(){
        parent::__construct();
        $this->ruleDb = M('Auth_rule');
    }
    /**
    * 规则列表
    */
    public function index(){
        $tree = new \Org\Tree\Tree;
        $data = $this->ruleDb->order('sort desc,id desc')->select();
        $ruleList = $tree->makeTree($data);
        $this->assign("ruleList",$ruleList);
        $this->display("rule_list");
    }
    /**
    * 规则增加
    */
    public function ruleAdd(){
        if(IS_POST){
            $data = $_POST['info'];
            $result = $this->ruleDb->add($data);
            if($result){
                $this->success("操作成功");
            }else{
                $this->error("操作失败");
            }
        }else{
            $where['parent_id'] = 0;
            $data = $this->ruleDb->where($where)->select();
            $this->assign("ruleList",$data);
            layout(false);
            $this->display("rule_add");
        }
    }

    /**
    * 规则编辑
    */
    public function ruleEdit(){
        if(IS_POST){
            $where['id'] = $id = I("id");
            $data = $_POST['info'];
            $this->ruleDb->where($where)->save($data);
            $this->success("操作成功");
        }else{
            $where['id'] = $id = I("id");
            $whereParent['parent_id'] = 0;
            $parentData = $this->ruleDb->where($whereParent)->select();
            $this->assign("parentData",$parentData);
            $data = $this->ruleDb->where($where)->find();
            $this->assign($data);
            layout(false);
            $this->display('rule_edit');
        }

    }
    /**
    *规则删除
    */
    public function ruleDelete(){
        if(IS_POST){
            $id = intval($_POST['id']);
            $whereData['parent_id'] = $id;
            $ruleData = $this->ruleDb->where($whereData)->find();
            if($ruleData){
                $this->error("请先删除子规则");
            }else{
                $where['id'] = $id;
                $this->ruleDb->where($where)->delete();
                $this->success("删除成功");
            }
        }
    }
}