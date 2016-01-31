<?php

namespace   Admin\Controller;
use         Think\Controller;
/**
 * 后台用户登录模块
 *
 * 作者：  Reagan
 */
class LoginController extends Controller {

    /**
     * 获取用户登录界面
     * 
     * @author Reagan
     */
	public function index() {
                         $username=cookie('dians_username');
                        if($username){
                            $this->assign('username',$username);
                        }
		$this->display('login2');
	}

    /**
     * 验证登录
     *
     * @author Reagan
     */
    public function checkLogin() {

        // 用户名和密码是否验证通过
        $data['username'] = I('post.username');
        $data['password'] = I('post.password', '', 'sha1');

        $admin = M('admin_users');
        $res = $admin->field('id, role_id, status, login_times')->where($data)->find();

        if ( ! $res['status'])
        {
            $this->error('你的帐户受到限制，请联系管理员.');
            exit;
        }

        if ($res) {
            // 用户的登录信息保存在SESSION中
            session('id', $res['id']);                  // 用户ID
            session('username', $data['username']);     // 用户名
            session('role_id', $res['role_id']);        // 角色ID
            session('status', $res['status']);          // 用户状态
            session('isLogin', true);                   // 是否为登录状态

            // 记住登录名时，使用Cookie存储用户信息
            $remember = I('post.remember');
            if ( ! empty($remember)) {

                // 检查用户是否登录的一个加密字符串，将在验证用户是否登录时比对
                $blue_symbol = md5('Diansheng');
                cookie('id', $res['id'], array('prefix' => 'dians_', 'expire' => 3600));
                cookie('username', $data['username'], array('prefix' => 'dians_', 'expire' => 3600));
                cookie('role_id', $res['role_id'], array('prefix' => 'dians_', 'expire' => 3600));
                cookie('status', $res['status'], array('prefix' => 'dians_', 'expire' => 3600));
                cookie('symbol', $blue_symbol, array('prefix' => 'dians_', 'expire' => 3600));
            }
            // 修改管理此次登录的状态
            $upData['last_login_time'] = time();
            $upData['last_login_ip'] = ip2long(I('server.REMOTE_ADDR'));
            $upData['login_times'] = $res['login_times'] + 1;

            $admin->where('id = ' . $res['id'])->save($upData);
            // 登录成功，跳转到主页
            $this->success('欢迎回来：' . $data['username'], U('Index/index'));
        } else {
            $this->error('用户名或密码错误！');
        }
    }


    /**
     * 方法功能：后台用户退出登录
     *
     * @author Reagan
     */
    public function logOut() {
        // 删除后台需要存储的ID信息
        session('id', NULL);
        session('username', NULL);
        session('role_id', NULL);
        session('status', NULL);
        session('isLogin', NULL);

        // 清除Cookie中保存的用户信息
        cookie('dians_id',NULL);
        cookie('dians_role_id',NULL);
        cookie('dians_status',NULL);
        cookie('dians_symbol',NULL);
        $this->redirect('Login/index');
    }
}