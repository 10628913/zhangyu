<?php
namespace Admin\Controller;
class GoodsController extends BaseController {
    public function __construct(){
        parent::__construct();
        $this->goodsDb = M('Goods');
        $this->adminDb = M('Admin');
    }
    // 商品列表
    public function index(){
        // if(isset($_GET['search'])){
            if(isset($_GET['keyword']) && !empty($_GET['keyword'])){
                $whereSearch['goods_name'] = array('like',"%".I('keyword')."%");
                $searchInfo['keyword'] = I("keyword");
            }
        // }
        $uid = session('admin_uid');
        if($uid != 1){
            $whereSearch['uid'] = $uid;
        }
        $this->assign('searchInfo',$searchInfo);
        $count = $this->goodsDb->where($whereSearch)->count();

        $Page = new \Think\Page($count,20);
        $list = $this->goodsDb->limit($Page->firstRow.','.$Page->listRows)->where($whereSearch)->order('goods_id desc')->select();
        $show = $Page->show();
        $this->assign('page',$show);
        $this->assign('list',$list);
        $this->display("goods_list");
    }
    // 商品增加
    public function goodsAdd(){
    	if(IS_POST){
    		$data = $_POST['info'];
            $data['uid'] = session("admin_uid");
            $data['goods_ontime'] = $data['goods_update_time'] = time();
            //判断商品是否存在
            $where['goods_sn'] = $data['goods_sn'];
            // $where['_logic'] = 'OR';
            $isIn = $this->goodsDb->where($where)->find();
            if($isIn){
                $this->error('商品型号已存在');
            }
            if(!$data['goods_name'] && !$data['goods_sn']){
                $this->error('操作失败');
            }else{
                $result = $this->goodsDb->data($data)->add();
                if($result){
                    $this->success('操作成功');
                }else{
                    $this->error('操作失败');
                }
            }
    	}else{
    		$this->display("goods_add");
    	}
    }
    // 商品编辑
    public function goodsEdit(){
    	if(IS_POST){
            $data = $_POST['info'];
    		$goods_id = $where['goods_id'] = intval($_POST['goods_id']);
            $data['goods_id'] = $goods_id;
            $data['goods_update_time'] = time();            
            $this->goodsDb->where($where)->save($data);
            $this->success("操作成功");
    	}else{
    		$goods_id = $where['goods_id'] = I('goods_id');
    		$goodsInfo = $this->goodsDb->where($where)->find();
    		$this->assign($goodsInfo);
    		$this->display("goods_edit");
    	}
    }
    // 浏览商品信息
    public function goodsInfo(){
    	$where['goods_id'] = I("goods_id");
    	$goodsInfo = $this->goodsDb->where($where)->find();
		$this->assign($goodsInfo);
        layout(false);
		$this->display("goods_info");
    }
    // 会员删除操作
    public function memberDelete(){
    	$userid = $where['userid'] = I("userid");
    	$memberInfo = $this->memberDb->where($where)->find();
		if($memberInfo){
			$result = $this->memberDb->where($where)->delete();
		}
		if($result){
			$this->success("操作成功");
		}else{
			$this->error("操作失败");
		}
    }

}