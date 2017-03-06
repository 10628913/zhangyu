<?php
/**
* 房源模块管理
*/
namespace Admin\Controller;
class BuildingController extends BaseController {
    public function __construct(){
        parent::__construct();
        $this->memberDb = M('Member');
        $this->areaDb = M('Area');
        $this->buildingDb = M("Building");
        $this->buildingTagsDb = M('Building_tags');
        $this->buildingTypeDb = M('Building_type');
        $this->buildingHouseStyleDb = M('Building_house_style');
        $this->buildingHouseDb = M('Building_house');
        $this->buildingReserveDb = M('Building_reserve');
        $this->buildingCommentDb = M('Building_comment');
        $this->buildingAttentionDb = M('Building_attention');
        $this->buildingCollectDb = M('Building_collect');
    }
    // 房源首页
    public function index(){
        if(IS_POST){
            if(isset($_POST['dosubmit'])) {
                foreach($_POST['sorts'] as $building_id => $sort) {
                    $where['building_id'] = $building_id;
                    $data['building_sort'] = $sort;
                    $this->buildingDb->where($where)->save($data);
                }
                $this->success("操作成功");
            }
        }else{
            if(isset($_GET['keywords']) && !empty($_GET['keywords'])){
                $whereSearch['building_name'] = array('like',"%".I('keywords')."%");
                $searchInfo['keywords'] = I("keywords");
            }
            $this->assign('searchInfo',$searchInfo);
            $count = $this->buildingDb->where($whereSearch)->count();
            $Page = new \Think\Page($count,20);
            $list = $this->buildingDb->limit($Page->firstRow.','.$Page->listRows)->where($whereSearch)->order('building_sort asc,building_id desc')->select();
            foreach ($list as $key => $v) {
                $whereHouse['building_id'] = $v['building_id'];
                $list[$key]['house_count'] = $this->buildingHouseDb->where($whereHouse)->count();
            }
            $show = $Page->show();
            $this->assign('page',$show);
            $this->assign('list',$list);
            $this->display("building_list");
        }
    }
    // 楼盘添加
    public function buildingAdd(){
        if(IS_POST){
            $data = $_POST['info'];
            $data['building_tags_ids'] = implode(',',$data['building_tags_ids']);
            $data['building_house_style_ids'] = implode(',',$data['building_house_style_ids']);
            if($data['building_photos']){
                $data['building_photos'] = serialize($data['building_photos']);
            }
            $whereBuildType['type_id'] = $data['building_type_id'];
            $data['building_build_type_name'] = $this->buildingTypeDb->where($whereBuildType)->getField("type_name");
            $data['addtime'] = $data['update_time'] = time();
            $data['admin_uid'] = session("admin_uid");
            $result = $this->buildingDb->data($data)->add();
            if($result){
                $this->success("发布成功");
            }else{
                $this->error("发布失败");
            }
        }else{
            //标签
            $buildingTagsList = $this->buildingTagsDb->select();
            $this->assign("buildingTagsList",$buildingTagsList);
            //标签
            $buildingTypeList = $this->buildingTypeDb->select();
            $this->assign("buildingTypeList",$buildingTypeList);
            //房屋样式
            $buildingHouseStyleList = $this->buildingHouseStyleDb->select();
            $this->assign("buildingHouseStyleList",$buildingHouseStyleList);
            // 获取地区
            $whereArea['parent_id'] = 0;
            $whereArea['id'] = array('gt',1);
            $areaList = $this->areaDb->where($whereArea)->order('sort asc, id asc')->select();
            $this->assign("areaList",$areaList);
            // 编辑器
            $editor = new \Org\Editor\Editor;
            $this->assign('editor',$editor);
            $this->display("building_add");
        }
    }
    // 楼盘编辑
    public function buildingEdit(){
        if(IS_POST){
            $where['building_id'] = I('building_id');
            $data = $_POST['info'];
            $data['building_tags_ids'] = implode(',',$data['building_tags_ids']);
            $data['building_house_style_ids'] = implode(',',$data['building_house_style_ids']);
            if($data['building_photos']){
                $data['building_photos'] = serialize($data['building_photos']);
            }
            $whereBuildType['type_id'] = $data['building_type_id'];
            $data['building_build_type_name'] = $this->buildingTypeDb->where($whereBuildType)->getField("type_name");
            $data['update_time'] = time();
            $data['admin_uid'] = session("admin_uid");
            $result = $this->buildingDb->where($where)->save($data);
            $this->success("操作成功");
        }else{
            $where['building_id'] = I('building_id');
            $info = $this->buildingDb->where($where)->find();
            $info['building_tags_ids'] = explode(",",$info['building_tags_ids']);
            $info['building_house_style_ids'] = explode(",",$info['building_house_style_ids']);
            $info['building_photos'] = unserialize($info['building_photos']);
            $this->assign($info);
            //标签
            $buildingTagsList = $this->buildingTagsDb->select();
            $this->assign("buildingTagsList",$buildingTagsList);
            //标签
            $buildingTypeList = $this->buildingTypeDb->select();
            $this->assign("buildingTypeList",$buildingTypeList);
            //房屋样式
            $buildingHouseStyleList = $this->buildingHouseStyleDb->select();
            $this->assign("buildingHouseStyleList",$buildingHouseStyleList);
            // 获取地区
            $whereArea['parent_id'] = 0;
            $whereArea['id'] = array('gt',1);
            $areaList = $this->areaDb->where($whereArea)->order('sort asc, id asc')->select();
            $this->assign("areaList",$areaList);
            // 编辑器
            $editor = new \Org\Editor\Editor;
            $this->assign('editor',$editor);
            $this->display("building_edit");
        }

    }
    // 房源删除
    public function buildingDelete(){
        if(IS_POST){
            $where['building_id'] = I('building_id');
            $this->buildingDb->where($where)->delete();
            $this->buildingHouseDb->where($where)->delete();
            $this->success("操作成功");
        }
    }
    /**---户型管理---**/
    // 户型列表
    public function buildingHouseList(){
        if(IS_POST){
            if(isset($_POST['dosubmit'])) {
                foreach($_POST['sorts'] as $house_id => $sort) {
                    $where['house_id'] = $house_id;
                    $data['house_sort'] = $sort;
                    $this->buildingHouseDb->where($where)->save($data);
                }
                $this->success("操作成功");
            }
        }else{
            $where['building_id'] = I('building_id');
            $list = $this->buildingHouseDb->where($where)->order("house_sort asc,house_id desc")->select();
            $building_name = $this->buildingDb->where($where)->getField("building_name");
            $this->assign('building_id',I('building_id'));
            $this->assign('building_name',$building_name);
            $this->assign('list',$list);
            $this->display("building_house_list");
        }

    }
    // 户型发布
    public function buildingHouseAdd(){
        if(IS_POST){
            $data = $_POST['info'];
            if($data['house_photos']){
                $data['house_photos'] = serialize($data['house_photos']);
            }
            $data['addtime'] = $data['update_time'] = time();
            $data['admin_uid'] = session("admin_uid");
            $result = $this->buildingHouseDb->data($data)->add();
            if($result){
                $this->success("发布成功");
            }else{
                $this->error("发布失败");
            }
        }else{
            //房屋样式
            $buildingHouseStyleList = $this->buildingHouseStyleDb->select();
            $this->assign("buildingHouseStyleList",$buildingHouseStyleList);
            // 编辑器
            $editor = new \Org\Editor\Editor;
            $this->assign('editor',$editor);
            $whereBuilding['building_id'] = I('building_id');
            $building_name = $this->buildingDb->where($whereBuilding)->getField("building_name");
            $this->assign('building_id',I('building_id'));
            $this->assign('building_name',$building_name);
            $this->display("building_house_add");
        }
    }
    // 户型编辑
    public function buildingHouseEdit(){
        if(IS_POST){
            $whereHouse['house_id'] = I('house_id');
            $data = $_POST['info'];
            if($data['house_photos']){
                $data['house_photos'] = serialize($data['house_photos']);
            }
            $data['update_time'] = time();
            $data['admin_uid'] = session("admin_uid");
            $result = $this->buildingHouseDb->where($whereHouse)->save($data);
            $this->success("操作成功");
        }else{
            $whereHouse['house_id'] = I('house_id');
            $info = $this->buildingHouseDb->where($whereHouse)->find();
            $info['house_photos'] = unserialize($info['house_photos']);
            $this->assign($info);
            $whereBuilding['building_id'] = $info['building_id'];
            $building_name = $this->buildingDb->where($whereBuilding)->getField("building_name");
            $this->assign('building_name',$building_name);
            //房屋样式
            $buildingHouseStyleList = $this->buildingHouseStyleDb->select();
            $this->assign("buildingHouseStyleList",$buildingHouseStyleList);
            // 编辑器
            $editor = new \Org\Editor\Editor;
            $this->assign('editor',$editor);
            $this->display("building_house_edit");
        }
    }
    // 户型删除
    public function buildingHouseDelete(){
        if(IS_POST){
            $where['house_id'] = I('house_id');
            $this->buildingHouseDb->where($where)->delete();
            $this->success("操作成功");
        }
    }
    /**---标签---**/
    // 标签管理
    public function buildingTagsList(){
        $list = $this->buildingTagsDb->order("tags_sort asc, tags_id desc")->select();
        $this->assign('list',$list);
        $this->display("building_tags_list");
    }
    // 标签添加
    public function buildingTagsAdd(){
        if(IS_POST){
            $data = $_POST['info'];
            $result = $this->buildingTagsDb->data($data)->add();
            if($result){
                $this->success("操作成功");
            }else{
                $this->error("操作失败");
            }
        }else{
            layout(false);
            $this->display("building_tags_add");
        }
    }
    // 标签编辑
    public function buildingTagsEdit(){
        if(IS_POST){
            $where['tags_id'] = I('tags_id');
            $data = $_POST['info'];
            $this->buildingTagsDb->where($where)->save($data);
            $this->success("操作成功");
        }else{
            $where['tags_id'] = I('tags_id');
            $info = $this->buildingTagsDb->where($where)->find();
            $this->assign($info);
            layout(false);
            $this->display("building_tags_edit");
        }
    }
    // 标签删除
    public function buildingTagsDelete(){
        if(IS_POST){
            $where['tags_id'] = I('tags_id');
            $this->buildingTagsDb->where($where)->delete();
            $this->success("操作成功");
        }
    }
    /**---房屋类型---**/
    // 楼盘类型管理
    public function buildingTypeList(){
        $list = $this->buildingTypeDb->order("type_sort asc, type_id desc")->select();
        $this->assign('list',$list);
        $this->display("building_type_list");
    }
    // 楼盘类型添加
    public function buildingTypeAdd(){
        if(IS_POST){
            $data = $_POST['info'];
            $result = $this->buildingTypeDb->data($data)->add();
            if($result){
                $this->success("操作成功");
            }else{
                $this->error("操作失败");
            }
        }else{
            layout(false);
            $this->display("building_type_add");
        }
    }
    // 楼盘类型编辑
    public function buildingTypeEdit(){
        if(IS_POST){
            $where['type_id'] = I('type_id');
            $data = $_POST['info'];
            $this->buildingTypeDb->where($where)->save($data);
            $this->success("操作成功");
        }else{
            $where['type_id'] = I('type_id');
            $info = $this->buildingTypeDb->where($where)->find();
            $this->assign($info);
            layout(false);
            $this->display("building_type_edit");
        }
    }
    // 楼盘类型删除
    public function buildingTypeDelete(){
        if(IS_POST){
            $where['type_id'] = I('type_id');
            $this->buildingTypeDb->where($where)->delete();
            $this->success("操作成功");
        }
    }

    /**---楼盘户型样式类型---**/
    // 户型样式管理
    public function buildingHouseStyleList(){
        $list = $this->buildingHouseStyleDb->order("house_style_sort asc, house_style_id desc")->select();
        $this->assign('list',$list);
        $this->display("building_house_style_list");
    }
    // 户型样式添加
    public function buildingHouseStyleAdd(){
        if(IS_POST){
            $data = $_POST['info'];
            $result = $this->buildingHouseStyleDb->data($data)->add();
            if($result){
                $this->success("操作成功");
            }else{
                $this->error("操作失败");
            }
        }else{
            layout(false);
            $this->display("building_house_style_add");
        }
    }
    // 户型样式编辑
    public function buildingHouseStyleEdit(){
        if(IS_POST){
            $where['house_style_id'] = I('house_style_id');
            $data = $_POST['info'];
            $this->buildingHouseStyleDb->where($where)->save($data);
            $this->success("操作成功");
        }else{
            $where['house_style_id'] = I('house_style_id');
            $info = $this->buildingHouseStyleDb->where($where)->find();
            $this->assign($info);
            layout(false);
            $this->display("building_house_style_edit");
        }
    }
    // 户型样式删除
    public function buildingHouseStyleDelete(){
        if(IS_POST){
            $where['house_style_id'] = I('house_style_id');
            $this->buildingHouseStyleDb->where($where)->delete();
            $this->success("操作成功");
        }
    }

    /**---预约---**/
    public function buildingReserveList(){
        if(isset($_GET['keywords']) && !empty($_GET['keywords'])){
            $keywords = I('keywords');
            $where['reserve_sn'] = array('like',"%".$keywords."%");
            $where['contact_name'] = array('like',"%".$keywords."%");
            $where['contact_mobile'] = array('like',"%".$keywords."%");
            $where['_logic'] = 'or';
            $searchInfo['keywords'] = $keywords;
            $this->assign('searchInfo',$searchInfo);
        }
        $this->assign('searchInfo',$searchInfo);
        $count = $this->buildingReserveDb->where($where)->count();
        $Page = new \Think\Page($count,20);
        $list = $this->buildingReserveDb->limit($Page->firstRow.','.$Page->listRows)->where($where)->order('reserve_id desc')->select();
        foreach ($list as $key => $v) {
            $whereBuilding['building_id'] = $v['building_id'];
            $list[$key]['building_name'] = $this->buildingDb->where($whereBuilding)->getField("building_name");
        }
        $show = $Page->show();
        $this->assign('page',$show);
        $this->assign('list',$list);
        $this->display("building_reserve_list");
    }
    public function buildingReserveEdit(){
        if(IS_POST){
            $where['reserve_id'] = I('reserve_id');
            $data = $_POST['info'];
            $data['admin_uid'] = session("admin_uid");
            $data['update_time'] = time();
            $this->buildingReserveDb->where($where)->save($data);
            $this->success("操作成功");
        }else{
            $where['reserve_id'] = I('reserve_id');
            $info = $this->buildingReserveDb->where($where)->find();
            $this->assign($info);
            layout(false);
            $this->display("building_reserve_edit");
        }
    }
    public function buildingReserveDelete(){
        if(IS_POST){
            $where['reserve_id'] = I('reserve_id');
            $this->buildingReserveDb->where($where)->delete();
            $this->success("操作成功");
        }
    }
    /**---点评---**/
    public function buildingCommentList(){
        if(isset($_GET['keywords']) && !empty($_GET['keywords'])){
            $keywords = I('keywords');
            $where['comment_content'] = array('like',"%".$keywords."%");
            $searchInfo['keywords'] = $keywords;
            $this->assign('searchInfo',$searchInfo);
        }
        $this->assign('searchInfo',$searchInfo);
        $count = $this->buildingCommentDb->where($where)->count();
        $Page = new \Think\Page($count,20);
        $list = $this->buildingCommentDb->limit($Page->firstRow.','.$Page->listRows)->where($where)->order('comment_id desc')->select();
        foreach ($list as $key => $v) {
            $whereMember['userid'] = $v['userid'];
            $list[$key]['user_nickname'] = $this->memberDb->where($whereMember)->getField("nickname");
        }
        $show = $Page->show();
        $this->assign('page',$show);
        $this->assign('list',$list);
        $this->display("building_comment_list");
    }
    public function buildingCommentDetail(){
        $where['comment_id'] = I('comment_id');
        $info = $this->buildingCommentDb->where($where)->find();
        $whereMember['userid'] = $info['userid'];
        $info['user_nickname'] = $this->memberDb->where($whereMember)->getField("nickname");
        $whereBuilding['building_id'] = $info['building_id'];
        $info['building_name'] = $this->buildingDb->where($whereBuilding)->getField("building_name");
        $this->assign($info);
        layout(false);
        $this->display("building_comment_detail");
    }
    public function buildingCommentDelete(){
        if(IS_POST){
            $where['comment_id'] = I('comment_id');
            $this->buildingCommentDb->where($where)->delete();
            $this->success("操作成功");
        }
    }
    /**---关注---**/
    public function buildingAttentionList(){
        $count = $this->buildingAttentionDb->count();
        $Page = new \Think\Page($count,20);
        $list = $this->buildingAttentionDb->limit($Page->firstRow.','.$Page->listRows)->order('attention_id desc')->select();
        foreach ($list as $key => $v) {
            $whereMember['userid'] = $v['userid'];
            $list[$key]['user_nickname'] = $this->memberDb->where($whereMember)->getField("nickname");
            $whereBuilding['building_id'] = $v['building_id'];
            $list[$key]['building_name'] = $this->buildingDb->where($whereBuilding)->getField("building_name");
        }
        $show = $Page->show();
        $this->assign('page',$show);
        $this->assign('list',$list);
        $this->display("building_attention_list");
    }
    public function buildingAttentionDelete(){
        if(IS_POST){
            $where['attention_id'] = I('attention_id');
            $this->buildingAttentionDb->where($where)->delete();
            $this->success("操作成功");
        }
    }
    /**---收藏---**/
    public function buildingCollectList(){
        $count = $this->buildingCollectDb->count();
        $Page = new \Think\Page($count,20);
        $list = $this->buildingCollectDb->limit($Page->firstRow.','.$Page->listRows)->order('collect_id desc')->select();
        foreach ($list as $key => $v) {
            $whereMember['userid'] = $v['userid'];
            $list[$key]['user_nickname'] = $this->memberDb->where($whereMember)->getField("nickname");
            if($v['item_type'] == 1){
                $whereBuilding['building_id'] = $v['item_id'];
                $list[$key]['building_name'] = $this->buildingDb->where($whereBuilding)->getField("building_name");
            }

        }
        $show = $Page->show();
        $this->assign('page',$show);
        $this->assign('list',$list);
        $this->display("building_collect_list");
    }
    public function buildingCollectDelete(){
        if(IS_POST){
            $where['attention_id'] = I('attention_id');
            $this->buildingCollectDb->where($where)->delete();
            $this->success("操作成功");
        }
    }

}
