<?php
namespace App\Controller;
use Think\Controller;
class UserController extends AppController {
    function _initialize() {
        parent::_initialize();
        parent::requestMethodValidate(REQUEST_METHOD);
        $this->adminInfo = parent::validateAdmin(I("token"));
        vendor('Ucpaas.Ucpaas');
    }
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
    // public function _initialize() {
    //     vendor('Ucpaas.Ucpaas');
    // }
    // 修改密码
    public function forgetPassword(){

        $mobile = I('mobile');
        $code = I('code');;

        $partten = "/^1[3-9]\d{9}$/";
        if(!$mobile || !preg_match($partten,$mobile)){
            $this->httpResponse(-1,"请输入正确的手机号");
        }
        if(!$code){
            $this->httpResponse(-1,"请输入验证码");
        }

        $whereCode['mobile'] = $mobile;
        $whereCode['code'] = $code;
        $result = $this->smsDb->where($whereCode)->find();

        if($result){
            //距离验证码发送时间超过5分钟.判定验证码超时
            $sendtime = $result["sendtime"];
            $diff = time() - (int) substr($sendtime,0,10);
            $min = floor($diff/60);
            if($result["status"] == 2){
                if($min > 5){
                    $this->httpResponse(-1,"验证码超时");
                }else{
                    $whereAdmin['mobile'] = $mobile;
                    $password = password(I('password'));
                    $passwordData['password'] = $password['password'];
                    $passwordData['encrypt'] = $password['encrypt'];
                    if($this->adminDb->where($whereAdmin)->save($passwordData)){
                        $upWhere["id"] = $result["id"];
                        $upWhere["status"] = 3;
                        $this->smsDb->save($upWhere);
                        $this->httpResponse(1,"密码修改成功");
                    }else{
                        $this->httpResponse(-1,"密码修改失败");
                    }
                }
            }else{
                $this->httpResponse(-1,"验证码失效");
            }
        }else{
            $this->httpResponse(-1,"验证码错误");
        }
    }
    // 发送忘记密码验证码
    public function sendSmsByForgetPassword(){

        $info = $this->configDb->find();
        $options['accountsid'] = $info["ucpaas_account_sid"];
        $options['token'] = $info["ucpaas_auth_token"];
        $appId = $info["ucpaas_app_id"];
        //初始化 $options必填
        $mobile = I('mobile');
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
                $this->httpResponse(-1,"60s内请勿重复发送");
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
                $smsData['status'] = 1;
                $this->smsDb->data($smsData)->add();
                $this->httpResponse(1,"发送成功");
            }else if($smsCode=='105122'){
                $this->httpResponse(-1,"发送数量超出限制");
            }else{
                $this->httpResponse(-1,"发送失败");
            }
            $this->httpResponse(-1,"验证码错误状态",$smsCode);
        }else{
            $this->httpResponse(-1,"手机号码未注册");
        }
    }
    //登录
    public function login()
    {

        $mobile = I('mobile');
        $password = I('password');

        $partten = "/^1[3-9]\d{9}$/";
        if(!$mobile || !preg_match($partten,$mobile)){
            $this->httpResponse(-1,"请输入正确手机号码");
        }
        if(!$password){
            $this->httpResponse(-1,"请输入密码");
        }

        $whereAdmin['mobile'] = $mobile;
        //根据用户名检查用户是否存在
        $info = $this->adminDb->where($whereAdmin)->find();
        $whereAdminIs['mobile'] = $mobile;
        $whereAdminIs['is_register'] = 0;
        $infoIs = $this->registerDb->where($whereAdminIs)->find();
        $uid = $info['uid'];
        $data = array();
        if(!$info){
            $this->httpResponse(-1,"用户不存在");
        }elseif($infoIs){
            $this->httpResponse(-1,"用户未通过审核，请联系管理员");
        }else{
            //存在、验证密码是否正确
            $where["uid"] = $info["uid"];
            $password = password(I('password'),$info['encrypt']);
            if($password==$info['password']){
                $random = rand(0,1000);
                $token = md5('NONO'.$random.$mobile.time());
                $updateData['token'] = $token;
                $updateData['last_date'] = time();
                $updateData['last_ip'] = ip();
                $this->adminDb->where($whereAdmin)->save($updateData);
                $infoData = $this->adminDb->where($where)->field("password,encrypt",true)->find();
                $this->httpResponse(1,"登录成功",$infoData);
            }else{
                $this->httpResponse(-1,"密码错误");
            }
        }
    }
    //会员注册
    public function register()
    {

        $mobile = I('mobile');
        $realname = I('realname');
        $password = I('password');
        $data['mobile'] = $mobile;
        $code = I('code');
        $partten = "/^1[3-9]\d{9}$/";
        if(!$mobile || !preg_match($partten,$mobile))
        {
            $this->httpResponse(-1,"输入正确的手机号");
        }

        $whereCode['mobile'] = $mobile;
        $whereCode['code'] = $code;
        $codeinfo = $this->smsDb->where($whereCode)->find();
        if(!$code)
        {
            $this->httpResponse(-1,"请输入验证码");
        }
        if($codeinfo['status'] != 2)
        {
            $this->httpResponse(-1,"验证码失效");
        }
        if(!$realname)
        {
            $this->httpResponse(-1,"请输入昵称");
        }
        if(!$password)
        {
            $this->httpResponse(-1,"请输入密码");
        }
        $whereMobile['mobile'] = $mobile;
        $isInto = $this->registerDb->where($whereMobile)->find();
        if($isInto && $isInto['is_register'] == '0'){
            $this->httpResponse(-1,"已注册，请等待管理员审核");
        }
        //判断用户名是否存在
        $isIn = $this->adminDb->where($whereMobile)->find();
        if(!$isIn)
        {
            //不存在
            $data['realname'] = $realname;
            $data['is_register'] = '0';
            $data['sex'] = I('sex');
            $data['birthday'] = I('birthday');
            $data['signature'] = I('signature');
            $password = password(I('password'));
            $data['password'] = $password['password'];
            $data['encrypt'] = $password['encrypt'];
            $data['avatar'] = I('avatar');
            $data['reg_date'] = $data['last_date'] = $data["update_date"] = time();
            $data['reg_ip']  = $data['last_ip'] = ip();
            $random = rand(0,1000);
            $token = md5('NONO'.$random.$data['mobile'].time());
            $data['token'] = $token;
            $result = $this->registerDb->data($data)->add();
            if($result)
            {
                //数据返回
                $where["id"] = $result;
                $smsData['status'] = 3;
                $this->smsDb ->where($whereCode)->save($smsData);
                $userData = $this->registerDb->where($where)->field("password,encrypt",true)->find();
                $this->httpResponse(1,"注册完成",$userData);
            }else
            {
                $this->httpResponse(-1,"注册失败");
            }
        }else
        {
            $this->httpResponse(-1,"用户名已经存在");
        }
    }
    // 发送注册验证码
    public function sendSms(){

        $info = $this->configDb->find();
        $options['accountsid'] = $info["ucpaas_account_sid"];
        $options['token'] = $info["ucpaas_auth_token"];
        //初始化 $options必填
        $mobile = I('mobile');
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
                $this->httpResponse(-1,"60s内请勿重复发送");
            }
        }

        //检查手机号是否被注册
        $adminInfo = $this->adminDb->where("mobile=".$mobile)->find();
        if($adminInfo){
            $this->httpResponse(-1,"手机号码已经注册");
        }else{
            $resultArr = $ucpass->templateSMS($appId,$to,$templateId,$param);
            $resultArr = json_decode($resultArr);
            $smsCode = $resultArr->resp->respCode;
            if($smsCode=='000000'){
                //写入短信记录
                $smsData['mobile'] = $mobile;
                $smsData['code'] = $code;
                $smsData['sendtime'] = time();
                $smsData['status'] = 1;
                $this->smsDb->data($smsData)->add();
                $this->httpResponse(1,"发送成功");
            }else if($smsCode=='105122'){
                $this->httpResponse(-1,"发送数量超出限制");
            }else{
                $this->httpResponse(-1,"发送失败错误码",$smsCode);
            }
        }
    }
    //检查短信验证码
    public function checkSmsCode(){
            
        $where["mobile"] = I('mobile');
        $where["code"] = I('code');

        $result = $this->smsDb->where($where)->find();
        if($result){
            $sendtime = $result["sendtime"];
            $diff = time() - (int) substr($sendtime,0,10);
            $min = floor($diff/60);
            if($min > 5){
                $this->httpResponse(-1,"验证码超时");
            }else{
                $upWhere["id"] = $result["id"];
                $upWhere["status"] = 2;
                $this->smsDb->save($upWhere);
                $this->httpResponse(1,"校检通过");
            }
        }else{
            $this->httpResponse(-1,"验证码错误");
        }
    }

    //修改用户信息
    public function changeUserInfo(){

        $where["uid"] = $saveData["uid"] = $this->adminInfo['uid'];
        $realname = I('realname');
        $sex = I('sex');
        $birthday = I('birthday');
        $signature = I('signature');
        $realname ? $saveData["realname"] = $realname : "";
        I('avatar') ? $saveData["avatar"] = I('avatar') : "";
        $sex ? $saveData["sex"] = $sex : "";
        $signature ? $saveData["signature"] = $signature : "";
        $birthday ? $saveData["birthday"] = $birthday : "";
        I('lat') ? $saveData["lat"] = I('lat') : "";
        I('lon') ? $saveData["lon"] = I('lon') : "";
        I('province') ? $saveData["province"] = I('province') : "";
        I('city') ? $saveData["city"] = I('city') : "";
        I('area') ? $saveData["area"] = I('area')  : "";
        $saveData["update_time"] = time();
        if($this->adminDb->save($saveData)){
            $info = $this->adminDb->where($where)->field("password,encrypt",true)->find();
            $this->httpResponse(1,"修改成功",$info);
        }else{
            $this->httpResponse(-1,"信息无修改");
        }
    }

    //修改头像
    public function changeUserAvatar(){

        $data["uid"] = $this->adminInfo['uid'];
        $upload = new \Think\Upload();
        $upload->maxSize = 5242880 ;
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');
        $info = $upload->upload();
        if(!$info) {
            $this->error($upload->getError());
            $this->httpResponse(-1,"无上传图片");
        }else{
            $imageUrl = C("SITE_URL").C("UPLOAD_PATH").$info["file"]["savepath"].$info["file"]["savename"];
            $thumb = thumb($imageUrl,200,200);
            $path = str_replace(C('SITE_URL'),"",$imageUrl);
            unlink($path);
            $data["avatar"] = $thumb;
            if($this->adminDb->save($data)){
                $this->httpResponse(1,"头像修改成功",$thumb);
            }else{
                $this->httpResponse(-1,"头像修改失败");
            }
        }
    }

    // 用户发货地址添加
    public function addressAdd(){
        // 一手id
        $data["uid"] = $this->adminInfo['uid'];
        //发货人
        $data["consignee"] = I("consignee");
        //发货人电话
        $data["mobile"] = I("mobile");
        //公司
        $data["company"] = I("company");
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

        $where['uid'] = $data['uid'];
        $count = $this->adminAddressDb->where($where)->count();
        if($count){
            $this->httpResponse(-1,"只能有一个发货地址"); 
        }
        if(!$data['consignee']){
            $this->httpResponse(-1,"请输入发货人姓名");
        }
        if(!$data['mobile']){
            $this->httpResponse(-1,"请输入手机号");
        }
        $result = $this->adminAddressDb->add($data);
        if($result){
            $this->httpResponse(1,"添加成功");
        }else{
            $this->httpResponse(-1,"添加失败");
        }
    }
    // 用户发货地址列表
    public function userAddressList(){

        $where['uid'] = $this->adminInfo['uid'];
        $data = $this->adminAddressDb->where($where)->find();
        if($data){
            $this->httpResponse(1,"获取列表成功",$data);
        }else{
            $this->httpResponse(-1,"获取列表失败");
        }
    }

    // 用户发货地址编辑
    public function userAddressEdit(){

        // 一手id
        $data["uid"] = $this->adminInfo['uid'];
        // 地址id
        $data['address_id'] = I("address_id");
        //发货人
        $data["consignee"] = I("consignee");
        //发货人电话
        $data["mobile"] = I("mobile");
        //公司
        $data["company"] = I("company");
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

        $where['uid'] = $data['uid'];
        $where['address_id'] = $data['address_id'];
        $result = $this->adminAddressDb->where($where)->save($data);
        if($result){
            $this->httpResponse(1,"修改成功");
        }else{
            $this->httpResponse(-1,"信息无修改");
        }
    }
}
