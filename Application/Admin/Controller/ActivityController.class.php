<?php
/**
* 活动模块管理
*/
namespace Admin\Controller;
class ActivityController extends BaseController {
    public function __construct(){
        parent::__construct();
        $this->memberDb = M('Member');
        $this->areaDb = M('Area');
        $this->activityDb = M("Activity");
        $this->activityCommentDb = M('Activity_comment');
        $this->activityAttentionDb = M('Activity_attention');
        $this->activityCollectDb = M('Activity_collect');
        $this->activityOrderDb = M('Activity_order');
    }
    // 看楼活动列表
    public function activityLookList(){
        if(IS_POST){
            if(isset($_POST['dosubmit'])) {
                foreach($_POST['sorts'] as $activity_id => $sort) {
                    $where['activity_id'] = $activity_id;
                    $data['building_sort'] = $sort;
                    $this->activityDb->where($where)->save($data);
                }
                $this->success("操作成功");
            }
        }else{
            $whereSearch['activity_type'] = 1;
            $this->assign('searchInfo',$searchInfo);
            $count = $this->activityDb->where($whereSearch)->count();
            $Page = new \Think\Page($count,20);
            $list = $this->activityDb->limit($Page->firstRow.','.$Page->listRows)->where($whereSearch)->order('activity_sort asc,activity_id desc')->select();
            $show = $Page->show();
            $this->assign('page',$show);
            $this->assign('list',$list);
            $this->display("activity_look_list");
        }
    }
    // 看楼活动添加
    public function activityLookAdd(){
        if(IS_POST){
            $data = $_POST['info'];
            $data['activity_type'] = 1;
            if($data['activity_photos']){
                $data['activity_photos'] = serialize($data['activity_photos']);
            }
            if($_POST['activity_line_building_name']){
                foreach ($_POST['activity_line_building_name'] as $key => $v) {
                    $data['activity_look_line'][$key]['building_name'] = $v;
                    $data['activity_look_line'][$key]['building_price'] = $_POST['activity_line_building_price'][$key];
                }
                $data['activity_look_line']  = serialize($data['activity_look_line']);
            }
            $data['addtime'] = $data['update_time'] = time();
            $data['admin_uid'] = session("admin_uid");
            $result = $this->activityDb->data($data)->add();
            if($result){
                $this->success("发布成功");
            }else{
                $this->error("发布失败");
            }
        }else{
            // 获取地区
            $whereArea['parent_id'] = 0;
            $whereArea['id'] = array('gt',1);
            $areaList = $this->areaDb->where($whereArea)->order('sort asc, id asc')->select();
            $this->assign("areaList",$areaList);
            // 编辑器
            $editor = new \Org\Editor\Editor;
            $this->assign('editor',$editor);
            $this->display("activity_look_add");
        }
    }
    // 看楼活动编辑
    public function activityLookEdit(){
        if(IS_POST){
            $where['activity_id'] = I('activity_id');
            $data = $_POST['info'];
            if($data['activity_photos']){
                $data['activity_photos'] = serialize($data['activity_photos']);
            }
            if($_POST['activity_line_building_name']){
                foreach ($_POST['activity_line_building_name'] as $key => $v) {
                    $data['activity_look_line'][$key]['building_name'] = $v;
                    $data['activity_look_line'][$key]['building_price'] = $_POST['activity_line_building_price'][$key];
                }
                $data['activity_look_line']  = serialize($data['activity_look_line']);
            }

            $data['update_time'] = time();
            $data['admin_uid'] = session("admin_uid");
            $result = $this->activityDb->where($where)->save($data);
            $this->success("操作成功");
        }else{
            $where['activity_id'] = I('activity_id');
            $info = $this->activityDb->where($where)->find();
            $info['activity_photos'] = unserialize($info['activity_photos']);
            $info['activity_look_line'] = unserialize($info['activity_look_line']);
            $this->assign($info);
            // 获取地区
            $whereArea['parent_id'] = 0;
            $whereArea['id'] = array('gt',1);
            $areaList = $this->areaDb->where($whereArea)->order('sort asc, id asc')->select();
            $this->assign("areaList",$areaList);
            // 编辑器
            $editor = new \Org\Editor\Editor;
            $this->assign('editor',$editor);
            $this->display("activity_look_edit");
        }

    }
    // 看楼活动删除
    public function activityLookDelete(){
        if(IS_POST){
            $where['activity_id'] = I('activity_id');
            $this->activityDb->where($where)->delete();
            $this->success("操作成功");
        }
    }


    // 团购活动列表
    public function activityBuyList(){
        if(IS_POST){
            if(isset($_POST['dosubmit'])) {
                foreach($_POST['sorts'] as $activity_id => $sort) {
                    $where['activity_id'] = $activity_id;
                    $data['building_sort'] = $sort;
                    $this->activityDb->where($where)->save($data);
                }
                $this->success("操作成功");
            }
        }else{
            $whereSearch['activity_type'] = 2;
            $this->assign('searchInfo',$searchInfo);
            $count = $this->activityDb->where($whereSearch)->count();
            $Page = new \Think\Page($count,20);
            $list = $this->activityDb->limit($Page->firstRow.','.$Page->listRows)->where($whereSearch)->order('activity_sort asc,activity_id desc')->select();
            $show = $Page->show();
            $this->assign('page',$show);
            $this->assign('list',$list);
            $this->display("activity_buy_list");
        }
    }
    // 团购活动添加
    public function activityBuyAdd(){
        if(IS_POST){
            $data = $_POST['info'];
            $data['activity_type'] = 2;
            if($data['activity_photos']){
                $data['activity_photos'] = serialize($data['activity_photos']);
            }
            $data['addtime'] = $data['update_time'] = time();
            $data['admin_uid'] = session("admin_uid");
            $result = $this->activityDb->data($data)->add();
            if($result){
                $this->success("发布成功");
            }else{
                $this->error("发布失败");
            }
        }else{
            // 获取地区
            $whereArea['parent_id'] = 0;
            $whereArea['id'] = array('gt',1);
            $areaList = $this->areaDb->where($whereArea)->order('sort asc, id asc')->select();
            $this->assign("areaList",$areaList);
            // 编辑器
            $editor = new \Org\Editor\Editor;
            $this->assign('editor',$editor);
            $this->display("activity_buy_add");
        }
    }
    // 团购活动编辑
    public function activityBuyEdit(){
        if(IS_POST){
            $where['activity_id'] = I('activity_id');
            $data = $_POST['info'];
            if($data['activity_photos']){
                $data['activity_photos'] = serialize($data['activity_photos']);
            }

            $data['update_time'] = time();
            $data['admin_uid'] = session("admin_uid");
            $result = $this->activityDb->where($where)->save($data);
            $this->success("操作成功");
        }else{
            $where['activity_id'] = I('activity_id');
            $info = $this->activityDb->where($where)->find();
            $info['activity_photos'] = unserialize($info['activity_photos']);
            $this->assign($info);
            // 获取地区
            $whereArea['parent_id'] = 0;
            $whereArea['id'] = array('gt',1);
            $areaList = $this->areaDb->where($whereArea)->order('sort asc, id asc')->select();
            $this->assign("areaList",$areaList);
            // 编辑器
            $editor = new \Org\Editor\Editor;
            $this->assign('editor',$editor);
            $this->display("activity_buy_edit");
        }

    }
    // 团购活动删除
    public function activityBuyDelete(){
        if(IS_POST){
            $where['activity_id'] = I('activity_id');
            $this->activityDb->where($where)->delete();
            $this->success("操作成功");
        }
    }
    // 活动报名列表
    public function activityOrderLists(){
        if(IS_POST){

        }else{
            // $whereSearch['fs_activity_order.activity_type'] = 1;
            $this->assign('searchInfo',$searchInfo);
            $count = $this->activityOrderDb->where($whereSearch)->count();
            $Page = new \Think\Page($count,20);
            $list = $this->activityOrderDb
                    ->join('fs_activity ON fs_activity_order.activity_id=fs_activity.activity_id')
                    ->field('fs_activity_order.order_id,fs_activity_order.order_sn,fs_activity_order.userid,fs_activity_order.activity_id,fs_activity_order.order_people_number,fs_activity_order.order_contact_name,fs_activity_order.order_contact_mobile,fs_activity_order.order_status,fs_activity_order.addtime,fs_activity.activity_type,fs_activity.activity_id,fs_activity.activity_title')
                    ->limit($Page->firstRow.','.$Page->listRows)
                    ->where($whereSearch)
                    ->order('addtime desc')
                    ->select();
            $show = $Page->show();
            $this->assign('page',$show);
            $this->assign('list',$list);
            $this->display("activity_order_list");
        }
    }
     // 报名删除
    public function activityOrderDelete(){
        if(IS_POST){
            $where['order_id'] = I('order_id');
            $this->activityOrderDb->where($where)->delete();
            $this->success("操作成功");
        }
    }
    //活动须知
    public function activityNotice(){
        $noticeDb = M("Activity_notice");
        $info = $noticeDb->where("id=1")->find();
        $this->assign($info);
        // 编辑器
        $editor = new \Org\Editor\Editor;
        $this->assign('editor',$editor);
        $this->display("activity_notice");
    }
    public function activityNoticeSave(){
        if(IS_POST){
            $noticeDb = M("Activity_notice");
            $where["id"] = 1;
            $where["content"] = $_POST["content"];
            if($noticeDb->save($where)){
                $this->success("操作成功");
            }else{
                $this->error("操作失败");
            }
        }
    }
}
