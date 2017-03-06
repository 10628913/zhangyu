<?php
namespace Member\Controller;
use Think\Controller;
class MemberController extends BaseController {
    public function __construct(){
        parent::__construct();
        // $this->memberDb = M('Member');
    }
    public function index(){
    	echo 1;
    }
}