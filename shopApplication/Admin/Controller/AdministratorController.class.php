<?php

namespace   Admin\Controller;
use         Think\Controller;
/**
 * 	后台管理员模块控制器
 * 	包含管理员分配
 */
class AdministratorController extends MyController {

	/**
     * 构造方法
     * 初始化管理员设置类
     */
	public function __construct() {
		parent::__construct();
	}

	/**
     * 获取管理员列表
     */
	public function index() {
        //获取管理员
        $userModel = M('admin_users');
        $userCount= $userModel->table('__ADMIN_USERS__ user, __ADMIN_ROLE__ role')
                                ->where('user.role_id = role.id')
                                ->count();

        $userPage = new \Think\Page($userCount, 10);
        $userShow = $userPage->show();

        // 遍历所有管理员用户
        $users = $userModel ->field('user.id, 
                                    user.username, 
                                    user.last_login_time, 
                                    user.last_login_ip, 
                                    user.login_times, 
                                    role.title, 
                                    user.status')
                            ->table('__ADMIN_USERS__ user, __AUTH_GROUP__ role')
                            ->where('user.role_id = role.id')
                            ->order('user.role_id ASC')
                            ->limit($userPage->firstRow.','.$userPage->listRows)->select();

        //渲染模板
        $this->assign(array(
            'users'=>$users,
            'userShow'=>$userShow,
            ));
        $this->display('admin_user_list');
	}

    /**
     * 获取用户修改页面
     */
    public function userEdit() {
        $userId = I('get.id');

        $modelUsers = M('admin_users');
        $userInfo = $modelUsers->find($userId);

        // 获取角色列表
        $roleLists = $this->_getRoleList();
        $this->assign('roleLists', $roleLists);
        $this->assign('userInfo', $userInfo);
        $this->display('admin_user_edit');
    }

    /**
     * 修改用户信息
     */
    public function doUserEdit ()
    {

        $orgPassword    = I('post.org_pass');
        $newPassword    = I('post.password');
        $newRePassword  = I('post.repassword');

        if ( ! empty($newPassword))
        {
            $encodePassword = sha1($newPassword);

            if (strcmp($newPassword, $newRePassword) != 0)
            {
                $this->error('错误：密码不一致！');
                exit;
            }

            if (strcmp($orgPassword, $encodePassword) == 0)
            {
                $this->error('提示：不能与原密码相同');
                exit;
            }

            $data['password'] = $encodePassword;
        }

        $data['role_id'] = $data2['group_id'] = I('post.role_id');
        $data['id'] = I('post.user_id');
        $data['status'] = I('post.status');

        $modelUser = M('admin_users');
        $modelUserAccess=M('auth_group_access');
        // dump($data2);
        // die;
        if ($modelUser->save($data) && $modelUserAccess->where('uid ='.$data['id'])->save($data2))
        {
            $setLog = new \Admin\Controller\LogController();
            $setLog->setLog('用户（UID: ' .I('post.user_id') . '）修改成功！');
            $this->success('用户修改成功.' . I('post.user_id'), 'index');
            exit;
        }

        $this->error('修改失败！');
    }

    /**
     * 私有方法，多个页面都需要得到管理角色列表
     * @return array 所有角色列表
     */
    private function _getRoleList()
    {
        $modelRole = M('auth_group');
        return $modelRole->field('id, title')->select();
    }

    /**
     * 添加管理员，获取添加管理员界面 
     * 
     * @return void
     */
    public function userAdd() {
        $roleLists = $this->_getRoleList();
        $this->assign('roleLists', $roleLists);
        $this->display('admin_user_add');
    }

    /**
     * 修改用账号状态
     * 1 表示活跃， 0表示禁用
     * @return void
     */
    public function toggleStatus ()
    {
        $userID = I('get.id');
        $prevStatus = I('get.prev');
        $prevUserId = I('session.id');

        // 验证权限

        if ($userID == $prevUserId)
        {
            $this->error('自己不能修改自己');
            exit;
        }

        $modelUsers = M('admin_users');
        $res = $modelUsers->where("id=" . $userID)->save(array('status' => ! $prevStatus));

        if ($res)
        {
            // 写入日志
            $log = new \Admin\Controller\LogController();
            $statusStr = empty($prevStatus) ? '启用' : '禁用';
            $log->setLog('用户（UID：' . $userID . '）账户状态被' . $statusStr);
            $this->success('修改成功.');
        }
        else
        {
            $this->error('修改失败.');
        }
    }

    /**
     * 批量删除用户
     */
    public function batchDelete()
    {
        $userIdsArr = I('post.user_ids');
        $userIdsStr = implode(', ', $userIdsArr);

        $modelUser = M('admin_users');
        $modelInfo = M('admin_info');
        $modelUserAccess=M('auth_group_access');

        if(in_array('1',$userIdsArr)){
            $this->error('不能删除超级管理员.');
            exit;
            }else{
            $res1 = $modelUser->delete($userIdsStr);
            $res2 = $modelInfo->where("uid in (" . $userIdsStr . ")")->delete();
            $res3 = $modelUserAccess->where("uid in (" . $userIdsStr . ")")->delete();
            if ($res1 && $res2)
                {
            // 记入日志
            $objLog = new \Admin\Controller\LogController();
            $objLog->setLog('管理员用户（UID：' . $userIdsStr . '）帐户删除成功');

            $this->success('删除成功.');
            }else{
            $this->error('删除失败.');
            }
        }
    }

    /**
     * 添加管理员
     * 用户加密方式为 sha1() 40位加密算法，将插入ID插入到admin_info表
     */
    public function doUserAdd() {

        // 获取客户端提交的数据，整合保存入数据库
        $data['username'] = I('post.username');
        $data['password'] = I('post.password', '', sha1);
        $data['role_id']  = I('post.role_id');
        $data['create_time'] = time();
        $data['last_login_time'] = time();
        $data['last_login_ip'] = ip2long(I('server.REMOTE_ADDR'));

        $modelUser = M('admin_users');

        if ($modelUser->create($data))
        {
            // 执行插入操作
            $lastID = $modelUser->add();

            // 执行成功需要将插入的ID插入用户详细表
            if ($lastID > 0) 
            {
                $modelUserInfo = M('admin_info');
                $modelUserAccess=M('auth_group_access');
                $res1 = $modelUserInfo->add(array('uid'=>$lastID));
                $res2 = $modelUserAccess->add(array('uid'=>$lastID,'group_id'=>$data['role_id']));
                if ($res1 && $res2)
                {
                    // 实例化日志类，添加日志
                    $setLog = new \Admin\Controller\LogController();
                    $setLog->setLog('用户（UID: ' . $lastID . '）添加成功！');

                    $this->success('用户添加成功.' . $lastID, 'index');
                    exit;
                }
            }
        }
        // 执行失败跳回添加页
        $this->error('用户添加失败');
    }

    /**
     * 单个删除用户
     */
    public function userDelete($id) {
        $userId = I('get.id', 0);

        $modelUsers  = M('admin_users');
        $modelInfo   = M('admin_info');
        $modelUserAccess=M('auth_group_access');
        // 判断自己不能删除自己
        $myId = I('session.id');
        if ($myId == $userId)
        {
            $this->error('自己不能删除自己');
            exit;
        }

        $res1 = $modelUsers->delete($userId);
        $res2 = $modelInfo->where("uid='{$userId}'")->delete();
        $res3 = $modelUserAccess->where("uid='{$userId}'")->delete();
        if ($res1 && $res2 &&$res3)
        {
            $objLog = new \Admin\Controller\LogController();
            $objLog->setLog('管理员用户（UID：' . I('get.id') . '）帐户删除成功');
            $this->success('删除成功！');
        }
        else
        {
            $this->error('删除失败！');
        }
    }

    /**
     * 添加管理员Ajax验证
     */
    public function checkUserExists () {

        $userModel = M('admin_users');
        $res = $userModel->field('id')->where("username = '{$_GET['username']}'")->select();

        if ( ! $res)
        {
            echo 1;
        }
        else {
            echo 0;
        }
    }

}