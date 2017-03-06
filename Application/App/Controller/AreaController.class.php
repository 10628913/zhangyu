<?php
/**
* 地区表
*/
namespace App\Controller;
use Think\Controller;
class AreaController extends Controller {
    public function __construct(){
        parent::__construct();
        $this->provinceDb = M('Province');
        $this->cityDb = M('City');
        $this->districtDb = M('District');
        $this->areaDb = M('Area');
    }
    // 获取省
    public function getProvince(){
    	if(IS_POST){
            $data = $this->provinceDb->select();
            $this->ajaxReturn($data);
		}
    }
    // 根据省code获取市
    public function getCity(){
        if(IS_POST){
            $where['province_code'] = intval($_POST['provinceCode']);
            $data = $this->cityDb->where($where)->select();
            $this->ajaxReturn($data);
        }
    }
    // 根据市citycode获取区
    public function getDistrict(){
        if(IS_POST){
            $where['city_code'] = intval($_POST['cityCode']);
            $data = $this->districtDb->where($where)->select();
            if($data){
                $this->ajaxReturn($data);
            }
        }
    }
}