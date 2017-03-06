<?php
/**
* 用户位置处理
*/
namespace App\Controller;
use Think\Controller;
class LocationController extends Controller {
    public function __construct(){
        parent::__construct();
        $this->locationDb = M('Member_location');
        $this->memberDb = M('Member');
        $this->courierOrderLocationDb = M('Courier_order_location');
        $this->deliverOrderDb = M('Deliver_order');
        $this->storeOrderDb = M('Store_order');
    }
    // 用户坐标更新
    public function updateLocation(){
        if(IS_POST){
            $data['userid'] = intval($_POST['userid']);
            $data['lat'] = $_POST['lat'];
            $data['lon'] = $_POST['lon'];
            $data['province'] = $_POST['province'] ? $_POST['province'] : '';
            $data['city'] = $_POST['city'] ? $_POST['city'] : '';
            $data['district'] = $_POST['district'] ? $_POST['district'] : '';
            $data['update_time'] = time();
            $where['userid'] = $data['userid'];
            $data['user_type'] = $this->memberDb->where($where)->getField("type");
            $locationInfo = $this->locationDb->where($where)->find();
            if($locationInfo){
                $result = $this->locationDb->where($where)->save($data);
            }else{
                $result = $this->locationDb->add($data);
            }
            if($result){
                $returnData['status'] = "success";
                $returnData['code'] = 200;
                $this->ajaxReturn($returnData);
            }
        }
    }
    //更新用户坐标并返回附近配送员
    public function getNearByCourier(){
        if(IS_POST){
            $data = $_POST;
            $data['update_time'] = time();
            $where['userid'] = $data['userid'];
            $data['user_type'] = $this->memberDb->where($where)->getField("type");
            $myLat = $data['lat'];
            $myLon = $data['lon'];
            // $locationInfo = $this->locationDb->where($where)->find();
            // if($locationInfo){
            //     $result = $this->locationDb->where($where)->save($data);
            // }else{
            //     $result = $this->locationDb->add($data);
            // }
            // if($result){
                // 查找附近的配送员
                $orderBy = 'ACOS(SIN(('.$myLat.' * 3.1415) / 180 ) *SIN((lat * 3.1415) / 180 ) +COS(('.$myLat.' * 3.1415) / 180 ) * COS((lat * 3.1415) / 180 ) *COS(('.$myLon.'* 3.1415) / 180 - (lon * 3.1415) / 180 ) ) * 6380 asc';
                $whereCourier['user_type'] = 2;
                $whereCourier['userid'] = array('neq',$data['userid']);
                // $whereCourier = "user_type = 2 AND userid != ".$data['userid'];
                $courierDatas = $this->locationDb->where($whereCourier)->order($orderBy)->limit(50)->select();
                foreach ($courierDatas as $key => $v) {
                    $distance = getDistance($myLat,$myLon,$v['lat'],$v['lon']);
                    if($distance < 10000){
                        $_courierDatas[$key]['distance'] = $distance;
                        $_courierDatas[$key]['userid'] = $v['userid'];
                        $_courierDatas[$key]['lat'] = $v['lat'];
                        $_courierDatas[$key]['lon'] = $v['lon'];
                        $_courierDatas[$key]['update_time'] = $v['update_time'];
                    }
                }
                $returnData['status'] = "success";
                $returnData['code'] = 200;
                $returnData['courierData'] = $_courierDatas;
                $this->ajaxReturn($returnData);
            // }
        }
    }
    public function demo(){
        $aa['lat'] = "111.11";
        $aa['lon'] = "22.22";
        // // $data['datas'] = array();
        // $data[0] = $a;
        // // $data[1] = $a;
        // $b['datas'] = $data;
        // dump($data);
        $where['orderid'] = 744;
        $d = $this->courierOrderLocationDb->where($where)->find();
        $a = unserialize($d['datas']);
        foreach ($a as $key => $v) {
            $data[$key] = "[".$v['lon'].",".$v['lat']."]";
        }
        dump($data);
        $c = json_encode($data);
        echo str_replace('"',"",$c);
    }
    // 更新配送员坐标
    public function updateCourierLocation(){
        if(IS_POST){
            $userid = intval($_POST['userid']);
            $lat = $locationData['lat'] = $_POST['lat'];
            $lon = $locationData['lon'] = $_POST['lon'];
            $timestamp = $locationData['timestamp'] = $_POST['timestamp'];
            $whereCourier['courier_id'] = $whereOrder['courier_id'] = $userid;
            $newData['courier_id'] = $userid;
            $newData['update_time'] = time();
            /**** 查询是否有正在配送中的订单****/

            $whereOrder['status'] = array('lt',6);
            // 帮我送订单
            $derliverOrderDatas = $this->deliverOrderDb->where($whereOrder)->select();
            if($derliverOrderDatas){
                $newData['order_type'] = 1;
                foreach ($derliverOrderDatas as $key => $v) {
                    $newData['orderid'] = $v['id'];
                    // 判断是否存在当前订单数据
                    $whereDeliverOrderLocation['orderid'] = $v['id'];
                    $whereDeliverOrderLocation['order_type'] = 1;
                    $locationOrderData = $this->courierOrderLocationDb->where($whereDeliverOrderLocation)->find();
                    if($locationOrderData){
                        // 更新,数组中追加数据
                        $oldData = unserialize($locationOrderData['datas']);
                        array_push($oldData,$locationData);
                        $updateData['datas'] = serialize($oldData);
                        $updateData['update_time'] = time();
                        $this->courierOrderLocationDb->where($whereDeliverOrderLocation)->save($updateData);
                    }else{
                        // 写入
                        $datas[0] = $locationData;
                        $newData['datas'] = serialize($datas);
                        $this->courierOrderLocationDb->data($newData)->add();
                    }
                }
            }
            // 商城订单
            $storeOrderDatas = $this->storeOrderDb->where($whereOrder)->select();
            if($storeOrderDatas){
                $newData['order_type'] = 1;
                foreach ($storeOrderDatas as $key => $v) {
                    $newData['orderid'] = $v['id'];
                    // 判断是否存在当前订单数据
                    $whereDeliverOrderLocation['orderid'] = $v['id'];
                    $whereDeliverOrderLocation['order_type'] = 2;
                    $locationOrderData = $this->courierOrderLocationDb->where($whereDeliverOrderLocation)->find();
                    if($locationOrderData){
                        // 更新,数组中追加数据
                        $oldData = unserialize($locationOrderData['datas']);
                        array_push($oldData,$locationData);
                        $updateData['datas'] = serialize($oldData);
                        $updateData['update_time'] = time();
                        $this->courierOrderLocationDb->where($whereDeliverOrderLocation)->save($updateData);
                    }else{
                        // 写入
                        $datas[0] = $locationData;
                        $newData['datas'] = serialize($datas);
                        $this->courierOrderLocationDb->data($newData)->add();
                    }
                }
            }
        }
    }

}