<?php
namespace App\Controller;
use Think\Controller;
class OrderController extends AppController {
    function _initialize() {
        parent::_initialize();
        parent::requestMethodValidate(REQUEST_METHOD);
        $this->adminInfo = parent::validateAdmin(I("token"));
    }
    public function __construct(){
        parent::__construct();
        $this->orderDb = M("Order");
        $this->orderGoodsDb = M("Order_goods");
    }

    //获取订单列表
    public function getOrderList(){

    	$pageSize = I("pageSize") ? I("pageSize") : 20;
        $pageNum = I("pageNum") ? I("pageNum") : 1;
        $startCount = ($pageNum - 1) * $pageSize;

    	//用户id
    	$where["uid"] = $this->userInfo["uid"];
    	//订单状态
		I("order_status") ? $where["order_status"] = I("order_status") : "";
		//客户id
		I("client_id") ? $where["client_id"] = I("client_id") : "";

		$orderList = $this->orderDb
			->where($where)
			->field("order_id,order_sn,order_status,consignee,mobile,order_addtime,province,city,county,district,town,address,remark")
			->order("order_addtime desc")
			->limit($startCount,$pageSize)
			->select();
		if($orderList){
			foreach ($orderList as $i => $v) {
				$orderWhere["order_id"] = $v["order_id"];
				$goodsList = $this->orderGoodsDb
					->where($orderWhere)
					->field("goods_name,goods_num")
					->select();
				if($goodsList){
					$orderList[$i]["child"] = $goodsList;
				}
			}
			$this->httpResponse(1,"订单信息获取成功",$orderList);
		}else{
			$msg = $pageNum == 1 ? "暂无订单" : "已无更多订单";
			$this->httpResponse(-1,$msg);
		}
    }


    //生成订单
    public function createOrder(){
    	//一手id
    	$data["uid"] = $userInfo["uid"];
    	//订单号
    	$data["order_sn"] = create_sn();
    	//客户id
    	$data["client_id"] = I("client_id");
    	//收货人
    	$data["consignee"] = I("consignee");
    	//收货人电话
    	$data["mobile"] = I("mobile");
    	//下单时间
    	$data["order_addtime"] = time();
    	//省
    	$data["province"] = I("province");
    	//市
    	$data["city"] = I("city");
    	//县区
    	$data["county"] = I("county");
    	//地址
    	$data["address"] = I("address");
    	//邮编
    	$data["zipcode"] = I("zipcode");
    	//备注
    	$data["remark"] = I("remark");

    	$order_id = $this->orderDb->add($data);
    	if($order_id){

    	}else{

    	}
    	$this->ajaxReturn($ret);
    }
}