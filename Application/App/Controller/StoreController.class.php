<?php
namespace App\Controller;
use Think\Controller;
class StoreController extends Controller {
    public function __construct(){
        parent::__construct();
        $this->memberDb = M('Member');
        $this->storeDb = M('Store');
        $this->storeClassifyDb = M('Store_classify');
        $this->memberLocationDb = M('Member_location');
        $this->goodsDb = M('Goods');
        $this->goodsClassifyDb = M('Goods_classify');
        $this->storeOrderDb = M('Store_order');
        $this->storeCommentDb = M('Store_comment');
        $this->storeDataDb = M('Store_data');
    }
    // 获取商家详情
    public function getStoreData(){
        if(IS_POST){
            $storeid = intval($_POST['storeid']);
            $where['id'] = $storeid;
            $info = $this->storeDb
            ->where($where)
            ->join('dm_store_data ON dm_store.id = dm_store_data.storeid')
            ->getField('id,store_name,description,content,m_content,logo,classify_id,grade_id,area_id,area_name,lat,lon,address,mobile,recommend,type,dm_store_data.rest_type,dm_store_data.rest_open_time,dm_store_data.rest_closed_time,dm_store_data.contact_mobile,dm_store_data.min_courier_price,dm_store_data.store_status');
            // $this->ajaxReturn($info);
            // exit;
            // $info = $this->storeDb->where($where)->getField('id,store_name,description,content,m_content,logo,classify_id,grade_id,area_id,area_name,lat,lon,address,mobile,recommend,type');

            // 获取商家订单数
            $storeData = $info[$storeid];
            // 营业状态
            if($storeData['rest_type'] == 1){//非24小时制
                $nowTime = date("H:i",time());
                if($nowTime >= $storeData['rest_open_time'] && $nowTime <= $storeData['rest_closed_time']){
                    $storeData['open_status'] = 1;
                }else{
                    $storeData['open_status'] = 0;
                }
            }else{
                $storeData['open_status'] = 1;
            }
            $whereOrder['storeid'] = $storeid;
            $storeData['order_count'] = $this->storeOrderDb->where($whereOrder)->count('id');
            if($info){
                $data['code'] = 200;
                $data['status'] = 'success';
                $data['data'] = $storeData;
                $this->ajaxReturn($data);
            }
        }
    }
    // 获取商家列表
    public function getStoreLists(){
    	if(IS_POST){
            $userid = intval($_POST['userid']);
            if(!$userid) exit;
            // 获取用户坐标
            $whereLocation['userid'] = $userid;
            $memberLocationData = $this->memberLocationDb->where($whereLocation)->find();
            $orderBy = 'ACOS(SIN(('.$memberLocationData['lat'].' * 3.1415) / 180 ) *SIN((lat * 3.1415) / 180 ) +COS(('.$memberLocationData['lat'].' * 3.1415) / 180 ) * COS((lat * 3.1415) / 180 ) *COS(('.$memberLocationData['lon'].'* 3.1415) / 180 - (lon * 3.1415) / 180 ) ) * 6380 asc';
		    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
		    $start = ($page-1)*10;
		    $limit = $start.',10';
            if(isset($_POST['classify_id'])){
                $where['classify_id'] = intval($_POST['classify_id']);
            }
            $where['status'] = 99;
            if(isset($_POST['classify_id'])){
                $where['classify_id'] = intval($_POST['classify_id']);
            }
            $where['dm_store_data.store_status'] = 1;
            $list = $this->storeDb
            ->where($where)->limit($limit)->where($where)->order($orderBy)
            ->join('dm_store_data ON dm_store.id = dm_store_data.storeid')
            ->getField('id,store_name,description,content,m_content,logo,classify_id,grade_id,area_id,area_name,lat,lon,address,mobile,recommend,type,dm_store_data.rest_type,dm_store_data.rest_open_time,dm_store_data.rest_closed_time,dm_store_data.contact_mobile,dm_store_data.min_courier_price,dm_store_data.store_status');
            foreach ($list as $key => $v) {
                // 营业状态
                if($v['rest_type'] == 1){//非24小时制
                    $nowTime = date("H:i",time());
                    if($nowTime >= $v['rest_open_time'] && $nowTime <= $v['rest_closed_time']){
                        $list[$key]['open_status'] = 1;
                    }else{
                        $list[$key]['open_status'] = 0;
                    }
                }else{
                    $list[$key]['open_status'] = 1;
                }
                // 距离计算
                if($v['lat'] && $v['lon']){
                    $distance = getDistance($memberLocationData['lat'],$memberLocationData['lon'],$v['lat'],$v['lon']);
                    if($distance < 1000){
                        $list[$key]['distance'] =  $distance."米";
                    }else{
                        $list[$key]['distance'] = round($distance/1000,2)."公里";
                    }
                }
                // 计算销售数量
                $whereOrder['storeid'] = $v['id'];
                $list[$key]['order_count'] = $this->storeOrderDb->where($whereOrder)->count('id');
                // 计算星数
                $whereComment['storeid'] = $v['id'];
                $stars = $this->storeCommentDb->where($whereComment)->avg('shop_stars');
                $list[$key]['stars'] = round($stars,1);
                $list[$key]['stars_percent'] = (round($stars,1)/5)*100;
            }
		    if($list){
		    	$data['status'] = "success";
		    	$data['code'] = 200;
		    	$data['data'] = $list;
                $data['pageSize'] = 10;
		        $this->ajaxReturn($data);
		    }else{
		        $data = array();
		        $data['code'] = 0;
		        $this->ajaxReturn($data);
		    }
		}
    }

    // 获取商家分类列表
    public function getStoreClassifyLists(){
        if(IS_POST){
            $where['status'] = 1;
            $where['parent_id']  = 0;
            $list = $this->storeClassifyDb->where($where)->order('sort asc, id desc')->limit(8)->select();
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

    // 获取商品列表
    public function getGoodsLists(){
        // echo 1;
        if(IS_POST){
            $userid = intval($_POST['userid']);
            if(!$userid) exit;
            $storeid = intval($_POST['storeid']);
            $where['storeid'] = $storeid;
            $where['status'] = 1;
            $where['parent_id'] = 0;
            $list = $this->goodsClassifyDb->where($where)->order('sort asc,id desc')->select();
             if($list){
                 foreach ($list as $key => $v) {
                    $whereGoods['classify_id'] = $v['id'];
                    $whereGoods['status'] = 1;
                    $data[$key]['classify_id'] = $v['id'];
                    $data[$key]['classify_name'] = $v['classify_name'];
                    $data[$key]['classify_recommend_type'] = $v['recommend_type'];
                    $data[$key]['goodsData'] = $this->goodsDb->where($whereGoods)->order('id desc')->select();
                 }
                 $returnDdata['status'] = "success";
                 $returnDdata['code'] = "200";
                 $returnDdata['data'] = $data;
                 $this->ajaxReturn($returnDdata);
             }else{
                 $returnDdata['code'] = 0;
                    $this->ajaxReturn($returnDdata);
             }
        }
    }
    // 获取指定商家商品分类
    public function getGoodsClassifyLists(){
        if(IS_POST){
            $userid = intval($_POST['userid']);
            if(!$userid) exit;
            $storeid = intval($_POST['storeid']);
            $where['storeid'] = $storeid;
            $where['status'] = 1;
            $where['parent_id'] = 0;
            $list = $this->goodsClassifyDb->where($where)->order('sort asc,id desc')->select();
            if($list){
                $data['status'] = "success";
                $data['data'] = $list;
                $this->ajaxReturn($data);
            }else{
                $data['code'] = 0;
                $this->ajaxReturn($data);
            }
        }
    }
    // 根据商品关键词搜索商家
    public function getStoreListsByKeyword(){
        if(IS_POST){
            $userid = intval($_POST['userid']);
            $keyword = $_POST['keyword'];
            if(!$keyword || empty($keyword)){
                $userid = intval($_POST['userid']);
                if(!$userid) exit;
                // 获取用户坐标
                $whereLocation['userid'] = $userid;
                $memberLocationData = $this->memberLocationDb->where($whereLocation)->find();
                $orderBy = 'ACOS(SIN(('.$memberLocationData['lat'].' * 3.1415) / 180 ) *SIN((lat * 3.1415) / 180 ) +COS(('.$memberLocationData['lat'].' * 3.1415) / 180 ) * COS((lat * 3.1415) / 180 ) *COS(('.$memberLocationData['lon'].'* 3.1415) / 180 - (lon * 3.1415) / 180 ) ) * 6380 asc';
            }else{
                $orderBy = "id desc";
            }
            $Model = new \Think\Model();
            $list = $Model->query("select * from dm_store where id in (select distinct storeid from dm_goods where title like '%$keyword%') order by '$orderBy'");
            foreach ($list as $key => $v) {
                if($v['lat'] && $v['lon']){
                    $distance = getDistance($memberLocationData['lat'],$memberLocationData['lon'],$v['lat'],$v['lon']);
                    if($distance < 1000){
                        $list[$key]['distance'] =  $distance."米";
                    }else{
                        $list[$key]['distance'] = round($distance/1000,2)."公里";
                    }
                }
                $whereStore['storeid'] = $v['id'];
                $list[$key]['min_courier_price'] = $this->storeDataDb->where($whereStore)->getField("min_courier_price");
                // 计算销售数量
                $whereOrder['storeid'] = $v['id'];
                $list[$key]['order_count'] = $this->storeOrderDb->where($whereOrder)->count('id');
                // 计算星数
                $whereComment['storeid'] = $v['id'];
                $stars = $this->storeCommentDb->where($whereComment)->avg('shop_stars');
                $list[$key]['stars'] = round($stars,1);
                $list[$key]['stars_percent'] = (round($stars,1)/5)*100;
            }
            if($list){
                $data['status'] = "success";
                $data['data'] = $list;
                $this->ajaxReturn($data);
            }else{
                $data['code'] = 0;
                $this->ajaxReturn($data);
            }
        }
    }
    // 获取商家点评
    public function getStoreCommentList(){
        if(IS_POST){
            $storeid = intval($_POST['storeid']);
            $userid = intval($_POST['userid']);
            $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
            $start = ($page-1)*10;
            $limit = $start.',10';
            $where['storeid'] = $storeid;
            $commentData = $this->storeCommentDb->where($where)->limit($limit)->order('id desc')->select();
            foreach ($commentData as $key => $v) {
                $commentData[$key]['addtime'] = mdate($v['addtime']);
                $whereMember['userid'] = $v['userid'];
                $memberInfo = $this->memberDb->where($whereMember)->getField("userid,nickname,avatar");
                $commentData[$key]['user_nickname'] = $memberInfo[$v['userid']]['nickname'];
                $commentData[$key]['user_avatar'] = $memberInfo[$v['userid']]['avatar'];
            }
            if($commentData){
                $returnData['status'] = "success";
                $returnData['code'] = 200;
                $returnData['pageSize'] = 10;
                $returnData['data'] = $commentData;
                $this->ajaxReturn($returnData);
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

}