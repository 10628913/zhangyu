<?php
namespace App\Controller;
use Think\Controller;
class GoodsController extends AppController {
    function _initialize() {
        parent::_initialize();
        parent::requestMethodValidate(REQUEST_METHOD);
        $this->adminInfo = parent::validateAdmin(I("token"));
    }
    public function __construct(){
        parent::__construct(); 
        $this->adminDb = M("Admin");
        $this->goodsDb = M("Goods");
    }
    // 获取商品列表
    public function getGoodsLists(){

        $uid = $this->adminInfo["uid"];

        //分页
        $pageSize = I("pageSize") ? I("pageSize") : 20;
        $pageNum = I("pageNum") ? I("pageNum") : 1;
        $startCount = ($pageNum - 1) * $pageSize;

        $where["uid"] = $uid;
        $where["is_on_sale"] = "1";
        $where["is_commit"] = "1";
        $where["is_show"] = "1";
        $goodsList = $this->goodsDb
        ->where($where)
        ->field("goods_id,uid,goods_name,goods_remark,goods_original_img,goods_sn")
        ->limit($startCount,$pageSize)
        ->order("goods_id desc")
        ->select();
        if($goodsList){
            foreach ($goodsList as $i => $v) {
                if($v["goods_original_img"]){
                    $goodsList[$i]["goods_original_img"] = thumb($v["goods_original_img"],320,240);
                }
            }
            $ret["code"] = 1;
            $ret["msg"] = "商品信息获取成功";
            $ret["data"] = $goodsList;
            $this->ajaxReturn($ret);

        }else{
            $msg = $pageNum == 1 ? "无商品信息" : "已无更多商品";
            $this->httpResponse(-1,$msg);
        }
    }
            


    // 添加商品
    public function goodsAdd()
    {

        $data["uid"] = $this->adminInfo["uid"];
        $upload = new \Think\Upload();
        $upload->maxSize = 5242880 ;
        $upload->exts = array("jpg", "gif", "png", "jpeg");
        $info = $upload->upload();
        
        if(!$info) 
        {
            $this->error($upload->getError());
            $this->httpResponse(-1,"无上传图片");
        }else
        {

            $imageUrl = C("SITE_URL").C("UPLOAD_PATH").$info["file"]["savepath"].$info["file"]["savename"];
            // $thumb = thumb($imageUrl,320,240);
            $data["goods_original_img"] = $imageUrl;

        }

        $data["goods_name"] = I("goods_name");
        $data["goods_sn"] = I("goods_sn");
        $data["goods_remark"] = I("goods_remark");
        $data["goods_ontime"] = $data["goods_update_time"] = time();
        // 默认上架
        $data["is_on_sale"] = 1;
        // 默认可下单
        $data["is_commit"] = 1;
        // 默认在前台显示
        $data["is_show"] = 1;
        
        if(!$data["goods_name"])
        {
            $this->httpResponse(-1,"商品名不能为空");
        }
        if(!$data["goods_remark"]){
            $this->httpResponse(-1,"商品简介不能为空");
        }
        $result = $this->goodsDb->data($data)->add();
        if($result)
        {   
            $ret["code"] = 1;
            $ret["msg"] = "添加商品成功";
            $ret["data"]["goods_id"] = $result;
            $ret["data"]["imageUrl"] =$imageUrl;
            $this->ajaxReturn($ret);
        }else
        {
            $this->httpResponse(-1,"添加商品失败");
        }
        
    }

    // 编辑商品
    public function goodsEdit()
    {
        $where["goods_id"] = I("goods_id");
        $where['uid'] = $data["uid"] = $this->adminInfo["uid"];
        
        $goodsInfo = $this->goodsDb->where($where)->find();
        if(I("goods_original_img")){
            $data["goods_original_img"] = I("goods_original_img");
        }else{
            $upload = new \Think\Upload();
            $upload->maxSize = 5242880 ;
            $upload->exts = array("jpg", "gif", "png", "jpeg");
            $info = $upload->upload();
            
            if(!$info)
            {
                $this->error($upload->getError());
                $this->httpResponse(-1,"无上传图片");
            }else
            {

                $imageUrl = C("SITE_URL").C("UPLOAD_PATH").$info["file"]["savepath"].$info["file"]["savename"];
                // $thumb = thumb($imageUrl,320,240);
                $data["goods_original_img"] = $imageUrl;

            } 
        }
        
        
       
        $data["goods_name"] = I("goods_name") ? I("goods_name") : "";
        $data["goods_sn"] = I("goods_sn") ? I("goods_sn") : "";
        $data["goods_remark"] = I("goods_remark") ? I("goods_remark") : "";
        $data["goods_ontime"] = $data["goods_update_time"] = time();
        $data["is_on_sale"] = 1;
        $data["is_commit"] = 1;
        
        if(!$data["goods_name"])
        {
            $this->httpResponse(-1,"商品名不能为空");
        }
        if(!$data["goods_remark"]){
            $this->httpResponse(-1,"商品简介不能为空");
        }
        $result = $this->goodsDb->where($where)->data($data)->save();
        if($result)
        {   
            $ret["code"] = 1;
            $ret["msg"] = "修改成功";
            $this->ajaxReturn($ret);
        }else
        {
            $this->httpResponse(-1,"信息无修改");
        }
        
    }  

    // 获取商品详情
    public function getGoodsInfo(){
        if (IS_POST) {
           $goods_id = intval($_POST["goods_id"]);
           $where["goods_id"] = $goods_id;
           $goodsInfo = $this->goodsDb->where($where)->find();
           if ($goodsInfo) {
                $this->httpResponse(1,"获取商品信息成功",$goodsInfo);
            }else{
                $this->httpResponse(-1,"获取商品信息失败");
            }
        }
    }

    // 删除商品
    public function deleteGoods(){        

           $goods_id = I("goods_id");
           $where["goods_id"] = $goods_id;
           $data["is_show"] = 0;
           $result = $this->goodsDb->where($where)->save($data);
           if ($result) {
                $this->httpResponse(1,"删除成功");
            }else{
                $this->httpResponse(-1,"删除失败");
            }
        
    }











}