<?php
namespace App\Controller;
use Think\Controller;
class CourierController extends Controller {
    public function _initialize() {
        vendor('Ucpaas.Ucpaas');
        vendor('RongCloud.ServerAPI');
    }
    public function __construct(){
        parent::__construct();
        $this->deliverOrderDb = M('Deliver_order');
        $this->storeDb = M("Store");
        $this->memberLocationDb = M('Member_location');
    }

    /**
    *修改订单状态
    */
    public function changeDeliverOrderStatus(){
        if(IS_POST){
            // $orderDb = M("Order");
            $orderid = $where['id'] = intval($_POST['orderid']);
            $userid = $where['courier_id'] = intval($_POST['userid']);
            $status = intval($_POST['status']);
            $updateData['status'] = $status;
            $updateData['update_time'] = time();
            if(isset($_POST['send_image'])){
                $updateData['send_image'] = $_POST['send_image'];
            }
            if(isset($_POST['receive_image'])){
                $updateData['receive_image'] = $_POST['receive_image'];
            }
            $result = $this->deliverOrderDb->where($where)->save($updateData);
            if($result){
                courierManagerNoticeMember($orderid);
                $returnData['status'] = "success";
                $returnData['code'] = 200;
                $this->ajaxReturn($returnData);
            }
        }
    }
    /**
    *获取时间范围内未抢订单
    */
    public function getOrderListNoGrab(){
        if(IS_POST){
            // $orderDb = M("Order");
            // $storeDb = M("Seller");
            $nowTime = time();
            $_time = $nowTime-30000;
            $where = "status = 1 AND addtime > ".$_time;
            $orderInfo = $this->deliverOrderDb->where($where)->order('id asc')->limit(10)->select();
            foreach ($orderInfo as $key => $v) {

                $orderInfo[$key]['date'] = date("H:i",$v['addtime']);
                $orderInfo[$key]['addtime'] = date("m-d H:i",$v['addtime']);
                $orderInfo[$key]['get_goods_time'] = date("m-d H:i",$v['get_goods_time']);
                $sellerInfo = $this->storeDb->where("id=".$v['seller_id'])->getField("id,title,mobile,province,city,area,address");
                $orderInfo[$key]['seller_title'] = $sellerInfo[1]['title'];
                $orderInfo[$key]['seller_mobile'] = $sellerInfo[1]['mobile'];
                $orderInfo[$key]['seller_address'] = $sellerInfo[1]['province'].$sellerInfo[1]['city'].$sellerInfo[1]['area'].$sellerInfo[1]['address'];
            }
            if($orderInfo){
                $this->ajaxReturn($orderInfo);
            }
        }
    }
    /**
    *获取帮我送订单列表
    */
    public function getDeliverOrderList(){
        if(IS_POST){
            // $orderDb = M("Order");
            $userid = intval($_POST['userid']);
            $status = intval($_POST['status']);
            $step = intval($_POST['step']);
            $where['courier_id'] = $userid;
            $where['status'] = $status;
            $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
            $pageSize = 10;
            $start = ($page-1)*$pageSize;
            $limit = $start.','.$pageSize;
            $orderData = $this->deliverOrderDb->where($where)->limit($limit)->order('update_time desc')->select();
            if($orderData){
                foreach ($orderData as $key => $v) {
                    $_time = time();
                    if($v['get_goods_time'] - $_time > 0){
                        $orderData[$key]['wait_time'] = floor(($v['get_goods_time'] - $_time)/60);
                    }else{
                        $orderData[$key]['wait_time'] = -1;
                    }
                    $orderData[$key]['distance'] = round($v['distance']/1000,2);
                    $orderData[$key]['addtime'] = date("m-d H:i",$v['addtime']);
                    $orderData[$key]['get_goods_time'] = date("m-d H:i",$v['get_goods_time']);
                    if($v['type']=='1'){//普通订单

                    }else if($v['type']=='2'){//商家订单
                    }
                }
                $returnData['pageSize'] = $pageSize;
                $returnData['status'] = "success";
                $returnData['code'] = 200;
                $returnData['data'] = $orderData;
                $this->ajaxReturn($returnData);
            }
        }
    }
    public function getDeliverOrderInfo(){
    	if(IS_POST){
    		$orderid = intval($_POST['orderid']);
    		$where['id'] = $orderid;
    		$orderInfo = $this->deliverOrderDb->where($where)->find();
    		if($orderInfo){
                $_time = time();
                if($orderInfo['get_goods_time'] - $_time > 0){
                    $orderInfo['wait_time'] = floor(($orderInfo['get_goods_time'] - $_time)/60);
                }else{
                    $orderInfo['wait_time'] = -1;
                }
                $orderInfo['distance'] = round($orderInfo['distance']/1000,2);
                $returnData['status'] = "success";
                $returnData['code'] = 200;
                $returnData['data'] = $orderInfo;
    			$this->ajaxReturn($returnData);
    		}
    	}
    }
}