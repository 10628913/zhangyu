<?php
namespace Admin\Controller;
class SchemeController extends BaseController {
    public function __construct(){
        parent::__construct();
        $this->schemeDb = M('Scheme');
    }
    // 列表
    public function index(){

        $count = $this->schemeDb->where($whereSearch)->count();

        $Page = new \Think\Page($count,20);
        $list = $this->schemeDb->limit($Page->firstRow.','.$Page->listRows)->where($whereSearch)->order('id desc')->select();
        $show = $Page->show();
        $this->assign('page',$show);
        $this->assign('list',$list);
        $this->display("scheme_list");
    }
    public function schemeDelete(){
        if(IS_POST){
            $where['id'] = I('id');
            $this->schemeDb->where($where)->delete();
            $this->success("操作成功");
        }
    }


}