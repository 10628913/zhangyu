<?php
namespace App\Controller;
// use Think\Controller;
class MemberController extends BaseController {
    public function __construct(){
        parent::__construct();
        $this->addressDb = M('Member_address');
        $this->memberDb = M('Member');
        $this->courierDb = M('Courier');
    }
    //登录
    public function login(){
        if(IS_POST){
            $mobile = $_POST['mobile'];
            $password = $_POST['password'];
            $whereMember['mobile'] = $mobile;
            //根据用户名检查用户是否存在
            $info = $this->memberDb->where($whereMember)->find();
            $userid = $info['userid'];
            $data = array();
            if(!$info){
                $data['code'] = 2;
                $data['msg'] = "用户不存在";
            }else{
                //存在、验证密码是否正确
                $password = password($_POST['password'],$info['encrypt']);
                if($password==$info['password']){
                    $random = rand(0,1000);
                    $token = md5('NONO'.$random.$mobile.time());
                    $updateData['token'] = $token;
                    $updateData['last_date'] = time();
                    $updateData['last_ip'] = ip();
                    $this->memberDb->where($whereMember)->save($updateData);
                    $data['code'] = 200;
                    $data['msg'] = "登录成功";
                    $data['data']['userid'] = $info['userid'];
                    $data['data']['mobile'] = $mobile;
                    $data['data']['nickname'] = $info['nickname'];
                    $data['data']['avatar'] = $info['avatar'];
                    $data['data']['encrypt'] = $info['encrypt'];
                    $data['data']['token'] = $info['token'];
                    $data['data']['type'] = $info['type'];
                    $data['data']['token'] = $token;
                    $data['data']['jpush_registration_id'] = $info['jpush_registration_id'];
                }else{
                    $data['code'] = 3;
                    $data['msg'] = "密码错误";
                }
            }
            $this->ajaxReturn($data);
        }
    }
    //会员注册
    public function register(){
        if(IS_POST){
            $data = array();
            $data['mobile'] = $_POST['mobile'];
            //判断用户名是否存在
            $isIn = $this->memberDb->where("mobile = '".$data['mobile']."'")->find();
            if(!$isIn){
                //不存在
                $data['nickname'] = $_POST['nickname'];
                $password = password($_POST['password']);
                $data['password'] = $password['password'];
                $data['encrypt'] = $password['encrypt'];
                $data['avatar'] = $_POST['avatar'];
                $data['sex'] = $_POST['sex'];
                $data['type'] = 1;
                $data['reg_date'] = $data['last_date'] = time();
                $data['reg_ip']  = $data['last_ip'] = ip();
                $random = rand(0,1000);
                $token = md5('NONO'.$random.$data['mobile'].time());
                $data['token'] = $token;
                $result = $this->memberDb->data($data)->add();
                if($result){
                    //数据返回
                    $returnData = array();
                    $returnData['code'] = 200;
                    $returnData['msg'] = "注册成功";
                    $returnData['data']['userid'] = $result;
                    $returnData['data']['avatar'] = $data['avatar'];
                    $returnData['data']['mobile'] = $data['mobile'];
                    $returnData['data']['nickname'] = $data['nickname'];
                    $returnData['data']['token'] = $token;
                    $returnData['data']['type'] = 1;
                    $returnData['data']['token'] = $token;

                    //测试期处理默认成为配送员
                    // $courierDb = M("Courier");
                    // $courierData['userid'] = $result;
                    // $courierData['name'] = $data['nickname'];
                    // $courierData['mobile'] = $data['mobile'];
                    // $courierData['avatar'] = $data['avatar'];
                    // $courierData['status'] = 2;
                    // $courierDb->data($data)->add();

                    $this->ajaxReturn($returnData);
                }else{
                    $returnData = array();
                    $returnData['code'] = '-1';
                    $returnData['msg'] = "注册失败";
                    $this->ajaxReturn($returnData);
                }
            }else{
                $returnData = array();
                $returnData['code'] = '0';
                $returnData['msg'] = "账号已经存在";
                $this->ajaxReturn($returnData);
            }
        }
    }
    public function sendSms(){
        if(IS_POST){
            $options['accountsid']='90f3a1d1a11a4e817ca1e981b67b31ea';
            $options['token']='252d8666df1ca3a994cb54e689e9d56f';
            //初始化 $options必填
            $mobile = $_POST['mobile'];
            $code = random(4,'123456789');
            $ucpass = new \Ucpaas($options);
            $appId = "cd820f8a7ab4426d881d1b1ef1fa46a9";
            $to = "".$mobile."";
            $templateId = "18635";
            $param="".$code.",5";

            //检查手机号是否被注册
            $memberInfo = $this->memberDb->where("mobile=".$mobile)->find();
            if($memberInfo){
                $data['code'] = "-1";
                $data['msg'] = "手机号码被注册";
                $this->ajaxReturn($data);
            }else{
                $resultArr = $ucpass->templateSMS($appId,$to,$templateId,$param);
                $resultArr = json_decode($resultArr);
                $smsCode = $resultArr->resp->respCode;
                if($smsCode=='000000'){
                    $data['msg'] = "发送成功";
                    //写入短信记录
                    $smsDb = M('Sms');
                    $smsData['mobile'] = $mobile;
                    $smsData['code'] = $code;
                    $smsData['sendtime'] = time();
                    $smsDb->data($smsData)->add();
                }else if($smsCode=='105122'){
                    $data['msg'] = "发送数量超出限制";
                }else{
                    $data['msg'] = "发送失败";
                }
                $data['code'] = $smsCode;
                $this->ajaxReturn($data);
            }
        }
    }
    //检查短信验证码
    public function checkSmsCode(){
        if(IS_POST){
            $mobile = $_POST['mobile'];
            $code = $_POST['code'];
            $smsDb = M('Sms');
            $result = $smsDb->where("mobile='".$mobile."' AND code='".$code."'")->find();
            if($result){
                $data['code'] = "1";
                $data['msg'] = "成功";
            }else{
                $data['code'] = "-1";
                $data['msg'] = "验证码不正确";
            }
            $this->ajaxReturn($data);
        }
    }
    // 地址写入
    public function addressInsert(){
        if(IS_POST){
            $data = $_POST;
            $result = $this->addressDb->add($data);
            if($result){
                $returnData['status'] = "success";
                $returnData['code'] = 200;
                $returnData['id'] = $result;
                $this->ajaxReturn($returnData);
            }
        }
    }
    // 获取用户全部地址
    public function addressList(){
        if(IS_POST){
            $userid = $_POST['userid'];
            $where['userid'] = $userid;
            $where['status'] = 1;
            $data = $this->addressDb->where($where)->select();
            if($data){
                $returnData['status'] = "success";
                $returnData['code'] = 200;
                $returnData['data'] = $data;
                $this->ajaxReturn($returnData);
            }
        }
    }
    // 根据id获取用户地址
    public function addressInfo(){
        if(IS_POST){
            $userid = intval($_POST['userid']);
            $id = intval($_POST['id']);
            $where['userid'] = $userid;
            $where['id'] = $id;
            $data = $this->addressDb->where($where)->find();
            if($data){
                $returnData['status'] = "success";
                $returnData['code'] = 200;
                $returnData['data'] = $data;
                $this->ajaxReturn($returnData);
            }
        }
    }
    // 编辑用户地址
    public function addressEdit(){
        if(IS_POST){
            $userid = intval($_POST['userid']);
            $id = intval($_POST['id']);
            $where['userid'] = $userid;
            $where['id'] = $id;
            $updateData['name'] = $_POST['name'];
            $updateData['mobile'] = $_POST['mobile'];
            $updateData['address'] = $_POST['address'];
            $updateData['address_detail'] = $_POST['address_detail'];
            // 判断当前地址是否在未结束的订单中存在
            $result = $this->addressDb->where($where)->save($updateData);
            if($result){
                $returnData['status'] = "success";
                $returnData['code'] = 200;
                $this->ajaxReturn($returnData);
            }
        }
    }
    // 删除用户地址(假删除，修改状态)
    public function addressDelete(){
        if(IS_POST){
            $userid = intval($_POST['userid']);
            $id = intval($_POST['id']);
            $where['userid'] = $userid;
            $where['id'] = $id;
            $updateData['status'] = 0;
            $result = $this->addressDb->where($where)->save($updateData);
            if($result){
                $returnData['status'] = "success";
                $returnData['code'] = 200;
                $this->ajaxReturn($returnData);
            }
        }
    }
    /**
    *申请成为配送员
    */
    public function applyToCourier(){
        if(IS_POST){
            $userid = intval($_POST['userid']);
            $data['name'] = $_POST['name'];
            $data['mobile'] = $_POST['mobile'];
            $data['address'] = $_POST['address'];
            $data['avatar'] = $_POST['avatar'];
            $data['card'] = $_POST['card'];
            $data['card_photo_1'] = $_POST['card_photo_1'];
            $data['card_photo_2'] = $_POST['card_photo_2'];
            $data['status'] = 1;
            $data['addtime'] = time();
            $where['userid'] = $userid;
            $courierInfo = $this->courierDb->where($where)->find();
            if($courierInfo){
                if($courierInfo['status']==1){
                    //不做处理
                    $returnData['msg'] = "等待管理员审核";
                }else if($courierInfo['status']==2){
                    //不做处理
                    $returnData['msg'] = "您当前已经是配送员了";
                }else if($courierInfo['status']=='-1'){
                    $this->courierDb->where($where)->save($data);
                    $returnData['msg'] = "提交成功，等待重新审核";
                }
            }else{
                $data['userid'] = $userid;
                $this->courierDb->data($data)->add();
                $returnData['msg'] = "提交成功，等待审核";
            }

            $this->ajaxReturn($returnData);
        }
    }
    // 获取配送员信息
    public function getCourierInfo(){
        if(IS_POST){
            $userid = intval($_POST['userid']);
            $where['userid'] = $userid;
            $courierInfo = $this->courierDb->where($where)->find();
            if($courierInfo){
                $this->ajaxReturn($courierInfo);
            }
        }
    }
    //更新极光id
    public function updateMemberJpush(){
        if(IS_POST){
            $userid = intval($_POST['userid']);
            $where['userid'] = $userid;
            $data['jpush_registration_id'] = $_POST['registrationId'];
            $this->memberDb->where($where)->save($data);
        }
    }
}