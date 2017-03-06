<?php
namespace Admin\Controller;
class ModuleController extends BaseController {
    public function __construct(){
        parent::__construct();
        $this->moduleExtendDb = M('Module_extend');
        $this->ucpaasTplDb = M('Ucpaas_tpl');
    }
    //支付类
    public function paySetting(){
        if(IS_POST){
            $pingxx_secret_key = $_POST['pingxx_secret_key'];
            $pingxx_app_id = $_POST['pingxx_app_id'];
            $data['pingxx_secret_key'] = $pingxx_secret_key;
            $data['pingxx_app_id'] = $pingxx_app_id;
            $info = $this->moduleExtendDb->find();
            if($info){
                $where['id'] = $info['id'];
                $this->moduleExtendDb->where($where)->save($data);
            }else{
                $this->moduleExtendDb->data($data)->add();
            }
            $this->success("操作成功");
        }else{
            $info = $this->moduleExtendDb->find();
            $this->assign($info);
            $this->display("pay_setting");
        }
    }
    // 极光推送
    public function jpushSetting(){
        if(IS_POST){
            $data['jpush_app_key'] = $_POST['jpush_app_key'];
            $data['jpush_master_secret'] = $_POST['jpush_master_secret'];
            $info = $this->moduleExtendDb->find();
            if($info){
                $where['id'] = $info['id'];
                $this->moduleExtendDb->where($where)->save($data);
            }else{
                $this->moduleExtendDb->data($data)->add();
            }
            $this->success("操作成功");
        }else{
            $info = $this->moduleExtendDb->find();
            $this->assign($info);
            $this->display("jpush_setting");
        }
    }
    // 极光推送消息发送
    public function jpushMsg(){
        if(IS_POST){
            vendor('JPush.JPush');
            // 获取极光配置参数
            $info = $this->moduleExtendDb->find();
            $jpush_app_key = $info['jpush_app_key'];
            $jpush_master_secret = $info['jpush_master_secret'];
            if(!$jpush_app_key || !$jpush_master_secret){
                $this->error("请先配置极光相关参数");
            }
            $msg = $_POST['msg'];
            $client = new \JPush($jpush_app_key, $jpush_master_secret);
            // 推送发送
            $result = $client->push()
                    ->setPlatform('all')
                    ->addAllAudience()
                    ->setNotificationAlert($msg)
                    ->setSmsMessage($msg, 60)
                    ->setOptions(100000, 0, null, true)
                    ->send();
            if($result){
                $this->success("推送成功");
            }else{
                $this->error("推送失败");
            }
        }else{
            $this->display("jpush_msg");
        }
    }
    // 云之讯短信
    public function ucpaasSetting(){
        if(IS_POST){
            $data['ucpaas_app_id'] = $_POST['ucpaas_app_id'];
            $data['ucpaas_account_sid'] = $_POST['ucpaas_account_sid'];
            $data['ucpaas_auth_token'] = $_POST['ucpaas_auth_token'];
            $info = $this->moduleExtendDb->find();
            if($info){
                $where['id'] = $info['id'];
                $this->moduleExtendDb->where($where)->save($data);
            }else{
                $this->moduleExtendDb->data($data)->add();
            }
            $this->success("操作成功");
        }else{
            $info = $this->moduleExtendDb->find();
            $this->assign($info);
            $this->display("ucpaas_setting");
        }
    }
    // 云之讯平台信息
    public function ucpaasInfo(){
        vendor('Ucpaas.Ucpaas');
        $info = $this->moduleExtendDb->find();
        $options['accountsid'] = $info['ucpaas_account_sid'];
        $options['token'] = $info['ucpaas_auth_token'];
        $ucpaas = new \Ucpaas($options);
        $ucpaasInfo = json_decode($ucpaas->getDevinfo());
        if($ucpaasInfo){
            $this->assign("ucpaasInfo",$ucpaasInfo);
        }
        $this->assign($info);
        $this->display("ucpaas_info");
    }
    // 云之讯模板配置
    public function ucpaasTpl(){
        if(IS_POST){
            $data = $_POST['info'];
            foreach ($data as $key => $v) {
                $where['name'] = $key;
                $info = $this->ucpaasTplDb->where($where)->find();
                if($info){
                    $where['id'] = $v['id'];
                    $data['name'] = $key;
                    $data['tpl_id'] = $v['tpl_id'];
                    $data['status'] = $v['status'];
                    $this->ucpaasTplDb->where($where)->save($data);
                }else{
                    $data['name'] = $key;
                    $data['tpl_id'] = $v['tpl_id'];
                    $data['status'] = $v['status'];
                    $this->ucpaasTplDb->data($data)->add();
                }
            }
            $this->success("操作成功");
        }else{
            $info = $this->ucpaasTplDb->select();
            foreach ($info as $key => $v) {
                $data[$v['name']]['name'] = $v['name'];
                $data[$v['name']]['tpl_id'] = $v['tpl_id'];
                $data[$v['name']]['status'] = $v['status'];
            }
            $this->assign("data",$data);
            $this->display("ucpaas_tpl");
        }
    }
}