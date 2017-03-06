<?php
namespace App\Controller;
use Think\Controller;
class IndexController extends Controller {
	public function _initialize() {
        vendor('Pingpp.Init');
    }
    public function test(){
    	\Pingpp\Pingpp::setApiKey('sk_live_NOgxsXwagmr6rIXAuvx6KWy3');
		try {
		    $ch = \Pingpp\Charge::create(
		        array(
		            "subject"   => "1111",
		            "body"      => "22222",
		            "amount"    => "100",
		            "order_no"  => "123345",
		            "currency"  => "cny",
		            "channel"   => 'alipay',
		            "client_ip" => "117.123.249.76",
		            "app"       => array("id" => "app_rbXbDSLSq9a9vvXv"),
		            'metadata'  => array('type' => $paytype)
		        )
		    );
		    echo $ch;
		} catch (\Pingpp\Error\Base $e) {
		    header('Status: ' . $e->getHttpStatus());
		    echo($e->getHttpBody());

		}
    }
}