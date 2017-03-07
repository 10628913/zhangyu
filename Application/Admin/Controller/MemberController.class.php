<?php
namespace Admin\Controller;
class MemberController extends BaseController {
    public function __construct(){
        parent::__construct();
        $this->memberDb = M('Member');
        $this->groupDb = M("Member_group");
        $this->gradeSettingDb = M('Grade_setting');
        $this->memberGradeDb = M('Member_grade');
        // $this->pointSettingDb = M('Point_setting');
        // $this->pointDetailDb = M('Point_detail');
    }
    // 会员列表
    public function index(){
        // if(isset($_GET['search'])){
            if(isset($_GET['keyword']) && !empty($_GET['keyword'])){
                $whereSearch['nickname'] = array('like',"%".I('keyword')."%");
                $searchInfo['keyword'] = I("keyword");
            }
        // }

        $this->assign('searchInfo',$searchInfo);
        $count = $this->memberDb->where($whereSearch)->count();

        $Page = new \Think\Page($count,20);
        $list = $this->memberDb->limit($Page->firstRow.','.$Page->listRows)->where($whereSearch)->order('userid desc')->select();
        $show = $Page->show();
        $this->assign('page',$show);
        $this->assign('list',$list);
        $groupList = $this->groupDb->select();
        $this->assign("groupList",$groupList);
        $this->display("member_list");
    }
    // 会员增加
    public function memberAdd(){
    	if(IS_POST){
    		$data = $_POST['info'];
            $password = password($_POST['password']);
            $data['password'] = $password['password'];
            $data['encrypt'] = $password['encrypt'];
            $data['reg_date'] = $data['last_date'] = time();
            $data['reg_ip'] = $data['last_ip'] = ip();
            //判断账号是否存在
            // $where['username'] = $data['username'];
            $where['mobile'] = $data['mobile'];
            // $where['_logic'] = 'OR';
            $isIn = $this->memberDb->where($where)->find();
            if($isIn){
                $this->error('手机号已存在');
                return false;
            }
            if(!$data['mobile'] && !$data['password']){
                $this->error('操作失败');
            }else{
                $result = $this->memberDb->data($data)->add();
                if($result){
                    // checkMemberGrade($result);
                    $this->success('操作成功');
                }else{
                    $this->error('操作失败');
                }
            }
    	}else{
    		$whereGroup['status'] = 1;
    		$groupList = $this->groupDb->where($where)->select();
    		$this->assign("groupList",$groupList);
    		$this->display("member_add");
    	}
    }
    // 会员编辑
    public function memberEdit(){
    	if(IS_POST){
            $data = $_POST['info'];
    		$userid = $where['userid'] = intval($_POST['userid']);
            $data['userid'] = $userid;
            if(isset($_POST['password']) && !empty($_POST['password'])){
                $password = password($_POST['password']);
                $passwordData['password'] = $password['password'];
                $passwordData['encrypt'] = $password['encrypt'];
                $this->memberDb->where($where)->save($passwordData);
            }
            $this->memberDb->where($where)->save($data);
            // checkMemberGrade($userid);
            $this->success("操作成功");
    	}else{
    		$userid = $where['userid'] = I('userid');
    		$memberInfo = $this->memberDb->where($where)->find();
    		$this->assign($memberInfo);
    		$whereGroup['status'] = 1;
    		$groupList = $this->groupDb->where($where)->select();
    		$this->assign("groupList",$groupList);
            // layout(false);
    		$this->display("member_edit");
    	}
    }
    // 浏览会员信息
    public function memberInfo(){
    	$userid = $where['userid'] = I("userid");
    	$memberInfo = $this->memberDb->where($where)->find();
		$whereGroup['id'] = $memberInfo['group_id'];
		$memberInfo['group_name'] = $this->groupDb->where($where)->getField("title");
		$this->assign($memberInfo);
		layout(false);
		$this->display("member_info");
    }
    // 会员删除操作
    public function memberDelete(){
    	$userid = $where['userid'] = I("userid");
    	$memberInfo = $this->memberDb->where($where)->find();
		if($memberInfo){
			$result = $this->memberDb->where($where)->delete();
		}
		if($result){
			$this->success("操作成功");
		}else{
			$this->error("操作失败");
		}
    }
    /**
    *批量操作禁止
    */
    public function memberLock(){
        if(IS_POST){
            $userids = explode(",",$_POST['userids']);
            foreach ($userids as $key => $v) {
                $this->memberDb->where(array("userid"=>$v))->setField("islock",1);
            }
            $this->success('操作成功');
        }
    }
    /**
    *批量操作解除禁止
    */
    public function memberUnLock(){
        if(IS_POST){
            $userids = explode(",",$_POST['userids']);
            foreach ($userids as $key => $v) {
                $this->memberDb->where(array("userid"=>$v))->setField("islock",0);
            }
            $this->success('操作成功');
        }
    }
    /**
    *批量操作删除
    */
    public function membersDelete(){
        if(IS_POST){
            $userids = explode(",",$_POST['userids']);
            foreach ($userids as $key => $v) {
                $this->memberDb->where(array("userid"=>$v))->delete();
            }
            $this->success('操作成功');
        }
    }
    // 会员组列表
    public function groupLists(){
        $list = $this->groupDb->order('id desc')->select();
        foreach ($list as $key => $v) {
            $where['group_id'] = $v['id'];
            $list[$key]['member_count'] = $this->memberDb->where($where)->count();
        }
        $this->assign('list',$list);
        $this->display("group_list");
    }
    // 会员组增加
    public function groupAdd(){
    	if(IS_POST){
    		$data = $_POST['info'];
    		$result = $this->groupDb->data($data)->add();
    		if($result){
    			$this->success("操作成功");
    		}else{
    			$this->error("操作失败");
    		}
    	}else{
    		layout(false);
    		$this->display("group_add");
    	}
    }
    public function groupEdit(){
    	if(IS_POST){
    		$id = $where['id'] = I('id');
    		$data = $_POST['info'];
    		$this->groupDb->where($where)->save($data);
    		$this->success("操作成功");
    	}else{
    		$id = $where['id'] = I('id');
    		$groupInfo = $this->groupDb->where($where)->find();
    		$this->assign($groupInfo);
    		layout(false);
    		$this->display("group_edit");
    	}
    }
    public function groupDelete(){
    	if(IS_POST){
    		$id = I('id');
    		$where['id'] = $id;
    		$result = $this->groupDb->where($where)->delete();
    		if($result){
    			$this->success("操作成功");
    		}else{
    			$this->error("操作失败");
    		}
    	}
    }
    // 等级列表
    public function gradeLists(){
        $list = $this->gradeSettingDb->order('id desc')->select();
        foreach ($list as $key => $v) {
            $where['grade_id'] = $v['id'];
            $list[$key]['member_count'] = $this->memberGradeDb->where($where)->count();
        }
        $this->assign('list',$list);
        $this->display("grade_list");
    }
    // 等级新增
    public function gradeAdd(){
        if(IS_POST){
            $data = $_POST['info'];
            $result = $this->gradeSettingDb->data($data)->add();
            if($result){
                $this->success("操作成功");
            }else{
                $this->error("操作失败");
            }
        }else{
            layout(false);
            $this->display("grade_add");
        }
    }
    // 等级编辑
    public function gradeEdit(){
        if(IS_POST){
            $id = $where['id'] = I('id');
            $data = $_POST['info'];
            $this->gradeSettingDb->where($where)->save($data);
            $this->success("操作成功");
        }else{
            $id = $where['id'] = I('id');
            $gradeInfo = $this->gradeSettingDb->where($where)->find();
            $this->assign($gradeInfo);
            layout(false);
            $this->display("grade_edit");
        }
    }
    // 等级删除
    public function gradeDelete(){
        if(IS_POST){
            $id = I('id');
            $where['id'] = $id;
            $result = $this->gradeSettingDb->where($where)->delete();
            if($result){
                $this->success("操作成功");
            }else{
                $this->error("操作失败");
            }
        }
    }
    // 积分配置列表
    public function pointLists(){
        $list = $this->pointSettingDb->order('id desc')->select();
        $this->assign('list',$list);
        $this->display("point_list");
    }
    // 新增积分配置
    public function pointAdd(){
        if(IS_POST){
            $data = $_POST['info'];
            $where['method'] = $data['method'];
            $pointSettingInfo = $this->pointSettingDb->where($where)->find();
            if($pointSettingInfo){
                $this->error("当前方法名已存在");
            }
            $result = $this->pointSettingDb->data($data)->add();
            if($result){
                $this->success("操作成功");
            }else{
                $this->error("操作失败");
            }
        }else{
            layout(false);
            $this->display("point_add");
        }
    }
    // 编辑积分配置
    public function pointEdit(){
        if(IS_POST){
            $id = $where['id'] = I('id');
            $data = $_POST['info'];
            $this->pointSettingDb->where($where)->save($data);
            $this->success("操作成功");
        }else{
            $id = $where['id'] = I('id');
            $gradeInfo = $this->pointSettingDb->where($where)->find();
            $this->assign($gradeInfo);
            layout(false);
            $this->display("point_edit");
        }
    }
    // 积分配置删除
    public function pointDelete(){
        if(IS_POST){
            $id = I('id');
            $where['id'] = $id;
            $result = $this->pointSettingDb->where($where)->delete();
            if($result){
                $this->success("操作成功");
            }else{
                $this->error("操作失败");
            }
        }
    }
    // 积分明细表
    public function pointDetailLists(){
        $count = $this->pointDetailDb->count();
        $Page = new \Think\Page($count,20);
        $list = $this->pointDetailDb->limit($Page->firstRow.','.$Page->listRows)->order('id desc')->select();
        foreach ($list as $key => $v) {
            $where['userid'] = $v['userid'];
            $list[$key]['nickname'] = $this->memberDb->where($where)->getField("nickname");
        }
        $show = $Page->show();
        $this->assign('page',$show);
        $this->assign('list',$list);
        $this->display("point_detail_list");
    }
    // 积分明细删除
    public function pointDetailDelete(){
        if(IS_POST){
            $where['id'] = $id = I('id');
            $result = $this->pointDetailDb->where($where)->delete();
            if($result){
                $this->success("操作成功");
            }else{
                $this->error("操作失败");
            }
        }
    }

}