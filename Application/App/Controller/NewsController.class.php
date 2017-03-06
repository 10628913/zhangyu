<?php
namespace App\Controller;
use Think\Controller;
class NewsController extends Controller {
    public function __construct(){
        parent::__construct();
        $this->classifyDb = M("News_classify");
        $this->newsDb = M("News");
        $this->thumbsDb = M("News_thumbs");
        $this->commentDb = M("News_comment");
        $this->collectDb = M("News_collect");
        $this->typeDb = M("News_type");
        $this->memberDb = M("Member");
        $this->attentionDb = M("Member_attention");
    }
    //获取新闻类型
    public function getNewsTypeList(){
        if(IS_POST){
            $where["is_display"] = 1;
            $typeList = $this->typeDb->where($where)->order("sort desc")->select();
            if($typeList){
                $ret["code"] = 1;
                $ret["msg"] = "新闻类型获取成功";
                $ret["data"] = $typeList;
            }else{
                $ret["code"] = 1;
                $ret["msg"] = "新闻类型获取失败";
            }
            $this->ajaxReturn($ret);
        }
    }
    //获取新闻分类 classify_id
    public function getNewsClassify(){
        if(IS_POST){
            $classify_id = $_POST["classify_id"];
            $oneWhere["is_display"] = $twoWhere["is_display"] = 1;
            if($classify_id){
                $oneWhere["parent_id"] = $classify_id;
                $info = $this->classifyDb->where($oneWhere)->order("classify_sort asc")->select();
                if($info){
                    $ret["code"] = 1;
                    $ret["msg"] = "二级分类获取成功";
                    $ret["data"] = $info;
                }else{
                    $ret["code"] = -1;
                    $ret["msg"] = "二级分类获取失败";
                }
            }else{
                $twoWhere["parent_id"] = 0;
                $twoWhere["classify_id"] = array("gt",2);
                $info = $this->classifyDb->where($twoWhere)->order("classify_sort asc")->select();
                if($info){
                    $ret["code"] = 1;
                    $ret["msg"] = "一级分类获取成功";
                    $ret["data"] = $info;
                }else{
                    $ret["code"] = 1;
                    $ret["msg"] = "一级分类获取失败";
                }
            }
            $this->ajaxReturn($ret);
        }
    }
    //投稿获取分类
    public function getClassify(){
        if(IS_POST){
            // $sql = "select classify_id,classify_name from fs_news_classify where classify_id not in (select classify_id from fs_news_classify where parent_id > 0 and is_display = 1 GROUP BY classify_id) and is_display = 1";
            $sql = "select classify_id,classify_name from fs_news_classify where parent_id = 0 and is_display = 1";
            $classifyList = $this->classifyDb->query($sql);
            if($classifyList){
                $ret["code"] = 1;
                $ret["msg"] = "分类获取成功";
                $ret["data"] = $classifyList;
            }else{
                $ret["code"] = -1;
                $ret["msg"] = "分类获取失败";
            }
            $this->ajaxReturn($ret);
        }
    }

    //获取焦点新闻
    public function getNewsListByFocus(){
        if(IS_POST){
            $_POST["classify_id"] ? $where["classify_id"] = $_POST["classify_id"] : "";
            $_POST["parent_classify_id"] ? $where["parent_classify_id"] = $_POST["parent_classify_id"] : "";
            $_POST["city_name"] ? $where["city_name"] = array("like","%".$_POST["city_name"]."%") : "";
            $where["news_status"] = 99;
            $where["is_focus"] = 1;
            $newsList = $this->newsDb->where($where)->order("addtime desc")->field("news_id,news_status,is_focus,news_title,type_id,news_subtitle,news_thumb,news_atlas,news_sort,news_pub_time")->order("news_sort asc,news_pub_time desc")->select();
            if($newsList){
                foreach ($newsList as $i => $v) {
                    if($v["news_thumb"]){
                        $newsList[$i]["news_thumb"] = reduce($v["news_thumb"],480,321);
                    }else{
                        if($v["news_atlas"]){
                            $atlas = unserialize($v["news_atlas"]);
                            $newsList[$i]["news_thumb"] = reduce($atlas[0]["url"],480,321);
                        }
                    }
                }
                $ret["code"] = 1;
                $ret["msg"] = "图集获取成功";
                $ret["data"] = $newsList;
            }else{
                $ret["code"] = -1;
                $ret["msg"] = "图集为空";
            }
            $this->ajaxReturn($ret);
        }
    }
    //获取新闻列表
    public function getNewsList(){
        if(IS_POST){
            $token = $_POST["token"];
            //分页
            $pageSize = $_POST["pageSize"] ? $_POST["pageSize"] : 20;
            $pageNum = $_POST["pageNum"] ? $_POST["pageNum"] : 1;
            $startCount = ($pageNum - 1) * $pageSize;
            //请求param
            //分类id
            //查询二级新闻
            $_POST["classify_id"] ? $where["classify_id"] = $_POST["classify_id"] : "";
            //查询一级及二级新闻
            $_POST["parent_classify_id"] ? $where["parent_classify_id"] = $_POST["parent_classify_id"] : "";

            $_POST["req_time"] ? $where["news_pub_time"] = array("gt",$_POST["req_time"]) : "";
            //城市名称
            $_POST["city_name"] ? $where["city_name"] = array("like","%".$_POST["city_name"]."%") : "";
            //tag
            $_POST["tag"] ? $where["news_tags"] = array("like","%".$_POST["tag"]."%") : "";
            //热点
            $_POST["is_hot"] ? $where["is_hot"] = $_POST["is_hot"] : "";
            //是否推荐
            $_POST["recommend"] ? $where["recommend"] = $_POST["recommend"] : "";
            if($_POST["recommend"]){
                $newid = $_POST["news_id"] ? $_POST["news_id"] : 0;
                $where["news_id"] = array("neq",$newid);
            }
            //新闻类型
            // $_POST["type_id"] ? $where["type_id"] = $_POST["type_id"] : "";

            $searchWhere["news_status"] = $where["news_status"] = 99;
            $searchWhere["is_focus"] = $where["is_focus"] = 0;
            if($_POST["keywords"]){
                $keywords = $_POST["keywords"];
                $searchOrWhere["news_id"] = array("neq",$_POST["news_id"]);
                $searchOrWhere["news_title"] = array("like","%".$keywords."%");
                $searchOrWhere["news_keywords"] = array("like","%".$keywords."%");
                $searchOrWhere["news_tags"] = array("like","%".$keywords."%");
                $searchOrWhere['_logic'] = 'or';
                $searchWhere['_complex'] = $searchOrWhere;
                $newsList = $this->newsDb
                    ->field("news_id,fs_news.type_id,type_name,news_title,news_subtitle,news_description,news_thumb,news_atlas,news_tags,news_media,news_source,news_pub_time")
                    ->join("left join fs_news_type on fs_news_type.type_id = fs_news.type_id")
                    ->where($searchWhere)
                    ->order("news_pub_time desc")
                    ->limit($startCount,$pageSize)
                    ->select();
                    // $this->ajaxReturn($this->newsDb->getLastSql());
            }else{
                $newsList = $this->newsDb
                    ->field("news_id,fs_news.type_id,type_name,news_title,news_subtitle,news_description,news_thumb,news_atlas,news_tags,news_media,news_source,news_pub_time")
                    ->join("left join fs_news_type on fs_news_type.type_id = fs_news.type_id")
                    ->where($where)
                    ->order("news_pub_time desc")
                    ->limit($startCount,$pageSize)
                    ->select();
            }
            if($newsList){
                foreach ($newsList as $i => $v) {

                    // $str = mb_check_encoding($v["news_source"], 'UTF-8') ? $v["news_source"] : utf8_encode($v["news_source"])
                    if(mb_strlen( $v["news_source"], 'utf-8') > 6){
                        $newsList[$i]["news_source"] = mb_substr($v["news_source"],0,6,'utf-8')."...";
                    }

                    $diff = time() - (int) substr($v["news_pub_time"],0,10);
                    // $this->ajaxReturn($diff."=".date("Y-m-d H:i:s",time())."=".date("Y-m-d H:i:s",$v["news_pub_time"]));
                    $newsList[$i]["news_pub_time"] = calcTime($diff);

                    if($v["news_thumb"]){
                        $newsList[$i]["news_thumb"] = reduce($v["news_thumb"],360,240);
                    }
                    if($v["news_atlas"]){
                        $atlas = unserialize($v["news_atlas"]);
                        $atlasLength = count($atlas);
                        $height = 180;
                        switch ($atlasLength) {
                            case 1:
                                $width = 360;
                                $count = 1;
                                break;
                            case 2:
                                $width = 180;
                                $count = 2;
                                break;
                            case 3:
                                $count = 3;
                                $width = 120;
                                break;
                            default:
                                $count = 3;
                                $width = 120;
                        }
                        for($j=0;$j<$count;$j++){
                            // $this->ajaxReturn($atlas[$j]["url"]);
                            if($count == 1){
                                $thumb[$j] = reduce($atlas[$j]["url"],$width,$height);
                            }else{
                                $thumb[$j] = thumb($atlas[$j]["url"],$width,$height);
                            }

                        }

                        $newsList[$i]["thumb"] = $thumb;
                    }
                }
                $ret["code"] = 1;
                $ret["msg"] = "新闻列表拉取成功";
                $ret["req_time"] = time();
                $ret["data"] = $newsList;
            }else{
                $ret["code"] = -1;
                $ret["msg"] = $pageNum == 1 ?  "无新闻信息" : "已无更多内容";
            }
            $this->ajaxReturn($ret);
        }
    }
    public function getNewsRecommend(){
        if(IS_POST){
            //分页
            $pageSize = $_POST["pageSize"] ? $_POST["pageSize"] : 20;
            $pageNum = $_POST["pageNum"] ? $_POST["pageNum"] : 1;
            $startCount = ($pageNum - 1) * $pageSize;
            $news_id = $_POST["news_id"];
            $tags = $_POST["tags"];
            $arr = explode(",",$tags);
            $sql = "select news_id,type_id,news_title,news_subtitle,news_description,news_thumb,news_atlas,news_tags,news_media,news_source,news_pub_time from fs_news where 1=1 ";
            if($news_id){
                $sql.= " and news_id <>".$news_id;
            }
            if($arr){
                $sql .= " and (";
                for($i=0;$i<count($arr);$i++){
                    $sql .=" find_in_set('".$arr[$i]."',news_tags) or";
                }
                $sql = substr($sql,0,strlen($sql)-2).")";
            }
            $sql.=" order by news_pub_time desc";
            $sql.=" limit $startCount,$pageSize";
            $newsList = $this->newsDb->query($sql);
            if($newsList){
                foreach ($newsList as $i => $v) {

                    // $str = mb_check_encoding($v["news_source"], 'UTF-8') ? $v["news_source"] : utf8_encode($v["news_source"])
                    if(mb_strlen( $v["news_source"], 'utf-8') > 6){
                        $newsList[$i]["news_source"] = mb_substr($v["news_source"],0,6,'utf-8')."...";
                    }

                    $diff = time() - (int) substr($v["news_pub_time"],0,10);
                    // $this->ajaxReturn($diff."=".date("Y-m-d H:i:s",time())."=".date("Y-m-d H:i:s",$v["news_pub_time"]));
                    $newsList[$i]["news_pub_time"] = calcTime($diff);

                    if($v["news_thumb"]){
                        $newsList[$i]["news_thumb"] = reduce($v["news_thumb"],360,240);
                    }
                    if($v["news_atlas"]){
                        $atlas = unserialize($v["news_atlas"]);
                        $atlasLength = count($atlas);
                        $height = 180;
                        switch ($atlasLength) {
                            case 1:
                                $width = 360;
                                $count = 1;
                                break;
                            case 2:
                                $width = 180;
                                $count = 2;
                                break;
                            case 3:
                                $count = 3;
                                $width = 120;
                                break;
                            default:
                                $count = 3;
                                $width = 120;
                        }
                        for($j=0;$j<$count;$j++){
                            // $this->ajaxReturn($atlas[$j]["url"]);
                            if($count == 1){
                                $thumb[$j] = reduce($atlas[$j]["url"],$width,$height);
                            }else{
                                $thumb[$j] = thumb($atlas[$j]["url"],$width,$height);
                            }

                        }

                        $newsList[$i]["thumb"] = $thumb;
                    }
                }
                $ret["code"] = 1;
                $ret["msg"] = "新闻列表拉取成功";
                $ret["req_time"] = time();
                $ret["data"] = $newsList;
            }else{
                $ret["code"] = -1;
                $ret["msg"] = $pageNum == 1 ?  "无新闻信息" : "已无更多内容";
            }
            $this->ajaxReturn($ret);
        }
    }
    //获取新闻详情
    public function getNewsDetail(){
        if(IS_POST){
            $token = $_POST["token"];
            $collectWhere["news_id"] = $thumbsWhere["item_id"] = $where["news_id"] = $_POST["news_id"] ? $_POST["news_id"] : 0;
            $info = $this->newsDb->where($where)->find();
            if($info){
                if($info["news_thumb"]){
                    $info["news_thumb"] = reduce($info["news_thumb"],480,321);
                    $info["share_thumb"] = thumb($info["news_thumb"],100,100);
                }else{
                    $atlas = unserialize($info["news_atlas"]);
                    $atlas = $atlas[0];
                    $info["share_thumb"] = thumb($atlas["url"],100,100);
                }
                if($info["userid"]){
                    $user = $this->memberDb->where("userid=".$info["userid"])->field("nickname,avatar,province,city,area")->find();
                    if($user){
                        $info["nickname"] = $user["nickname"];
                        $info["avatar"] = $user["avatar"];
                        $info["province"] = $user["province"];
                        $info["city"] = $user["city"];
                        $info["area"] = $user["area"];
                    }
                }

                $diff = time() - (int) substr($info["news_pub_time"],0,10);
                $info["news_pub_time"] = calcTime($diff);
                if($info["news_view_count"]){
                    if($this->newsDb->execute("update fs_news set news_view_count = news_view_count+1 where news_id=".$_POST["news_id"])){
                        $info["news_view_count"] = $info["news_view_count"] + 1;
                    }
                }else{
                    $this->newsDb->execute("update fs_news set news_view_count = 1 where news_id=".$_POST["news_id"]);
                    $info["news_view_count"] = 1;
                }
                // $info["news_content"] = strip_tags($info["news_content"],"<img> <p>");
                $info["news_content"] = str_replace("src=\"/u","src=\"http://www.szlucent.cn/u",$info["news_content"]);
                $info["thumbsStatus"] = false;
                $info["collectStatus"] = false;
                if($info["type_id"] == 5){
                    $info["news_comment_count"] = $this->commentDb->where("news_id=".$info["news_id"])->count();
                }
                if($token){
                    $user = checkUser($token);
                    if($user){
                        //获取点赞状态
                        $collectWhere["userid"] = $thumbsWhere["userid"] = $user["userid"];
                        if($this->thumbsDb->where($thumbsWhere)->find()){
                            $info["thumbsStatus"] = true;
                        }
                        //获取收藏状态
                        if($this->collectDb->where($collectWhere)->find()){
                            $info["collectStatus"] = true;
                        }
                    }
                }
                //获取收藏状态

                $commentWhere["news_id"] = $where["item_id"] = $info["news_id"];
                //获取点赞数量
                $thumbsCount = $this->thumbsDb->where($where)->count();
                $info["news_laud_count"] = $thumbsCount;
                //获取评论数量
                $commentCount = $this->commentDb->where($commentWhere)->count();
                $info["news_comment_count"] = $commentCount;
                $atlas = $info["news_atlas"] = $info["news_atlas"] ? unserialize($info["news_atlas"]) : "";
                if($atlas){
                    foreach ($atlas as $i => $v) {
                        $url= getimagesize($v["url"]);
                        $atlas[$i]["width"] = $url[0];
                        $atlas[$i]["height"] = $url[1];
                    }
                    $info["news_atlas"] = $atlas;
                }
                $ret["code"] = 1;
                $ret["msg"] = "新闻详情拉取成功";
                $ret["data"] = $info;
            }else{
                $ret["code"] = -1;
                $ret["msg"] = "新闻详情拉取失败";
            }
            $this->ajaxReturn($ret);
        }
    }
    //用户App登录成功,拉取一次用户是否收藏和点赞
    public function getNewsStatus(){
        if(IS_POST){
            $user = checkUser($_POST["token"]);
            if($user){
                $thumbsWhere["item_id"] = $collectWhere["news_id"] = $_POST["news_id"];
                $thumbsWhere["userid"] = $collectWhere["userid"] = $user["userid"];
                if($this->thumbsDb->where($thumbsWhere)->find()){
                    $data["thumbsStatus"] = true;
                }else{
                    $data["thumbsStatus"] = false;
                }
                if($this->collectDb->where($collectWhere)->find()){
                    $data["collectStatus"] = true;
                }else{
                    $data["collectStatus"] = false;
                }
            }else{
                $ret["code"] = 0;
                $ret["msg"] = "新闻详情拉取失败";
            }
            $this->ajaxReturn($ret);
        }
    }
    public function getAreaInfo(){
        $areaDb = M("Area");
        $list = $areaDb->where("parent_id > 0 and id > 999")->select();
        foreach ($list as $i => $v) {
            $initial = get_letter($v["name"]);
            $redata["id"] = $v["id"];
            if($initial){
                if(preg_match('/^[a-zA-Z]+$/',substr($initial,0,1))){
                    $redata['initial'] = substr($initial,0,1);
                }else{
                    $redata['initial'] = "#";
                }
            }else{
                $redata['initial'] = "#";

            }
        }
    }
    public function getAreaJson(){
        $areaDb = M("Area");
        $arr = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
        $data = array();
        for($i=0;$i<count($arr);$i++){
            $area = $areaDb->where('initial="'.$arr[$i].'"')->field("name,initial")->select();
            $key = $arr[$i];
            $data[$key] = $area;
        }
        $this->ajaxReturn($data);
    }
    //用户投稿
    public function contribute(){
        if(IS_POST){
            $token = $_POST["token"];
            $user = checkUser($token);
            if($user){
                $data["userid"] = $user["userid"];
                $data["classify_id"] = $_POST["classify_id"];
                $data["parent_classify_id"] = $_POST["parent_classify_id"];
                $data["news_title"] = $_POST["news_title"];
                $data["news_content"] = $_POST["news_content"];
                $data["addtime"] = $data["update_time"] = $data["news_pub_time"] = time();
                $data["news_source"] = $user["nickname"];
                $data["news_source_type"] = 1;
                $data["news_status"] = 1;
                $data["admin_uid"] = 0;
                if($_POST["url"]){
                    // $atlas = $_POST["news_atlas"];
                    $data["type_id"] = 2;
                    // eval("\$array = \"$atlas\";");
                    // $data["news_atlas"] = $array;
                    $urlList = explode(",",$_POST["url"]);
                    $descList = explode(",",$_POST["desc"]);
                    for($i=0;$i<count($urlList);$i++){
                        $atlas[$i]["url"] = $urlList[$i];
                        $atlas[$i]["desc"] = $descList[$i];
                    }
                    $data["news_atlas"] = serialize($atlas);
                }else{
                    $data["type_id"] = 1;
                }
                $result = $this->newsDb->add($data);
                if($result){
                    $ret["code"] = 1;
                    $ret["msg"] = "投稿成功,请等待审核";
                    $ret["news_id"] = $result;
                }else{
                    $ret["code"] = -1;
                    $ret["msg"] = "投稿失败";
                }
            }else{
                $ret["code"] = 0;
                $ret["msg"] = "用户已在其他终端登录";
            }
            $this->ajaxReturn($ret);
        }
    }

    //用户的投稿
    public function getContribute(){
        if(IS_POST){
            $token = $_POST["token"];
            $pageSize = $_POST["pageSize"] ? $_POST["pageSize"] : 20;
            $pageNum = $_POST["pageNum"] ? $_POST["pageNum"] : 1;
            $startCount = ($pageNum - 1) * $pageSize;
            if($_POST["userid"]){
                $where["userid"] = $_POST["userid"];
            }
            if($_POST["token"]){
                $user = checkUser($token);
                if($user){
                    $where["userid"] = $user["userid"];
                }else{
                    $ret["code"] = 0;
                    $ret["msg"] = "用户已在其他终端登录";
                    $this->ajaxReturn($ret);
                }
            }
                $_POST["news_status"] ? $where["news_status"] = $_POST["news_status"] : "";

                $newsList = $this->newsDb
                    ->field("news_pub_time,news_id,fs_news.type_id,type_name,news_title,news_subtitle,news_description,news_thumb,news_atlas,news_media,news_source,news_status,news_pub_time")
                    ->join("left join fs_news_type on fs_news_type.type_id = fs_news.type_id")
                    ->where($where)
                    ->order("news_status desc,news_pub_time desc")
                    ->limit($startCount,$pageSize)
                    ->select();
                if($newsList){
                    foreach ($newsList as $i => $v) {
                        $diff = time() - (int) substr($v["news_pub_time"],0,10);
                        // $this->ajaxReturn($diff."=".date("Y-m-d H:i:s",time())."=".date("Y-m-d H:i:s",$v["news_pub_time"]));
                        $newsList[$i]["news_pub_time"] = calcTime($diff);

                        if($v["news_thumb"]){
                            $newsList[$i]["news_thumb"] = reduce($v["news_thumb"],220,180);
                        }
                        if($v["type_id"] == 2){
                            if($v["news_atlas"]){
                                $atlas = unserialize($v["news_atlas"]);
                                $atlas = $atlas[0];
                                $newsList[$i]["news_thumb"] = thumb($atlas["url"],220,180);
                            }
                        }
                        $newsList[$i]["time"] = date("Y-m-d",$v["news_pub_time"]);
                    }
                    $ret["code"] = 1;
                    $ret["msg"] = "投稿列表获取成功";
                    $ret["data"] = $newsList;
                }else{
                    $ret["code"] = -1;
                    $ret["msg"] = $pageNum == 1 ? "无投稿内容" : "已无更多投稿内容";
                }
                $this->ajaxReturn($ret);
        }
    }
    //获取用户信息
    public function getUserInfo(){
        if(IS_POST){
            $where["userid"] = $_POST["userid"];
            $userInfo = $this->memberDb->where($where)->field("userid,nickname,avatar,signature,province,city,area")->find();
            if($userInfo){
                $userInfo["attentionStatus"] = false;
                $token = $_POST["token"];
                if($token){
                    $user = checkUser($token);
                    if($user){
                        $attentionWhere["userid"] = $user["userid"];
                        $attentionWhere["attention_userid"] = $userInfo["userid"];
                        if($this->attentionDb->where($attentionWhere)->find()){
                            $userInfo["attentionStatus"] = true;
                        }
                    }
                }
                $ret["code"] = 1;
                $ret["msg"] = "用户信息获取成功";
                $ret["data"] = $userInfo;
            }else{
                $ret["code"] = -1;
                $ret["msg"] = "用户信息获取失败";
            }
            $this->ajaxReturn($ret);
        }
    }
}
