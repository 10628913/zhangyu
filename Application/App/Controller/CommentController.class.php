<?php
namespace App\Controller;
use Think\Controller;
class CommentController extends Controller {

    public function __construct(){
        parent::__construct();
        $this->commentDb = M("News_comment");
        $this->thumbsDb = M("News_thumbs");
        $this->memberDb = M("Member");
        $this->collectDb = M("News_collect");
    }
    //获取评论列表
    public function getCommentList(){
        if(IS_POST){
            $pageSize = $_POST["pageSize"] ? $_POST["pageSize"] : 20;
            $pageNum = $_POST["pageNum"] ? $_POST["pageNum"] : 1;
            $startCount = ($pageNum - 1) * $pageSize;

            $commentWhere["news_id"] =  $where["news_id"] = $_POST["news_id"];
            $where["parent_id"] = 0;
            // $commentList = $this->commentDb->where($where)->order("addtime desc")->select();
            $commentList = $this->commentDb->join("left join fs_member t on t.userid = fs_news_comment.userid")->field("t.userid,t.nickname,t.avatar,t.province,t.city,t.area,news_id,parent_id,comment_id,comment_content,addtime")->where($where)->order("addtime desc")->limit($startCount,$pageSize)->select();
            if($commentList){
                foreach ($commentList as $i => $v) {
                    $countWhere["thumbs_type"] = 2;
                    $countWhere["item_id"] = $v["comment_id"];
                    $thumbsCount = $this->thumbsDb->where($countWhere)->count();
                    // $this->ajaxReturn($this->thumbsDb->getLastSql());
                    $commentList[$i]["thumbs_count"] = $thumbsCount;
                    $commentList[$i]["is_comment"] = false;
                    if($_POST["token"]){
                        $user = checkUser($_POST["token"]);
                        if($user){
                            $isComment["userid"] = $user["userid"];
                            $isComment["comment_id"] = $v["comment_id"];
                            if($this->thumbsDb->where($isComment)->find()){

                                $commentList[$i]["is_comment"] = true;
                            }
                        }
                    }
                    //点赞数

                    $diff = time() - (int) substr($v["addtime"],0,10);
                    $commentList[$i]["comment_time"] = calcTime($diff);
                    //获取下级评论
                    $childWhere["parent_id"] = $v["comment_id"];
                    $childCommentList = $this->commentDb->join("left join fs_member t on t.userid = fs_news_comment.userid")->field("t.userid,t.nickname,t.avatar,t.province,t.city,t.area,news_id,parent_id,comment_id,comment_content,addtime")->where($childWhere)->order("addtime asc")->limit(3)->select();
                    $childCount = $this->commentDb->where($childWhere)->count();
                    if($childCommentList){
                        $commentList[$i]["childCount"] = $childCount;
                        if($childCommentList){
                            foreach ($childCommentList as $j => $o) {
                                $childCommentList[$j]["avatar"] = thumb($o["avatar"],50,50);
                            }
                        }
                        $commentList[$i]["child"] = $childCommentList;
                    }
                }
                if($pageNum == 1){
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
            $countWhere["parent_id"] = $where["comment_id"] = $_POST["comment_id"];
            $commentInfo = $this->commentDb->join("left join fs_member t on t.userid = fs_news_comment.userid")->field("t.userid,t.nickname,t.avatar,t.province,t.city,t.area,news_id,parent_id,comment_id,comment_content,addtime")->where($where)->find();
            if($commentInfo){
                //获取点赞数
                $countWhere["thumbs_type"] = 2;
                $thumbsCount = $this->thumbsDb->where($countWhere)->count();
                $commentInfo["thumbs_count"] = $thumbsCount;
                //是否已点赞
                $commentInfo["is_thumbs"] = false;
                if($_POST["token"]){
                    $user = checkUser($_POST["token"]);
                    if($user){
                        $countWhere["userid"] = $user["userid"];
                        $countWhere["comment_id"] = $commentInfo["comment_id"];
                        if($this->thumbsDb->where($countWhere)->find()){
                            $commentInfo["is_thumbs"] = true;
                        }
                    }
                }
                $diff = time() - (int) substr($commentInfo["addtime"],0,10);
                $commentInfo["comment_time"] = calcTime($diff);
                $childWhere["parent_id"] = $commentInfo["comment_id"];
                $childCommentList = $this->commentDb->join("left join fs_member t on t.userid = fs_news_comment.userid")->field("t.userid,t.nickname,t.avatar,t.province,t.city,t.area,news_id,parent_id,comment_id,comment_content,addtime")->where($childWhere)->order("addtime asc")->select();
                if($childCommentList){

                    foreach ($childCommentList as $i => $v) {
                        $diff = time() - (int) substr($v["addtime"],0,10);
                        $childCommentList[$i]["comment_time"] = calcTime($diff);
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
            $commentWhere["news_id"] = $_POST["news_id"] ? $data["news_id"] = $_POST["news_id"] : "";
            $data["comment_content"] = $_POST["comment_content"];
            $data["parent_id"] = $_POST["comment_id"] ? $_POST["comment_id"] : 0;
            $user = checkUser($token);
            if($user){
                $data["userid"] = $user["userid"];
                $data["addtime"] = time();
                if($this->commentDb->add($data)){
                    $ret["code"] = 1;
                    $ret["msg"] = "评论成功";
                    if($_POST["news_id"]){
                        $ret["comment_count"] =  $this->commentDb->where("news_id=".$_POST["news_id"])->count();
                    }
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
    //删除评论
    public function delComment(){
        if(IS_POST){
            if(!$_POST["token"] || !$_POST["itemid"]){
                $ret["code"] = -1;
                $ret["msg"] = "必需请求param:token,itemid";
                $this->ajaxReturn($ret);
            }
            $token = $_POST["token"];
            $comment_id = $_POST["comment_id"];
            $where["comment_id"] = $comment_id;
            $user = checkUser($token);
            if($user){
                $where["userid"] = $user["userid"];
                if($this->commentDb->where($where)->delete()){
                    $this->commentDb->where('parent_id = "'.$comment_id.'"')->delete();
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
    //点赞
    public function thumbsUp(){
        if(IS_POST){
            $token = $_POST["token"];
            $itemid = $_POST["item_id"];
            $parentId = $_POST["parent_id"];
            $countWhere["parent_id"] = $where["parent_id"] = $parentId ? $parentId : 0;
            $type = $_POST["thumbs_type"] ? $_POST["thumbs_type"] : 1;

            $user = checkUser($token);
            if($user){
                $userid = $user["userid"];
                //判断是否点过赞 如果有,删除它,没有,添加记录
                $where["userid"] = $userid;
                $countWhere["item_id"] = $where["item_id"] = $itemid;
                $countWhere["thumbs_type"] = $where["thumbs_type"] = $type;
                $result = $this->thumbsDb->where($where)->find();
                if($result){
                    $deleteWhere["thumbs_id"] = $result["thumbs_id"];
                    if($this->thumbsDb->where($deleteWhere)->delete()){
                        $count = $this->thumbsDb->where($countWhere)->count();
                        $ret["code"] = 2;
                        $ret["msg"] = "成功取消点赞";
                        $ret["count"] = $count;
                    }else{
                        $ret["code"] = -2;
                        $ret["msg"] = "取消点赞失败";
                    }
                }else{
                    $where["addtime"] = time();
                    if($this->thumbsDb->add($where)){
                        $count = $this->thumbsDb->where($countWhere)->count();
                        $ret["code"] = 1;
                        $ret["msg"] = "成功点赞";
                        $ret["count"] = $count;
                    }else{
                        $ret["code"] = -1;
                        $ret["msg"] = "点赞失败";
                    }
                }
            }else{
                $ret["code"] = 0;
                $ret["msg"] = "用户已在其他终端登录";
            }
            $this->ajaxReturn($ret);
        }
    }


    //新闻收藏
    public function newsCollect(){
        if(IS_POST){
            $token = $_POST["token"];
            $user = checkUser($token);
            if($user){
                $where["news_id"] = $_POST["news_id"];
                $where["userid"] = $user["userid"];
                if($this->collectDb->where($where)->find()){
                    $ret["code"] = -1;
                    $ret["msg"] = "您已收藏";
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
    public function delCollect(){
        if(IS_POST){
            $token = $_POST["token"];
            $user = checkUser($token);
            if($user){
                $where["news_id"] = $_POST["news_id"];
                $where["userid"] = $user["userid"];
                if($this->collectDb->where($where)->delete()){
                    $ret["code"] = 1;
                    $ret["msg"] = "取消收藏成功";
                }else{
                    $ret["code"] = -1;
                    $ret["msg"] = "取消收藏失败";
                }
            }else{
                $ret["code"] = 0;
                $ret["msg"] = "用户已在其他终端登录";
            }
            $this->ajaxReturn($ret);
        }
    }
    //获取用户新闻收藏列表
    public function getUserNewsCollectList(){
        if(IS_POST){
            $user = checkUser($_POST["token"]);
            if($user){
                $pageSize = $_POST["pageSize"] ? $_POST["pageSize"] : 20;
                $pageNum = $_POST["pageNum"] ? $_POST["pageNum"] : 1;
                $startCount = ($pageNum - 1) * $pageSize;

                // $where["userid"] = $user["userid"];
                $newsList = $this->collectDb->
                table("__NEWS__ n,__NEWS_COLLECT__ c")
                ->where("n.news_id = c.news_id and c.userid=".$user["userid"])
                ->field("n.news_id,n.news_title,n.news_subtitle,n.classify_id,n.type_id,n.news_tags,n.news_source,n.news_atlas,n.addtime,n.news_pub_time,n.news_thumb")
                ->limit($startCount,$pageSize)
                ->order("c.addtime desc")
                ->select();

                // $newsList = $this->collectDb->join("fs_news on fs_news.news_id = fs_news_collect.news_id")->field("fs_news_collect.news_content")->where('fs_news_collect.userid='.$user["userid"])->limit($startCount,$pageSize)->order("fs_news_collect.addtime desc")->select();
                if($newsList){
                    foreach ($newsList as $i => $v) {
                        $diff = time() - (int) substr($v["news_pub_time"],0,10);
                        // $this->ajaxReturn($diff."=".date("Y-m-d H:i:s",time())."=".date("Y-m-d H:i:s",$v["news_pub_time"]));
                        $newsList[$i]["news_pub_time"] = calcTime($diff);
                        if($v["news_thumb"]){
                            $newsList[$i]["news_thumb"] = thumb($v["news_thumb"],210,180);
                        }
                        if($v["news_atlas"]){
                            $atlas = unserialize($v["news_atlas"]);
                            $thumb = thumb($atlas[0]["url"],210,180);
                            $newsList[$i]["news_thumb"] = $thumb;
                        }
                    }
                    $ret["code"] = 1;
                    $ret["msg"] = "收藏拉取成功";
                    $ret["data"] = $newsList;
                }else{
                    $ret["code"] = -1;
                    $ret["msg"] = $pageNum == 1 ?  "无收藏" : "已无更多收藏";
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

                $commentList = $this->commentDb
                    ->table("__NEWS_COMMENT__ c,__NEWS__ n")
                    ->where("c.news_id = n.news_id and c.userid=".$user["userid"])
                    ->field("c.comment_id,c.comment_content,c.addtime,n.news_id,n.news_title,n.type_id,n.news_thumb,n.news_atlas")
                    ->limit($startCount,$pageSize)
                    ->order("c.addtime desc")
                    ->select();
                if($commentList){
                    foreach ($commentList as $i => $v) {
                        $commentList[$i]["time"] = date("Y-m-d",$v["addtime"]);
                        if($v["news_thumb"]){
                            $commentList[$i]["news_thumb"] = thumb($v["news_thumb"],120,80);
                        }
                        if($v["news_atlas"]){
                            $atlas = unserialize($v["news_atlas"]);
                            $thumb = thumb($atlas[0]["url"],120,80);
                            $commentList[$i]["news_thumb"] = $thumb;
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
}
