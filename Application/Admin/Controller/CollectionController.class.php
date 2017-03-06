<?php
// 后台菜单管理
namespace Admin\Controller;
class CollectionController extends BaseController {
    public function __construct(){
        parent::__construct();
        // $this->menuDb = M('Admin_menu');
        $this->featureDb = M('T_feature');
    }
    public function index(){
        $count = $this->featureDb->count();
        $Page = new \Think\Page($count,50);
        $list = $this->featureDb->limit($Page->firstRow.','.$Page->listRows)->order('id asc')->select();
        $show = $Page->show();
        $this->assign('page',$show);
        $this->assign('list',$list);
        $this->display("collection_list");
    }
    public function collectionAdd(){
        if(IS_POST){
            $data = $_POST['info'];
            $data['create_time'] = date("Y-m-d H:i:s",time());
            $result = $this->featureDb->data($data)->add();
            if($result){
                $this->success("操作成功");
            }else{
                $this->error("操作失败");
            }
        }else{
            $this->display("collection_add");
        }
    }
    public function collectionEdit(){
        if(IS_POST){
            $data = $_POST['info'];
            $url = "http://112.126.70.51:8080/crawler/crawler/feature/TFeatureController/update.do";
            $headers['RestFlag'] = '1';
            $headers['UserCode'] = 'fangshi';
            $headers['AuthNO'] = '33B454652A7A4D02ABCC9DDD5BADE556';
            $headers['VerifyCode'] = md5('fangshi'.date("YmdHis",time()).'33B454652A7A4D02ABCC9DDD5BADE556').date("YmdHis",time());
            $headerArr = array();
            foreach( $headers as $n => $v ) {
                $headerArr[] = $n .':' . $v;
            }
            $dataArr = array(
                    'id' => intval($_POST['id']), //特征ID
                    'fname' => $data['fname'], //站点名称
                    'fcode' => $data['fcode'], //采集编码
                    'domain' => $data['domain'], //站点域名
                    'seedurl' => $data['seedurl'], //种子地址
                    'listurl' => $data['listurl'], //列表规则
                    'thumbPattern' => $data['thumb_pattern'],
                    'contenturl' => $data['contenturl'], //内容链接规则
                    'footerPattern' => $data['footer_pattern'], //分页规则
                    'summaryPattern' => $data['summary_pattern'], //摘要规则
                    'keywordsPattern' => $data['keywords_pattern'],//关键词规则
                    'contenturlPattern' => $data['contenturl_pattern'],//正文链接
                    'titlePattern' => $data['title_pattern'],//标题规则
                    'authorPattern' => $data['author_pattern'],//作者规则
                    'pubTimePattern' => $data['pubtime_pattern'],//发布时间规则
                    'pubTimeFormat' => $data['pubtime_format'],//时间格式
                    'contentPattern' => $data['content_pattern'],//文章内容
                    'pagenum' => $data['pagenum'],//页数
                    'cron' => $data['cron'],//触发规则
                    'channelid' => $data['channelid']//栏目id
                );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArr);
            // curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 300);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataArr);
            $output = curl_exec($ch);
            curl_close($ch);
            echo $output;
            // $this->ajaxReturn($data);
        }else{
            $where['id'] = I('id');
            $info = $this->featureDb->where($where)->find();
            $this->assign($info);
            $this->display("collection_edit");
        }
    }
    public function conllectionDelete(){
        if(IS_POST){
            $url = "http://112.126.70.51:8080/crawler/crawler/feature/TFeatureController/delete.do";
            $headers['RestFlag'] = '1';
            $headers['UserCode'] = 'fangshi';
            $headers['AuthNO'] = '33B454652A7A4D02ABCC9DDD5BADE556';
            $headers['VerifyCode'] = md5('fangshi'.date("YmdHis",time()).'33B454652A7A4D02ABCC9DDD5BADE556').date("YmdHis",time());
            $headerArr = array();
            foreach( $headers as $n => $v ) {
                $headerArr[] = $n .':' . $v;
            }
            $data = array(
                    'id' => I('id')
                );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArr);
            // curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 300);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $output = curl_exec($ch);
            curl_close($ch);
            echo $output;
        }

    }
    public function d (){
        echo md5('fangshi'.date("YmdHis",time()).'33B454652A7A4D02ABCC9DDD5BADE556').date("YmdHis",time());
    }
    public function query(){
        $url = "http://112.126.70.51:8080/crawler/crawler/feature/TFeatureController/query.do";
        $headers['RestFlag'] = '1';
        $headers['UserCode'] = 'fangshi';
        $headers['AuthNO'] = '33B454652A7A4D02ABCC9DDD5BADE556';
        $headers['VerifyCode'] = md5('fangshi'.date("YmdHis",time()).'33B454652A7A4D02ABCC9DDD5BADE556').date("YmdHis",time());
        $headerArr = array();
        foreach( $headers as $n => $v ) {
            $headerArr[] = $n .':' . $v;
        }
        $data = array(
                'page' => 1,
                'rp' => 20
            );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArr);
        // curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $output = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($output);
        dump($data);
    }
    public function collectionStart(){
        if(IS_POST){
            // $url = "http://112.126.70.51:8080/crawler/crawler/feature/TFeatureController/start.do";
            $url = "http://112.126.70.51:8080/crawler//CrawlerController/run.do";
            $headers['RestFlag'] = '1';
            $headers['UserCode'] = 'fangshi';
            $headers['AuthNO'] = '33B454652A7A4D02ABCC9DDD5BADE556';
            $headers['VerifyCode'] = md5('fangshi'.date("YmdHis",time()).'33B454652A7A4D02ABCC9DDD5BADE556').date("YmdHis",time());
            $headerArr = array();
            foreach( $headers as $n => $v ) {
                $headerArr[] = $n .':' . $v;
            }
            $data = array(
                    'fid' => I('id')
                );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArr);
            // curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 300);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $output = curl_exec($ch);
            curl_close($ch);
            echo $output;
        }

    }
}