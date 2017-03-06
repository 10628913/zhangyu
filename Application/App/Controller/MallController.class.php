<?php
namespace App\Controller;
use Think\Controller;
class StoreController extends Controller {
    public function __construct(){
        parent::__construct();
        // $this->sellerDb = M('Seller');
        // $this->sellerDataDb = M('Seller_data');
        // $this->goodsDb = M('Goods');
        // $this->classifyDb = M('Goods_classify');
        // $this->shopCommentDb = M('Shop_comment');
        // $this->memberDb = M('Member');

        $this->storeDb = M('Store');
    }
    // 获取商家列表
    public function getStoreLists(){
    	// echo 1;
    	if(IS_POST){
		    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
		    $start = ($page-1)*10;
		    $limit = $start.',10';
		    $list = $this->storeDb->where($where)->limit($limit)->order('id desc')->select();
		    if($list){
		    	$data['status'] = "success";
		    	$data['code'] = 200;
		    	$data['data'] = $list;
		        $this->ajaxReturn($data);
		    }else{
		        $data = array();
		        $data['code'] = 0;
		        $this->ajaxReturn($data);
		    }
		}
    }
    // // 获取商家分类
    // public function getSellerGoodsClassify(){
    // 	if(IS_POST){
    // 		$sellerId = intval($_POST['sellerId']);
    // 		$where['seller_id'] = $sellerId;
    // 		$list = $this->classifyDb->where($where)->order('listorder desc,id desc')->select();
    // 		if($list){
    // 			$data['status'] = "success";
    // 			$data['data'] = $list;
    // 			$this->ajaxReturn($data);
    // 		}else{
    // 			$data['code'] = 0;
		  //       $this->ajaxReturn($data);
    // 		}
    // 	}
    // }
    // // 根据商家获取商品
    // public function getGoodsList(){
    // 	if(IS_POST){
    // 		$sellerId = intval($_POST['sellerId']);
    // 		$where['seller_id'] = $sellerId;
    // 		$list = $this->classifyDb->where($where)->order('listorder desc,id desc')->select();
    // 		if($list){
    // 			foreach ($list as $key => $v) {
    // 				$classifyWhere['classify_id'] = $v['id'];
    // 				$data[$key]['classify_id'] = $v['id'];
    // 				$data[$key]['classify_title'] = $v['title'];
    // 				$data[$key]['goodsData'] = $this->goodsDb->where($classifyWhere)->order('id desc')->select();
    // 			}
    // 			$returnDdata['status'] = "success";
    // 			$returnDdata['code'] = "200";
    // 			$returnDdata['data'] = $data;
    // 			$this->ajaxReturn($returnDdata);
    // 		}else{
    // 			$returnDdata['code'] = 0;
		  //       $this->ajaxReturn($returnDdata);
    // 		}
    // 	}
    // }
    // // 获取商家详情
    // public function getSellerData(){
    // 	if(IS_POST){
    // 		$sellerId = intval($_POST['sellerId']);
    // 		$where['seller_id'] = $sellerId;
    // 		$info = $this->sellerDataDb->where($where)->find();
    // 		if($info){
    // 			$data['code'] = 200;
    // 			$data['status'] = 'success';
    // 			$data['data'] = $info;
    // 			$this->ajaxReturn($data);
    // 		}
    // 	}
    // }
    // // 获取商家点评
    // public function getSellerCommentList(){
    //     if(IS_POST){
    //         $sellerId = intval($_POST['sellerId']);
    //         $userid = intval($_POST['userid']);
    //         $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    //         $count = isset($_POST['count']) ? intval($_POST['count']) : 10;
    //         $start = ($page-1)*$count;
    //         $limit = $start.','.$count;
    //         $where['userid'] = $userid;
    //         $where['status'] = array('NEQ',"-2");
    //         $commentData = $this->shopCommentDb->where($where)->limit($limit)->order('id desc')->select();
    //         foreach ($commentData as $key => $v) {
    //             $commentData[$key]['addtime'] = date("m-d H:i");
    //             $whereMember['userid'] = $v['userid'];
    //             $memberInfo = $this->memberDb->field("userid,nickname,avatar")->where($whereMember)->find();
    //             $commentData[$key]['user_nickname'] = $memberInfo['nickname'];
    //             $commentData[$key]['user_avatar'] = $memberInfo['avatar'];
    //         }
    //         if($commentData){
    //             $returnData['status'] = "success";
    //             $returnData['code'] = 200;
    //             $returnData['data'] = $commentData;
    //             $this->ajaxReturn($returnData);
    //         }
    //     }
    // }
}