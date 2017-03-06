<?php
namespace App\Controller;
use Think\Controller;
class BuildController extends Controller {
    public function __construct(){
        parent::__construct();
        $this->memberDb = M('Member');
        $this->areaDb = M("Area");
        $this->buildDb = M("Building");
        $this->attentionDb = M("Building_attention");
        $this->collectDb = M("Building_collect");
        $this->commentDb = M("Building_comment");
        $this->houseDb = M("Building_house");
        $this->houseStyleDb = M("Building_house_style");
        $this->tagsDb = M("Building_tags");
        $this->typeDb = M("Building_type");
        $this->newsDb = M("News");
        $this->thumbsDb = M("Building_comment_thumbs");
        $this->reserveDb = M("Building_reserve");
    }

    //通过城市获取区域
    public function getAreaByCity(){
        if(IS_POST){
            $city = $_POST["city"];
            $where["name"] = array("like",$city.'%');
            $cityInfo = $this->areaDb->where($where)->find();
            $areaWhere["parent_id"] = $cityInfo["id"];
            $areaList = $this->areaDb->where($areaWhere)->order("sort asc")->select();
            if($areaList){
                $ret["code"] = 1;
                $ret["msg"] = "区域列表拉取成功";
                $ret["data"] = $areaList;
            }else{
                $ret["code"] = -1;
                $ret["msg"] = "区域列表拉取失败";
            }
            $this->ajaxReturn($ret);
        }
    }
    //获取户型
    public function getHouseStyle(){
        if(IS_POST){
            $houseStyleList = $this->houseStyleDb->order("house_style_sort asc")->select();
            if($houseStyleList){
                $ret["code"] = 1;
                $ret["msg"] = "户型列表拉取成功";
                $ret["data"] = $houseStyleList;
            }else{
                $ret["code"] = -1;
                $ret["msg"] = "户型列表拉取失败";
            }
        }
        $this->ajaxReturn($ret);
    }
    //获取房屋类型
    public function getBuildingType(){
        if(IS_POST){
            $typeList = $this->typeDb->order("type_sort asc")->select();
            if($typeList){
                $ret["code"] = 1;
                $ret["msg"] = "房屋类型拉取成功";
                $ret["data"] = $typeList;
            }else{
                $ret["code"] = -1;
                $ret["msg"] = "房屋类型拉取失败";
            }
        }
        $this->ajaxReturn($ret);
    }
    //获取楼盘列表
    public function getBuilding(){
        if(IS_POST){
            //分页
            $pageSize = $_POST["pageSize"] ? $_POST["pageSize"] : 20;
            $pageNum = $_POST["pageNum"] ? $_POST["pageNum"] : 1;
            $startCount = ($pageNum - 1) * $pageSize;
            //条件
            $where["status"] = 99;
            //关键词搜索
            if($_POST["building_keywords"]){
                $keywordsWhere["building_keywords"] = array("like","%".$_POST["building_keywords"]."%");
                $keywordsWhere["building_name"] = array("like","%".$_POST["building_keywords"]."%");
                $keywordsWhere["_logic"] = "or";
                $where["_complex"] = $keywordsWhere;
            }
            //地区id查询
            $_POST["building_area_id"] ? $where["building_area_id"] = $_POST["building_area_id"] : "";
            //地区名查询
            if($_POST["building_area_name"]){
                $areaName = str_replace("市","",$_POST["building_area_name"]);
                $where["building_area_name"] = array("like","%".$areaName."%");
            }
            //价格查询

            $minPrice = $_POST["minPrice"];
            $maxPrice = $_POST["maxPrice"];
            if($minPrice && $maxPrice){
                $where["building_price"] = array('between',array(intval($minPrice),intval($maxPrice)));
                $_POST["priceType"] ? $where["building_price_type"] = $_POST["priceType"] : "";
            }else{
                $minPrice ? $where["building_price"] = array('egt',intval($minPrice)) : "";
                $maxPrice ? $where["building_price"] = array('elt',intval($maxPrice)) : "";
                $_POST["priceType"] ? $where["building_price_type"] = $_POST["priceType"] : "";
            }
            //户型查询
            $_POST["building_house_style_id"] ? $where["building_house_style_id"] = $_POST["building_house_style_id"] : "";
            //房屋类型查询
            $_POST["building_type_id"] ? $where["building_type_id"] = $_POST["building_type_id"] : "";
            //房龄查询
            $minAge = $_POST["minAge"];
            $maxAge = $_POST["maxAge"];
            if($minAge && $maxAge){
                $where["building_age"] = array('between',array(intval($minAge),intval($maxAge)));
            }else{
                $minAge ? $where["building_age"] = array('egt',intval($minAge)) : "";
                $maxAge ? $where["building_age"] = array('elt',intval($maxAge)) : "";
            }
            //面积查询 building_acreage
            $minAcreage = $_POST["minAcreage"];
            $maxAcreage = $_POST["maxAcreage"];
            if($minAcreage && $maxAcreage){
                $where["building_acreage"] = array('between',array(intval($minAcreage),intval($maxAcreage)));
            }else{
                $minAcreage ? $where["building_acreage"] = array('egt',intval($minAcreage)) : "";
                $maxAcreage ? $where["building_acreage"] = array('elt',intval($maxAcreage)) : "";
            }
            $houseList = $this->buildDb
                ->where($where)
                ->field("building_id,building_name,building_house_style_id,building_thumb,building_discount,building_area_name,building_address,building_price,building_price_type,building_tags_ids")
                ->order("addtime desc,building_sort asc")
                ->limit($startCount,$pageSize)
                ->select();
                // $this->ajaxReturn($this->buildDb->getLastSql());
            if($houseList){
                foreach ($houseList as $i => $v) {
                    $houseList[$i]["building_thumb"] = thumb($v["building_thumb"],210,180);
                    if($v["building_tags_ids"]){
                        $arr = explode(",",$v["building_tags_ids"]);
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
                            $houseList[$i]["tags"] = $str;
                        }
                    }
                }
                $ret["code"] = 1;
                $ret["msg"] = "房源列表拉取成功";
                $ret["data"] = $houseList;
            }else{
                $ret["code"] = -1;
                $pageNum == 1 ? $ret["msg"] = "无匹配房源" : $ret["msg"] = "无更多房源";
            }
        }
        $this->ajaxReturn($ret);
    }

    //获取楼盘详情
    public function getBuildDetail(){
        if (IS_POST) {
            $building_id = $_POST["building_id"];
            if($building_id){
                $where["building_id"] = $_POST["building_id"];
                $buildInfo = $this->buildDb->where($where)->find();
                if($buildInfo){
                    $buildInfo["share_thumb"] = thumb($buildInfo["building_thumb"],80,80);
                    $photos = unserialize($buildInfo["building_photos"]);
                    // if($buildInfo["building_panoramic"]){
                    //     $buildInfo["building_panoramic"] = reduce($buildInfo["building_panoramic"],500,500);
                    // }
                    foreach ($photos as $i => $v) {
                        $photos[$i] = thumb($v,420,200);
                    }
                    $buildInfo["building_content"] = strip_tags($buildInfo["building_content"],"<img> <p>");
                    $buildInfo["building_content"] = str_replace("src=\"","src=\"http://www.szlucent.cn",$buildInfo["building_content"]);
                    $buildInfo["building_photos"] = $photos;
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
                    $ret["code"] = 1;
                    $ret["msg"] = "房源详情拉取成功";
                    $ret["data"] = $buildInfo;
                }else{
                    $ret["code"] = -1;
                    $ret["msg"] = "房源详情拉取失败";
                }
            }else{
                $ret["code"] = -1;
                $ret["msg"] = "参数不足";
            }
            $this->ajaxReturn($ret);
        }
    }
    //获取主力户型
    public function getHouse(){
        if(IS_POST){
            $building_id = $_POST["building_id"];
            $_POST["house_recommend"] ? $where["house_recommend"] = $_POST["house_recommend"] : "";
            $limit = $_POST["limit"] ? $_POST["limit"] : 0;
            if($building_id){
                $where["building_id"] = $_POST["building_id"];
                $houseList = $this->houseDb
                ->join("left join fs_building_house_style t on fs_building_house.house_style_id = t.house_style_id")
                ->where($where)
                ->order("house_sort asc")
                ->limit($limit)
                ->select();
                if($houseList){
                    // foreach ($houseList as $i => $v) {
                    //     $houseList[$i]["house_thumb"] = reduce($v["house_thumb"],200,200);
                    // }
                    $ret["code"] = 1;
                    $ret["msg"] = "户型列表拉取成功";
                    $ret["data"] = $houseList;
                }else{
                    $ret["code"] = -1;
                    $ret["msg"] = "户型列表拉取失败";
                }
            }else{
                $ret["code"] = -1;
                $ret["msg"] = "参数不足";
            }
            $this->ajaxReturn($ret);
        }
    }
    //获取户型详情
    public function getHouseDetail(){
        if(IS_POST){
            $house_id = $_POST["house_id"];
            if($house_id){
                $where["house_id"] = $house_id;
                $houseInfo = $this->houseDb->join("left join fs_building_house_style t on fs_building_house.house_style_id = t.house_style_id")->where($where)->find();
                if($houseInfo){
                    $photos = unserialize($houseInfo["house_photos"]);
                    $houseInfo["house_photos"] = $photos;
                    foreach ($photos as $i => $v) {
                        $photos[$i] = reduce($v,420,200);
                    }
                    $houseInfo["house_photos_thumbnail"] = $photos;
                    $ret["code"] = 1;
                    $ret["msg"] = "户型详情获取成功";
                    $ret["data"] = $houseInfo;
                }else{
                    $ret["code"] = -1;
                    $ret["msg"] = "户型详情获取失败";
                }
            }else{
                $ret["code"] = -1;
                $ret["msg"] = "参数不足";
            }
            $this->ajaxReturn($ret);
        }
    }
    //预约看房
    public function addReserve(){
        if(IS_POST){
            $user = checkUser($_POST["token"]);
            if($user){
                $data["userid"] = $where["userid"] = $user["userid"];
                $data["building_id"] = $where["building_id"] = $_POST["building_id"];
                if($this->reserveDb->where($where)->find()){
                    $ret["code"] = -1;
                    $ret["msg"] = "您已预约,请勿重复预约";
                }else{
                    $data["reserve_sn"] = create_sn();
                    $data["contact_name"] = $_POST["contact_name"];
                    $data["contact_mobile"] = $_POST["contact_mobile"];
                    $data["reserve_date"] = $_POST["reserve_date"];
                    $data["reserve_status"] = 1;
                    $data["addtime"] = time();
                    $data["update_time"] = time();
                    if($this->reserveDb->add($data)){
                        $ret["code"] = 1;
                        $ret["msg"] = "预约成功,请等待工作人员与您联系";
                    }else{
                        $ret["code"] = -1;
                        $ret["msg"] = "预约失败";
                    }
                }
            }else{
                $ret["code"] = 0;
                $ret["msg"] = "用户已在其他终端登录";
            }
            $this->ajaxReturn($ret);
        }
    }
    //获取房源详情新闻推荐
    public function getNewsRecommend(){
        if(IS_POST){
            // $areaName = $_POST["building_area_name"];
            // if($areaName){
                // $where["area_name"] = $areaName;
                $where["news_title"] = array("like","%".$_POST["building_name"]."%");
                $where["news_status"] = 99;
                $recommendInfo = $this->newsDb->field("news_id,news_title,news_subtitle,news_description")->where($where)->order("news_pub_time desc")->limit(1)->find();
                if($recommendInfo){
                    $ret["code"] = 1;
                    $ret["msg"] = "推荐新闻获取成功";
                    $ret["data"] = $recommendInfo;
                }else{
                    $ret["code"] = -1;
                    $ret["msg"] = "没有推荐";
                }
            // }else{
            //     $ret["code"] = -1;
            //     $ret["msg"] = "参数不足";
            // }
            $this->ajaxReturn($ret);
        }
    }
    //获取猜你喜欢的房源列表
    public function getLikeBuildList(){
        if(IS_POST){
            $ids = $_POST["ids"];
            $area_name = $_POST["area_name"];
            if($ids){
                $sql = "select building_id,building_name,building_thumb,building_discount,building_area_name,building_address,building_price,building_price_type,building_tags_ids from fs_building where status=99";
                $idsList = explode(",",$ids);
                if($ids){
                    $sql .= " and (";
                    for($i=0;$i<count($idsList);$i++){
                        $sql .=" find_in_set(".$idsList[$i].",building_tags_ids) or";
                    }
                    $sql = substr($sql,0,strlen($sql)-2).")";
                }
                $areaList = explode(",",$area_name);
                if($area_name){
                    $sql .= " and (";
                    for($i=0;$i<count($areaList);$i++){
                        $sql .=" find_in_set('".$areaList[$i]."',building_area_name) or";
                    }
                    $sql = substr($sql,0,strlen($sql)-2).")";
                }
                $sql .= " order by rand() limit 3";
                // $this->ajaxReturn($sql);
                $houseList = $this->buildDb->query($sql);
                if($houseList){
                    foreach ($houseList as $i => $v) {
                        $houseList[$i]["building_thumb"] = thumb($v["building_thumb"],210,180);
                        if($v["building_tags_ids"]){
                            $arr = explode(",",$v["building_tags_ids"]);
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
                                $houseList[$i]["tags"] = $str;
                            }
                        }
                    }
                    $ret["code"] = 1;
                    $ret["msg"] = "房源列表获取成功";
                    $ret["data"] = $houseList;
                }else{
                    $ret["code"] = -1;
                    $ret["msg"] = "无推荐房源";
                }
            }else{
                $ret["code"] = -1;
                $ret["msg"] = "参数不足";
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
            if($_POST["building_id"]){
                $_POST["building_id"] ? $countWhere["building_id"] = $where["building_id"] = $_POST["building_id"] : "";

            }
            if($_POST["house_id"]){
                 $countWhere["house_id"] = $where["house_id"] = $_POST["house_id"];
            }else{
                $countWhere["house_id"] = $where["house_id"] = array("eq",0);
            }

            $where["parent_id"] = 0;
            // $commentList = $this->commentDb->where($where)->order("addtime desc")->select();
            $commentList = $this->commentDb
                ->join("left join fs_member t on t.userid = fs_building_comment.userid")
                ->field("t.nickname,t.avatar,t.province,t.city,t.area,fs_building_comment.*")
                ->where($where)
                ->order("addtime desc")
                ->limit($startCount,$pageSize)
                ->select();
            if($commentList){
                foreach ($commentList as $i => $v) {
                    $thumbsCountWhere["comment_id"] = $v["comment_id"];
                    $commentList[$i]["thumbs_count"] = $this->thumbsDb->where($thumbsCountWhere)->count();
                    $commentList[$i]["is_comment"] = false;
                    if($_POST["token"]){
                        $user = checkUser($_POST["token"]);
                        if($user){
                            $thumbsCountWhere["userid"] = $user["userid"];
                            if($this->thumbsDb->where($thumbsCountWhere)->find()){
                                $commentList[$i]["is_comment"] = true;
                            }
                        }
                    }
                    $diff = time() - (int) substr($v["addtime"],0,10);
                    $commentList[$i]["comment_time"] = calcTime($diff);
                    //获取下级评论
                    $childWhere["parent_id"] = $v["comment_id"];
                    $childCommentList = $this->commentDb->join("left join fs_member t on t.userid = fs_building_comment.userid")
                        ->field("t.nickname,t.avatar,t.province,t.city,t.area,fs_building_comment.*")->where($childWhere)->order("addtime asc")->limit(3)->select();
                    $childCount = $this->commentDb->where($childWhere)->count();
                    if($childCommentList){
                        $commentList[$i]["childCount"] = $childCount;
                        $commentList[$i]["child"] = $childCommentList;
                    }
                }
                //获取评论总数
                if($pageNum == 1){
                    $commentCount = $this->commentDb->where($countWhere)->count();
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
            $commentInfo = $this->commentDb->join("left join fs_member t on t.userid = fs_building_comment.userid")
                ->field("t.nickname,t.avatar,t.province,t.city,t.area,fs_building_comment.*")->where($where)->find();
            if($commentInfo){
                //点赞数
                $thumbsCountWhere["comment_id"] = $commentInfo["comment_id"];
                $commentInfo["thumbs_count"] = $this->thumbsDb->where($thumbsCountWhere)->count();
                //是否点赞
                $commentInfo["is_comment"] = false;
                if($_POST["token"]){
                    $user = checkUser($_POST["token"]);
                    if($user){
                        $thumbsCountWhere["userid"] = $user["userid"];
                        if($this->thumbsDb->where($thumbsCountWhere)->find()){
                            $commentInfo["is_comment"] = true;
                        }
                    }
                }
                $diff = time() - (int) substr($commentInfo["addtime"],0,10);
                $commentInfo["comment_time"] = calcTime($diff);
                $childWhere["parent_id"] = $commentInfo["comment_id"];
                $childCommentList = $this->commentDb->join("left join fs_member t on t.userid = fs_building_comment.userid")
                    ->field("t.nickname,t.avatar,t.province,t.city,t.area,fs_building_comment.*")->where($childWhere)->order("addtime asc")->select();
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
            $_POST["building_id"] ? $data["building_id"] = $_POST["building_id"] : "";
            $_POST["house_id"] ? $data["house_id"] = $_POST["house_id"] : "";
            $data["comment_source_type"] = $_POST["comment_source_type"] ? $_POST["comment_source_type"] : 1;
            $data["comment_content"] = $_POST["comment_content"];
            $data["parent_id"] = $_POST["comment_id"] ? $_POST["comment_id"] : 0;
            $data["comment_price_stars"] = $_POST["comment_price_stars"] ? $_POST["comment_price_stars"] : "";
            $data["comment_location_stars"] = $_POST["comment_location_stars"] ? $_POST["comment_location_stars"] : "";
            $data["comment_traffic_stars"] = $_POST["comment_traffic_stars"] ? $_POST["comment_traffic_stars"] : "";
            $data["comment_facility_stars"] = $_POST["comment_facility_stars"] ? $_POST["comment_facility_stars"] : "";
            $data["comment_env_stars"] = $_POST["comment_env_stars"] ? $_POST["comment_env_stars"] : "";
            $data["comment_tags"] = $_POST["comment_tags"] ? $_POST["comment_tags"] : "";
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
    //删除评论
    public function delComment(){
        if(IS_POST){
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
            $itemid = $_POST["comment_id"];

            $user = checkUser($token);
            if($user){
                $userid = $user["userid"];
                //判断是否点过赞 如果有,删除它,没有,添加记录
                $where["userid"] = $userid;
                $countWhere["comment_id"] = $where["comment_id"] = $itemid;
                $result = $this->thumbsDb->where($where)->find();
                if($result){
                    $ret["code"] = -1;
                    $ret["msg"] = "您已点赞";

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
    //取消点赞
    public function thumbsOff(){
        if(IS_POST){
            $token = $_POST["token"];
            $itemid = $_POST["comment_id"];
            $user = checkUser($token);
            if($user){
                $userid = $user["userid"];
                $countWhere["comment_id"] = $where["comment_id"] = $itemid;
                $result = $this->thumbsDb->where($where)->find();
                if($result){
                    $deleteWhere["thumbs_id"] = $result["thumbs_id"];
                    if($this->thumbsDb->where($deleteWhere)->delete()){
                        $count = $this->thumbsDb->where($countWhere)->count();
                        $ret["code"] = 1;
                        $ret["msg"] = "成功取消点赞";
                        $ret["count"] = $count;
                    }else{
                        $ret["code"] = -1;
                        $ret["msg"] = "取消点赞失败";
                    }
                }else{
                    $ret["code"] = -1;
                    $ret["msg"] = "取消点赞失败";
                }

            }else{
                $ret["code"] = 0;
                $ret["msg"] = "用户已在其他终端登录";
            }
            $this->ajaxReturn($ret);
        }
    }
    //户型/房源 收藏
    public function collectItem(){
        if(IS_POST){
            $token = $_POST["token"];
            $user = checkUser($token);
            if($user){
                $where["item_id"] = $_POST["item_id"];
                $where["userid"] = $user["userid"];
                $where["item_type"] = $_POST["item_type"] ? $_POST["item_type"] : 1;
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
    public function delCollectItem(){
        if(IS_POST){
            $token = $_POST["token"];
            $user = checkUser($token);
            if($user){
                $where["item_id"] = $_POST["item_id"];
                $where["userid"] = $user["userid"];
                $where["item_type"] = $_POST["item_type"] ? $_POST["item_type"] : 1;
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
    //获取用户房源/户型收藏列表
    public function getUserCollectList(){
        if(IS_POST){
            $user = checkUser($_POST["token"]);
            if($user){
                $pageSize = $_POST["pageSize"] ? $_POST["pageSize"] : 20;
                $pageNum = $_POST["pageNum"] ? $_POST["pageNum"] : 1;
                $startCount = ($pageNum - 1) * $pageSize;
                $itemType = $where["item_type"] = $_POST["item_type"] ? $_POST["item_type"] : 1;
                $userid = $user["userid"];
                    if($itemType == 1){
                        $houseList = $this->collectDb
                            ->table("fs_building b,fs_building_collect c")
                            ->field("c.userid,b.building_id,b.building_name,b.building_thumb,b.building_discount,b.building_area_name,b.building_address,b.building_price,b.building_price_type,b.building_tags_ids")
                            ->where('c.userid="'.$userid.'" and item_type = 1 and b.building_id=c.item_id')
                            ->limit($startCount,$pageSize)
                            ->order("c.addtime desc")
                            ->select();
                        if($houseList){
                            foreach ($houseList as $i => $v) {
                                $houseList[$i]["building_thumb"] = thumb($v["building_thumb"],210,180);
                                if($v["building_tags_ids"]){
                                    $arr = explode(",",$v["building_tags_ids"]);
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
                                        $houseList[$i]["tags"] = $str;
                                    }
                                }
                            }
                            $ret["code"] = 1;
                            $ret["msg"] = "房源收藏列表拉取成功";
                            $ret["data"] = $houseList;
                        }else{
                            $ret["code"] = -1;
                            $pageNum == 1 ? $ret["msg"] = "无收藏房源" : $ret["msg"] = "无更多收藏房源";
                        }
                        $this->ajaxReturn($ret);
                    }else{
                        $collectList = $this->collectDb->where('userid='.$userid.' and item_type = 2')->select();
                        if($collectList){
                            $arr = array();
                            foreach ($collectList as $i => $v) {
                                $arr[$i] = $v["item_id"];
                            }
                                $houseWhere["house_id"] = array("in",$arr);
                                $houseList = $this->houseDb->join("left join fs_building_house_style t on fs_building_house.house_style_id = t.house_style_id")->where($houseWhere)->order("house_sort asc")->select();
                                if($houseList){
                                    // foreach ($houseList as $i => $v) {
                                    //     $houseList[$i]["house_thumb"] = reduce($v["house_thumb"],200,200);
                                    // }
                                    $ret["code"] = 1;
                                    $ret["msg"] = "户型列表拉取成功";
                                    $ret["data"] = $houseList;
                                }else{
                                    $ret["code"] = -1;
                                    $ret["msg"] = "户型列表拉取失败";
                                }
                        }else{
                            $ret["code"] = -1;
                            $pageNum == 1 ? $ret["msg"] = "无收藏户型" : $ret["msg"] = "无更多收藏户型";
                        }
                        $this->ajaxReturn($ret);
                    }
            }else{
                $ret["code"] = 0;
                $ret["msg"] = "用户已在其他终端登录";
            }
            $this->ajaxReturn($ret);
        }
    }
    //获取用户房源/户型关注列表
    public function getUserAttentionList(){
        if(IS_POST){
            $user = checkUser($_POST["token"]);
            if($user){
                $pageSize = $_POST["pageSize"] ? $_POST["pageSize"] : 20;
                $pageNum = $_POST["pageNum"] ? $_POST["pageNum"] : 1;
                $startCount = ($pageNum - 1) * $pageSize;
                $itemType = $where["item_type"] = $_POST["item_type"] ? $_POST["item_type"] : 1;
                $userid = $user["userid"];
                    if($itemType == 1){
                        $houseList = $this->attentionDb
                        ->table("fs_building b,fs_building_attention c")
                        ->field("c.userid,b.building_id,b.building_name,b.building_thumb,b.building_discount,b.building_area_name,b.building_address,b.building_price_type,b.building_price,b.building_tags_ids")
                        ->where('c.userid="'.$userid.'" and item_type = 1 and b.building_id = c.building_id')
                        ->limit($startCount,$pageSize)
                        ->order("c.addtime desc")
                        ->select();
                        // $this->ajaxReturn($this->attentionDb->getLastSql());
                        if($houseList){
                            foreach ($houseList as $i => $v) {
                                $houseList[$i]["building_thumb"] = thumb($v["building_thumb"],210,180);
                                if($v["building_tags_ids"]){
                                    $arr = explode(",",$v["building_tags_ids"]);
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
                                        $houseList[$i]["tags"] = $str;
                                    }
                                }
                            }
                            $ret["code"] = 1;
                            $ret["msg"] = "房源关注列表拉取成功";
                            $ret["data"] = $houseList;
                        }else{
                            $ret["code"] = -1;
                            $pageNum == 1 ? $ret["msg"] = "无关注房源" : $ret["msg"] = "无更多关注房源";
                        }
                        $this->ajaxReturn($ret);
                    }else{
                        $collectList = $this->attentionDb->where('userid='.$userid.' and item_type = 2')->select();
                        if($collectList){
                            $arr = array();
                            foreach ($collectList as $i => $v) {
                                $arr[$i] = $v["item_id"];
                            }
                                $houseWhere["house_id"] = array("in",$arr);
                                $houseList = $this->houseDb->join("left join fs_building_house_style t on fs_building_house.house_style_id = t.house_style_id")->where($houseWhere)->order("house_sort asc")->select();
                                if($houseList){
                                    // foreach ($houseList as $i => $v) {
                                    //     $houseList[$i]["house_thumb"] = reduce($v["house_thumb"],200,200);
                                    // }
                                    $ret["code"] = 1;
                                    $ret["msg"] = "户型列表拉取成功";
                                    $ret["data"] = $houseList;
                                }else{
                                    $ret["code"] = -1;
                                    $ret["msg"] = "户型列表拉取失败";
                                }
                        }else{
                            $ret["code"] = -1;
                            $pageNum == 1 ? $ret["msg"] = "无关注户型" : $ret["msg"] = "无更多关注户型";
                        }
                        $this->ajaxReturn($ret);
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
            $user = checkUser($_POST["token"]);
            if($user){
                $user = $user["userid"];
                $where["userid"] = $user["userid"];
                $where["item_type"] = $_POST["item_type"] ? $_POST["item_type"] : 1;
                $where["building_id"] = $_POST["building_id"];
                if($this->attentionDb->where($where)->delete()){
                    $ret["code"] = 1;
                    $ret["msg"] = "取消关注成功";
                }else{
                    $ret["code"] = -1;
                    $ret["msg"] = "取消关注失败";
                }
            }else{
                $ret["code"] = 0;
                $ret["msg"] = "用户已在其他终端登录";
            }
            $this->ajaxReturn($ret);
        }
    }
    public function addAttention(){
        if(IS_POST){
            $user = checkUser($_POST["token"]);
            if($user){
                $user = $user["userid"];
                $where["userid"] = $user["userid"];
                $where["item_type"] = $_POST["item_type"] ? $_POST["item_type"] : 1;
                $where["building_id"] = $_POST["building_id"];
                if($this->attentionDb->where($where)->find()){
                    $ret["code"] = -1;
                    $ret["msg"] = "您已关注";
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
    //我的评论列表
    public function getUserBuildCommentList(){
        if(IS_POST){
            $user = checkUser($_POST["token"]);
            if($user){
                $pageSize = $_POST["pageSize"] ? $_POST["pageSize"] : 20;
                $pageNum = $_POST["pageNum"] ? $_POST["pageNum"] : 1;
                $startCount = ($pageNum - 1) * $pageSize;
                $type = $_POST["type"] ? 1 : 0;
                if($type == 1){
                    $commentList = $this->commentDb
                        ->table("__BUILDING_COMMENT__ c,__BUILDING__ n")
                        ->where("c.building_id = n.building_id and c.userid=".$user["userid"])
                        ->field("c.comment_id,c.comment_content,c.addtime,n.building_id,n.building_thumb,n.building_name")
                        ->limit($startCount,$pageSize)
                        ->order("c.addtime desc")
                        ->select();
                }else{
                    $commentList = $this->commentDb
                        ->table("__BUILDING_COMMENT__ c,__BUILDING_HOUSE__ n")
                        ->where("c.house_id = n.house_id and c.userid=".$user["userid"])
                        ->field("c.comment_id,c.comment_content,c.addtime,n.building_id,n.house_id,n.house_name,n.house_thumb")
                        ->limit($startCount,$pageSize)
                        ->order("c.addtime desc")
                        ->select();
                }

                if($commentList){
                    foreach ($commentList as $i => $v) {
                        $commentList[$i]["time"] = date("Y-m-d",$v["addtime"]);
                        // if($v["house_thumb"]){
                        //     $commentList[$i]["house_thumb"] = thumb($v["house_thumb"],120,80);
                        // }
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
