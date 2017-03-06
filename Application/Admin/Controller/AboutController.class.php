<?php
namespace Admin\Controller;
class AboutController extends BaseController {
    public function __construct(){
        parent::__construct();
        $this->columnDb = M('Column');
    }
    public function edit(){
        if(IS_POST){
            $where['id'] = $id = intval($_POST['id']);
            $data = $_POST['info'];
            $this->columnDb->where($where)->save($data);
            $this->success("操作成功");
        }else{
            $where['id'] = $id = intval($_GET['id']);
            $info = $this->columnDb->where($where)->find();
            $this->assign($info);
            // 编辑器
            $editor = new \Org\Editor\Editor;
            $this->assign('editor',$editor);
            $this->display("edit");
        }
    }
}