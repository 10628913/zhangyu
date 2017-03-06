<?php
namespace Member\Controller;
use Think\Controller;
class GoodsController extends Controller {
    public function __construct(){
        parent::__construct();
        $this->goodsDb = M('Goods');
        $this->goodsClassifyDb = M('Goods_classify');
        $this->attachmentDb = M('Attachment');
        $this->goodsSpecDb = M('Goods_spec');
    }
    public function index(){
    	echo 1;
    }
    // 商品发布
    public function goodsAdd(){
    	if(IS_POST){

            if(I('step') == 'two' && I('classifyId')){
                $classifyId = I('classifyId');
                echo $classifyId;
            }
    	}else{
    		if(I('step') == 'one' || !I('step')){
    			$this->display('Member/Goods/goods_add_one');
    		}else if(I('step') == 'two' && I('classifyId')){
                $editor = new \Org\Editor\Editor;
                $this->assign('editor',$editor);
                // 根据ID获取产品规格
                $whereSpec['classify_id'] = I('classifyId');
                $whereSpec['status'] = 1;
                $specList = $this->goodsSpecDb->where($where)->order('sort asc,id desc')->select();
                $this->assign('specList',$specList);
                $this->display('Member/Goods/goods_add_two');
    		}
    	}
    }
    // ajax获取分类接口
    public function getGoodsClassify(){
    	if(IS_AJAX){
    		$parentId = I('parentId') ? I('parentId') : "0";
    		$where['parent_id'] = $whereParent['id'] = $parentId;
    		$where['status'] = 1;
    		$list = $this->goodsClassifyDb->where($where)->select();
    		$this->ajaxReturn($list);
    	}
    }
    // ajax获取相册图片接口
    public function getAlbumImages(){
        if(IS_AJAX){
            $where['userid'] = session("userid");
            $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
            $start = ($page-1)*10;
            $limit = $start.',10';
            $list = $this->attachmentDb->where($where)->limit($limit)->order('id desc')->select();
            foreach ($list as $key => $v) {
                $list[$key]['thumb'] = reduce($v['url'],200,200);
            }

            $this->ajaxReturn($list);
        }
    }
}