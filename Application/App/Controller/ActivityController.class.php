<?php
namespace App\Controller;
use Think\Controller;
class ActivityController extends Controller {

    public function __construct(){
        parent::__construct();
        $this->activityDb = M("Activity");
        $this->attentionDb = M("Activity_attention");
        $this->collectDb = M("Activity_collect");
        $this->commentDb = M("Activity_comment");
        $this->orderDb = M("Activity_order");
        $this->tagsDb = M("Building_tags");
        $this->buildDb = M("Building");
        $this->houseDb = M("Building_house");
    }
    //获取活动列表
    public function getActivityList(){
        if(IS_POST){

            $pageSize = $_POST["pageSize"] ? $_POST["pageSize"] : 20;
            $pageNum = $_POST["pageNum"] ? $_POST["pageNum"] : 1;
            $startCount = ($pageNum - 1) * $pageSize;

            $where["activity_type"] = $activity_type = $_POST["activity_type"] ? $_POST["activity_type"] : 1;
            $where["activity_status"] = 99;
            $_POST["activity_recommend"] ? $where["activity_recommend"] = $_POST["activity_recommend"] : "";

            $activityList = $this->activityDb
                ->where($where)
                ->field("activity_description,activity_content,activity_photos",true)
                ->limit($startCount,$pageSize)
                ->order("addtime desc")
                ->select();
            if($activityList){
                foreach ($activityList as $i => $v) {
                    $activityList[$i]["activity_thumb"] = reduce($v["activity_thumb"],480,320);
                    if($activity_type == 1){
                        $activityList[$i]["activity_look_line"] = unserialize($v["activity_look_line"]);
                    }
                    $activityList[$i]["activity_status"] = 1;
                    $end = strtotime($v["activity_end_date"]);
                    if(time() > $end){
                        $activityList[$i]["activity_status"] = 0;
                    }
                }
                $ret["code"] = 1;
                $ret["msg"] = "活动内容获取成功";
                $ret["data"] = $activityList;
            }else{
                $ret["code"] = -1;
                $ret["msg"] = $pageNum == 1 ? "无活动内容" : "已无更多活动内容";
            }
            $this->ajaxReturn($ret);
        }
    }
    //获取活动详情
    public function getActivityDetail(){
        if(IS_POST){
            $where["activity_id"] = $_POST["activity_id"] ? $_POST["activity_id"] : 0;

            $activityInfo = $this->activityDb->where($where)->find();
            if($activityInfo){
                $activityInfo["activity_status"] = 1;
                $end = strtotime($activityInfo["activity_end_date"]);
                if(time() > $end){
                    $activityInfo["activity_status"] = 0;
                }
                $activityInfo["share_thumb"] = thumb($activityInfo["activity_thumb"],80,80);
                $photos = unserialize($activityInfo["activity_photos"]);
                foreach ($photos as $i => $v) {
                    $photos[$i] = reduce($v,480,320);
                    // if($activityInfo["activity_type"] == 1){
                    // }
                }
                $activityInfo["activity_look_line"] = unserialize($activityInfo["activity_look_line"]);
                $activityInfo["activity_content"] = strip_tags($activityInfo["activity_content"],"<img> <p>");
                // $activityInfo["activity_content"] = str_replace("src=\"","src=\"http://www.szlucent.cn",$activityInfo["activity_content"]);
                $activityInfo["activity_photos"] = $photos;
                if($activityInfo["activity_buy_building_id"]){
                    $buildWhere["building_id"] = $activityInfo["activity_buy_building_id"];
                    $buildInfo = $this->buildDb->field("building_id,building_name,building_thumb,building_discount,building_area_name,building_address,building_price_overall,building_tags_ids")->where($buildWhere)->find();
                    if($buildInfo){
                        $buildInfo["building_thumb"] = thumb($buildInfo["building_thumb"],210,180);
                        $houseList = $this->houseDb->where("building_id=".$buildInfo["building_id"])->field("house_name")->limit(3)->select();
                        if($houseList){
                            $str = "";
                            foreach ($houseList as $i => $v) {
                                $str .= $v["house_name"].",";
                            }
                            $buildInfo["houseInfo"] = substr($str,0,strlen($str)-1);
                        }
                        if($buildInfo["building_tags_ids"]){
                            $arr = explode(",",$buildInfo["building_tags_ids"]);
                            $tagsWhere["tags_id"] = array("in",$arr);
                            $tagsList = $this->tagsDb->where($tagsWhere)->field("tags_name")->select();
                            if($tagsList){
                                $str = "";
                                if(count($tagsList) > 1){
                                    foreach ($tagsList as $j => $o) {
                                        $str .= $o["tags_name"].",";
                                    }
                                    $str = substr($str,0,strlen($str)-1);
                                }else{
                                    $str = $tagsList[0]["tags_name"];
                                }
                                $buildInfo["tags"] = $str;
                            }
                        }
                        $activityInfo["build"] = $buildInfo;
                    }
                }
                $ret["code"] = 1;
                $ret["msg"] = "活动内容获取成功";
                $ret["data"] = $activityInfo;
            }else{
                $ret["code"] = -1;
                $ret["msg"] = "活动内容获取失败";
            }
            $this->ajaxReturn($ret);
        }
    }
    //报名
    public function activityRegister(){
        if(IS_POST){
            $user = checkUser($_POST["token"]);
            if($user){
                $where["userid"] = $data["userid"] = $user["userid"];
                $activityWhere["activity_id"] = $where["activity_id"] = $data["activity_id"] = $_POST["activity_id"];
                $activityInfo = $this->activityDb->where($activityWhere)->field("activity_start_date,activity_end_date")->find();
                $start = strtotime($activityInfo["activity_start_date"]);
                $end = strtotime($activityInfo["activity_end_date"]);
                // $this->ajaxReturn($start."==".$end);
                // if(time() > $start && time() < $end){
                //     $ret["code"] = -1;
                //     $ret["msg"] = "活动已开始,报名失败";
                //     $this->ajaxReturn($ret);
                // }
                if(time() > $end){
                    $ret["code"] = -1;
                    $ret["msg"] = "活动已结束,报名失败";
                    $this->ajaxReturn($ret);
                }

                if($this->orderDb->where($where)->find()){
                    $ret["code"] = -1;
                    $ret["msg"] = "您已报名";
                }else{
                    $data["order_sn"] = create_sn();
                    $data["activity_type"] = $_POST["activity_type"] ? $_POST["activity_type"] : 1;
                    $data["order_people_number"] = $_POST["order_people_number"];
                    $data["order_contact_name"] = $_POST["order_contact_name"];
                    $data["order_contact_mobile"] = $_POST["order_contact_mobile"];
                    $data["order_status"] = 1;
                    $data["addtime"] = time();
                    $data["update_time"] = time();
                    if($this->orderDb->add($data)){
                        $ret["code"] = 1;
                        $ret["msg"] = "报名成功";
                    }else{
                        $ret["code"] = -1;
                        $ret["msg"] = "报名失败";
                    }
                }
            }else{
                $ret["code"] = 0;
                $ret["msg"] = "用户已在其他终端登录";
            }
            $this->ajaxReturn($ret);
        }
    }
    //获取评论列表
    public function getCommentList(){
        if(IS_POST){
            $pageSize = $_POST["pageSize"] ? $_POST["pageSize"] : 20;
            $pageNum = $_POST["pageNum"] ? $_POST["pageNum"] : 1;
            $startCount = ($pageNum - 1) * $pageSize;

            $commentWhere["activity_id"] = $countWhere["item_id"] = $where["activity_id"] = $_POST["activity_id"];
            $where["comment_parent_id"] = 0;
            // $commentList = $this->commentDb->where($where)->order("addtime desc")->select();
            $commentList = $this->commentDb->join("left join fs_member t on t.userid = fs_activity_comment.userid")
                ->field("t.nickname,t.avatar,t.province,t.city,t.area,fs_activity_comment.*")->where($where)->order("addtime desc")->limit($startCount,$pageSize)->select();
            if($commentList){
                foreach ($commentList as $i => $v) {
                    $commentList[$i]["avatar"] = thumb($v["avatar"],50,50);
                    //评论时间
                    $diff = time() - (int) substr($v["addtime"],0,10);
                    $commentList[$i]["comment_time"] = calcTime($diff);
                    //获取下级评论
                    $childWhere["comment_parent_id"] = $v["comment_id"];
                    $childCommentList = $this->commentDb->join("left join fs_member t on t.userid = fs_activity_comment.userid")
                        ->field("t.nickname,t.avatar,t.province,t.city,t.area,fs_activity_comment.*")->where($childWhere)->order("addtime asc")->limit(3)->select();
                    $childCount = $this->commentDb->where($childWhere)->count();
                    if($childCommentList){
                        foreach ($childCommentList as $j => $o) {
                            $childCommentList[$j]["avatar"] = thumb($o["avatar"],50,50);
                            $diff = time() - (int) substr($o["addtime"],0,10);
                            $childCommentList[$j]["comment_time"] = calcTime($diff);
                        }
                        $commentList[$i]["childCount"] = $childCount;
                        $commentList[$i]["child"] = $childCommentList;
                    }
                }
                if($pageNum == 1){
                    $commentWhere["comment_parent_id"] = 0;
                    $commentCount = $this->commentDb->where($commentWhere)->count();
                    $ret["commentCount"] = $commentCount;
                }

                $ret["code"] = 1;
                $ret["msg"] = "评论拉取成功";
                $ret["data"] = $commentList;
            }else{
                $ret["code"] = -1;
                $ret["msg"] = $pageNum == 1 ?  "无评论信息" : "已无更多评论";
            }
            $this->ajaxReturn($ret);
        }
    }
    //获取评论详情
    public function getCommentDetail(){
        if(IS_POST){
            $where["comment_id"] = $_POST["comment_id"];
            $commentInfo = $this->commentDb->join("left join fs_member t on t.userid = fs_activity_comment.userid")
                ->field("t.nickname,t.avatar,t.province,t.city,t.area,fs_activity_comment.*")->where($where)->find();
            if($commentInfo){
                $diff = time() - (int) substr($commentInfo["addtime"],0,10);
                $commentInfo["comment_time"] = calcTime($diff);
                $childWhere["comment_parent_id"] = $commentInfo["comment_id"];
                $childCommentList = $this->commentDb->join("left join fs_member t on t.userid = fs_activity_comment.userid")
                    ->field("t.nickname,t.avatar,t.province,t.city,t.area,fs_activity_comment.*")->where($childWhere)->order("addtime asc")->select();
                if($childCommentList){
                    foreach ($childCommentList as $i => $v) {
                        $diff = time() - (int) substr($v["addtime"],0,10);
                        $childCommentList[$i]["comment_time"] = calcTime($diff);
                        $childCommentList[$i]["avatar"] = thumb($v["avatar"],50,50);
                    }
                    $commentInfo["child"] = $childCommentList;
                }
                $ret["code"] = 1;
                $ret["msg"] = "评论详情获取成功";
                $ret["data"] = $commentInfo;
            }else{
                $ret["code"] = -1;
                $ret["msg"] = "评论详情获取失败";
            }
            $this->ajaxReturn($ret);
        }
    }
    //添加评论
    public function addComment(){
        if(IS_POST){
            $token = $_POST["token"];
            $_POST["activity_id"] ? $data["activity_id"] = $_POST["activity_id"] : "";
            $data["comment_content"] = $_POST["comment_content"];
            $data["comment_parent_id"] = $_POST["comment_id"] ? $_POST["comment_id"] : 0;
            $user = checkUser($token);
            if($user){
                $data["userid"] = $user["userid"];
                $data["addtime"] = time();
                if($this->commentDb->add($data)){
                    $ret["code"] = 1;
                    $ret["msg"] = "评论成功";
                }else{
                    $ret["code"] = -1;
                    $ret["msg"] = "评论失败";
                }
            }else{
                $ret["code"] = 0;
                $ret["msg"] = "用户已在其他终端登录";
            }
            $this->ajaxReturn($ret);
        }
    }
    //收藏
    public function collectActivity(){
        if(IS_POST){
            $token = $_POST["token"];
            $user = checkUser($token);
            if($user){
                $where["activity_id"] = $_POST["activity_id"];
                $where["userid"] = $user["userid"];
                $where["activity_type"] = $_POST["activity_type"] ? $_POST["activity_type"] : 1;
                if($this->collectDb->where($where)->find()){
                    $ret["code"] = -1;
                    $ret["msg"] = "活动已收藏";
                }else{
                    $where["addtime"] = time();
                    if($this->collectDb->add($where)){
                        $ret["code"] = 1;
                        $ret["msg"] = "收藏成功";
                    }else{
                        $ret["code"] = -1;
                        $ret["msg"] = "收藏失败";
                    }
                }
            }else{
                $ret["code"] = 0;
                $ret["msg"] = "用户已在其他终端登录";
            }
            $this->ajaxReturn($ret);
        }
    }
    //取消收藏
    public function delCollection(){
        if(IS_POST){
            $token = $_POST["token"];
            $user = checkUser($token);
            if($user){
                $where["userid"] = $user["userid"];
                $where["activity_id"] = $_POST["activity_id"];
                if($this->collectDb->where($where)->delete()){
                    $ret["code"] = 1;
                    $ret["msg"] = "取消收藏成功";
                }else{
                    $ret["code"] = 1;
                    $ret["msg"] = "取消收藏失败";
                }
            }else{
                $ret["code"] = 0;
                $ret["msg"] = "用户已在其他终端登录";
            }
            $this->ajaxReturn($ret);
        }
    }
    //我收藏的活动
    public function getUserCollection(){
        if(IS_POST){

            $pageSize = $_POST["pageSize"] ? $_POST["pageSize"] : 20;
            $pageNum = $_POST["pageNum"] ? $_POST["pageNum"] : 1;
            $startCount = ($pageNum - 1) * $pageSize;

            $token = $_POST["token"];
            $user = checkUser($token);
            if($user){
                $where["userid"] = $user["userid"];
                $where["activity_type"] = $_POST["activity_type"] ? $_POST["activity_type"] : 1;
                $list = $this->collectDb->where($where)->order("addtime")->limit($startCount,$pageSize)->select();
                if($list){
                    $arr = array();
                    foreach ($list as $i => $v) {
                        $arr[$i] = $v["activity_id"];
                    }
                    $activityWhere["activity_id"] = array("in",$arr);
                    $activityList = $this->activityDb->where($activityWhere)->field("activity_description,activity_content,activity_photos",true)->select();
                    if($activityList){
                        foreach ($activityList as $j => $o) {
                            $start = strtotime($o["activity_start_date"]);
                            $end = strtotime($o["activity_end_date"]);
                            if(time() > $start && time() < $end){
                                $activityList[$j]["status"] = 1;
                                $activityList[$j]["status_content"] = "活动进行中";
                            }
                            if(time() > $end){
                                $activityList[$j]["status"] = 2;
                                $activityList[$j]["status_content"] = "活动已结束";
                            }
                            if(time() < $start){
                                $diff = $start - time();
                                $activityList[$j]["status"] = 3;
                                $activityList[$j]["status_content"] = calcTimeInfo($diff);
                            }
                            $activityList[$j]["activity_thumb"] = reduce($o["activity_thumb"],420,200);
                            if($activityList[$j]["activity_type"] == 1){
                                $activityList[$j]["activity_look_line"] = unserialize($o["activity_look_line"]);
                            }
                        }
                        $ret["code"] = 1;
                        $ret["msg"] = "收藏列表获取成功";
                        $ret["data"] = $activityList;
                    }else{
                        $ret["code"] = -1;
                        $ret["msg"] = "收藏列表获取失败";
                    }
                }else{
                    $ret["code"] = -1;
                    $ret["msg"] = $pageNum == 1 ? "无收藏" : "已无更多收藏";
                }
            }else{
                $ret["code"] = 0;
                $ret["msg"] = "用户已在其他终端登录";
            }
            $this->ajaxReturn($ret);
        }
    }
    //我报名的活动
    public function getOrderActivity(){
        if(IS_POST){
            $pageSize = $_POST["pageSize"] ? $_POST["pageSize"] : 20;
            $pageNum = $_POST["pageNum"] ? $_POST["pageNum"] : 1;
            $startCount = ($pageNum - 1) * $pageSize;

            $token = $_POST["token"];
            $user = checkUser($token);
            if($user){
                $where["userid"] = $user["userid"];
                $where["activity_type"] = $_POST["activity_type"] ? $_POST["activity_type"] : 1;
                $list = $this->orderDb->where($where)->order("addtime")->limit($startCount,$pageSize)->select();
                if($list){
                    $arr = array();
                    foreach ($list as $i => $v) {
                        $arr[$i] = $v["activity_id"];

                    }
                    $activityWhere["activity_id"] = array("in",$arr);
                    $activityList = $this->activityDb->where($activityWhere)->field("activity_description,activity_content,activity_photos",true)->select();
                    if($activityList){
                        foreach ($activityList as $j => $o) {
                            $start = strtotime($o["activity_start_date"]);
                            $end = strtotime($o["activity_end_date"]);
                            if(time() > $start && time() < $end){
                                $activityList[$j]["status"] = 1;
                                $activityList[$j]["status_content"] = "活动进行中";
                            }
                            if(time() > $end){
                                $activityList[$j]["status"] = 2;
                                $activityList[$j]["status_content"] = "活动已结束";
                            }
                            if(time() < $start){
                                $diff = $start - time();
                                $activityList[$j]["status"] = 3;
                                $activityList[$j]["status_content"] = calcTimeInfo($diff);
                            }
                            $activityList[$j]["activity_thumb"] = reduce($o["activity_thumb"],420,200);
                            if($activityList[$j]["activity_type"] == 1){
                                $activityList[$j]["activity_look_line"] = unserialize($o["activity_look_line"]);
                            }
                        }
                        $ret["code"] = 1;
                        $ret["msg"] = "报名列表获取成功";
                        $ret["data"] = $activityList;
                    }else{
                        $ret["code"] = -1;
                        $ret["msg"] = "报名列表获取失败";
                    }

                }else{
                    $ret["code"] = -1;
                    $ret["msg"] = $pageNum == 1 ? "无报名" : "已无更多报名活动";
                }
            }else{
                $ret["code"] = 0;
                $ret["msg"] = "用户已在其他终端登录";
            }
            $this->ajaxReturn($ret);
        }
    }
    //取消报名
    public function delOrder(){
        if(IS_POST){
            $status = $_POST["status"];
            if($status == 1){
                $ret["code"] = -1;
                $ret["msg"] = "活动已开始,无法取消报名";
                $this->ajaxReturn($ret);
            }
            $where["activity_id"] = $_POST["activity_id"];
            $token = $_POST["token"];
            $user = checkUser($token);
            if($user){
                $where["userid"] = $user["userid"];
                if($this->orderDb->where($where)->delete()){
                    $ret["code"] = 1;
                    $ret["msg"] = "报名取消成功";
                }else{
                    $ret["code"] = 1;
                    $ret["msg"] = "报名取消失败";
                }
            }else{
                $ret["code"] = 0;
                $ret["msg"] = "用户已在其他终端登录";
            }
            $this->ajaxReturn($ret);
        }
    }
    //关注
    public function addAttention(){
        if(IS_POST){
            $token = $_POST["token"];
            $user = checkUser($token);
            if($user){
                $where["activity_id"] = $_POST["activity_id"];
                $where["userid"] = $user["userid"];
                $where["activity_type"] = $_POST["activity_type"] ? $_POST["activity_type"] : 1;
                if($this->attentionDb->where($where)->find()){
                    $ret["code"] = -1;
                    $ret["msg"] = "活动已关注";
                }else{
                    $where["addtime"] = time();
                    if($this->attentionDb->add($where)){
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
    //取消关注
    public function delAttention(){
        if(IS_POST){
            $token = $_POST["token"];
            $user = checkUser($token);
            if($user){
                $where["userid"] = $user["userid"];
                $where["activity_id"] = $_POST["activity_id"];
                if($this->attentionDb->where($where)->delete()){
                    $ret["code"] = 1;
                    $ret["msg"] = "取消关注成功";
                }else{
                    $ret["code"] = 1;
                    $ret["msg"] = "取消关注失败";
                }
            }else{
                $ret["code"] = 0;
                $ret["msg"] = "用户已在其他终端登录";
            }
            $this->ajaxReturn($ret);
        }
    }
    //我关注的活动
    public function getUserAttention(){
        if(IS_POST){

            $pageSize = $_POST["pageSize"] ? $_POST["pageSize"] : 20;
            $pageNum = $_POST["pageNum"] ? $_POST["pageNum"] : 1;
            $startCount = ($pageNum - 1) * $pageSize;

            $token = $_POST["token"];
            $user = checkUser($token);
            if($user){
                $where["userid"] = $user["userid"];
                $where["activity_type"] = $_POST["activity_type"] ? $_POST["activity_type"] : 1;
                $list = $this->attentionDb->where($where)->order("addtime")->limit($startCount,$pageSize)->select();
                if($list){
                    $arr = array();
                    foreach ($list as $i => $v) {
                        $arr[$i] = $v["activity_id"];
                    }
                    $activityWhere["activity_id"] = array("in",$arr);
                    $activityList = $this->activityDb->where($activityWhere)->field("activity_description,activity_content,activity_photos",true)->select();
                    if($activityList){
                        foreach ($activityList as $j => $o) {
                            $start = strtotime($o["activity_start_date"]);
                            $end = strtotime($o["activity_end_date"]);
                            if(time() > $start && time() < $end){
                                $activityList[$j]["status"] = 1;
                                $activityList[$j]["status_content"] = "活动进行中";
                            }
                            if(time() > $end){
                                $activityList[$j]["status"] = 2;
                                $activityList[$j]["status_content"] = "活动已结束";
                            }
                            if(time() < $start){
                                $diff = $start - time();
                                $activityList[$j]["status"] = 3;
                                $activityList[$j]["status_content"] = calcTimeInfo($diff);
                            }
                            $activityList[$j]["activity_thumb"] = reduce($o["activity_thumb"],420,200);
                            if($activityList[$j]["activity_type"] == 1){
                                $activityList[$j]["activity_look_line"] = unserialize($o["activity_look_line"]);
                            }
                        }
                        $ret["code"] = 1;
                        $ret["msg"] = "关注列表获取成功";
                        $ret["data"] = $activityList;
                    }else{
                        $ret["code"] = -1;
                        $ret["msg"] = "关注列表获取失败";
                    }
                }else{
                    $ret["code"] = -1;
                    $ret["msg"] = $pageNum == 1 ? "无关注" : "已无更多关注";
                }
            }else{
                $ret["code"] = 0;
                $ret["msg"] = "用户已在其他终端登录";
            }
            $this->ajaxReturn($ret);
        }
    }
    //我的评论列表
    public function getUserCommentList(){
        if(IS_POST){
            $user = checkUser($_POST["token"]);
            if($user){
                $pageSize = $_POST["pageSize"] ? $_POST["pageSize"] : 20;
                $pageNum = $_POST["pageNum"] ? $_POST["pageNum"] : 1;
                $startCount = ($pageNum - 1) * $pageSize;

                $commentList = $this->activityDb
                    ->table("__ACTIVITY_COMMENT__ c,__ACTIVITY__ n")
                    ->where("c.activity_id = n.activity_id and c.userid=".$user["userid"])
                    ->field("c.comment_id,c.comment_content,c.addtime,n.activity_id,n.activity_title,n.activity_thumb,n.activity_type")
                    ->limit($startCount,$pageSize)
                    ->order("c.addtime desc")
                    ->select();
                if($commentList){
                    foreach ($commentList as $i => $v) {
                        $commentList[$i]["time"] = date("Y-m-d",$v["addtime"]);
                        if($v["activity_thumb"]){
                            $commentList[$i]["activity_thumb"] = thumb($v["activity_thumb"],120,80);
                        }
                    }
                    $ret["code"] = 1;
                    $ret["msg"] = "评论列表获取成功";
                    $ret["data"] = $commentList;
                }else{
                    $ret["code"] = -1;
                    $ret["msg"] = $pageNum == 1 ? "无评论内容" : "已无更多评论内容";
                }
            }else{
                $ret["code"] = 0;
                $ret["msg"] = "用户已在其他终端登录";
            }
            $this->ajaxReturn($ret);
        }
    }
    //删除评论内容
    function delUserComment(){
        if(IS_POST){
            $user = checkUser($_POST["token"]);
            if($user){
                $childWhere["parent_id"] = $where["comment_id"] = $_POST["comment_id"];
                $where["userid"] = $user["userid"];
                if($this->commentDb->where($where)->delete()){
                    $this->commentDb->where($childWhere)->delete();
                    $ret["code"] = 1;
                    $ret["msg"] = "评论删除成功";
                }else{
                    $ret["code"] = -1;
                    $ret["msg"] = "评论删除失败";
                }
            }else{
                $ret["code"] = 0;
                $ret["msg"] = "用户已在其他终端登录";
            }
            $this->ajaxReturn($ret);
        }
    }
    function getActivityNotice(){
        if(IS_POST){
            $noticeDb = M("Activity_notice");
            $info = $noticeDb->find();
            if($info){
                $ret["code"] = 1;
                $ret["msg"] = "活动须知获取成功";
                $ret["data"] = $info;
            }else{
                $ret["code"] = -1;
                $ret["msg"] = "活动须知获取失败";
            }
            $this->ajaxReturn($ret);
        }
    }
}
