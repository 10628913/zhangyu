<?php
namespace App\Controller;
use Think\Controller;
class UserController extends Controller {
    public function __construct(){
        parent::__construct();
        $this->memberDb = M('Member');
        $this->smsDb = M('Sms');
        $this->configDb = M("Module_extend");
        $this->connectDb = M("Member_connect");
        $this->suggestDb = M("Member_suggest");
        $this->attentionDb = M("Member_attention");
        $this->letterDb = M("Member_letter");
    }
    public function _initialize() {
        vendor('Ucpaas.Ucpaas');
    }
    // 修改密码
    public function forgetPassword(){
        if(IS_POST){
            $mobile = $_POST['mobile'];
            $code = $_POST['code'];
            $whereCode['mobile'] = $mobile;
            $whereCode['code'] = $code;
            $result = $this->smsDb->where($whereCode)->find();

            if($result){
                //距离验证码发送时间超过5分钟.判定验证码超时
                $sendtime = $result["sendtime"];
                $diff = time() - (int) substr($sendtime,0,10);
                $min = floor($diff/60);
                if($result["status"] == 1){
                    if($min > 5){
                        $returnData["code"] = -1;
                        $returnData["msg"] = "验证码超时";
                        $this->ajaxReturn($returnData);
                    }else{
                        $whereMember['mobile'] = $mobile;
                        $password = password($_POST['password']);
                        $passwordData['password'] = $password['password'];
                        $passwordData['encrypt'] = $password['encrypt'];
                        if($this->memberDb->where($whereMember)->save($passwordData)){
                            $returnData['code'] = 1;
                            $returnData['msg'] = "密码修改成功";
                            $upWhere["id"] = $result["id"];
                            $upWhere["status"] = 0;
                            $this->smsDb->save($upWhere);
                        }else{
                            $returnData["code"] = -1;
                            $returnData["msg"] = "密码修改失败";
                        }
                    }
                }else{
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
    // 忘记密码
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
            $templateId = "25050";
            $param="".$code.",5";

            //检查手机号是否存在
            $memberInfo = $this->memberDb->where("mobile=".$mobile)->find();
            if($memberInfo){
                $resultArr = $ucpass->templateSMS($appId,$to,$templateId,$param);
                $resultArr = json_decode($resultArr);
                $smsCode = $resultArr->resp->respCode;
                if($smsCode=='000000'){
                    $data['msg'] = "发送成功";
                    //写入短信记录
                    $smsData['mobile'] = $mobile;
                    $smsData['code'] = $code;
                    $smsData['sendtime'] = time();
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
            $whereMember['mobile'] = $mobile;
            //根据用户名检查用户是否存在
            $info = $this->memberDb->where($whereMember)->find();
            $userid = $info['userid'];
            $data = array();
            if(!$info){
                $data['code'] = -1;
                $data['msg'] = "用户不存在";
            }else{
                //存在、验证密码是否正确
                $where["userid"] = $info["userid"];
                $password = password($_POST['password'],$info['encrypt']);
                if($password==$info['password']){
                    $random = rand(0,1000);
                    $token = md5('NONO'.$random.$mobile.time());
                    $updateData['token'] = $token;
                    $updateData['last_date'] = time();
                    $updateData['last_ip'] = ip();
                    $this->memberDb->where($whereMember)->save($updateData);
                    $infoData = $this->memberDb->where($where)->field("password,encrypt",true)->find();

                    $connectResult = $this->connectDb->where("userid=".$infoData["userid"])->find();

                    $infoData["bindQQ"] = false;
                    $infoData["bindWechat"] = false;
                    $infoData["bindSina"] = false;

                    $connectResult["wechat_openid"] ? $infoData["bindQQ"] = true : "";
                    $connectResult["qq_openid"] ? $infoData["bindWechat"] = true : "";
                    $connectResult["sina_openid"] ? $infoData["bindSina"] = true : "";

                    $data['code'] = 1;
                    $data['msg'] = "登录成功";
                    $data['data'] = $infoData;
                }else{
                    $data['code'] = -1;
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
                $data['sex'] = $_POST['sex'];
                $data['birthday'] = $_POST['birthday'];
                $data['signature'] = $_POST['signature'];
                $password = password($_POST['password']);
                $data['password'] = $password['password'];
                $data['encrypt'] = $password['encrypt'];
                $data['avatar'] = $_POST['avatar'];
                $data['reg_date'] = $data['last_date'] = $data["update_time"] = time();
                $data['reg_ip']  = $data['last_ip'] = ip();
                $random = rand(0,1000);
                $token = md5('NONO'.$random.$data['mobile'].time());
                $data['token'] = $token;
                $result = $this->memberDb->data($data)->add();
                if($result){
                    //数据返回
                    $where["userid"] = $result;
                    $returnData = array();
                    $returnData['code'] = 1;
                    $returnData['msg'] = "注册成功";
                    $userData = $this->memberDb->where($where)->field("password,encrypt",true)->find();
                    $returnData["data"] = $userData;
                }else{
                    $returnData = array();
                    $returnData['code'] = -1;
                    $returnData['msg'] = "注册失败";
                }
            }else{
                $returnData = array();
                $returnData['code'] = -1;
                $returnData['msg'] = "用户名已经存在";
            }
            $this->ajaxReturn($returnData);
        }
    }
    public function sendSms(){
        if(IS_POST){
            $info = $this->configDb->find();
            $options['accountsid'] = $info["ucpaas_account_sid"];
            $options['token'] = $info["ucpaas_auth_token"];
            //初始化 $options必填
            $mobile = $_POST['mobile'];
            $code = random(4,'123456789');
            $ucpass = new \Ucpaas($options);
            $appId = $info["ucpaas_app_id"];
            $to = "".$mobile."";
            $templateId = "25050";
            $param="".$code.",5";

            //检查手机号是否被注册
            $memberInfo = $this->memberDb->where("mobile=".$mobile)->find();
            if($memberInfo){
                $data['code'] = -1;
                $data['msg'] = "手机号码已经注册";
            }else{
                $resultArr = $ucpass->templateSMS($appId,$to,$templateId,$param);
                $resultArr = json_decode($resultArr);
                $smsCode = $resultArr->resp->respCode;
                if($smsCode=='000000'){
                    $data['msg'] = "发送成功";
                    //写入短信记录
                    $smsData['mobile'] = $mobile;
                    $smsData['code'] = $code;
                    $smsData['sendtime'] = time();
                    $this->smsDb->data($smsData)->add();
                }else if($smsCode=='105122'){
                    $data['msg'] = "发送数量超出限制";
                }else{
                    $data['msg'] = "发送失败";
                }
                $data['code'] = $smsCode;
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
                if($result["status"] == 0){
                    $ret["code"] = -1;
                    $ret["msg"] = "验证码失效";
                    $this->ajaxReturn($ret);
                }
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
                    $upWhere["status"] = 0;
                    $this->smsDb->save($upWhere);
                }
            }else{
                $data['code'] = -1;
                $data['msg'] = "验证码错误";
            }
            $this->ajaxReturn($data);
        }
    }

    //修改信息
    public function changeUserInfo(){
        if(IS_POST){
            $token = $_POST["token"];
            $info = checkUser($token);
            if($info){
                $where["userid"] = $saveData["userid"] = $info["userid"];
                $nickname = $_POST["nickname"];
                $sex = $_POST["sex"];
                $birthday = $_POST["birthday"];
                $signature = $_POST["signature"];
                $nickname ? $saveData["nickname"] = $nickname : "";
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
                if($this->memberDb->save($saveData)){
                    $ret["code"] = 1;
                    $ret["msg"] = "修改成功";
                    $info = $this->memberDb->where($where)->field("password,encrypt",true)->find();
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
                $data["userid"] = $user["userid"];
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
                    if($this->memberDb->save($data)){
                        $ret["code"] = 1;
                        $ret["msg"] = "头像修改成功";
                        $ret["url"] = $thumb;
                    }else{
                        $ret["code"] = -1;
                        $ret["msg"] = "头像修改失败";
                    }
                }
            }else{
                $ret["code"] = 0;
                $ret["msg"] = "用户已在其他终端登录";
            }
            $this->ajaxReturn($ret);
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
                $memberWhere["userid"] = $result["userid"];
                $userInfo = $this->memberDb->where($memberWhere)->find();
                if($userInfo){
                    $random = rand(0,1000);
                    $token = md5('NONO'.$random.$mobile.time());
                    $updateData['token'] = $token;
                    $updateData['last_date'] = time();
                    $updateData['last_ip'] = ip();
                    //更新登录信息
                    $this->memberDb->where($memberWhere)->save($updateData);
                    //重新获取用户信息
                    $infoData = $this->memberDb->where($memberWhere)->field("password,encrypt",true)->find();
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
    public function bindMember(){
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
    //我的关注
    public function getAttention(){
        if(IS_POST){
            $token = $_POST["token"];

            $pageSize = $_POST["pageSize"] ? $_POST["pageSize"] : 20;
            $pageNum = $_POST["pageNum"] ? $_POST["pageNum"] : 1;
            $startCount = ($pageNum - 1) * $pageSize;

            $user = checkUser($token);
            if($user){
                $attentionList = $this->attentionDb
                    ->table("__MEMBER_ATTENTION__ t,__MEMBER__ m")
                    ->where("t.attention_userid = m.userid and t.userid=".$user["userid"])
                    ->field("m.userid,m.nickname,m.avatar,m.signature")
                    ->limit($startCount,$pageSize)
                    ->select();
                    // $atentionList = $this->attentionDb
                    //     ->join("left join __MEMBER__ m on m.userid = __MEMBER_ATTENTION__.attention_userid")
                    //     ->where("fs_member_attention.userid=".$user["userid"])
                    //     ->field("m.userid,m.nickname,m.avatar")
                    //     ->limit($startCount,$pageSize)
                    //     ->select();
                if($attentionList){
                    $ret["code"] = 1;
                    $ret["msg"] = "我的关注好友列表获取成功";
                    $ret["data"] = $attentionList;
                }else{
                    $ret["code"] = -1;
                    $ret["msg"] = $pageNum == 1 ? "无关注好友" : "已无更多关注好友";
                }
            }else{
                $ret["code"] = 0;
                $ret["msg"] = "用户已在其他终端登录";
            }
            $this->ajaxReturn($ret);
        }
    }
    //我的粉丝
    public function getFansList(){
        if(IS_POST){
            $token = $_POST["token"];

            $pageSize = $_POST["pageSize"] ? $_POST["pageSize"] : 20;
            $pageNum = $_POST["pageNum"] ? $_POST["pageNum"] : 1;
            $startCount = ($pageNum - 1) * $pageSize;

            $user = checkUser($token);
            if($user){
                $attentionList = $this->attentionDb
                    ->table("__MEMBER_ATTENTION__ t,__MEMBER__ m")
                    ->where("t.userid = m.userid and t.attention_userid=".$user["userid"])
                    ->field("m.userid,m.nickname,m.avatar,m.signature")
                    ->limit($startCount,$pageSize)
                    ->select();
                if($attentionList){

                    foreach ($attentionList as $i => $v) {
                        $attentionList[$i]["attentionStatus"] = false;
                        if($this->attentionDb->where("userid=".$user["userid"]." and attention_userid=".$v["userid"])){
                            $attentionList[$i]["attentionStatus"] = true;
                        }
                    }
                    $ret["code"] = 1;
                    $ret["msg"] = "我的粉丝列表获取成功";
                    $ret["data"] = $attentionList;
                }else{
                    $ret["code"] = -1;
                    $ret["msg"] = $pageNum == 1 ? "还木有粉丝" : "已无更多粉丝";
                }
            }else{
                $ret["code"] = 0;
                $ret["msg"] = "用户已在其他终端登录";
            }
            $this->ajaxReturn($ret);
        }
    }
    //关注好友
    public function attentionFriend(){
        if(IS_POST){
            $token = $_POST["token"];
            $user = checkUser($token);
            if($user){
                $data["attention_userid"] = $_POST["userid"];
                $data["userid"] = $user["userid"];
                if($this->attentionDb->where($data)->find()){
                    if($this->attentionDb->where($data)->delete()){
                        $ret["code"] = 2;
                        $ret["msg"] = "取消关注成功";
                    }else{
                        $ret["code"] = -1;
                        $ret["msg"] = "取消关注失败";
                    }
                }else{
                    if($this->attentionDb->add($data)){
                        $ret["code"] = 1;
                        $ret["msg"] = "关注成功";
                    }else{
                        $ret["code"] = -1;
                        $ret["msg"] = "关注失败";
                    }
                }
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
