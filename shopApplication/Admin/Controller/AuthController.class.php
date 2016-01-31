<?php

namespace   Admin\Controller;
use         Think\Controller;
/**
 * 	权限模块控制器
 * 	包括权限的设置与修改
 */

class AuthController extends MyController{
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
        $Model=D('RuleView');
        $Count=$Model->count();
        $Page=new \Think\Page($Count,5);
        $Show= $Page->show();
        $auths=$Model->limit($Page->firstRow.','.$Page->listRows)->select();
        //渲染模板
        $this->assign(array(
            'auths'=>$auths,
            'Show'=>$Show,
            'num'=>$Page->firstRow,
            ));
        $this->display('admin_auth_list');
	}

	/**
     *创建后台权限
     */
	public function authAdd(){
		$Model=M('modules');
    	$data=$Model->select();
    	$this->assign('modules',$data);
		$this->display('admin_auth_add');
	}

	/**
     * 添加权限
     */
    public function doAuthAdd(){
        // 获取客户端提交的数据，整合保存入数据库
        $data['name'] = I('post.name');
        $data['title'] = I('post.title');
        $data['condition']  = I('post.condition');
        $data['pid'] =I('post.pid');
        $Model = M('auth_rule');

        if ($Model->create($data)){
            // 执行插入操作
            $res = $Model->add($data);
            if ($res){
                    // 实例化日志类，添加日志
                    $setLog = new \Admin\Controller\LogController();
                    $setLog->setLog(' 权限（RULE_ID: ' . $res . '）添加成功！');
                    $this->success('权限添加成功.' . $res, 'index');
                    exit;
                }
            }
        // 执行失败跳回添加页
        $this->error('权限添加失败');
    }

    /**
     * 添加角色Ajax验证
     */
    public function checkAuthExists(){
        $Model = M('auth_rule');
        $res = $Model->field('id')->where("name = '{$_GET['name']}'")->select();
        if ( ! $res)
        {
            echo 1;
        }
        else {
            echo 0;
        }
    }

    /**
     * 获取权限修改页面
     */
    public function authEdit() {
        // 获取角色列表
        $id = I('get.id');
        $Model=D('RuleView');
        $auth = $Model->find($id);
      	$Model2=M('modules');
    	$data=$Model2->select();
    	$this->assign('modules',$data);
        //渲染模板
        $this->assign('auth', $auth);
        $this->display('admin_auth_edit');
    }

    /**
     * 修改权限信息
     */
    public function doAuthEdit(){
    	$data=I('post.');
        $Model = M('auth_rule');
        if ($Model->save($data))
        {
            $setLog = new \Admin\Controller\LogController();
            $setLog->setLog('权限（RULE_ID: ' .I('post.id') . '）修改成功！');
            $this->success('权限修改成功.' . I('post.id'), 'index');
            exit;
        }

        $this->error('修改失败！');
    }

    /**
     * 单个删除权限
     */
    public function authDelete() {
        $id = I('get.id', 0);
        $Model  = M('auth_rule');

        $res1 = $Model->delete($id);
        if ($res1)
        {
            $objLog = new \Admin\Controller\LogController();
            $objLog->setLog('权限角色（AUTH_ID：' . I('get.id') . '）删除成功');
            $this->success('删除成功！');
        }
        else
        {
            $this->error('删除失败！');
        }
    }

    /**
     * 修改用账号状态
     * 1 表示活跃， 0表示禁用
     * @return void
     */
    public function toggleStatus ()
    {
        $id = I('get.id');
        $prevStatus = I('get.prev');

        $Model = M('auth_rule');
        $res = $Model->where("id=" . $id)->save(array('status' => ! $prevStatus));

        if ($res){
            // 写入日志
            $log = new \Admin\Controller\LogController();
            $statusStr = empty($prevStatus) ? '启用' : '禁用';
            $log->setLog('权限（RULE_ID：' . $id . '）角色状态被' . $statusStr);
            $this->success('修改成功.');
        }
        else
        {
            $this->error('修改失败.');
        }
    }

	/**
     *权限模块列表
     */
	public function authModuleList(){
		$Model=M('modules');
        $Count=$Model->count();
        $Page=new \Think\Page($Count,10);
        $Show= $Page->show();
        $modules=$Model->limit($Page->firstRow.','.$Page->listRows)->order('id asc')->select();
        //渲染模板
        $this->assign(array(
            'modules'=>$modules,
            'Show'=>$Show,
            'num'=>$Page->firstRow,
            ));
        $this->display('admin_authmodule_list');
	}

	 /**
     *创建后台权限模块
     */
    public function authModuleAdd(){
        $this->display('admin_authmodule_add');
    }

    /**
     * 添加模块
     */
    public function doModuleAdd(){

        // 获取客户端提交的数据，整合保存入数据库
        $data['moduleName'] = I('post.modulename');
        $Model = M('modules');

        if ($Model->create($data)){
            // 执行插入操作
            $res = $Model->add($data);
            if ($res){
                    // 实例化日志类，添加日志
                    $setLog = new \Admin\Controller\LogController();
                    $setLog->setLog('新模块（MODULE_ID: ' . $res . '）添加成功！');
                    $this->success('新模块添加成功.' . $res, 'authmodulelist');
                    exit;
                }
            }
        // 执行失败跳回添加页
        $this->error('模块添加失败');
    }

    /**
     * 添加角色Ajax验证
     */
    public function checkModuleExists(){
        $Model = M('modules');
        $res = $Model->field('id')->where("moduleName = '{$_GET['modulename']}'")->select();
        if ( ! $res)
        {
            echo 1;
        }
        else {
            echo 0;
        }
    }

    /**
     * 获取角色修改页面
     */
    public function moduleEdit() {
        // 获取角色列表
        $id = I('get.id');
        $Model = M('modules');
        $module = $Model->find($id);
        //渲染模板
        $this->assign('module', $module);
        $this->display('admin_authmodule_edit');
    }

    /**
     * 修改角色信息
     */
    public function doModuleEdit(){
        $data['id'] = I('post.id');
        $data['moduleName']  = I('post.modulename');
        $Model = M('modules');
        if ($Model->save($data))
        {
            $setLog = new \Admin\Controller\LogController();
            $setLog->setLog('模块（UID: ' .I('post.id') . '）修改成功！');
            $this->success('模块修改成功.' . I('post.id'), 'authmodulelist');
            exit;
        }
        $this->error('修改失败！');
    }

    /**
     * 单个删除模块
     */
    public function moduleDelete() {
        $id = I('get.id', 0);
        $Model  = M('modules');

        // 判断自己不能删除自己
        $res1 = $Model->delete($id);
        if ($res1)
        {
            $objLog = new \Admin\Controller\LogController();
            $objLog->setLog('权限模块（MODULE_ID：' . I('get.id') . '）删除成功');
            $this->success('删除成功！');
        }
        else
        {
            $this->error('删除失败！');
        }
    }

}