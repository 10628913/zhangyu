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
        $this->orderActionDb = M("Order_action");
    }
    public function test(){
        $arr = [];
        $provinceList = M("Region")->where("level = 1 and parent_id = 0")->select();
        foreach ($provinceList as $i => $v) {
            $arr[$i]["name"] = $v["name"];
            $cityList = M("Region")->where("parent_id=".$v["id"])->select();
            foreach ($cityList as $j => $v1) {
                $arr1[$j]["name"] = $v1["name"];
                $areaList = M("Region")->where("parent_id=".$v1["id"])->select();
                foreach ($areaList as $z => $v2) {
                    $arr2[$z]["name"] = $v2["name"];
                }
                $arr1[$j]["sub"] = $arr2;
            }
            $arr[$i]["sub"] = $arr1;
        }
        $this->ajaxReturn($arr);
    }

    //获取订单列表
    public function getOrderList(){

    	$pageSize = I("pageSize") ? I("pageSize") : 20;
        $pageNum = I("pageNum") ? I("pageNum") : 1;
        $startCount = ($pageNum - 1) * $pageSize;

    	//用户id
    	$where["uid"] = $this->adminInfo["uid"];
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
					->field("goods_id,goods_name,goods_num")
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
    	$adminInfo = $this->adminInfo;
    	//一手id
    	$data["uid"] = $adminInfo["uid"];
    	//订单状态(免审为1)
    	$adminInfo["is_audit"] == 1 ? $data["order_status"] = 1 : "";
    	I("tracking_id") ? $data["tracking_id"] = I("tracking_id") : $this->httpResponse(-1,"快递id不能为空");
    	$trackingInfo = M("Tracking")->where("tracking_id ='".I("tracking_id")."'")->find();
    	if(!$trackingInfo){
    		$this->httpResponse(-1,"快递信息错误");
    	}
    	$data["tracking_id"] = $trackingInfo["tracking_id"];
    	$data["tracking_name"] = $trackingInfo["tracking_name"];

    	//订单号
    	$data["order_sn"] = create_sn();
    	//客户id
    	I("client_id") ? $data["client_id"] = I("client_id") : $this->httpResponse(-1,"客户id不能为空");
        I("address_id") ? $addressWhere["address_id"] = I("address_id") : $this->httpResponse(-1,"收货地址id不能为空");
        $clientAddressInfo = M("Client_address")->where($addressWhere)->find();
        if($clientAddressInfo){
            if($clientAddressInfo["client_id"] != I("client_id")){
                $this->httpResponse(-1,"收货地址信息与客户信息不匹配");
            }
        }else{
            $this->httpResponse(-1,"收货地址信息有误");
        }
    	//收货人
    	$clientAddressInfo["consignee"] ? $data["consignee"] = $clientAddressInfo["consignee"] : $this->httpResponse(-1,"收货人不能为空,请完善收货人信息");
    	//收货人电话
        $clientAddressInfo["mobile"] ? $data["mobile"] = $clientAddressInfo["mobile"] : $this->httpResponse(-1,"收货人电话不能为空,请完善收货人信息");
    	//下单时间
    	$data["order_addtime"] = time();
    	//省
        $clientAddressInfo["province"] ? $data["province"] = $clientAddressInfo["province"] : $this->httpResponse(-1,"省不能为空,请完善收货人信息");
    	//市
        $clientAddressInfo["city"] ? $data["city"] = $clientAddressInfo["city"] : $this->httpResponse(-1,"市不能为空,请完善收货人信息");
    	//县区
        // $clientAddressInfo["county"] ? $data["county"] = $clientAddressInfo["county"] : $this->httpResponse(-1,"县区不能为空,请完善收货人信息");
        $data["county"] = $clientAddressInfo["county"];
    	//地址
        $clientAddressInfo["address"] ? $data["address"] = $clientAddressInfo["address"] : $this->httpResponse(-1,"收货地址不能为空,请完善收货人信息");
    	//邮编
    	$data["zipcode"] = $clientAddressInfo["zipcode"];
    	//订单备注
    	$data["remark"] = I("remark");
    	$order_id = $this->orderDb->add($data);
    	if($order_id){
            if(!I("orderInfo")){
                $this->httpResponse(-1,"商品列表不能为空");
            }
    		$orderInfo = json_decode(htmlspecialchars_decode(I("orderInfo")));
    		if($orderInfo){
    			foreach ($orderInfo as $i => $v) {
	    			$orderData["order_id"] = $order_id;
		    		$orderData["goods_id"] = $v->goods_id;
		    		$orderData["goods_name"] = $v->goods_name;
		    		$orderData["goods_sn"] = $v->goods_sn;
		    		$orderData["goods_num"] = $v->goods_num;
		    		$this->orderGoodsDb->add($orderData);
	    		}

	    		//判断用户是否免审
	    		$isAudit = $adminInfo["is_audit"];
	    		if($isAudit == 1){
	    			//免审(进入预约下单流程)
	    			//构造在线下单提交信息
					$eorder = [];
					$eorder["ShipperCode"] = $trackingInfo["tracking_code"];
					$eorder["OrderCode"] = $data["order_sn"];
					$eorder["PayType"] = 1;
					$eorder["ExpType"] = 1;
					$sender = [];
					$sender["Name"] = $adminInfo["consigner"];
					$sender["Mobile"] = $adminInfo["consigner_mobile"];
					$sender["ProvinceName"] = $adminInfo["province"];
					$sender["CityName"] = $adminInfo["city"];
					$sender["ExpAreaName"] = $adminInfo["county"];
					$sender["Address"] = $adminInfo["address"];

					$receiver = [];
					$receiver["Name"] = $data["consignee"];
					$receiver["Mobile"] = $data["mobile"];
					$receiver["ProvinceName"] = $data["province"];
					$receiver["CityName"] = $data["city"];
					$receiver["ExpAreaName"] = $data["county"];
					$receiver["Address"] = $data["address"];

					$commodityOne = [];
					$commodityOne["GoodsName"] = "其他";
					$commodity = [];
					$commodity[] = $commodityOne;

					$eorder["Sender"] = $sender;
					$eorder["Receiver"] = $receiver;
					$eorder["Commodity"] = $commodity;

					$jsonParam = json_encode($eorder, JSON_UNESCAPED_UNICODE);
                    $configInfo = M("Module_extend")->field("kd_app_id,kd_app_key,kd_request_url")->find();
                    if(!$configInfo){
                        $this->httpResponse(-1,"物流配置信息有误");
                    }
                    $datas = array(
                        'EBusinessID' => $configInfo["kd_app_id"],
                        'RequestType' => '1007',
                        'RequestData' => urlencode($jsonParam) ,
                        'DataType' => '2'
                    );
                    $datas['DataSign'] = encrypt($jsonParam, $configInfo["kd_app_key"]);
                    $result = sendPost($configInfo["kd_request_url"], $datas);
                    if($result){
                        $resultInfo = json_decode($result,true);
                    }
                    if($resultInfo){
                        if($resultInfo["ResultCode"] == "100"){
                            $orderReturnInfo = $resultInfo["Order"];
                            // dump($orderReturnInfo);
                            $orderUpdate["order_id"] = $order_id;
                            $orderUpdate["tracking_num"] = $orderReturnInfo["LogisticCode"];
                            $orderUpdate["order_sort_time"] = time();
                            $orderUpdate["order_shipping_time"] = time();
                            $this->orderDb->save($orderUpdate);
                            $this->_addToOrderAction($adminInfo["uid"],$order_id,1,"提交新订单","",1);
                            $this->httpResponse(1,"订单提交成功");
                        }
                    }
	    		}else{
	    			//非免审(订单进入待审核状态)
	    			$this->_addToOrderAction($adminInfo["uid"],$order_id,1,"提交新订单,等待审核","","");
	    			$this->httpResponse(1,"订单提交成功,等待审核");
	    		}
    		}else{
    			$this->orderDb->where("order_id=".$order_id)->delete();
    			$this->httpResponse(-1,"订单错误");
    		}
    	}else{
    		$this->httpResponse(-1,"订单提交失败");
    	}
    }

    //修改订单信息
    public function changeOrderInfo(){
    	$adminInfo = $this->adminInfo;
    	$uid = $adminInfo["uid"];
    	$orderId = I("order_id");
    	$orderInfo = $this->orderDb->where("order_id=".$orderId." and uid=".$uid)->find();
    	if(!$orderInfo){
    		$this->httpResponse(-1,"订单信息获取失败");
    	}
    	//修改订单信息
		//如果订单处于已取消或已发货及以后的状态,不允许修改订单信息
		if($orderInfo["order_status"] != 0){
			$this->httpResponse(-1,"当前订单不允许修改订单信息");
		}else{
			//收货人
			I("consignee") ? $orderWhere["consignee"] = I("consignee") : "";
			//收货人电话
			I("mobile") ? $orderWhere["mobile"] = I("mobile") : "";
			//省
	    	I("province") ? $orderWhere["province"] = I("province") : "";
	    	//市
	    	I("city") ? $orderWhere["city"] = I("city") : "";
	    	//县区
	    	I("county") ? $orderWhere["county"] = I("county") : "";
	    	//地址
	    	I("address") ? $orderWhere["address"] = I("address") : "";
	    	//邮编
	    	I("zipcode") ? $orderWhere["zipcode"] = I("zipcode") : "";
		}
		if(!$orderWhere){
			$this->httpResponse(-1,"订单信息无修改");
		}
    	//订单id
    	$orderWhere["order_id"] = $orderId;
    	$orderChange = $this->orderDb->save($orderWhere);
    	if($orderChange){
    		$this->_addToOrderAction($adminInfo["uid"],$orderId,$orderInfo["order_status"],"修改收货人信息",0,"");
    		$this->httpResponse(1,"订单状态修改成功");
     	}else{
    		$this->httpResponse(-1,"订单状态修改失败");
    	}
    }
    //打包
    public function packageOrder(){
    	$adminInfo = $this->adminInfo;
    	$uid = $adminInfo["uid"];
    	$orderId = I("order_id");
    	$orderInfo = $this->orderDb->where("order_id=".$orderId." and uid=".$uid)->find();
    	//订单状态
		if($orderInfo["order_status"] == 1 || $orderInfo["order_status"] > 1){
			$this->httpResponse(-1,"订单打包失败");
		}
		$where["order_id"] = $orderId;
		$where["order_status"] = 1;
		$where["order_sort_time"] = time();
		if($this->orderDb->save($where)){
			$this->httpResponse(1,"订单打包成功");
			$this->_addToOrderAction($uid,$orderId,1,"打包成功","","");
		}else{
			$this->httpResponse(-1,"订单状态修改失败");
		}
    }
    //发货
    public function delivery(){
    	$adminInfo = $this->adminInfo;
    	$uid = $adminInfo["uid"];
    	I("order_id") ? $orderId = I("order_id") : $this->httpResponse("订单id不能为空");
    	$orderInfo = $this->orderDb->where("order_id=".$orderId." and uid=".$uid)->find();
    	if($orderInfo){
    		//订单状态不为已打包
    		if($orderInfo["order_status"] != 1){
    			$this->httpResponse(-1,"发货失败,订单状态异常");
    		}


    	}else{
    		$this->httpResponse(-1,"订单不存在");
    	}
    }
    public function getTrackingList(){
        $trackingList = M("Tracking")->where("is_display = 1")->field("is_display",true)->select();
        if($trackingList){
            $this->httpResponse(1,"物流列表拉取成功",$trackingList);
        }
    }

    //订单记录
    protected function _addToOrderAction($uid,$orderId,$orderStatus,$actionNote = "",$shippingStatus = "",$isAudit = ""){
    	$data["uid"] = $uid;
    	$data["order_id"] = $orderId;
    	$data["log_time"] = time();
    	$data["order_status"] = $orderStatus;
    	$data["action_note"] = $actionNote;
    	$shippingStatus != "" ? $data["shipping_status"] = $shippingStatus : "";
    	$isAudit != "" ? $data["is_audit"] = $isAudit : "";
    	$this->orderActionDb->add($data);
    }
}