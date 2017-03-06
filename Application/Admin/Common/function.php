<?php
/**
 * 检查管理员名称
 * @param array $data 管理员数据
 */
function checkuserinfo($data) {
	if(!is_array($data)){
		return false;
	} elseif (!is_adminname($data['username'])){
		return false;
	}
	return $data;
}
/**
 * 检查用户名是否符合规定
 *
 * @param STRING $username 要检查的用户名
 * @return 	TRUE or FALSE
 */
function is_adminname($username) {
	$strlen = strlen($username);
	if(is_badword($username) || !preg_match("/^[a-zA-Z0-9_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]+$/", $username)){
		return false;
	} elseif ( 20 < $strlen || $strlen < 2 ) {
		return false;
	}
	return true;
}
/**
 * 检测输入中是否含有错误字符
 *
 * @param char $string 要检查的字符串名称
 * @return TRUE or FALSE
 */
function is_badword($string) {
	$badwords = array("\\",'&',' ',"'",'"','/','*',',','<','>',"\r","\t","\n","#");
	foreach($badwords as $value){
		if(strpos($string, $value) !== FALSE) {
			return TRUE;
		}
	}
	return FALSE;
}
/**
 * 判断email格式是否正确
 * @param $email
 */
function is_email($email) {
	return strlen($email) > 6 && preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email);
}
/**
 * 获取管理员的权限分组
 * @param string $uid 管理员id
 * @param string $type  0=获取管理组名称  1=获取id
 * @return string
 */
function get_auth_group($uid,$type="0"){
	if(!$uid){
		return false;
	}
    $groupDb = M("Auth_group");
    $groupAccessDb = M("Auth_group_access");
    $whereGroupAccess['uid'] = $uid;
    $groupId = $groupAccessDb->where($whereGroupAccess)->getField("group_id");
    if($type==1){
    	return $groupId;
    }
    if($groupId){
    	$whereGroup['id'] = $groupId;
    	$groupTitle = $groupDb->where($whereGroup)->getField("title");
    	return $groupTitle;
    }else{
    	return false;
    }
}

function is_login(){
    $user = session('user_auth');
    if (empty($user)) {
        return 0;
    } else {
        return session('user_auth_sign') == data_auth_sign($user) ? $user['uid'] : 0;
    }
}
function is_administrator($uid = null){
	if(!$uid) return false;
}
// 获取管理员姓名
function get_admin_name($uid){
	if(!$uid){
		$uid = 1;
	}
	$admin_name = M('Admin')->where(array('uid'=>$uid))->getField("username");
	if($admin_name){
		return $admin_name;
	}
}
// 新闻列表处理地区
function get_city_name_array($cityName){
	if(!$cityName)return;
	$arr = explode("-", $cityName);
	return $arr[1];
}
//  获取新闻栏目名
function get_news_classify_name($classifyId){
	if(!$classifyId)return;
	$classify_name = M('News_classify')->where(array('classify_id'=>$classifyId))->getField("classify_name");
	if($classify_name)return $classify_name;
}
