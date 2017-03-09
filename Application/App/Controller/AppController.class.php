<?php
// App接口公共控制器 AppController
namespace App\Controller;
use Think\Controller\RestController;
class AppController extends RestController {
    // 自动加载的东西
    function _initialize() { }

    //请求方式校验
    protected function requestMethodValidate($type){
        if($type != "GET" && $type != "POST"){
            $ret["code"] = 400;
            $ret["msg"] = "不支持的请求方式!";
            $this->response($ret,"json");
            exit();
        }
    }

    //验证用户token
    protected function validateAdmin($token = null){
        $adminDb = M("Admin");
        $adminInfo = $adminDb->where("token = '".$token."'")->field("password,encrypt",true)->find();
        if($adminInfo){
            if($adminInfo["status"] == 0){
                $ret["code"] = "0";
                $ret["msg"] = "用户已被锁定";
                $this->response($ret,"json");
                exit;
            }
            return $adminInfo;
        }else{
            $ret["code"] = "0";
            $ret["msg"] = "用户已在其他终端登录";
            $this->response($ret,"json");
            exit;
        }
    }
    //通用ajax返回
    function httpResponse($code,$msg,$data,$type = "json"){
        $ret["code"] = $code;
        $ret["msg"] = $msg;
        if($data){
            $ret["data"] = $data;
        }
        $this->response($ret,$type);
        exit();
    }
}
?>