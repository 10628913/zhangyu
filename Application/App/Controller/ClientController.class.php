<?php
namespace App\Controller;
use Think\Controller;
class ClientController extends AppController {
    function _initialize() {
        parent::_initialize();
        parent::requestMethodValidate(REQUEST_METHOD);
        $this->adminInfo = parent::validateAdmin(I("token"));
    }
    public function __construct(){
        parent::__construct();
        $this->adminDb = M('Admin');
        $this->clientDb = M('Client');
        $this->orderDb = M('Order');
        $this->clientAddressDb = M('Client_address');
    }
    // 获取客户列表
    public function getClientsList()
    {
        $uid = $this->adminInfo['uid'];
        // 搜索关键词
        $keywords = I('keywords');
        $searchWhere["parent_id"] = $uid;
        $searchWhere["client_mobile"] = array("like","%".$keywords."%");
        $searchWhere["client_name"] = array("like","%".$keywords."%");
        $searchOrWhere['_logic'] = 'or';        

        $clientList = $this->clientDb
            ->where($searchWhere)
            ->field("client_id,client_name,client_remark,client_mobile,client_avatar,initial")
            ->select();
        if($clientList){
            $this->httpResponse(1,"客户列表获取成功",$clientList);
        }else{
            $this->httpResponse(-1,"获取信息失败");
        }
    }           

    // 添加客户
    public function clientAdd(){

        $data['parent_id'] = $this->adminInfo['uid'];
        $data['client_name'] = I('client_name');
        $data['client_mobile'] = I('client_mobile');
        if(!$data['client_name']){
            $this->httpResponse(-1,"客户名不能为空");
        }
        if(!$data['client_mobile']){
            $this->httpResponse(-1,"手机号不能为空");
        }
        $data['client_remark'] = I('client_remark');
        //默认上级为一手
        $data['parent_type'] = 0;
        //在前台显示
        $data['is_show'] = 1;
        $data['client_addtime'] = time();
        if($data['client_name'] && !$data['client_remark']){
            $data['initial'] = getFirstCharter($data['client_name']);
        }
        if($data['client_remark'] && !$data['client_name']){
            $data['initial'] = getFirstCharter($data['client_remark']);
        }
        if($data['client_name'] && $data['client_remark']){
            $data['initial'] = getFirstCharter($data['client_remark']);
        }
        if(!$data['client_name'] && !$data['client_remark']){
            return $data['initial'] = '#';
        }

        // 判断账号是否存在
        $where['parent_id'] = $data['parent_id'];
        $where['client_mobile'] = $data['client_mobile'];
        $where['is_show'] = 1;
        $isIn = $this->clientDb->where($where)->find();
        if($isIn){
            $this->httpResponse(-1,"客户已存在");
        }
        $result = $this->clientDb->data($data)->add();
        if($result){
            $this->httpResponse(1,"添加成功");
        }else{
            $this->httpResponse(-1,"添加失败");
        }
    }

    // 获取客户详情
    public function getClientInfo(){

       $parent_id = $this->adminInfo['uid'];
       $client_id = I('client_id');
       $where['client_id'] = $client_id;
       $where['parent_id'] = $parent_id;
       $clientInfo = $this->clientDb->where($where)->find();
       if ($clientInfo) {
            $this->httpResponse(1,"获取信息成功",$clientInfo);
        }else{
            $this->httpResponse(-1,"获取信息失败");
        }
            

    }

    //修改客户信息
    public function clientEdit(){

        $parent_id = $this->adminInfo['uid'];
        $client_id = I('client_id');
        $data['client_name'] = I('client_name');
        $data['client_mobile'] = I('client_mobile');
        $data['client_remark'] = I('client_remark');
        if($data['client_name'] && !$data['client_remark']){
            $data['initial'] = getFirstCharter($data['client_name']);
        }
        if($data['client_remark'] && !$data['client_name']){
            $data['initial'] = getFirstCharter($data['client_remark']);
        }
        if($data['client_name'] && $data['client_remark']){
            $data['initial'] = getFirstCharter($data['client_remark']);
        }
        if(!$data['client_name'] && !$data['client_remark']){
            return $data['initial'] = '#';
        }

        if(!$data['client_name']){
            $this->httpResponse(-1,"客户名不能为空");
        }
        if(!$data['client_mobile']){
            $this->httpResponse(-1,"手机号不能为空");
        }
        $where['parent_id'] = $parent_id;
        $where['client_id'] = $client_id;          
        $result = $this->clientDb->where($where)->save($data);
        if($result){
            // $clientInfo = $this->clientDb->where($where)->find();
            $this->httpResponse(1,"修改成功");
        }else{
            $this->httpResponse(-1,"信息无修改");
        }
    }

    // 删除客户
    public function clientDelete(){

        $parent_id = $this->adminInfo['uid'];
        $client_id = I('client_id');
        $data['is_show'] = 0;
        $where['parent_id'] = $parent_id;
        $where['client_id'] = $client_id;          
        $result = $this->clientDb->where($where)->find();
        if($result){
            if($this->clientDb->where($where)->save($data)){
                $this->httpResponse(1,"删除成功");
            }else{
                $this->httpResponse(-1,"删除失败");
            }
        }else{
            $this->httpResponse(-1,"客户不存在");
        }
    }

    // 客户收货地址添加
    public function addressAdd(){
        
        // 客户id
        $data["client_id"] = I('client_id');
        //收货人
        $data["consignee"] = I("consignee");
        //收货人电话
        $data["mobile"] = I("mobile");
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
        // 默认地址
        $data['is_default'] = 1;

        $result = $this->clientAddressDb->add($data);
        if($result){
            $this->httpResponse(1,"添加成功");
        }else{
            $this->httpResponse(-1,"添加失败");
        }
    }


    // 客户收货地址列表
    public function clientAddressList(){

            $client_id = I('client_id');

            $where['client_id'] = $client_id;
            $data = $this->clientAddressDb
            ->where($where)
            ->field("address_id,consignee,mobile,province,city,county,district,town,address,zipcode,is_default")
            ->order('is_default desc,address_id asc')->select();
            if($data){
                $this->httpResponse(1,"获取地址列表成功",$data);
            }else{
                $this->httpResponse(-1,"获取地址列表失败");
            }
        }


    // 客户收货地址编辑
    public function clientAddressEdit(){

        // 客户id
        $data["address_id"] = I('address_id');
        // 客户id
        $data["client_id"] = I('client_id');
        //收货人
        $data["consignee"] = I("consignee");
        //收货人电话
        $data["mobile"] = I("mobile");
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
        // 默认地址
        $data['is_default'] = I('is_default');

        $where['client_id'] = $data['client_id'];
        $where['address_id'] = $data['address_id'];
        $result = $this->clientAddressDb->where($where)->save($data);
        if($result){
            $this->httpResponse(1,"修改成功");
        }else{
            $this->httpResponse(-1,"未修改信息");
        }

    }










}