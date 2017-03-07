<?php
namespace App\Controller;
use Think\Controller;
class UserController extends Controller {
    public function __construct(){
        parent::__construct();
        $this->adminDb = M('Admin');
        $this->smsDb = M('Sms');
        $this->configDb = M("Module_extend");
        $this->suggestDb = M("Admin_suggest");
        $this->letterDb = M("Admin_letter");
        $this->registerDb = M('Register');
        $this->adminAddressDb = M('Admin_address');
    }
    public function _initialize() {
        vendor('Ucpaas.Ucpaas');
    }
    // 修改密码
    public function forgetPassword(){
        if(IS_POST){
            $mobile = $_POST['mobile'];
            $code = $_POST['code'];

            $partten = "/^1[3-9]\d{9}$/";
            if(!$mobile || !preg_match($partten,$mobile)){
                $returnData['code'] = -1;
                $returnData['msg'] = "请输入正确手机号码";
                $this->ajaxReturn($returnData);
            }
            if(!$code){
                $returnData['code'] = -1;
                $returnData['msg'] = "请输入验证码";
                $this->ajaxReturn($returnData);
            }

            $whereCode['mobile'] = $mobile;
            $whereCode['code'] = $code;
            $result = $this->smsDb->where($whereCode)->find();

            if($result){
                //距离验证码发送时间超过5分钟.判定验证码超时
                $sendtime = $result["sendtime"];
                $diff = time() - (int) substr($sendtime,0,10);
                $min = floor($diff/60);
                if($result["status"] == 3){
                    if($min > 5){
                        $returnData["code"] = -1;
                        $returnData["msg"] = "验证码超时";
                        $this->ajaxReturn($returnData);
                    }else{
                        $whereAdmin['mobile'] = $mobile;
                        $password = password($_POST['password']);
                        $passwordData['password'] = $password['password'];
                        $passwordData['encrypt'] = $password['encrypt'];
                        if($this->adminDb->where($whereAdmin)->save($passwordData)){
                            $returnData['code'] = 1;
                            $returnData['msg'] = "密码修改成功";
                            $upWhere["id"] = $result["id"];
                            $upWhere["status"] = 4;
                            $this->smsDb->save($upWhere);
                        }else{
                            $returnData["code"] = -1;
                            $returnData["msg"] = "密码修改失败";
                        }
                    }
                }
                else{
                    $returnData["code"] = -1;
                    $returnData["msg"] = "验证码失效";
                }

            }else{
                $returnData['code'] = -1;
                $returnData['msg'] = "验证码错误";
            }
            $this->ajaxReturn($returnData);
        }
    }
    // 发送忘记密码验证码
    public function sendSmsByForgetPassword(){
        if(IS_POST){
            $info = $this->configDb->find();
            $options['accountsid'] = $info["ucpaas_account_sid"];
            $options['token'] = $info["ucpaas_auth_token"];
            $appId = $info["ucpaas_app_id"];
            //初始化 $options必填
            $mobile = $_POST['mobile'];
            $code = random(4,'123456789');
            $ucpass = new \Ucpaas($options);
            $to = "".$mobile."";
            $templateId = "38152";
            $param="".$code.",5";

            $whereMobile['mobile'] = $mobile;
            $whereMobile['status'] = 1;
            $result = $this->smsDb->where($whereMobile)->find();
            if($result){
                $sendtime = $result["sendtime"];
                $diff = time() - (int) substr($sendtime,0,10);
                $min = floor($diff/60);
                if($min < 1){
                    $returnData["code"] = -1;
                    $returnData["msg"] = "60s内请勿重复发送";
                    $this->ajaxReturn($returnData);
                }
            }


            //检查手机号是否存在
            $adminInfo = $this->adminDb->where("mobile=".$mobile)->find();
            if($adminInfo){
                $resultArr = $ucpass->templateSMS($appId,$to,$templateId,$param);
                $resultArr = json_decode($resultArr);
                $smsCode = $resultArr->resp->respCode;
                if($smsCode=='000000'){
                    $data['code'] = 1;
                    $data['msg'] = "发送成功";
                    //写入短信记录
                    $smsData['mobile'] = $mobile;
                    $smsData['code'] = $code;
                    $smsData['sendtime'] = time();
                    $smsData['status'] = 2;
                    $this->smsDb->data($smsData)->add();
                }else if($smsCode=='105122'){
                    $data['msg'] = "发送数量超出限制";
                }else{
                    $data['msg'] = "发送失败";
                }
                $data['code'] = $smsCode;
                $this->ajaxReturn($data);
            }else{
                $data['code'] = -1;
                $data['msg'] = "手机号码未注册";
                $this->ajaxReturn($data);
            }
        }
    }
    //登录
    public function login(){
        if(IS_POST){
            $mobile = $_POST['mobile'];
            $password = $_POST['password'];

            $partten = "/^1[3-9]\d{9}$/";
            if(!$mobile || !preg_match($partten,$mobile)){
                $data['code'] = -1;
                $data['msg'] = "请输入正确手机号码";
                $this->ajaxReturn($data);
            }
            if(!$password){
                $data['code'] = -1;
                $data['msg'] = "请输入密码";
                $this->ajaxReturn($data);
            }

            $whereAdmin['mobile'] = $mobile;
            //根据用户名检查用户是否存在
            $info = $this->adminDb->where($whereAdmin)->find();
            $uid = $info['uid'];
            $data = array();
            if(!$info){
                $data['code'] = -1;
                $data['msg'] = "用户不存在或未审核通过";
                $this->ajaxReturn($data);
            }else{
                //存在、验证密码是否正确
                $where["uid"] = $info["uid"];
                $password = password($_POST['password'],$info['encrypt']);
                if($password==$info['password']){
                    $random = rand(0,1000);
                    $token = md5('NONO'.$random.$mobile.time());
                    $updateData['token'] = $token;
                    $updateData['last_date'] = time();
                    $updateData['last_ip'] = ip();
                    $this->adminDb->where($whereAdmin)->save($updateData);
                    $infoData = $this->adminDb->where($where)->field("password,encrypt",true)->find();

                    // $connectResult = $this->connectDb->where("userid=".$infoData["userid"])->find();

                    // $infoData["bindQQ"] = false;
                    // $infoData["bindWechat"] = false;
                    // $infoData["bindSina"] = false;

                    // $connectResult["wechat_openid"] ? $infoData["bindQQ"] = true : "";
                    // $connectResult["qq_openid"] ? $infoData["bindWechat"] = true : "";
                    // $connectResult["sina_openid"] ? $infoData["bindSina"] = true : "";

                    $data['code'] = 1;
                    $data['msg'] = "登录成功";
                    $data['data'] = $infoData;
                    $data['token'] = $token;
                    $this->ajaxReturn($data);
                }else{
                    $data['code'] = -1;
                    $data['msg'] = "密码错误";
                    $this->ajaxReturn($data);
                }
            }
            $this->ajaxReturn($data);
        }
    }
    //会员注册
    public function register(){
        if(IS_POST){
            $data = array();
            $returnData = array();
            $mobile = $_POST['mobile'];
            $realname = $_POST['realname'];
            $password = $_POST['password'];
            $data['mobile'] = $mobile;
            $code = $_POST['code'];
            $partten = "/^1[3-9]\d{9}$/";
            if(!$mobile || !preg_match($partten,$mobile)){
                $returnData['code'] = -1;
                $returnData['msg'] = "请输入正确手机号码";
                $this->ajaxReturn($returnData);
            }

            $whereCode['mobile'] = $mobile;
            $whereCode['code'] = $code;
            $codeinfo = $this->smsDb->where($whereCode)->find();
            if(!$code || $codeinfo['status'] != '3'){
                $returnData['code'] = -1;
                $returnData['msg'] = "请输入验证码/验证码失效";
                $this->ajaxReturn($returnData);
            }

            if(!$realname){
                $returnData['code'] = -1;
                $returnData['msg'] = "请输入昵称";
                $this->ajaxReturn($returnData);
            }
            if(!$password){
                $returnData['code'] = -1;
                $returnData['msg'] = "请输入密码";
                $this->ajaxReturn($returnData);
            }
            $whereMobile['mobile'] = $mobile;
            $isInto = $this->registerDb->where($whereMobile)->find();
            if($isInto['is_register'] == '0'){
                $returnData['code'] = -1;
                $returnData['msg'] = "已注册，请等待管理员审核";
                $this->ajaxReturn($returnData);
            }
            //判断用户名是否存在
            $isIn = $this->adminDb->where($whereMobile)->find();
            if(!$isIn){
                //不存在
                $data['realname'] = $realname;
                $data['is_register'] = 0;
                $data['sex'] = $_POST['sex'];
                $data['birthday'] = $_POST['birthday'];
                $data['signature'] = $_POST['signature'];
                $password = password($_POST['password']);
                $data['password'] = $password['password'];
                $data['encrypt'] = $password['encrypt'];
                $data['avatar'] = $_POST['avatar'];
                $data['reg_date'] = $data['last_date'] = $data["update_date"] = time();
                $data['reg_ip']  = $data['last_ip'] = ip();
                $random = rand(0,1000);
                $token = md5('NONO'.$random.$data['mobile'].time());
                $data['token'] = $token;
                $result = $this->registerDb->data($data)->add();
                if($result){
                    //数据返回
                    $where["id"] = $result;
                    $returnData['code'] = 1;
                    $returnData['msg'] = "注册完成";
                    $smsData['code'] = $code;
                    $smsData['status'] = '4';
                    $this->smsDb ->where($whereMobile)->save($smsData);
                    $userData = $this->registerDb->where($where)->field("password,encrypt",true)->find();
                    $returnData["data"] = $userData;
                    $this->ajaxReturn($returnData);
                }else{
                    $returnData['code'] = -1;
                    $returnData['msg'] = "注册失败";
                    $this->ajaxReturn($returnData);
                }
            }else{
                $returnData['code'] = -1;
                $returnData['msg'] = "用户名已经存在";
                $this->ajaxReturn($returnData);
            }
            $this->ajaxReturn($returnData);
        }
    }
    // 发送注册验证码
    public function sendSms(){
        if(IS_POST){
            $returnData = array();
            $info = $this->configDb->find();
            $options['accountsid'] = $info["ucpaas_account_sid"];
            $options['token'] = $info["ucpaas_auth_token"];
            //初始化 $options必填
            $mobile = $_POST['mobile'];
            $code = random(4,'123456789');
            $ucpass = new \Ucpaas($options);
            $appId = $info["ucpaas_app_id"];
            $to = "".$mobile."";
            $templateId = "38152";
            $param="".$code.",5";

            $whereMobile['mobile'] = $mobile;
            $whereMobile['status'] = 2;
            $result = $this->smsDb->where($whereMobile)->find();
            if($result){
                $sendtime = $result["sendtime"];
                $diff = time() - (int) substr($sendtime,0,10);
                $min = floor($diff/60);
                if($min < 1){
                    $data["code"] = -1;
                    $data["msg"] = "60s内请勿重复发送";
                    $this->ajaxReturn($data);
                }
            }

            //检查手机号是否被注册
            $adminInfo = $this->adminDb->where("mobile=".$mobile)->find();
            if($adminInfo){
                $data['code'] = -1;
                $data['msg'] = "手机号码已经注册";
                $this->ajaxReturn($data);
            }else{
                $resultArr = $ucpass->templateSMS($appId,$to,$templateId,$param);
                $resultArr = json_decode($resultArr);
                $smsCode = $resultArr->resp->respCode;
                if($smsCode=='000000'){
                    $data['code'] = "1";
                    $data['msg'] = "发送成功";
                    //写入短信记录
                    $smsData['mobile'] = $mobile;
                    $smsData['code'] = $code;
                    $smsData['sendtime'] = time();
                    $smsData['status'] = 2;
                    $this->smsDb->data($smsData)->add();
                }else if($smsCode=='105122'){
                    $data['msg'] = "发送数量超出限制";
                    $this->ajaxReturn($data);
                }else{
                    $data['msg'] = "发送失败";
                    $this->ajaxReturn($data);
                }
                $data['data'] = $smsCode;
            }
            $this->ajaxReturn($data);
        }
    }
    //检查短信验证码
    public function checkSmsCode(){
        if(IS_POST){
            $where["mobile"] = $_POST['mobile'];
            $where["code"] = $_POST['code'];

            $result = $this->smsDb->where($where)->find();
            if($result){
                // if($result["status"] == 0){
                //     $ret["code"] = -1;
                //     $ret["msg"] = "验证码失效";
                //     $this->ajaxReturn($ret);
                // }
                $sendtime = $result["sendtime"];
                $diff = time() - (int) substr($sendtime,0,10);
                $min = floor($diff/60);
                if($min > 5){
                    $data["code"] = -1;
                    $data['msg'] = "验证码超时";
                }else{
                    $data['code'] = 1;
                    $data['msg'] = "成功";
                    $upWhere["id"] = $result["id"];
                    $upWhere["status"] = 3;
                    $this->smsDb->save($upWhere);
                }
            }else{
                $data['code'] = -1;
                $data['msg'] = "验证码错误";
            }
            $this->ajaxReturn($data);
        }
    }

    //修改用户信息
    public function changeUserInfo(){
        if(IS_POST){
            $token = $_POST["token"];
            $info = checkUser($token);
            if($info){
                $where["uid"] = $saveData["uid"] = $info["uid"];
                $realname = $_POST["realname"];
                $sex = $_POST["sex"];
                $birthday = $_POST["birthday"];
                $signature = $_POST["signature"];
                $realname ? $saveData["realname"] = $realname : "";
                $_POST["avatar"] ? $saveData["avatar"] = $_POST["avatar"] : "";
                $sex ? $saveData["sex"] = $sex : "";
                $signature ? $saveData["signature"] = $signature : "";
                $birthday ? $saveData["birthday"] = $birthday : "";
                $_POST["lat"] ? $saveData["lat"] = $_POST["lat"] : "";
                $_POST["lon"] ? $saveData["lon"] = $_POST["lon"] : "";
                $_POST["province"] ? $saveData["province"] = $_POST["province"] : "";
                $_POST["city"] ? $saveData["city"] = $_POST["city"] : "";
                $_POST["area"] ? $saveData["area"] = $_POST["area"]  : "";
                $saveData["update_time"] = time();
                if($this->adminDb->save($saveData)){
                    $ret["code"] = 1;
                    $ret["msg"] = "修改成功";
                    $info = $this->adminDb->where($where)->field("password,encrypt",true)->find();
                    $ret["data"] = $info;
                }else{
                    $ret["code"] = -1;
                    $ret["msg"] = "信息无修改";
                }

            }else{
                $ret["code"] = 0;
                $ret["msg"] = "用户已在其他终端登录";
            }
            $this->ajaxReturn($ret);
        }
    }
    //修改头像
    public function changeUserAvatar(){
        if(IS_POST){
            $token = $_POST["token"];
            $user = checkUser($token);
            if($user){
                $data["uid"] = $user["uid"];
                $upload = new \Think\Upload();
                $upload->maxSize = 5242880 ;
                $upload->exts = array('jpg', 'gif', 'png', 'jpeg');
                $info = $upload->upload();
                if(!$info) {
                    $this->error($upload->getError());
                    $ret["code"] = -1;
                    $ret["msg"] = "无上传图片";
                }else{
                    $imageUrl = C("SITE_URL").C("UPLOAD_PATH").$info["file"]["savepath"].$info["file"]["savename"];
                    $thumb = thumb($imageUrl,200,200);
                    $path = str_replace(C('SITE_URL'),"",$imageUrl);
                    unlink($path);
                    $data["avatar"] = $thumb;
                    if($this->adminDb->save($data)){
                        $ret["code"] = 1;
                        $ret["msg"] = "头像修改成功";
                        $ret["url"] = $thumb;
                    }else{
                        $ret["code"] = -1;
                        $ret["msg"] = "头像修改失败";
                    }
                }
            }else{
                $ret["code"] = -1;
                $ret["msg"] = "用户已在其他终端登录";
            }
            $this->ajaxReturn($ret);
        }
    }

    // 用户发货地址添加
    public function addressAdd(){
        if(IS_POST){
            $token = $_POST["token"];
            $user = checkUser($token);
            if($user){
                $data["uid"] = $user["uid"];
                $data = $_POST;
                if(!$data['consignee']){
                    $returnData['code'] = -1;
                    $returnData['msg'] = '发货人不能为空';
                    $this->ajaxReturn($returnData);
                }
                if(!$data['mobile']){
                    $returnData['code'] = -1;
                    $returnData['msg'] = '发货人不能为空';
                    $this->ajaxReturn($returnData);
                }
                $result = $this->adminAddressDb->add($data);
                if($result){
                    $returnData['status'] = "success";
                    $returnData['code'] = 1;
                    $returnData['id'] = $result;
                    $this->ajaxReturn($returnData);
                }else{
                    $returnData['code'] = -1;
                    $returnData["msg"] = "添加失败";
                    $this->ajaxReturn($returnData);
                }
            }
            else{
                $ret["code"] = -1;
                $ret["msg"] = "用户已在其他终端登录";
            }
            
        }
        // if (IS_POST) {
        //     $client_id = intval($_POST['client_id']);
        //     $data['consignee'] = $_POST['consignee'];
        //     $data['mobile'] = $_POST['mobile'];
        //     $data['consignee'] = $_POST['consignee'];
        //     $data['consignee'] = $_POST['consignee'];
        // }
    }

    // 用户发货地址列表
    public function userAddressList(){
        if(IS_POST){
            $token = $_POST["token"];
            $user = checkUser($token);
            $this->ajaxReturn($user);
            if($user){
                $where['uid'] = $user["uid"];
                $data = $this->adminAddressDb->where($where)->order('address_id')->select();
                if($data){
                    $returnData['status'] = "success";
                    $returnData['code'] = 1;
                    $returnData['data'] = $data;
                    $this->ajaxReturn($returnData);
                }else{
                    $returnData['code'] = -1;
                    $this->ajaxReturn($returnData);
                }
            }else{
                $returnData["code"] = -1;
                $returnData["msg"] = "用户已在其他终端登录";
                $this->ajaxReturn($returnData);
            }
            
        }
    }

    // 客户收货地址编辑
    public function userAddressEdit(){
        if(IS_POST){
            $token = $_POST["token"];
            $user = checkUser($token);
            if($user){
                $where['uid'] = $user["uid"];
                $data = $_POST;
                $result = $this->clientAddressDb->save($data);
                if($result){
                    $returnData['status'] = "success";
                    $returnData['code'] = 1;
                    $returnData["msg"] = "修改成功";
                    $this->ajaxReturn($returnData);
                }else{
                    $returnData['code'] = -1;
                    $returnData["msg"] = "未修改信息";
                    $this->ajaxReturn($returnData);
                }
            }else{
                $ret["code"] = -1;
                $ret["msg"] = "用户已在其他终端登录";
            }
            
        }
    }
    //第三方登录通用接口
    public function loginByThirdparty(){
        if(IS_POST){

            $data["nickname"] = $_POST["nickname"];
            $data["avatar"] = $_POST["avatar"];
            $data["province"] = $_POST["province"];
            $data["city"] = $_POST["city"];
            $data["sex"] = $_POST["sex"];

            $party = "";$bindSource="";
            if($_POST["wechat_openid"]){
                $where["wechat_openid"] = $data["wechat_openid"] = $_POST["wechat_openid"];
                $party = "微信";
                $bindSource = "bindWechat";
            }
            if($_POST["qq_openid"]){
                $where["qq_openid"] = $data["qq_openid"] = $_POST["qq_openid"];
                $party = "QQ";
                $bindSource = "bindQQ";
            }
            if($_POST["sina_openid"]){
                $where["sina_openid"] = $data["sina_openid"] = $_POST["sina_openid"];
                $party = "微博";
                $bindSource = "bindSina";
            }


            $result = $this->connectDb->where($where)->find();
            if($result){
                $adminWhere["userid"] = $result["userid"];
                $userInfo = $this->adminDb->where($adminWhere)->find();
                if($userInfo){
                    $random = rand(0,1000);
                    $token = md5('NONO'.$random.$mobile.time());
                    $updateData['token'] = $token;
                    $updateData['last_date'] = time();
                    $updateData['last_ip'] = ip();
                    //更新登录信息
                    $this->adminDb->where($adminWhere)->save($updateData);
                    //重新获取用户信息
                    $infoData = $this->adminDb->where($adminWhere)->field("password,encrypt",true)->find();
                    if($infoData){
                        $connectInfo = $this->connectDb->where("userid=".$infoData["userid"])->find();
                        $infoData["bindQQ"] = false;
                        $infoData["bindWechat"] = false;
                        $infoData["bindSina"] = false;
                        $connectInfo["wechat_openid"] ? $infoData["bindWechat"] = true : "";
                        $connectInfo["qq_openid"] ? $infoData["bindQQ"] = true : "";
                        $connectInfo["sina_openid"] ? $infoData["bindSina"] = true : "";
                        $ret['code'] = 1;
                        $ret['msg'] = "登录成功";
                        $ret['data'] = $infoData;
                    }else{
                        $ret['code'] = -1;
                        $ret['msg'] = "登录失败->用户信息获取失败";
                    }

                }else{
                    $ret['code'] = -1;
                    $ret['msg'] = "登录失败->用户信息获取失败";
                }
            }else{
                $data['reg_date'] = $data['last_date'] = $data["update_time"] = time();
                $data['reg_ip']  = $data['last_ip'] = ip();
                $random = rand(0,1000);
                $token = md5('NONO'.$random.$data['mobile'].time());
                $data['token'] = $token;
                $addResult = $this->memberDb->add($data);
                if($addResult){
                    $where["userid"] = $addResult;
                    if($this->connectDb->add($where)){
                        $infoData = $this->memberDb->where("userid=".$addResult)->field("password,encrypt",true)->find();
                        if($infoData){
                            $connectInfo = $this->connectDb->where("userid=".$infoData["userid"])->find();
                            $infoData["bindQQ"] = false;
                            $infoData["bindWechat"] = false;
                            $infoData["bindSina"] = false;
                            $connectInfo["wechat_openid"] ? $infoData["bindWechat"] = true : "";
                            $connectInfo["qq_openid"] ? $infoData["bindQQ"] = true : "";
                            $connectInfo["sina_openid"] ? $infoData["bindSina"] = true : "";
                            $ret['code'] = 1;
                            $ret['msg'] = "登录成功";
                            $ret["data"] = $infoData;
                        }else{
                            $ret['code'] = -1;
                            $ret['msg'] = "登录失败->用户信息获取失败";
                        }
                    }else{
                        $ret['code'] = -1;
                        $ret['msg'] = "登录失败->connect写入错误";
                    }
                }else{
                    $ret['code'] = -1;
                    $ret['msg'] = "登录失败->member写入错误";
                }
            }
            $this->ajaxReturn($ret);
        }
    }
    //用户绑定
    public function bindAdmin(){
        if(IS_POST){
            $wechat_openid = $_POST["wechat_openid"];
            $qq_openid = $_POST["qq_openid"];
            $sina_openid = $_POST["sina_openid"];

            $party = "";$bindSource="";
            if($_POST["wechat_openid"]){
                $where["wechat_openid"] = $_POST["wechat_openid"];
                $party = "微信";
                $bindSource = "bindWechat";
            }
            if($_POST["qq_openid"]){
                $where["qq_openid"] = $_POST["qq_openid"];
                $party = "QQ";
                $bindSource = "bindQQ";
            }
            if($_POST["sina_openid"]){
                $where["sina_openid"] = $_POST["sina_openid"];
                $party = "微博";
                $bindSource = "bindSina";
            }

            $token = $_POST["token"];
            $user = checkUser($token);
            if($user){
                $where["userid"] = $user["userid"];
                if($this->connectDb->where($where)->find()){
                    $ret["code"] = -1;
                    $ret["msg"] = $party."已绑定";
                    $ret[$bindSource] = false;
                }else{
                    $connectResult = $this->connectDb->where("userid=".$user["userid"])->find();
                    if($connectResult){
                        $result = $this->connectDb->save($where);
                        if($result){
                            $ret["code"] = 1;
                            $ret["msg"] = $party."绑定成功";
                            $ret[$bindSource] = true;
                        }
                    }else{
                        if($this->connectDb->add($where)){
                            $ret["code"] = 1;
                            $ret["msg"] = $party."绑定成功";
                            $ret[$bindSource] = true;
                        }
                    }
                }
            }else{
                $ret["code"] = 0;
                $ret["msg"] = "用户已在其他终端登录";
            }
            $this->ajaxReturn($ret);
        }
    }
    //意见反馈
    public function suggest(){
        if(IS_POST){
            $token = $_POST["token"];

            // $data["title"] = $_POST["title"];
            $data["content"] = $_POST["content"];
            $data["mobile"] = $_POST["mobile"];
            $data["addtime"] = time();
            if($token){
                $user = checkUser($token);
                $user ? $data["userid"] = $user["userid"] : "";
            }
            if($this->suggestDb->add($data)){
                $ret["code"] = 1;
                $ret["msg"] = "提交成功";
            }else{
                $ret["code"] = -1;
                $ret["msg"] = "提交失败";
            }
            $this->ajaxReturn($ret);
        }
    }
    //各种数量
    public function getCount(){
        if(IS_POST){
            $token = $_POST["token"];
            $user = checkUser($token);
            if($user){
                $fansCount = 0;
                $attentionCount = 0;
                $attentionCount = $this->attentionDb->where("userid=".$user["userid"])->count();
                $fansCount = $this->attentionDb->where("attention_userid=".$user["userid"])->count();
                $ret["code"] = 1;
                $ret["msg"] = "获取成功";
                $ret["fansCount"] = $fansCount;
                $ret["attentionCount"] = $attentionCount;
            }else{
                $ret["code"] = 0;
                $ret["msg"] = "用户已在其他终端登录";
            }
            $this->ajaxReturn($ret);
        }
    }
    //获取未读消息数量
    public function getLetterCount(){
        if(IS_POST){
            $token = $_POST["token"];
            $user = checkUser($token);
            if($user){
                $where["receive_userid"] = $user["userid"];
                $where["status"] = 0;
                $letterCount = $this->letterDb->where($where)->count();
                $ret["code"] = 1;
                $ret["msg"] = "未读消息数量获取成功";
                $ret["count"] = $letterCount;
            }else{
                $ret["code"] = 0;
                $ret["msg"] = "用户已在其他终端登录";
            }
            $this->ajaxReturn($ret);
        }
    }
    //获取消息列表
    public function getLetterList(){
        if(IS_POST){
            $pageSize = $_POST["pageSize"] ? $_POST["pageSize"] : 20;
            $pageNum = $_POST["pageNum"] ? $_POST["pageNum"] : 1;
            $startCount = ($pageNum - 1) * $pageSize;

            $token = $_POST["token"];
            $user = checkUser($token);
            if($user){
                $query = "m.receive_userid=".$user["userid"]." and u.userid = m.userid";
                $where["receive_userid"] = $user["userid"];
                $letterList = $this->letterDb
                    ->table("__MEMBER_LETTER__ m,__MEMBER__ u")
                    ->where($query)
                    ->field("m.letter_id,m.userid,m.letter_content,m.addtime,m.letter_status,u.avatar,u.nickname,u.province,u.city,u.area")
                    ->order("m.letter_status asc,m.addtime desc")
                    ->limit($startCount,$pageSize)
                    ->select();
                if($letterList){
                    foreach ($letterList as $i => $v) {
                        $diff = time() - (int) substr($v["addtime"],0,10);
                        $letterList[$i]["time"] = calcTime($diff);
                    }
                    $ret["code"] = 1;
                    $ret["msg"] = "消息列表获取成功";
                    $ret["data"] = $letterList;
                }else{
                    $ret["code"] = -1;
                    $ret["msg"] = $pageNum == 1 ? "无消息内容" : "已无更多消息内容";
                }
            }else{
                $ret["code"] = 0;
                $ret["msg"] = "用户已在其他终端登录";
            }
            $this->ajaxReturn($ret);
        }
    }
    //发送消息
    public function sendLetter(){
        if(IS_POST){
            $token = $_POST["token"];
            $user = checkUser($token);
            if($user){
                $data["send_letter_id"] = $_POST["letter_id"];
                $data["userid"] = $user["userid"];
                $data["letter_content"] = $_POST["letter_content"];
                $data["receive_userid"] = $_POST["userid"];
                $data["addtime"] = time();
                $data["letter_status"] = 0;
                $result = $this->letterDb->add($data);
                if($result){
                    // jpushMsg('letterPush',array("userid"=>$userid['userid'],'receive_userid'=>$data["receive_userid"],'content'=>$data["letter_content"],'letter_id'=>$result));
                    $ret["code"] = 1;
                    $ret["msg"] = "消息发送成功";
                }else{
                    $ret["code"] = -1;
                    $ret["msg"] = "消息发送失败";
                }
            }else{
                $ret["code"] = 0;
                $ret["msg"] = "用户已在其他终端登录";
            }
            $this->ajaxReturn($ret);
        }
    }
    //删除消息
    public function delLetter(){
        if(IS_POST){
            $token = $_POST["token"];
            $user = checkUser($token);
            if($user){
                $where["receive_userid"] = $user["userid"];
                $where["letter_id"] = $_POST["letter_id"];
                if($this->letterDb->where($where)->delete()){
                    $ret["code"] = 1;
                    $ret["msg"] = "消息删除成功";
                }else{
                    $ret["code"] = -1;
                    $ret["msg"] = "消息删除失败";
                }
            }else{
                $ret["code"] = 0;
                $ret["msg"] = "用户已在其他终端登录";
            }
            $this->ajaxReturn($ret);
        }
    }
    //标记消息为已读
    public function readLetterInfo(){
        if(IS_POST){
            $token = $_POST["token"];
            $user = checkUser($token);
            if($user){
                $upData["letter_status"] = 1;
                $upData["letter_id"] = $_POST["letter_id"];
                if($this->letterDb->save($upData)){
                    $ret["code"] = 1;
                    $ret["msg"] = "标记成功";
                }else{
                    $ret["code"] = -1;
                    $ret["msg"] = "标记失败";
                }
            }else{
                $ret["code"] = 0;
                $ret["msg"] = "用户已在其他终端登录";
            }
            $this->ajaxReturn($ret);
        }
    }
    //获取消息详情
    public function getLetterInfo(){
        if(IS_POST){
            $token = $_POST["token"];
            $user = checkUser($token);
            if($user){
                $where["receive_userid"] = $user["userid"];
                $where["letter_id"] = $_POST["letter_id"];
                $letterStatus = $_POST["letter_status"];
                $letterInfo = $this->letterDb->where($where)->field("userid,letter_id,letter_content,addtime")->find();
                if($letterInfo){
                    $userInfo = $this->memberDb->where("userid=".$letterInfo["userid"])->field("nickname,avatar,signature,province,city,area")->find();
                    if($userInfo){
                        $letterInfo["avatar"] = $userInfo["avatar"];
                        $letterInfo["nickname"] = $userInfo["nickname"];
                        $letterInfo["signature"] = $userInfo["signature"];
                        $letterInfo["province"] = $userInfo["province"];
                        $letterInfo["city"] = $userInfo["city"];
                        $letterInfo["area"] = $userInfo["area"];
                    }
                    if($letterStatus == 0){
                        $upData["letter_status"] = 1;
                        $upData["letter_id"] = $letterInfo["letter_id"];
                        if($this->letterDb->save($upData)){
                            $letterStatus = 1;
                        }
                    }
                    $letterInfo["time"] = date("Y-m-d H:s",$letterInfo["addtime"]);
                    $ret["code"] = 1;
                    $ret["msg"] = "消息内容获取成功";
                    $ret["data"] = $letterInfo;
                }else{
                    $ret["code"] = -1;
                    $ret["msg"] = "消息内容获取失败";
                }

            }else{
                $ret["code"] = 0;
                $ret["msg"] = "用户已在其他终端登录";
            }
            $this->ajaxReturn($ret);
        }
    }
    // 更新用户极光推送ID
    public function updateMemberRegistrationId(){
        if(IS_POST){
            $token = $_POST["token"];
            $user = checkUser($token);
            if($user){
                $where["userid"] = $user["userid"];
                $data["jpush_registration_id"] = $_POST["registrationId"];
                $this->memberDb->where($where)->save($data);
            }
        }
    }
}
