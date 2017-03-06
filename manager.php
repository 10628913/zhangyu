<?php
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');
define('APP_DEBUG',True);
define('BIND_MODULE','Admin');
define('APP_PATH','./Application/');
//define("CUSTOM_TAGLIBS", 'cx,Common\Lib\Taglib\TagLibAdmin,Common\Lib\Taglib\TagLibUeditor');
require './ThinkPHP/ThinkPHP.php';