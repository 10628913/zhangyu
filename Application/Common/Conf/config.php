<?php
/**
 * 系统配文件
 * 所有系统级别的配置
 */
return array(
    'DEFAULT_MODULE'     => 'Index',
    'URL_MODEL'          => '3',
    'DEFAULT_FILTER' => 'strip_sql,htmlspecialchars',
    'SESSION_AUTO_START' => true,
    'DEFAULT_THEME' => 'default',
    'LOG_RECORD' => true,
    'LOG_LEVEL' =>'WARN',
    'VIEW_PATH' => './Tpl/',
    //站点url
    'SITE_URL' => 'http://127.0.0.1/',
    //样式表
    'CSS_PATH' => __ROOT__.'/Public/Css',
    //JS
    'JS_PATH' => __ROOT__.'/Public/Js',
    //IMAGE
    'IMAGE_PATH' => __ROOT__.'/Public/Image',
    'PLUGIN_PATH' => __ROOT__.'/Public/Plugin',
    //附件上传目录
    'UPLOAD_PATH'=> 'Uploads/',
    /* 数据库配置 */
    'DB_TYPE'   => 'mysql', // 数据库类型
    'DB_HOST'   => '127.0.0.1', // 服务器地址
    'DB_NAME'   => 'zhangyudb', // 数据库名
    'DB_USER'   => 'root', // 用户名
    'DB_PWD'    => 'cjw123',  // 密码
    'DB_PORT'   => '3306', // 端口
    'DB_PREFIX' => 'zy_', // 数据库表前缀
);
