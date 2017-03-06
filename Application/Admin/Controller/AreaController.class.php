<?php
// 地区管理
namespace Admin\Controller;
class AreaController extends BaseController {
    public function __construct(){
        parent::__construct();
        $this->areaDb = M('Area');
    }
    public function getAreaChildLists(){
        if(IS_POST){
            $where['parent_id'] = intval($_POST['pid']);
            $childLists = $this->areaDb->where($where)->select();
            if($childLists){
                $this->ajaxReturn($childLists);
            }
        }
    }
    // 获取只有省和市的数组
    public function getProvinceLists(){
        $whereProvince['id'] = array('gt',1);
        $whereProvince['parent_id'] = 0;
        $provinceList = $this->areaDb->where($whereProvince)->select();
        foreach ($provinceList as $key => $v) {
            if($v['id'] <= 5){
                $provinceList[$key]['city'] = array(
                        0 => array(
                            'id' => $v['id'],
                            'name' => $v['name']
                        )
                    );
            }else{
                $provinceList[$key]['city'] = $this->getCitys($v['id']);
            }

        }
        $returnData['hot'] = '';
        $returnData['province'] = $provinceList;
        $this->ajaxReturn($returnData);

    }
    public function getCitys($parent_id){
        $whereCity['parent_id'] = $parent_id;
        $cityList = $this->areaDb->where($whereCity)->select();
        return $cityList;
    }
}