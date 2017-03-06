<?php
namespace Index\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
    	$data['userid'] = session('userid');
    	$data['realname'] = session('realname');
    	$this->assign($data);
        $this->display('Index/index');
    }
    
    public function app(){
    	$this->display('Index/app');
    }
    public function about(){
    	$data['userid'] = session('userid');
    	$data['realname'] = session('realname');
    	$this->assign($data);
    	$this->display('Index/about');
    }
    public function contact(){
    	$data['userid'] = session('userid');
    	$data['realname'] = session('realname');
    	$this->assign($data);
    	$this->display('Index/contact');
    }
    public function privacy(){
    	$data['userid'] = session('userid');
    	$data['realname'] = session('realname');
    	$this->assign($data);
    	$this->display('Index/privacy');
    }
    public function termofservice(){
    	$data['userid'] = session('userid');
    	$data['realname'] = session('realname');
    	$this->assign($data);
    	$this->display('Index/termofservice');
    }
    public function lineData(){
        echo ipCity('118.123.249.76');
        //获取今天
        //$date  = date('Y-m-d');
        //$date = date('Y-m-d',strtotime($date.'-1 day'));
        //echo $date;
        $num  = 7;
        for ($i=7; $i >=1 ; $i--) { 
            $dateStart = strtotime(date('Y-m-d',strtotime('-'.$i.' day')));
            $d = strtotime($dateStart.'+'.$i.' day');
            echo $dateStart;
            //echo $d;
            //echo date('Y-m-d',strtotime($dateStart.' +'.$i.' day'))."<br />";
            /*$dateEnd = date('Y-m-d',strtotime($dateStart.'+'.$i.' day'))."<br />";
            echo $dateStart;
            echo $dateEnd;*/
        }
        /*if($_GET['type']=='send'){
            echo '[{"y":0,"Label":"08-29"},{"y":0,"Label":"08-30"},{"y":0,"Label":"08-31"},{"y":0,"Label":"09-01"},{"y":0,"Label":"09-02"},{"y":0,"Label":"09-03"},{"y":0,"Label":"09-04"}]';
        }else if($_GET['type']=='read'){
            echo '[{"y":183,"Label":"08-29"},{"y":25,"Label":"08-30"},{"y":23,"Label":"08-31"},{"y":5,"Label":"09-01"},{"y":16,"Label":"09-02"},{"y":17,"Label":"09-03"},{"y":6,"Label":"09-04"}]';
        }*/
        
    }

}