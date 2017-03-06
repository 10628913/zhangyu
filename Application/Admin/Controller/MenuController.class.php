<?php
// 后台菜单管理
namespace Admin\Controller;
class MenuController extends BaseController {
    public function __construct(){
        parent::__construct();
        $this->menuDb = M('Admin_menu');
    }
    /**
    * 菜单列表
    */
    public function menuLists(){
         if(IS_POST){
            if(isset($_POST['dosubmit'])) {
                foreach($_POST['sorts'] as $id => $sort) {
                    $where['id'] = $id;
                    $data['sort'] = $sort;
                    $this->menuDb->where($where)->save($data);
                }
                $this->success("操作成功");
            }
        }else{
            $tree = new \Org\Tree\Tree;
            $data = $this->menuDb->where($where)->order('sort asc,id desc')->select();
            $menuList = $tree->makeTree($data);
            $this->assign("menuList",$menuList);
            $this->display("menu_list");
        }
    }
    // 菜单添加
    public function menuAdd(){
        if(IS_POST){
            $data = $_POST['info'];
            $result = $this->menuDb->add($data);
            if($result){
                $this->success("操作成功");
            }else{
                $this->error("操作失败");
            }
        }else{
            $where['parent_id'] = 0;
            $data = $this->menuDb->where($where)->select();
            $this->assign("menuList",$data);
            layout(false);
            $this->display("menu_add");
        }

    }
    // 菜单编辑
    public function menuEdit(){
        if(IS_POST){
            $id = $where['id'] = I("id");
            $data = $_POST['info'];
            $this->menuDb->where($where)->save($data);
            $this->success("操作成功");
        }else{
            $id = $where['id'] = I("id");
            $whereParent['parent_id'] = 0;
            $parentData = $this->menuDb->where($whereParent)->select();
            $this->assign("parentData",$parentData);
            $data = $this->menuDb->where($where)->find();
            $this->assign($data);
            layout(false);
            $this->display('menu_edit');
        }
    }
    // 菜单删除
    public function menuDelete(){
        if(IS_POST){
            $id = I('id');
            $whereData['parent_id'] = $id;
            $menuData = $this->menuDb->where($whereData)->find();
            if($menuData){
                $this->error("请先删除子菜单");
            }else{
                $where['id'] = $id;
                $this->menuDb->where($where)->delete();
                $this->success("删除成功");
            }
        }
    }
}