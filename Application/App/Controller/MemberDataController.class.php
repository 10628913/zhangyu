<?php
namespace App\Controller;
use Think\Controller;
class MemberDataController extends Controller {
    public function _initialize() {
        vendor('Ucpaas.Ucpaas');
        vendor('RongCloud.ServerAPI');
    }
    public function __construct(){
        parent::__construct();
        $this->memberDb = M('Member');
        $this->courierDb = M("Courier");
        $this->locationDb = M('Member_location');
    }
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

    /**
    *更新用户位置
    */
    public function updateMemberLocation(){
        if(IS_POST){
            $userid = intval($_POST['userid']);
            $where['userid'] = $userid;
            $locationInfo = $this->locationDb->where($where)->find();
            $data['lat'] = $_POST['lat'];
            $data['lon'] = $_POST['lon'];
            $data['update_time'] = time();
            if($locationInfo){
                $this->locationDb->where($where)->save($data);
            }else{
                $data['userid'] = $userid;
                $this->locationDb->data($data)->add();
            }
        }
    }
    public function updateMemberInfo(){
        if(IS_POST){
            $userid = intval($_POST['userid']);
            $where['userid'] = $userid;
            $data['avatar'] = $_POST['avatar'];
            $data['nickname'] = $_POST['nickname'];
            $data['update_time'] = time();
            $result = $this->memberDb->where($where)->save($data);
            if($result){
                $returnData['msg'] = "修改成功";
                $this->ajaxReturn($returnData);
            }
        }
    }
    public function updateMemberJpush(){
        if(IS_POST){
            $userid = intval($_POST['userid']);
            $where['userid'] = $userid;
            $data['jpush_registration_id'] = $_POST['registrationId'];
            $this->memberDb->where($where)->save($data);
        }
    }
    public function getMemberAssetInfo(){
        if(IS_POST){
            $userid = intval($_POST['userid']);
            $where['userid'] = $userid;
            $memberInfo = $this->memberDb->where($where)->field('point,amount')->find();
            if($memberInfo){
                $returnData['point'] = $memberInfo['point'];
                $returnData['amount'] = $memberInfo['amount'];
                $this->ajaxReturn($returnData);
            }
        }
    }
}