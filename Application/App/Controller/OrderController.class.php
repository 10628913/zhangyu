<?php
/**
* 订单类操作，包含订单的写入、支付、查询、删除、抢单
* 订单点评
*/
namespace App\Controller;
use Think\Controller;
class OrderController extends Controller {
    public function __construct(){
        parent::__construct();
        $this->deliverOrderDb = M('Deliver_order');
        $this->storeOrderDb = M('Store_order');
        $this->storeOrderGoodsDb = M('Store_order_goods');
        $this->storeCommentDb = M('Store_comment');
        $this->storeDb = M('Store');
        $this->addressDb = M('Member_address');
        $this->priceSettingDb = M('Price_setting');
        $this->courierDb = M('Courier');
        $this->goodsDb = M('Goods');
        $this->orderRelationDb = M('Order_relation');
        $this->memberDb = M('Member');
    }
    public function _initialize() {
        vendor('Pingpp.Init');
        vendor('JPush.JPush');
    }
    // 商城类订单写入
    public function storeOrderCreate(){
        if(IS_POST){
            $data['userid'] = intval($_POST['userid']);
            $data['storeid'] = intval($_POST['storeid']);
            $data['count'] = $_POST['goodsCount'];
            $data['goods_price'] = $_POST['goodsPriceTotal'];
            $data['pay_price'] = $_POST['payPrice'];
            $data['courier_price'] = $_POST['courierPrice'];
            $data['total_price'] = $_POST['goodsPriceTotal'] + $_POST['courierPrice'];
            $data['distance'] = $_POST['distance'];
            $data['trade_sn'] = create_sn();
            $data['status'] = 1;
            $data['ip'] = ip();
            $data['addtime'] = $data['update_time'] = time();
            $data['remarks'] = $_POST['remarks'];

            // 获取商家标题
            $whereStore['id'] = $data['storeid'];
            $data['store_name'] = $this->storeDb->where($whereStore)->getField("store_name");

            // 获取地址信息
            $data['address_id'] = intval($_POST['addressId']);
            $whereAddress['id'] = $data['address_id'];
            $whereAddress['userid'] = $data['userid'];
            $addressInfo = $this->addressDb->where($whereAddress)->find();
            $data['receive_name'] = $addressInfo['name'];
            $data['receive_mobile'] = $addressInfo['mobile'];
            $data['receive_address'] = $addressInfo['address'];
            $data['receive_address_detail'] = $addressInfo['address_detail'];
            $data['receive_lat'] = $addressInfo['lat'];
            $data['receive_lon'] = $addressInfo['lon'];

            $result = $this->storeOrderDb->add($data);
            if($result){
                $goodsDataList = json_decode($_POST['goodsData']);
                foreach ($goodsDataList as $key => $v) {
                    $goodsData['userid'] = $data['userid'];
                    $goodsData['order_id'] = $result;
                    $goodsData['goods_id'] = $v->goodsId;
                    $goodsData['goods_title'] = $v->goodsTitle;
                    $goodsData['count'] = $v->count;
                    $goodsData['goods_price'] = $v->goodsPrice;
                    $goodsData['total_price'] = $v->count*$v->goodsPrice;
                    $this->storeOrderGoodsDb->add($goodsData);
                }
                $returnData['status'] = "success";
                $returnData['code'] = "200";
                $returnData['id'] = $result;
                $this->ajaxReturn($returnData);
            }


        }
    }
    /**
    * 配送员获取商城订单列表
    */
    public function courierGetStoreOrderList(){
        if(IS_POST){
            // $orderDb = M("Order");
            $userid = intval($_POST['userid']);
            $status = intval($_POST['status']);
            $step = intval($_POST['step']);
            $where['courier_id'] = $userid;
            $where['status'] = $status;
            $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
            $count = 10;
            $start = ($page-1)*$count;
            $limit = $start.','.$count;
            $orderData = $this->storeOrderDb->where($where)->limit($limit)->order('id desc')->select();
            if($orderData){
                foreach ($orderData as $key => $v) {
                    $orderData[$key]['distance'] = round($v['distance']/1000,2);
                    $whereStore['id'] = $v['storeid'];
                    $orderData[$key]['addtime'] = date("Y-m-d H:i:s",$v['addtime']);
                    $storeData = $this->storeDb->where($whereStore)->getField("id,logo,lat,lon,address,area_name,mobile");
                    $orderData[$key]['store_lat'] = $storeData[$v['storeid']]['lat'];
                    $orderData[$key]['store_lon'] = $storeData[$v['storeid']]['lon'];
                    $orderData[$key]['store_area_name'] = $storeData[$v['storeid']]['area_name'];
                    $orderData[$key]['store_address'] = $storeData[$v['storeid']]['address'];
                    $orderData[$key]['store_mobile'] = $storeData[$v['storeid']]['mobile'];
                    $orderData[$key]['store_logo'] = thumb($storeData[$v['storeid']]['logo'],200,200);
                    $whereGoods['order_id'] = $v['id'];
                    $orderData[$key]['goodsData'] =$this->storeOrderGoodsDb->where($whereGoods)->select();
                }

                $returnData['pageSize'] = 10;
                $returnData['status'] = "success";
                $returnData['code'] = 200;
                $returnData['data'] = $orderData;
                $this->ajaxReturn($returnData);
            }
        }
    }
    // 获取商城订单
    public function getStoreOrderList(){
        if(IS_POST){
            $userid = intval($_POST['userid']);
            $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
            $count = isset($_POST['count']) ? intval($_POST['count']) : 10;
            $start = ($page-1)*$count;
            $limit = $start.','.$count;
            $where['userid'] = $userid;
            $where['status'] = array('NEQ',"-2");
            $orderData = $this->storeOrderDb->where($where)->limit($limit)->order('id desc')->select();
            if($orderData){
                foreach ($orderData as $key => $v) {
                    $orderData[$key]['distance'] = round($v['distance']/1000,2);
                    $whereStore['id'] = $v['storeid'];
                    $orderData[$key]['addtime'] = date("Y-m-d H:i:s",$v['addtime']);
                    $store_logo = $this->storeDb->where($whereStore)->getField("logo");
                    $orderData[$key]['store_logo'] = thumb($store_logo,200,200);
                    $whereGoods['order_id'] = $v['id'];
                    $orderData[$key]['goodsData'] =$this->storeOrderGoodsDb->where($whereGoods)->select();
                }

                $returnData['pageSize'] = 10;
                $returnData['status'] = "success";
                $returnData['code'] = 200;
                $returnData['data'] = $orderData;
                $this->ajaxReturn($returnData);
            }
        }
    }
    // 商城订单详情
    public function getStoreOrderInfo(){
        if(IS_POST){
            // $userid = $where['userid'] = intval($_POST['userid']);
            $orderid = $where['id'] = intval($_POST['orderid']);
            $orderInfo = $this->storeOrderDb->where($where)->find();
            if($orderInfo){
                $orderInfo['addtime'] = date("Y-m-d H:i:s");
                // 商家基本信息
                $whereStore['id'] = $orderInfo['storeid'];
                $storeInfo = $this->storeDb->where($whereStore)->getField("id,store_name,description,logo,area_name,address,lat,lon,mobile");
                $returnData['storeData'] = $storeInfo[$orderInfo['storeid']];
                // 商品信息
                $whereGoods['order_id'] = $orderInfo['id'];
                $goodsInfo = $this->storeOrderGoodsDb->where($whereGoods)->select();
                $returnData['status'] = "success";
                $returnData['code'] = 200;
                $returnData['data'] = $orderInfo;
                $returnData['goodsData'] = $goodsInfo;
                $this->ajaxReturn($returnData);
            }
        }
    }
    // 商城订单删除、关闭
    public function storeOrderDelete(){
        if(IS_POST){
            $userid = $where['userid'] = intval($_POST['userid']);
            $orderid = $where['id'] = intval($_POST['orderid']);
            $orderInfo = $this->storeOrderDb->where($where)->find();
            if($orderInfo){
                $this->storeOrderDb->where($where)->setField("status",'-2');
                $returnData['status'] = "success";
                $returnData['code'] = 200;
                $this->ajaxReturn($returnData);
            }
        }
    }


    // 商城订单评论写入
    public function storeOrderCommentInsert(){
        if(IS_POST){
            $data['userid'] = intval($_POST['userid']);
            $data['order_id'] = $orderid = intval($_POST['order_id']);
            $data['shop_stars'] = intval($_POST['shop_stars']);
            $data['content'] = $_POST['content'];
            $data['deliver_time'] = $_POST['deliver_time'];
            $whereOrder['id'] = $orderid;
            $data['storeid'] = $this->storeOrderDb->where($whereOrder)->getField("storeid");
            $data['addtime'] = time();
            $result = $this->storeCommentDb->add($data);
            if($result){
                // 更新订单点评状态
                $this->storeOrderDb->where($whereOrder)->setField("comment_status",1);
                $returnData['status'] = "success";
                $returnData['code'] = 200;
                $returnData['id'] = $result;
                $this->ajaxReturn($returnData);
            }
        }
    }
    /**
    *商城-配送员修改订单状态
    */
    public function courierChangeStoreOrderStatus(){
        if(IS_POST){
            // $orderDb = M("Order");
            $orderid = $where['id'] = intval($_POST['orderid']);
            $userid = $where['courier_id'] = intval($_POST['userid']);
            $status = intval($_POST['status']);
            $updateData['status'] = $status;
            $updateData['update_time'] = time();
            $result = $this->storeOrderDb->where($where)->save($updateData);
            if($result){
                // 确认收货后配送员获得收益
                if($status == 6){
                    // 获取订单信息
                    $whereOrder['id'] = $orderid;
                    $orderInfo = $this->storeOrderDb->where($whereOrder)->find();
                    // 配送员写入资产动态表
                    courierAssetDynamicInsert("商城订单",$userid,$orderid,$orderInfo['courier_price'],1,1,2);
                    // 商家写入资产动态表
                    storeAssetDynamicInsert("商城订单",$orderid,1,1);
                    // 配送员获得收益
                    $whereMember['userid'] = $userid;
                    $this->memberDb->where($whereMember)->setInc("amount",$orderInfo['courier_price']);
                    // 商家获得收益
                    $whereStore['id'] = $orderInfo['storeid'];
                    $store_userid = $this->storeDb->where($whereStore)->getField("userid");
                    $whereMember['userid'] = $store_userid;
                    // 收益金额为订单支付金额-配送金额
                    $this->memberDb->where($whereMember)->setInc("amount",($orderInfo['pay_price']-$orderInfo['courier_price']));
                }
                // 推送消息给商家端
                _pcPushToStore($orderid);
                // 通知用户
                storeManagerNoticeMember($orderid);
                $returnData['status'] = "success";
                $returnData['code'] = 200;
                $this->ajaxReturn($returnData);
            }
        }
    }
    /**
    *配送员抢单-商城
    */
    public function courierGrabStoreOrder(){
        if(IS_POST){
            $orderid = intval($_POST['orderid']);
            $courier_id = intval($_POST['userid']); //当前用户id为配送员的id
            $where['id'] = $orderid;
            $orderInfo = $this->storeOrderDb->where($where)->find();
            if(!$orderInfo) return;
            if($orderInfo['status'] >= 4){
                $returnData['msg'] = "订单已被抢";
                $returnData['code'] = "1001";
                $this->ajaxReturn($returnData);
            }else{
                $returnData['msg'] = "抢单成功";
                $returnData['code'] = "200";
                $returnData['orderid'] = $orderid;
                $updateData['courier_id'] = $courier_id;
                $updateData['update_time'] = $updateData['grap_time'] = time();
                $updateData['status'] = 4;//将订单状态改为待取货
                $this->storeOrderDb->where($where)->save($updateData);
                // courierManagerNoticeMember($orderid); //通知商家
                $this->ajaxReturn($returnData);
                // 通知商家
                _pcPushToStore($orderid);
            }
        }
    }

    /*----------帮我送类---------------*/

    // 帮我送类订单写入
    public function sendOrderCreate(){
        if(IS_POST){
            $data = $_POST;
            $data['type'] = 1;
            $data['trade_sn'] = create_sn();
            $data['addtime'] = $data['update_time'] = time();
            $data['ip'] = ip();
            $data['status'] = 1;//1=未支付
            $result = $this->deliverOrderDb->add($data);
            if($result){
                $returnData['status'] = "success";
                $returnData['code'] = 200;
                $returnData['id'] = $result;
                $this->ajaxReturn($returnData);
            }
        }
    }
    // 用户获取我的帮我送订单
    public function getDeliverOrderList(){
        if(IS_POST){
            $userid = intval($_POST['userid']);
            $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
            $count = isset($_POST['count']) ? intval($_POST['count']) : 10;
            $start = ($page-1)*$count;
            $limit = $start.','.$count;
            $where['userid'] = $userid;
            $where['status'] = array('NEQ',"-2");
            $orderData = $this->deliverOrderDb->where($where)->limit($limit)->order('id desc')->select();
            foreach ($orderData as $key => $v) {
                $orderData[$key]['addtime'] = date("Y-m-d H:i:s",$v['addtime']);
                $orderData[$key]['distance'] = round($v['distance']/1000,2);
                if($v['status'] >= 3){
                    $orderData[$key]['grap_time'] = date("Y-m-d H:i:s",$v['grap_time']);
                    $orderData[$key]['update_time'] = date("Y-m-d H:i:s",$v['update_time']);
                    $whereCourier['userid'] = $v['courier_id'];
                    $courierInfo = $this->courierDb->where($whereCourier)->find();
                    // $orderData[$key]['courierData'] = $courierInfo;
                    $orderData[$key]['courierData']['name'] = $courierInfo['name'];
                    $orderData[$key]['courierData']['avatar'] = $courierInfo['avatar'];
                    $orderData[$key]['courierData']['mobile'] = $courierInfo['mobile'];
                }
            }
            if($orderData){
                $returnData['status'] = "success";
                $returnData['code'] = 200;
                $returnData['data'] = $orderData;
                $this->ajaxReturn($returnData);
            }
        }
    }
    /**
    * 配送员获取帮我送订单列表
    */
    public function courierGetDeliverOrderList(){
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
    // 帮我送获取极光推送消息送达数量
    public function getPushReceivedCount(){
        if(IS_POST){
            $userid = intval($_POST['userid']);
            $orderid = intval($_POST['orderid']);
            $where['id'] = $orderid;
            $msgId = $this->deliverOrderDb->where($where)->getField('jpush_msg_id');
            $count = getPushReceived($msgId);
            $returnData['count'] = 0;
            if($msgId){
                $returnData['status'] = 'success';
                $returnData['count'] = $count;
            }else{
                $returnData['status'] = 'success';
                $returnData['count'] = 1;
            }
            $this->ajaxReturn($returnData);
        }
    }
    /**
    *帮我送-配送员修改订单状态
    */
    public function courierChangeDeliverOrderStatus(){
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
                // 确认收货后配送员获得收益
                if($status == 6){
                    $whereOrder['id'] = $orderid;
                    $orderInfo = $this->deliverOrderDb->where($whereOrder)->find();
                    // 写入资产动态表
                    courierAssetDynamicInsert("帮我送订单",$userid,$orderid,$orderInfo['total_price'],1,1,1);
                    // 用户余额增加
                    $whereMember['userid'] = $userid;
                    $this->memberDb->where($whereMember)->setInc("amount",$orderInfo['total_price']);
                }
                courierManagerNoticeMember($orderid);
                $returnData['status'] = "success";
                $returnData['code'] = 200;
                $this->ajaxReturn($returnData);
            }
        }
    }
    // 配送员抢单弹出页面获取订单信息
    public function getDeliverOrderInfoToWaitGrap(){
        if(IS_POST){
            $orderid = intval($_POST['orderid']);
            $where['id'] = $orderid;
            $orderInfo = $this->deliverOrderDb->where($where)->find();
            if($orderInfo){
                $orderInfo['distance'] = round($orderInfo['distance']/1000,2);
                $this->ajaxReturn($orderInfo);
            }
        }
    }
    // 配送端获取未抢订单
    public function getSendOrderListToNoGrap(){
        if(IS_POST){
            $nowTime = time();
            $_time = $nowTime-30000;
            $where = "status = 2 AND addtime > ".$_time;
            $orderDatas = $this->deliverOrderDb->where($where)->order('id asc')->limit(10)->select();
            foreach ($orderDatas as $key => $v) {
                $orderDatas[$key]['date'] = date("H:i",$v['addtime']);
                $orderDatas[$key]['addtime'] = date("m-d H:i",$v['addtime']);
                $orderDatas[$key]['distance'] = round($v['distance']/1000,2);
            }
            if($orderDatas){
                $returnData['status'] = "success";
                $returnData['code'] = 200;
                $returnData['data'] = $orderDatas;
                $this->ajaxReturn($returnData);
            }
        }
    }
    /**
    *配送员抢单-帮我送
    */
    public function courierGrabDeliverOrder(){
        if(IS_POST){
            $orderid = intval($_POST['orderid']);
            $courier_id = intval($_POST['userid']); //当前用户id为配送员的id
            $where['id'] = $orderid;
            $orderInfo = $this->deliverOrderDb->where($where)->find();
            if(!$orderInfo) return;
            if($orderInfo['status'] >= 3){
                $returnData['msg'] = "订单已被抢";
                $returnData['code'] = "1001";
            }else{
                $returnData['msg'] = "抢单成功";
                $returnData['code'] = "200";
                $returnData['orderid'] = $orderid;
                $updateData['courier_id'] = $courier_id;
                $updateData['update_time'] = $updateData['grap_time'] = time();
                $updateData['status'] = 3;//将订单状态改为待取货
                $this->deliverOrderDb->where($where)->save($updateData);
                //通知用户
                courierManagerNoticeMember($orderid);
            }
            $this->ajaxReturn($returnData);
        }
    }

    // 帮我送订单根据距离计算价格,将配置表中公里数换算成米
    public function countSendOrderPriceBydistance(){
        if(IS_POST){
            $distance = $_POST['distance'];
            $priceSettingInfo = $this->priceSettingDb->find();
            $baseDistance = $priceSettingInfo['base_distance']*1000;
            $basePrice = $priceSettingInfo['base_price'];
            $multipleDistance = $priceSettingInfo['multiple']*1000;
            $multiplePrice = $priceSettingInfo['multiple_price'];
            if($distance <= $baseDistance){
                $price = $basePrice;
            }else{
                $price = round($basePrice+(($distance-$baseDistance)/$multipleDistance)*$multiplePrice,0);
            }
            $returnData['status'] = "success";
            $returnData['price'] = $price;
            $this->ajaxReturn($returnData);
        }
    }
    /*****----------订单支付类-------*******/
    //订单支付
    public function orderPay(){
        if(IS_POST){
            $payment = $_POST['payment'];
            $userid = intval($_POST['userid']);
            $orderid = intval($_POST['orderid']);
            $orderType = $_POST['orderType'];
            \Pingpp\Pingpp::setApiKey(C('pingxx_secret_key'));
            if($orderType == 'shop'){
                $where['id'] = $orderid;
                $orderInfo = $this->storeOrderDb->where($where)->find();
                $extra = array();
                switch ($payment) {
                    case 'alipay':
                        $extra = array();
                        break;
                    case 'wx':
                        $extra = array();
                        break;
                }

                try {
                    $ch = \Pingpp\Charge::create(
                        array(
                            'subject'   => '商城订单支付',
                            'body'      => '商家：'.$orderInfo['store_name'].";数量：".$orderInfo['count'],
                            'amount'    => $orderInfo['pay_price']*100,
                            'order_no'  => $orderInfo['trade_sn'],
                            'currency'  => 'cny',
                            'extra'     => $extra,
                            'channel'   => $payment,
                            'client_ip' => ip(),
                            'app'       => array('id' => 'app_yrTOa1fDi5e9eb5C'),
                            'metadata'  => array('orderType' => $orderType,'orderid'=>$orderid)
                        )
                    );
                    echo $ch;
                } catch (\Pingpp\Error\Base $e) {
                    header('Status: ' . $e->getHttpStatus());
                    echo($e->getHttpBody());
                }

            }else if($orderType == 'send'){
                $where['id'] = $orderid;
                $orderInfo = $this->deliverOrderDb->where($where)->find();
                $extra = array();
                switch ($payment) {
                    case 'alipay':
                        $extra = array();
                        break;
                    case 'wx':
                        $extra = array();
                        break;
                }

                try {
                    $ch = \Pingpp\Charge::create(
                        array(
                            'subject'   => '帮我送订单支付',
                            'body'      => '名称：'.$orderInfo['title'],
                            'amount'    => $orderInfo['total_price']*100,
                            'order_no'  => $orderInfo['trade_sn'],
                            'currency'  => 'cny',
                            'extra'     => $extra,
                            'channel'   => $payment,
                            'client_ip' => ip(),
                            'app'       => array('id' => 'app_yrTOa1fDi5e9eb5C'),
                            'metadata'  => array('orderType' => $orderType,'orderid'=>$orderid)
                        )
                    );
                    echo $ch;
                } catch (\Pingpp\Error\Base $e) {
                    header('Status: ' . $e->getHttpStatus());
                    echo($e->getHttpBody());
                }
            }
        }
    }
    public function paySuccess(){
        $event = json_decode(file_get_contents("php://input"));
        $payment = $event->data->object->channel;
        $orderTradeSn = $event->data->object->order_no;
        $orderid = $event->data->object->metadata->orderid;
        $orderType = $event->data->object->metadata->orderType; // 订单类型
        $pingxxId = $event->data->object->id;
        // 对异步通知做处理
        if (!isset($event->type)) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
            exit("fail");
        }
        switch ($event->type) {
            case "charge.succeeded":
                // 支付成功
                if($orderType == 'shop'){ //商城订单
                    $data['status'] = 2;
                    $data['payment'] = $payment;
                    $where['trade_sn'] = $orderTradeSn;
                    $where['id'] = $orderid;
                    $data['pingxx_id'] = $pingxxId;
                    $this->storeOrderDb->where($where)->save($data);

                    // 商品品销售数量增加，库存减少
                    $whereOrderGoods['order_id'] = $orderid;
                    $orderGoodsDatas = $this->storeOrderGoodsDb->where($whereOrderGoods)->select();
                    if($orderGoodsDatas){
                        foreach ($orderGoodsDatas as $key => $v) {
                            $whereGoods['id'] = $v['goods_id'];
                            $this->goodsDb->where($whereGoods)->setInc('saled_count',$v['count']);
                            $this->goodsDb->where($whereGoods)->setDec('stock',$v['count']);
                        }
                    }
                    // 推送消息给商家端
                    _pcPushToStore($orderid);
                }else if($orderType == 'send'){ //帮我送订单
                    $data['status'] = 2;
                    $data['payment'] = $payment;
                    $where['trade_sn'] = $orderTradeSn;
                    $where['id'] = $orderid;
                    $data['pingxx_id'] = $pingxxId;
                    $this->deliverOrderDb->where($where)->save($data);
                    //推送抢单抢单消息
                    pushGrapOrderMessage($orderid,1);
                }
                break;
        }
    }
}