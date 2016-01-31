<?php

namespace   Admin\Controller;
use         Think\Controller;
/**
 * 	角色模块控制器
 * 	包括角色的设置与修改
 */
class RoleController extends MyController{

	/**
     * 构造方法
     * 初始化 角色设置 类下的一些数据
     */
	public function __construct(){
		parent::__construct();
	}

	/**
     * 获取管理员列表
     */
	public function index(){
        // 获取管理角色
        $roleModel=M('auth_group');
        $roleCount=$roleModel->count();
        $rolePage=new \Think\Page($roleCount,5);
        $roleShow= $rolePage->show();
        $roles=$roleModel->limit($rolePage->firstRow.','.$rolePage->listRows)->order('id desc')->select();

        //渲染模板
        $this->assign(array(
            'roles'=>$roles,
            'roleShow'=>$roleShow,
            'num'=>$rolePage->firstRow,
            ));
        $this->display('admin_role_list');
	}


    /**
     *创建后台角色权限
     */
    public function roleAdd(){
        $this->display('admin_role_add');
    }

    /**
     * 添加角色
     */
    public function doRoleAdd(){

        // 获取客户端提交的数据，整合保存入数据库
        $data['title'] = I('post.groupName');
        $data['describe']  = I('post.describe');
        $data['status'] =I('post.status');
        $Model = M('auth_group');

        if ($Model->create($data)){
            // 执行插入操作
            $res = $Model->add($data);
            if ($res){
                    // 实例化日志类，添加日志
                    $setLog = new \Admin\Controller\LogController();
                    $setLog->setLog('角色（ROLE_ID: ' . $res . '）添加成功！');
                    $this->success('角色添加成功.' . $res, 'index');
                    exit;
                }
            }
        // 执行失败跳回添加页
        $this->error('角色添加失败');
    }

    /**
     * 添加角色Ajax验证
     */
    public function checkRoleExists(){
        $Model = M('auth_group');
        $res = $Model->field('id')->where("title = '{$_GET['rolename']}'")->select();
        if ( ! $res)
        {
            echo 1;
        }
        else {
            echo 0;
        }
    }

    /**
     * 修改用账号状态
     * 1 表示活跃， 0表示禁用
     * @return void
     */
    public function toggleStatus ()
    {
        $roleID = I('get.id');
        $prevStatus = I('get.prev');

        $Model = M('auth_group');
        $res = $Model->where("id=" . $roleID)->save(array('status' => ! $prevStatus));

        if ($res){
            // 写入日志
            $log = new \Admin\Controller\LogController();
            $statusStr = empty($prevStatus) ? '启用' : '禁用';
            $log->setLog('用户（UID：' . $roleID . '）角色状态被' . $statusStr);
            $this->success('修改成功.');
        }
        else
        {
            $this->error('修改失败.');
        }
    }

    /**
     * 获取角色修改页面
     */
    public function roleEdit() {
        // 获取角色列表
        $roleId = I('get.id');
        $Model = M('auth_group');
        $roleInfo = $Model->find($roleId);
        //渲染模板
        $this->assign('roleInfo', $roleInfo);
        $this->display('admin_role_edit');
    }

    /**
     * 修改角色信息
     */
    public function doRoleEdit(){
        $data['id'] = I('post.id');
        $data['describe']  = I('post.describe');
        $data['status'] = I('post.status');
        $Model = M('auth_group');
        if ($Model->save($data))
        {
            $setLog = new \Admin\Controller\LogController();
            $setLog->setLog('角色（UID: ' .I('post.id') . '）修改成功！');
            $this->success('角色修改成功.' . I('post.id'), 'index');
            exit;
        }

        $this->error('修改失败！');
    }

    /**
     * 单个删除角色
     */
    public function userDelete() {
        $roleId = I('get.id', 0);
        $Model  = M('auth_group');

        // 判断自己不能删除自己
        $role_Id = I('session.role_id');
        if ($role_Id == $roleId)
        {
            $this->error('不能删除自己的角色');
            exit;
        }

        $res1 = $Model->delete($roleId);
        if ($res1)
        {
            $objLog = new \Admin\Controller\LogController();
            $objLog->setLog('用户角色（ROLE_ID：' . I('get.id') . '）删除成功');
            $this->success('删除成功！');
        }
        else
        {
            $this->error('删除失败！');
        }
    }

    /**
     * 角色组成员列表
     */
    public function groupMember(){
        $group['id']=$where['group_id']=I('get.id');
        $group['groupName']=I('get.name');
        $m=D('GroupMemberView');
        $count=$m->where($where)->count();
        $page=new \Think\Page($count,10);
        $show=$page->show();
        $result=$m->where($where)->limit($page->firstRow.','.$page->listRows)->order('uid desc')->select();
        $this->result=$result;
        $this->group=$group;
        $this->assign('page',$show);
        $this->assign('num',$page->firstRow);
        $this->display('admin_role_groupMember');
    }


    /**
     * 角色授权页面
     */
    public function accessSelect(){
        //角色id
        $group['id']=$where['id']=I('id');
        //角色名称
        $group['name']=I('name');
        $m=M('auth_group');
        //获取所有规则id
        $ruleID=$m->field('rules')->where($where)->select();
        // dump($ruleID);
        $rule=D("RuleView");
        $pid=$rule->field('pid,moduleName')->group('pid')->select();
        // dump($pid);
        $result=array();
        foreach ($pid as $v) {
            $map['pid']=array('in',$v['pid']);
            $map['status']='1';
            $result[$v['modulename']]=$rule->where($map)->select();
        }
        // dump($result);
        $this->group=$group;
        // dump($group);
        // die;
        $this->result=$result;
        $this->ruleID=$ruleID;
        $this->display('admin_role_accessSelect');
    }

     /**
     * 更新角色授权
     */
     public function accessSelectHandle(){
        $arr=I('post.rule');
        $where['id']=I("post.groupID");
        if($arr ==NULL){
        	$data['rules']='';
        }else{
        $data['rules']=implode(',',$arr);
        	}
        $m=M('auth_group');
        //更新,返回影响行数
        $num=$m->where($where)->save($data);
       if($num){
            $setLog = new \Admin\Controller\LogController();
            $setLog->setLog('角色权限（ROLE_ID: ' .I("post.groupID") . '）修改成功！');
            $this->success('角色权限修改成功.' . I("post.groupID"), 'index');
            exit;
        }

        $this->error('权限更新失败!','index');
    }

}