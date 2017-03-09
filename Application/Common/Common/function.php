<?php
    //极光推送类
    vendor('JPush.JPush');
    /**
     * 电商Sign签名生成
     * @param data 内容
     * @param appkey Appkey
     * @return DataSign签名
     */
    function encrypt($data, $appkey) {
        return urlencode(base64_encode(md5($data.$appkey)));
    }
     /**
     *  post提交数据
     * @param  string $url 请求Url
     * @param  array $datas 提交的数据
     * @return url响应返回的html
     */
    function sendPost($url, $datas) {
        $temps = array();
        foreach ($datas as $key => $value) {
            $temps[] = sprintf('%s=%s', $key, $value);
        }
        $post_data = implode('&', $temps);
        $url_info = parse_url($url);
        $httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
        $httpheader.= "Host:" . $url_info['host'] . "\r\n";
        $httpheader.= "Content-Type:application/x-www-form-urlencoded;charset=utf-8\r\n";
        $httpheader.= "Content-Length:" . strlen($post_data) . "\r\n";
        $httpheader.= "Connection:close\r\n\r\n";
        $httpheader.= $post_data;
        $fd = fsockopen($url_info['host'], $url_info['port']);
        fwrite($fd, $httpheader);
        $gets = "";
        $headerFlag = true;
        while (!feof($fd)) {
            if (($header = @fgets($fd)) && ($header == "\r\n" || $header == "\n")) {
                break;
            }
        }
        while (!feof($fd)) {
            $gets.= fread($fd, 128);
        }
        fclose($fd);

        return $gets;
    }

    /**
     * 对用户的密码进行加密
     * @param $password
     * @param $encrypt //传入加密串，在修改密码时做认证
     * @return array/password
     */
    function password($password, $encrypt='') {
        $pwd = array();
        $pwd['encrypt'] =  $encrypt ? $encrypt : create_randomstr();
        $pwd['password'] = md5(md5(trim($password)).$pwd['encrypt']);
        return $encrypt ? $pwd['password'] : $pwd;
    }
    /**
     * 检查密码长度是否符合规定
     *
     * @param STRING $password
     * @return  TRUE or FALSE
     */
    function is_password($password) {
        $strlen = strlen($password);
        if($strlen >= 6 && $strlen <= 20) return true;
        return false;
    }
    /**
     * 生成随机字符串
     * @param string $lenth 长度
     * @return string 字符串
     */
    function create_randomstr($lenth = 6) {
        return random($lenth, '123456789abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ');
    }
    /**
    * 产生随机字符串
    *
    * @param    int        $length  输出长度
    * @param    string     $chars   可选的 ，默认为 0123456789
    * @return   string     字符串
    */
    function random($length, $chars = '0123456789') {
        $hash = '';
        $max = strlen($chars) - 1;
        for($i = 0; $i < $length; $i++) {
            $hash .= $chars[mt_rand(0, $max)];
        }
        return $hash;
    }
    /**
     * 取得文件扩展
     *
     * @param $filename 文件名
     * @return 扩展名
     */
    function fileext($filename) {
        return strtolower(trim(substr(strrchr($filename, '.'), 1, 10)));
    }
    /**
     * 字符截取
     */
    function str_cut($title, $num) {
        if (mb_strlen($title, "utf-8") > $num) {
            $title = mb_substr($title, 0, $num, "utf-8") . "...";
        }
        return $title;
    }
    /**
     * 检测验证码
     * @param  integer $id 验证码ID
     */
    function check_verify($code, $id = '1'){
        $verify = new \Think\Verify();
        return $verify->check($code, $id);
    }
    /**
    *缩略图生成
    */
    function thumb($url, $target_width = 300, $target_height = 300, $fixed = ''){
        $imgurl = str_replace("http://".$_SERVER['HTTP_HOST']."/", '', $url);
        if(!file_exists($imgurl)){
            return $url;
        }
        $imgName = basename($imgurl);
        $imgPath = dirname($imgurl);
        $image = new \Think\Image();
        $image->open(''.$imgurl.'');
        $thumbPath = dirname($imgurl).'/thumb_'.$target_width.'_'.$target_height.'_'.basename($imgurl);
        if(file_exists($thumbPath)){
            return "http://".$_SERVER['HTTP_HOST']."/".$thumbPath;
        }else{
            $result = $image->thumb($target_width, $target_height,\Think\Image::IMAGE_THUMB_CENTER)->save(''.$thumbPath.'');
            if($result){
                return "http://".$_SERVER['HTTP_HOST']."/".$thumbPath;
            }else{
                return $url;
            }
        }
    }
    function reduce($imgurl, $target_width = 300, $target_height = 300, $fixed = ''){
        $imgurl = str_replace("http://".$_SERVER['HTTP_HOST']."/", '', $imgurl);
        ///return $imgurl;
        if(!file_exists($imgurl)){
            return false;
        }
        $imgName = basename($imgurl);
        $imgPath = dirname($imgurl);
        $image = new \Think\Image();
        $image->open(''.$imgurl.'');
        $thumbPath = dirname($imgurl).'/thumb_'.$target_width.'_'.$target_height.'_'.basename($imgurl);
        if(file_exists($thumbPath)){
            return "http://".$_SERVER['HTTP_HOST']."/".$thumbPath;
        }else{
            $result = $image->thumb($target_width, $target_height,\Think\Image::IMAGE_THUMB_SCALE)->save(''.$thumbPath.'');
            return "http://".$_SERVER['HTTP_HOST']."/".$thumbPath;
        }
    }
    /**
     * 生成订单流水号
     */
    function create_sn(){
        mt_srand((double )microtime() * 1000000 );
        return date("YmdHis" ).str_pad( mt_rand( 1, 99999 ), 5, "0", STR_PAD_LEFT );
    }
    /**
     * 对数据进行编码转换
     * @param array/string $data       数组
     * @param string $input     需要转换的编码
     * @param string $output    转换后的编码
     */
    function array_iconv($data, $input = 'gbk', $output = 'utf-8') {
        if (!is_array($data)) {
            return iconv($input, $output, $data);
        } else {
            foreach ($data as $key=>$val) {
                if(is_array($val)) {
                    $data[$key] = array_iconv($val, $input, $output);
                } else {
                    $data[$key] = iconv($input, $output, $val);
                }
            }
            return $data;
        }
    }
    /**
    * 将时间戳转为文字表示
    *@param $time
    */
    function mdate($time = NULL) {
        $text = '';
        $time = $time === NULL || $time > time() ? time() : intval($time);
        $t = time() - $time; //时间差 （秒）
        $y = date('Y', $time)-date('Y', time());//是否跨年
        switch($t){
            case $t == 0:
                $text = '刚刚';
                break;
            case $t < 60:
                $text = $t . '秒前'; // 一分钟内
                break;
            case $t < 60 * 60:
                $text = floor($t / 60) . '分钟前'; //一小时内
                break;
            case $t < 60 * 60 * 24:
                $text = floor($t / (60 * 60)) . '小时前'; // 一天内
                break;
            case $t < 60 * 60 * 24 * 3:
                $text = floor($time/(60*60*24)) ==1 ?'昨天 ' . date('H:i', $time) : '前天 ' . date('H:i', $time) ; //昨天和前天
                break;
            case $t < 60 * 60 * 24 * 30:
                $text = date('m月d日 H:i', $time); //一个月内
                break;
            case $t < 60 * 60 * 24 * 365&&$y==0:
                $text = date('m月d日', $time); //一年内
                break;
            default:
                $text = date('Y年m月d日', $time); //一年以前
            break;
        }
        return $text;
    }
    /**
     * 获取请求ip
     *
     * @return ip地址
     */
    function ip() {
        if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
            $ip = getenv('REMOTE_ADDR');
        } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return preg_match ( '/[\d\.]{7,15}/', $ip, $matches ) ? $matches [0] : '';
    }

    /**
    * 根据ip获取城市
    */
    function ip_city($ip){
        //IP数据库路径
        $dat_path = './Public/File/ip.dat';
        //判断IP地址是否有效
        if(!ereg("^([0-9]{1,3}.){3}[0-9]{1,3}$", $ip)){
            return 'IP Address Invalid';
        }
        //打开IP数据库
        if(!$fd = @fopen($dat_path, 'rb')){
            return 'IP data file not exists or access denied';
        }
        //explode函数分解IP地址，运算得出整数形结果
        $ip = explode('.', $ip);
        $ipNum = $ip[0] * 16777216 + $ip[1] * 65536 + $ip[2] * 256 + $ip[3];
        //获取IP地址索引开始和结束位置
        $DataBegin = fread($fd, 4);
        $DataEnd = fread($fd, 4);
        $ipbegin = implode('', unpack('L', $DataBegin));
        if($ipbegin < 0) $ipbegin += pow(2, 32);
        $ipend = implode('', unpack('L', $DataEnd));
        if($ipend < 0) $ipend += pow(2, 32);
        $ipAllNum = ($ipend - $ipbegin) / 7 + 1;
        $BeginNum = 0;
        $EndNum = $ipAllNum;
        //使用二分查找法从索引记录中搜索匹配的IP地址记录
        while($ip1num>$ipNum || $ip2num<$ipNum) {
            $Middle= intval(($EndNum + $BeginNum) / 2);
            //偏移指针到索引位置读取4个字节
            fseek($fd, $ipbegin + 7 * $Middle);
            $ipData1 = fread($fd, 4);
            if(strlen($ipData1) < 4) {
                fclose($fd);
                return 'File Error';
            }
            //提取出来的数据转换成长整形，如果数据是负数则加上2的32次幂
            $ip1num = implode('', unpack('L', $ipData1));
            if($ip1num < 0) $ip1num += pow(2, 32);
            //提取的长整型数大于我们IP地址则修改结束位置进行下一次循环
            if($ip1num > $ipNum) {
                $EndNum = $Middle;
                continue;
            }
            //取完上一个索引后取下一个索引
            $DataSeek = fread($fd, 3);
            if(strlen($DataSeek) < 3) {
                fclose($fd);
                return 'File Error';
            }
            $DataSeek = implode('', unpack('L', $DataSeek.chr(0)));
            fseek($fd, $DataSeek);
            $ipData2 = fread($fd, 4);
            if(strlen($ipData2) < 4) {
                fclose($fd);
                return 'File Error';
            }
            $ip2num = implode('', unpack('L', $ipData2));
            if($ip2num < 0) $ip2num += pow(2, 32);
            //找不到IP地址对应城市
            if($ip2num < $ipNum) {
                if($Middle == $BeginNum) {
                    fclose($fd);
                    return 'No Data';
                }
                $BeginNum = $Middle;
            }
        }
        $ipFlag = fread($fd, 1);
        if($ipFlag == chr(1)) {
            $ipSeek = fread($fd, 3);
            if(strlen($ipSeek) < 3) {
                fclose($fd);
                return 'System Error';
            }
            $ipSeek = implode('', unpack('L', $ipSeek.chr(0)));
            fseek($fd, $ipSeek);
            $ipFlag = fread($fd, 1);
        }
        if($ipFlag == chr(2)) {
            $AddrSeek = fread($fd, 3);
            if(strlen($AddrSeek) < 3) {
                fclose($fd);
                return 'System Error';
            }
            $ipFlag = fread($fd, 1);
            if($ipFlag == chr(2)) {
                $AddrSeek2 = fread($fd, 3);
                if(strlen($AddrSeek2) < 3) {
                    fclose($fd);
                    return 'System Error';
                }
                $AddrSeek2 = implode('', unpack('L', $AddrSeek2.chr(0)));
                fseek($fd, $AddrSeek2);
            } else {
                fseek($fd, -1, SEEK_CUR);
            }
            while(($char = fread($fd, 1)) != chr(0))
                $ipAddr2 .= $char;
            $AddrSeek = implode('', unpack('L', $AddrSeek.chr(0)));
            fseek($fd, $AddrSeek);
            while(($char = fread($fd, 1)) != chr(0))
                $ipAddr1 .= $char;
        } else {
            fseek($fd, -1, SEEK_CUR);
            while(($char = fread($fd, 1)) != chr(0))
                $ipAddr1 .= $char;
            $ipFlag = fread($fd, 1);
            if($ipFlag == chr(2)) {
                $AddrSeek2 = fread($fd, 3);
                if(strlen($AddrSeek2) < 3) {
                    fclose($fd);
                    return 'System Error';
                }
                $AddrSeek2 = implode('', unpack('L', $AddrSeek2.chr(0)));
                fseek($fd, $AddrSeek2);
            } else {
                fseek($fd, -1, SEEK_CUR);
            }
            while(($char = fread($fd, 1)) != chr(0)){
                $ipAddr2 .= $char;
            }
        }
        fclose($fd);
        //返回IP地址对应的城市结果
        if(preg_match('/http/i', $ipAddr2)) {
            $ipAddr2 = '';
        }
        //显示服务商
        $ipaddr = "$ipAddr1";
        $ipaddr = preg_replace('/CZ88.Net/is', '', $ipaddr);
        $ipaddr = preg_replace('/^s*/is', '', $ipaddr);
        $ipaddr = preg_replace('/s*$/is', '', $ipaddr);
        if(preg_match('/http/i', $ipaddr) || $ipaddr == '') {
            $ipaddr = 'No Data';
        }
        return array_iconv($ipaddr);
    }
    /**
    * @desc 根据两点间的经纬度计算距离 ,返回米
    * @param float $lat 纬度值
    * @param float $lng 经度值
    */
    function getDistance($lat1, $lng1, $lat2, $lng2) {
        $earthRadius = 6371000;
        $lat1 = ($lat1 * pi() ) / 180;
        $lng1 = ($lng1 * pi() ) / 180;

        $lat2 = ($lat2 * pi() ) / 180;
        $lng2 = ($lng2 * pi() ) / 180;
        $calcLongitude = $lng2 - $lng1;
        $calcLatitude = $lat2 - $lat1;
        $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
        $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
        $calculatedDistance = $earthRadius * $stepTwo;
        return round($calculatedDistance,0);
    }
    // 获取会员用户组名称
    function getMemberGroupName($groupId){
        if(!$groupId)return;
        $db = M('Member_group');
        $where['id'] = $groupId;
        $groupName = $db->where($where)->getField("title");
        if($groupName){
            return $groupName;
        }

    }

        // 获取客户上级代理名称
    function getClientMemberName($parent_id){
        if(!$parent_id)return;
        $db = M('Member');
        $where['userid'] = $parent_id;
        $parentName = $db->where($where)->getField("nickname");
        if($parentName){
            return $parentName;
        }

    }
    // 获取客户上级boss名称
    function getClientAdminName($parent_id){
        if(!$parent_id)return;
        $db = M('Admin');
        $where['uid'] = $parent_id;
        $parentName = $db->where($where)->getField("realname");
        if($parentName){
            return $parentName;
        }

    }
    // 获取商品发布人名称
    function getGoodsAdminName($uid){
        if(!$parent_id)return;
        $db = M('Admin');
        $where['uid'] = $parent_id;
        $parentName = $db->where($where)->getField("realname");
        if($parentName){
            return $parentName;
        }

    }



    /**
    * 检查用户等级并更新
    * @param $userid
    */
    function checkMemberGrade($userid){
        if(!$userid) return;
        $memberDb = M('Member');
        $gradeSettingDb = M('Grade_setting');
        $memberGradeDb = M('Member_grade');
        $whereMember['userid'] = $userid;
        $memberEmpiric = $memberDb->where($whereMember)->getField("empiric");
        if(!$memberEmpiric) return;
        $whereGradeSetting['min_empiric'] = array('elt',$memberEmpiric);
        $whereGradeSetting['max_empiric'] = array('egt',$memberEmpiric);
        $gradeInfo = $gradeSettingDb->where($whereGradeSetting)->find();
        if($gradeInfo){
            $whereMemberGrade['userid'] = $userid;
            $data['userid'] = $userid;
            $data['grade_id'] = $gradeInfo['id'];
            $memberGradeInfo = $memberGradeDb->where($whereMemberGrade)->find();
            if($memberGradeInfo){
                $memberGradeDb->where($whereMemberGrade)->setField("grade_id",$gradeInfo['id']);
            }else{
                $memberGradeDb->add($data);
            }
        }else{
            return;
        }
    }
    /**
    * 获取用户等级信息
    * @param $userid
    * @param $field 字段名称，为空时查询全部
    */
    function getMemberGrade($userid,$field=''){
        if(!$userid) return;
        $gradeSettingDb = M('Grade_setting');
        $memberGradeDb = M('Member_grade');
        $where['userid'] = $userid;
        $gradeId = $memberGradeDb->where($where)->getField("grade_id");
        if(!$gradeId) return;
        $whereGrade['id'] = $gradeId;
        $gradeInfo = $gradeSettingDb->where($whereGrade)->find();
        if($gradeInfo){
            if(!empty($field) && isset($gradeInfo[$field]) &&!empty($gradeInfo[$field])){
                return $gradeInfo[''.$field.''];
            }else{
                return $gradeInfo;
            }
        }
    }
    /**
    * 用户积分增加或减少
    * @param $userid
    * @param $method
    */
    function setMemberPoint($userid,$method){
        if(!$userid || !$method) return;
        $memberDb = M('Member');
        $pointSettingDb = M('Point_setting');
        $pointDetailDb = M('Point_detail');
        $whereMember['userid'] = $userid;
        $wherePoint['method'] = $method;
        $memberInfo = $memberDb->where($whereMember)->find();
        $pointSettingInfo = $pointSettingDb->where($wherePoint)->find();
        if(!memberInfo || !$pointSettingInfo || $memberInfo['islock'] == 1){
            return;
        }else{
            if($pointSettingInfo['type'] == '1'){ //增加
                $result = $memberDb->where($whereMember)->setInc("point",$pointSettingInfo['count']);
            }elseif($pointSettingInfo['type'] == '2'){ //减少
                if($memberInfo <= 0 || $memberInfo['point']-$pointSettingInfo['count'] <= 0){
                    $result = $memberDb->where($whereMember)->setField("point",0);
                }else{
                    $result = $memberDb->where($whereMember)->setDec("point",$pointSettingInfo['count']);
                }
            }
            // 插入积分明细表
            if($result){
                $data['userid'] = $userid;
                $data['method'] = $pointSettingInfo['method'];
                $data['title'] = $pointSettingInfo['title'];
                $data['type'] = $pointSettingInfo['type'];
                $data['count'] = $pointSettingInfo['count'];
                $data['addtime'] = time();
                $pointDetailDb->data($data)->add();
            }
        }
    }

    /**
    * 极光推送消息
    * @param $type类型
    * @param $array
    */
    function jpushMsg($type,$data){
        if(!$type)return;
        // 获取极光配置
        $moduleExtendDb = M('Module_extend');
        $extendInfo = $moduleExtendDb->find();
        $jpush_title = $extendInfo['jpush_title'];
        $jpush_app_key = $extendInfo['jpush_app_key'];
        $jpush_master_secret = $extendInfo['jpush_master_secret'];
        $client = new \JPush($jpush_app_key, $jpush_master_secret);
        switch ($type) {
            // 私信
            case 'letterPush':
                $whereMember['userid'] = $data['receive_userid'];
                $receive_userid_jpush_registration_id = M('Member')->where($whereMember)->getField('jpush_registration_id');
                $result = $client->push()
                    ->setPlatform(array('ios', 'android'))
                    ->addRegistrationId(array(''.$receive_userid_jpush_registration_id.''))
                    ->setNotificationAlert('您有一条新的私信消息')
                    ->addAndroidNotification("您有一条新的私信消息", ''.$jpush_title .'', 1, array("letter_id"=>"".$data['letter_id']."","type"=>"letterPush"))
                    ->addIosNotification("您有一条新的私信消息", 'iOS sound', \JPush::DISABLE_BADGE, true, 'iOS category', array("letter_id"=>"".$data['letter_id']."","type"=>"letterPush"))

                    ->setOptions(100000, 0, null, false)
                    ->send();
                break;

            default:
                # code...
                break;
        }
        // 推送发送
        $result = $client->push()
                    ->setPlatform(array('ios', 'android'))
                    ->addAllAudience()
                    ->addAndroidNotification($news_title, ''.$jpush_title .'', 1, array("news_id"=>"".$news_id."","news_type_id"=>"".$news_type_id."","type"=>"newsPush"))
                    ->addIosNotification($news_title, 'iOS sound', \JPush::DISABLE_BADGE, true, 'iOS category', array("news_id"=>"".$news_id."","news_type_id"=>"".$news_type_id."","type"=>"newsPush"))
                    ->setOptions(100000, 0, null, false)
                    ->send();
    }
    // 获取送达数
    function getPushReceived($msgId){
        // 获取极光配置
        $moduleExtendDb = M('Module_extend');
        $extendInfo = $moduleExtendDb->find();
        $jpush_app_key = $extendInfo['jpush_app_key'];
        $jpush_master_secret = $extendInfo['jpush_master_secret'];
        $client = new \JPush($jpush_app_key, $jpush_master_secret);

        $result = $client->report()->getReceived(array(''.$msgId.''));
        $data = $result->data;
        if(!$data[0]->android_received){
            $android_count = 1;
        }
        if(!$data[0]->ios_msg_received){
            $ios_count = 1;
        }
        if(!$data[0]->ios_apns_sent){
            $ios_apns_count = 1;
        }
        $count = $android_count+$ios_count+$ios_apns_count;
        return $count;
    }

    // 生成8位推广号
    function createSpreadCode(){
        $memberDb = M('Member');
        $spread_code = random(8, '123456789');
        $where['spread_code'] = $spread_code;
        $isIn = $memberDb->where($where)->find();
        if($isIn){
            createSpreadCode();
        }else{
            return $spread_code;
        }
    }
    /**
     * 转换SQL关键字
     *
     * @param unknown_type $string
     * @return unknown
     */
    function strip_sql($string) {
        $pattern_arr = array(
                "/\bunion\b/i",
                "/\bselect\b/i",
                "/\bupdate\b/i",
                "/\bdelete\b/i",
                "/\boutfile\b/i",
                "/\bor\b/i",
                "/\bchar\b/i",
                "/\bconcat\b/i",
                "/\btruncate\b/i",
                "/\bdrop\b/i",
                "/\binsert\b/i",
                "/\brevoke\b/i",
                "/\bgrant\b/i",
                "/\breplace\b/i",
                "/\balert\b/i",
                "/\brename\b/i",
                "/\bmaster\b/i",
                "/\bdeclare\b/i",
                "/\bsource\b/i",
                "/\bload\b/i",
                "/\bcall\b/i",
                "/\bexec\b/i",
                "/\bdelimiter\b/i",

        );
        $replace_arr = array(
                'ｕｎｉｏｎ',
                'ｓｅｌｅｃｔ',
                'ｕｐｄａｔｅ',
                'ｄｅｌｅｔｅ',
                'ｏｕｔｆｉｌｅ',
                'ｏｒ',
                'ｃｈａｒ',
                'ｃｏｎｃａｔ',
                'ｔｒｕｎｃａｔｅ',
                'ｄｒｏｐ',
                'ｉｎｓｅｒｔ',
                'ｒｅｖｏｋｅ',
                'ｇｒａｎｔ',
                'ｒｅｐｌａｃｅ',
                'ａｌｅｒｔ',
                'ｒｅｎａｍｅ',
                'ｍａｓｔｅｒ',
                'ｄｅｃｌａｒｅ',
                'ｓｏｕｒｃｅ',
                'ｌｏａｄ',
                'ｃａｌｌ',
                'ｅｘｅｃ',
                'ｄｅｌｉｍｉｔｅｒ',

        );

        return is_array($string) ? array_map('strip_sql', $string) : preg_replace($pattern_arr, $replace_arr, $string);
    }
    // 定义一个函数getIP() 客户端IP，
    function getIP(){
        if (getenv("HTTP_CLIENT_IP"))
             $ip = getenv("HTTP_CLIENT_IP");
        else if(getenv("HTTP_X_FORWARDED_FOR"))
                $ip = getenv("HTTP_X_FORWARDED_FOR");
        else if(getenv("REMOTE_ADDR"))
             $ip = getenv("REMOTE_ADDR");
        else $ip = "Unknow";

        if(preg_match('/^((?:(?:25[0-5]|2[0-4]\d|((1\d{2})|([1-9]?\d)))\.){3}(?:25[0-5]|2[0-4]\d|((1\d{2})|([1 -9]?\d))))$/', $ip))
            return $ip;
        else
            return '';
    }
    function getFirstCharter($str){
        if(empty($str))
        {
            return '';
        }
        $fchar=ord($str{0});
        if($fchar>=ord('A')&&$fchar<=ord('z')) return strtoupper($str{0});
        $s1=iconv('UTF-8','gb2312',$str);
        $s2=iconv('gb2312','UTF-8',$s1);
        $s=$s2==$str?$s1:$str;
        $asc=ord($s{0})*256+ord($s{1})-65536;
        if($asc>=-20319&&$asc<=-20284) return 'A';
        if($asc>=-20283&&$asc<=-19776) return 'B';
        if($asc>=-19775&&$asc<=-19219) return 'C';
        if($asc>=-19218&&$asc<=-18711) return 'D';
        if($asc>=-18710&&$asc<=-18527) return 'E';
        if($asc>=-18526&&$asc<=-18240) return 'F';
        if($asc>=-18239&&$asc<=-17923) return 'G';
        if($asc>=-17922&&$asc<=-17418) return 'H';
        if($asc>=-17417&&$asc<=-16475) return 'J';
        if($asc>=-16474&&$asc<=-16213) return 'K';
        if($asc>=-16212&&$asc<=-15641) return 'L';
        if($asc>=-15640&&$asc<=-15166) return 'M';
        if($asc>=-15165&&$asc<=-14923) return 'N';
        if($asc>=-14922&&$asc<=-14915) return 'O';
        if($asc>=-14914&&$asc<=-14631) return 'P';
        if($asc>=-14630&&$asc<=-14150) return 'Q';
        if($asc>=-14149&&$asc<=-14091) return 'R';
        if($asc>=-14090&&$asc<=-13319) return 'S';
        if($asc>=-13318&&$asc<=-12839) return 'T';
        if($asc>=-12838&&$asc<=-12557) return 'W';
        if($asc>=-12556&&$asc<=-11848) return 'X';
        if($asc>=-11847&&$asc<=-11056) return 'Y';
        if($asc>=-11055&&$asc<=-10247) return 'Z';
        return '#';
        }
?>