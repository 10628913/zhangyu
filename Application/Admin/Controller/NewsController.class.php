<?php
/**
* 新闻模块管理
*/
namespace Admin\Controller;
class NewsController extends BaseController {
    public function _initialize() {
        vendor('JPush.JPush');
    }
    public function __construct(){
        parent::__construct();
        $this->newsDb = M("News");
        $this->newsClassifyDb = M("News_classify");
        $this->newsTypeDb = M("News_type");
        $this->newTagsDb = M("News_tags");
        $this->areaDb = M("Area");
        $this->authGroupDb = M("Auth_group");
        $this->memberGroupDb = M("Member_group");
        $this->newsCommentDb = M('News_comment');
        $this->newsAttentionDb = M('News_attention');
        $this->newsCollectDb = M('News_collect');
        $this->moduleExtendDb = M('Module_extend');
        $this->memberDb = M('Member');
    }
    // 新闻首页
    public function index(){
        $this->assign('searchInfo',$searchInfo);
        $count = $this->newsDb->where($whereSearch)->count();
        $Page = new \Think\Page($count,50);
        $list = $this->newsDb->limit($Page->firstRow.','.$Page->listRows)->where($whereSearch)->order('news_id desc')->select();
        $show = $Page->show();
        $this->assign('page',$show);
        $this->assign('list',$list);

        // 新闻分类
        $tree = new \Org\Tree\TreeClassify;
        $newsClassifyList = $this->newsClassifyDb->where($where)->order('classify_sort desc,classify_id desc')->select();
        $newsClassifyList = $tree->makeTree($newsClassifyList);
        $this->assign("newsClassifyList",$newsClassifyList);
        $this->display("news_index");
    }
    // 新闻分类获取
    public function newsClassifyTree(){
        $tree = new \Org\Tree\TreeClassify;
        $newsClassifyList = $this->newsClassifyDb->where($where)->order('classify_sort desc,classify_id desc')->select();
        $newsClassifyList = $tree->makeTree($newsClassifyList);
        $this->assign("newsClassifyList",$newsClassifyList);
        $this->display("news_list_classify_frame");
    }
    // 新闻列表
    public function newsList(){
        $classify_id = I("classify_id");
        if($classify_id){
            $where['parent_classify_id'] = $classify_id;
        }
        if(isset($_GET['news_status']) && !empty($_GET['news_status'])){
            $where['news_status'] = intval($_GET['news_status']);
            $searchInfo['news_status'] = I("news_status");
        }
        if(isset($_GET['addtime']) && !empty($_GET['addtime'])){
            $where['addtime'] = array("egt",strtotime($_GET['addtime']));
            $searchInfo['addtime'] = I('addtime');
        }
        if($_GET['news_type_id'] && !empty($_GET['news_type_id'])){
            $where['type_id'] = $_GET['news_type_id'];
            $searchInfo['news_type_id'] = I("news_type_id");
        }

        if(isset($_GET['city_name']) && !empty($_GET['city_name'])){
            $where['city_name'] = array('like',"%".I('city_name')."%");
            $searchInfo['city_name'] = I("city_name");
        }
        $this->assign('searchInfo',$searchInfo);
        // if(isset($_GET['keywords']) && !empty($_GET['keywords'])){
        //     $keywords = I('keywords');
        //     $whereSearch['news_title'] = array('like',"%".$keywords."%");
        //     $whereSearch['news_keywords'] = array('like',"%".$keywords."%");
        //     $whereSearch['_logic'] = 'or';
        //     if(I("classify_id") && !empty($_GET['keywords'])){
        //         $where['_complex'] = $whereSearch;
        //         $where['parent_classify_id'] = I("classify_id");
        //     }else{
        //         $where = $whereSearch;
        //     }
        //     $searchInfo['keywords'] = $keywords;
        //     $this->assign('searchInfo',$searchInfo);
        // }
        $this->assign("classify_id",$classify_id);

        $count = $this->newsDb->where($where)->count();
        $Page = new \Think\Page($count,50);
        $list = $this->newsDb->limit($Page->firstRow.','.$Page->listRows)->where($where)->order('news_id desc')->select();
        $show = $Page->show();
        $this->assign('page',$show);
        $this->assign('list',$list);
        $this->display("news_list_frame");
    }
    // 列表页批量操作
    public function newsBatchManager(){
        if(IS_POST){
            $news_ids = explode(",",$_POST['news_ids']);
            $type = $_POST['type'];
            switch ($type) {
                //发布
                case 'publish':
                    foreach ($news_ids as $key => $v) {
                        $where['news_id'] = $v;
                        $this->newsDb->where($where)->setField("news_status",'99');
                    }
                    $this->success('操作成功');
                    break;
                //待审
                case 'waitting':
                    foreach ($news_ids as $key => $v) {
                        $where['news_id'] = $v;
                        $this->newsDb->where($where)->setField("news_status",'3');
                    }
                    $this->success('操作成功');
                    break;
                // 删除
                case 'delete':
                    foreach ($news_ids as $key => $v) {
                        $where['news_id'] = $v;
                        $this->newsDb->where($where)->delete();
                    }
                    $this->success('操作成功');
                    break;
            }

        }
    }
    public function newsListByUser(){
        $where['fs_news.userid'] = array("gt",0);
        $classify_id = I("classify_id");
        if($classify_id){
            $where['parent_classify_id'] = $classify_id;
        }
        if(isset($_GET['keywords']) && !empty($_GET['keywords'])){
            $keywords = I('keywords');
            $whereSearch['news_title'] = array('like',"%".$keywords."%");
            $whereSearch['news_keywords'] = array('like',"%".$keywords."%");
            $whereSearch['_logic'] = 'or';
            if(I("classify_id") && !empty($_GET['keywords'])){
                $where['_complex'] = $whereSearch;
                $where['parent_classify_id'] = I("classify_id");
            }else{
                $where = $whereSearch;
            }
            $searchInfo['keywords'] = $keywords;
            $this->assign('searchInfo',$searchInfo);
        }
        $this->assign("classify_id",$classify_id);

        $count = $this->newsDb->where($where)->count();
        $Page = new \Think\Page($count,50);
        // $list = $this->newsDb->limit($Page->firstRow.','.$Page->listRows)->where($where)->order('news_id desc')->select();
        $list = $this->newsDb
                    ->join('left join fs_member ON fs_news.userid=fs_member.userid')
                    ->field('fs_news.news_id,fs_news.news_title,fs_news.news_status,fs_news.addtime,fs_news.userid,fs_member.nickname,fs_member.userid')
                    ->limit($Page->firstRow.','.$Page->listRows)
                    ->where($where)
                    ->order('news_id desc')
                    ->select();
        $show = $Page->show();
        $this->assign('page',$show);
        $this->assign('list',$list);
        $this->display("news_user_list");
    }
    /**
    *用户投稿批量操作通过
    */
    public function newsPassByUser(){
        if(IS_POST){
            $news_ids = explode(",",$_POST['news_ids']);
            foreach ($news_ids as $key => $v) {
                $this->newsDb->where(array("news_id"=>$v))->setField("news_status",99);
            }
            $this->success('操作成功');
        }
    }
    /**
    *用户投稿批量操作拒绝
    */
    public function newsUnpassByUser(){
        if(IS_POST){
            $news_ids = explode(",",$_POST['news_ids']);
            foreach ($news_ids as $key => $v) {
                $where['news_id'] = $v;
                $this->newsDb->where($where)->setField("news_status",'-1');
            }
            $this->success('操作成功');
        }
    }
    // 新闻发布
    public function newsAdd(){
        if(IS_POST){
            $data = $_POST['info'];
            $data['news_source_type'] = 1;
            $data['addtime'] = $data['update_time'] = $data['news_pub_time'] = time();
            $data['admin_uid'] = session("admin_uid");
            //判断当前classify_id是否有上级栏目
            $whereClassify['classify_id'] = $data['classify_id'];
            $classifyParentId = $this->newsClassifyDb->where($whereClassify)->getField('parent_id');
            if($classifyParentId && $classifyParentId != 0){
                $data['parent_classify_id'] = $classifyParentId;
            }else{
                $data['parent_classify_id'] = $data['classify_id'];
            }
            if($_POST['photos_url']){
                foreach ($_POST['photos_url'] as $key => $v) {
                    $data['news_atlas'][$key]['url'] = $v;
                    $data['news_atlas'][$key]['desc'] = $_POST['photos_desc'][$key];
                }
                $data['news_atlas']  = serialize($data['news_atlas']);
            }
            $result = $this->newsDb->data($data)->add();
            if($result){
                $this->success("发布成功");
            }else{
                $this->error("发布失败");
            }
        }else{
            // 新闻分类
            $tree = new \Org\Tree\TreeClassify;
            $newsClassifyList = $this->newsClassifyDb->where($where)->order('classify_sort desc,classify_id desc')->select();
            $newsClassifyList = $tree->makeTree($newsClassifyList);
            $this->assign("newsClassifyList",$newsClassifyList);
            // print_r($newsClassifyList);
            $classify_id = I("classify_id");
            $this->assign("classify_id",$classify_id);
            // 获取分类名称
            $whereClassify['classify_id'] = $classify_id;
            $classify_name = $this->newsClassifyDb->where($whereClassify)->getField("classify_name");
            $this->assign("classify_name",$classify_name);
            // 新闻类型
            $newsTypeList = $this->newsTypeDb->select();
            $this->assign("newsTypeList",$newsTypeList);
            // 获取地区
            $whereArea['parent_id'] = 0;
            $whereArea['id'] = array('gt',1);
            $areaList = $this->areaDb->where($whereArea)->order('sort asc, id asc')->select();
            $this->assign("areaList",$areaList);
            // 编辑器
            $editor = new \Org\Editor\Editor;
            $this->assign('editor',$editor);
            $this->display("news_add");
        }
    }
    // 新闻编辑
    public function newsEdit(){
        if(IS_POST){
            $whereNews['news_id'] = I('news_id');
            $data = $_POST['info'];
            $data['update_time'] = time();
            $data['admin_uid'] = session("admin_uid");
            //判断当前classify_id是否有上级栏目
            $whereClassify['classify_id'] = $data['classify_id'];
            $classifyParentId = $this->newsClassifyDb->where($whereClassify)->getField('parent_id');
            if($classifyParentId && $classifyParentId != 0){
                $data['parent_classify_id'] = $classifyParentId;
            }else{
                $data['parent_classify_id'] = $data['classify_id'];
            }
            if($_POST['photos_url']){
                foreach ($_POST['photos_url'] as $key => $v) {
                    $data['news_atlas'][$key]['url'] = $v;
                    $data['news_atlas'][$key]['desc'] = $_POST['photos_desc'][$key];
                }
                $data['news_atlas']  = serialize($data['news_atlas']);
            }
            $result = $this->newsDb->where($whereNews)->save($data);
            $this->success("发布成功");
        }else{
            // 新闻分类
            $tree = new \Org\Tree\TreeClassify;
            $newsClassifyList = $this->newsClassifyDb->where($where)->order('classify_sort desc,classify_id desc')->select();
            $newsClassifyList = $tree->makeTree($newsClassifyList);
            $this->assign("newsClassifyList",$newsClassifyList);
            $whereNews['news_id'] = I('news_id');
            $info = $this->newsDb->where($whereNews)->find();
            $info['news_atlas'] = unserialize($info['news_atlas']);
            $this->assign($info);
            // 获取分类名称
            $whereClassify['classify_id'] = $info['classify_id'];
            $classify_name = $this->newsClassifyDb->where($whereClassify)->getField("classify_name");
            $this->assign("classify_name",$classify_name);
            // 新闻类型
            $newsTypeList = $this->newsTypeDb->select();
            $this->assign("newsTypeList",$newsTypeList);
            // 获取地区
            $whereArea['parent_id'] = 0;
            $whereArea['id'] = array('gt',1);
            $areaList = $this->areaDb->where($whereArea)->order('sort asc, id asc')->select();
            $this->assign("areaList",$areaList);
            // 编辑器
            $editor = new \Org\Editor\Editor;
            $this->assign('editor',$editor);
            $this->display("news_edit");
        }
    }
    public function newsDelete(){
        if(IS_POST){
            $where['news_id'] = I('news_id');
            $this->newsDb->where($where)->delete();
            $this->success("操作成功");
        }
    }
    // 新闻推送
    public function newsPush(){
        if(IS_POST){
            $news_id = I('news_id');
            $whereNews['news_id'] = $news_id;
            $newsData = $this->newsDb->where($whereNews)->find();
            $news_title = $newsData['news_title'];
            $news_type_id = $newsData['type_id'];
            if($news_title){
                // 获取极光配置
                $extendInfo = $this->moduleExtendDb->find();
                $jpush_title = $extendInfo['jpush_title'];
                $jpush_app_key = $extendInfo['jpush_app_key'];
                $jpush_master_secret = $extendInfo['jpush_master_secret'];
                $client = new \JPush($jpush_app_key, $jpush_master_secret);
                // 推送发送
                $result = $client->push()
                    ->setPlatform(array('ios', 'android'))
                    ->addAllAudience()
                    ->addAndroidNotification($news_title, ''.$jpush_title .'', 1, array("news_id"=>"".$news_id."","news_type_id"=>"".$news_type_id."","type"=>"newsPush"))
                    ->addIosNotification($news_title, 'iOS sound', \JPush::DISABLE_BADGE, true, 'iOS category', array("news_id"=>"".$news_id."","news_type_id"=>"".$news_type_id."","type"=>"newsPush"))
                    // ->setMessage($news_title, ''.$jpush_title .'', 'type', array("news_id"=>"".$news_id."","news_type_id"=>"".$news_type_id."","type"=>"newsPush"))//
                    ->setOptions(100000, 0, null, false)
                    ->send();
                if($result){
                    $this->newsDb->where($whereNews)->setField("is_jpush",1);
                    $this->success("推送成功");
                }else{
                    $this->error("推送失败");
                }
            }else{
                $this->error("推送失败");
            }
        }

    }

    // 新闻类型管理
    public function newsTypeManage(){
        $tree = new \Org\Tree\Tree;
        $data = $this->newsTypeDb->order('sort asc,type_id desc')->select();
        $this->assign("list",$data);
        $this->display("news_type_list");
    }
    //新闻类型添加
    public function newsTypeAdd(){
        if(IS_POST){
            $data = $_POST["info"];
            if($this->newsTypeDb->add($data)){
                $this->success("添加成功");
            }else{
                $this->error("添加失败");
            }
        }else{
            $this->display("news_type_add");
        }
    }
    //新闻类型删除
    public function newsTypeDelete(){
        if(IS_POST){
            $where["type_id"] = $_POST["id"];
            if($this->newsTypeDb->where($where)->delete()){
                $this->success("删除成功");
            }else{
                $this->error("删除失败");
            }
        }
    }
    //新闻类型编辑
    public function newsTypeEdit(){
        if(IS_POST){
            $data = $_POST["info"];
            if($this->newsTypeDb->save($data)){
                $this->success("编辑成功");
            }else{
                $this->error("编辑失败");
            }
        }else{
            $where["type_id"] = I("id");
            $info = $this->newsTypeDb->where($where)->find();
            $this->assign($info);
            $this->display("news_type_edit");
        }
    }
    /*************************************************news type manage end*************************************************/
    //新闻分类管理
    public function newsClassifyMange(){
        $tree = new \Org\Tree\Tree;
        $config = array(
            'primary_key' 	=> 'classify_id'
        );
        $data = $this->newsClassifyDb->order('classify_sort asc')->select();
        $columnList = $tree->makeTree($data,$config);
        $this->assign("list",$columnList);
        $this->display("news_classify_list");
    }
    //添加新闻分类
    public function newsClassifyAdd(){
        if(IS_POST){
            $info = $_POST["info"];
            if($this->newsClassifyDb->add($info)){
                $this->success("添加成功");
            }else{
                $this->error("添加失败");
            }
        }else{
            $parentId = I("pid");
            $classifyList = $this->newsClassifyDb->where('parent_id = 0')->field("classify_id,classify_name")->select();
            $memberGroupList = $this->memberGroupDb->where("status = 1")->field("id,title")->select();
            if ($parentId) {
                $this->assign("parent_id",$parentId);
            }
            $this->assign("list",$classifyList);
            $this->assign("memberGroupList",$memberGroupList);
            $this->display("news_classify_add");
        }
    }
    //新闻类型编辑
    public function newsClassifyEdit(){
        if(IS_POST){
            $info = $_POST["info"];
            if($this->newsClassifyDb->save($info)){
                $this->success("编辑成功");
            }else{
                $this->error("编辑失败");
            }
        }else{
            $id = I("id");
            $memberGroupList = $this->memberGroupDb->where("status = 1")->field("id,title")->select();
            $info = $this->newsClassifyDb->where('classify_id="'.$id.'"')->find();
            $info["auth_check_group_id"] = explode(",",$info["auth_check_group_id"]);
            $info["auth_edit_group_id"] = explode(",",$info["auth_edit_group_id"]);
            $classifyList = $this->newsClassifyDb->where('parent_id = 0')->field("classify_id,classify_name")->select();
            $this->assign("list",$classifyList);
            $this->assign("memberGroupList",$memberGroupList);
            $this->assign("info",$info);
            $this->display("news_classify_edit");
        }
    }
    //新闻分类删除
    public function newsClassifyDelete(){
        if(IS_POST){
            $id = $_POST["id"];
            if($this->newsClassifyDb->where('parent_id = "'.$id.'"')->find()){
                $this->error("请先删除下级分类");
            }else{
                if($this->newsClassifyDb->where('classify_id="'.$id.'"')->delete()){
                    $this->success("删除成功");
                }else{
                    $this->error("删除失败");
                }
            }
        }
    }
    /*************************************************news classify manage end*************************************************/
    public function tagsManage(){
        $list = $this->newTagsDb->order("sort asc")->select();
        $this->assign("list",$list);
        $this->display("news_tags_list");
    }
    public function newsTagsAdd(){
        if(IS_POST){
            $data = $_POST["info"];
            if($this->newTagsDb->add($data)){
                $this->success("添加成功");
            }else{
                $this->error("添加失败");
            }
        }else{
            $this->display("news_tags_add");
        }
    }
    public function newsTagsEdit(){
        if (IS_POST) {
            $info = $_POST["info"];
            if($this->newTagsDb->save($info)){
                $this->success("编辑成功");
            }else{
                $this->error("编辑失败");
            }
        }else{
            $where["id"] = I("id");
            $info = $this->newTagsDb->where($where)->find();
            $this->assign($info);
            $this->display("news_tags_edit");
        }
    }
    /*************************************************news tags manage end*************************************************/

    /**---点评---**/
    public function newsCommentList(){
        if(isset($_GET['keywords']) && !empty($_GET['keywords'])){
            $keywords = I('keywords');
            $where['comment_content'] = array('like',"%".$keywords."%");
            $searchInfo['keywords'] = $keywords;
            $this->assign('searchInfo',$searchInfo);
        }
        $this->assign('searchInfo',$searchInfo);
        $count = $this->newsCommentDb->where($where)->count();
        $Page = new \Think\Page($count,20);
        $list = $this->newsCommentDb->limit($Page->firstRow.','.$Page->listRows)->where($where)->order('comment_id desc')->select();
        foreach ($list as $key => $v) {
            $whereMember['userid'] = $v['userid'];
            $list[$key]['user_nickname'] = $this->memberDb->where($whereMember)->getField("nickname");
        }
        $show = $Page->show();
        $this->assign('page',$show);
        $this->assign('list',$list);
        $this->display("news_comment_list");
    }
    public function newsCommentDetail(){
        $where['comment_id'] = I('comment_id');
        $info = $this->newsCommentDb->where($where)->find();
        $whereMember['userid'] = $info['userid'];
        $info['user_nickname'] = $this->memberDb->where($whereMember)->getField("nickname");
        $whereNews['news_id'] = $info['news_id'];
        $info['news_title'] = $this->newsDb->where($whereNews)->getField("news_title");
        $this->assign($info);
        layout(false);
        $this->display("news_comment_detail");
    }
    public function newsCommentDelete(){
        if(IS_POST){
            $where['comment_id'] = I('comment_id');
            $this->newsCommentDb->where($where)->delete();
            $this->success("操作成功");
        }
    }
    /**---关注---**/
    public function newsAttentionList(){
        $count = $this->newsAttentionDb->count();
        $Page = new \Think\Page($count,20);
        $list = $this->newsAttentionDb->limit($Page->firstRow.','.$Page->listRows)->order('attention_id desc')->select();
        foreach ($list as $key => $v) {
            $whereMember['userid'] = $v['userid'];
            $list[$key]['nickname'] = $this->memberDb->where($whereMember)->getField('nickname');
            $whereNews['news_id'] = $v['news_id'];
            $list[$key]['news_title'] = $this->newsDb->where($whereNews)->getField("news_title");
        }
        $show = $Page->show();
        $this->assign('page',$show);
        $this->assign('list',$list);
        $this->display("news_attention_list");
    }
    public function newsAttentionDelete(){
        if(IS_POST){
            $where['attention_id'] = I('attention_id');
            $this->newsAttentionDb->where($where)->delete();
            $this->success("操作成功");
        }
    }
    /**---收藏---**/
    public function newsCollectList(){
        $count = $this->newsCollectDb->count();
        $Page = new \Think\Page($count,20);
        $list = $this->newsCollectDb->limit($Page->firstRow.','.$Page->listRows)->order('collect_id desc')->select();
        foreach ($list as $key => $v) {
            $whereMember['userid'] = $v['userid'];
            $list[$key]['user_nickname'] = $this->memberDb->where($whereMember)->getField('nickname');
            $whereNews['news_id'] = $v['news_id'];
            $list[$key]['news_title'] = $this->newsDb->where($whereNews)->getField("news_title");
        }
        $show = $Page->show();
        $this->assign('page',$show);
        $this->assign('list',$list);
        $this->display("news_collect_list");
    }
    public function newsCollectDelete(){
        if(IS_POST){
            $where['collect_id'] = I('collect_id');
            $this->newsCollectDb->where($where)->delete();
            $this->success("操作成功");
        }
    }

}
