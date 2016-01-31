<?php

namespace  Admin\Controller;
use        Think\Controller;
use        Think\Area;

class UserController extends MyController
{
    /**
     * 构造函数：执行本类初始化操作
     * @date 2015-01-17
     */
    public function __construct ()
    {
        parent::__construct();
    }

    /**
     * 获取会员模板，显示会员列表
     * @date 2015-01-18 
     * @return void 
     */
    public function index()
    {
        $modelUser = M('users');
        $pageCount = $modelUser->count();
        $pageSize  = 10;
        $page = new \Think\Page($pageCount, $pageSize);
        // 获取会员数据
        $userRes = $modelUser->field('id, uname, email, email_state, mobile, mobile_state, last_login_time, last_login_ip, status')->limit($page->firstRow, $page->listRows)->order('id DESC')->select();
        $this->assign('page', $page->show());
        $this->assign('userRes', $userRes);
        $this->display('user_list');
    }

    /**
     * 获取添加用户界面
     * @date 2015-01-18
     * @return void
     */
    public function userAdd ()
    {
        $city = array('选择省', '选择市', '选择县');
        $c = Area::city($city);
        $this->assign('city', $c);
        $this->display('user_add');
    }

    /**
     * 处理用户地址默认设置
     * @data 2015-01-18
     * @return void 
     */
    public function doToggleDefault ()
    {
        $getAddressId = I('get.id');
        $getPreStatus = I('get.pre');
        $getUserId    = I('get.user_id');

        $modelAddress = M('user_address');
        // 将原来的改为取消默认
        $modelAddress->data(array('is_default'=>0))->where("user_id='{$getUserId}'")->save();
        // 修改现在的默认设置
        $affectedRows = $modelAddress->data(array("is_default"=> ! $getPreStatus))->where("id='{$getAddressId}'")->save();

        if ($affectedRows)
        {
            $this->success('设置成功');
            exit;
        }
        $this->error('设置失败');
    }

    /**
     * 执行用户地址删除操作
     * @date 2015-01-18
     * @return void 
     */
    public function doAddressDelete()
    {
        $getAddressId = I('get.id');
        $modelAddress = M('user_address');
        $affectedRows = $modelAddress->where("id = '{$getAddressId}'")->delete();

        if ($affectedRows > 0)
        {
            $this->success('删除成功');
            exit;
        }
        $this->error('删除失败');
    }

    /**
     * 执行用户地址修改操作
     * @date 2015-01-18
     * @return void 
     */
    public function doUserAddressEdit()
    {
        $modelAddress = M('user_address');
        if (isset($_POST['is_default']))
        {
            $data['is_default'] = 0;
            $getUserId = I('user_id');
            $modelAddress->where("user_id = '{$getUserId}'")->data($data)->save();
            unset($_POST['user_id']);
        }

        if ($modelAddress->create($_POST))
        {
            if ($modelAddress->save())
            {
                $this->success('地址修改成功');
                exit;
            }
        }
        $this->error('地址修改失败');
    }

    /**
     * 获取用户地址编辑模板
     * @return void 
     */
    public function userAddressEdit ()
    {
        $getAddressId = I('get.id');
        $modelUserAddress = M('user_address');
        $userAddressRes = $modelUserAddress->where("id={$getAddressId}")->find();
        $city = array($userAddressRes['province'], $userAddressRes['city'], $userAddressRes['area']);
        $c = Area::city($city);
        $this->assign('city', $c);
        $this->assign('addr', $userAddressRes);
        $this->display('user_address_edit');
    }

    /**
     * 获取用户详细地址界面
     * @date 2015-01-18
     * @return void 
     */
    public function userAddress ()
    {
        $getUserId = I('get.id');
        $modelUserAddress = M('user_address');
        $userAddress = $modelUserAddress->where("user_id = '{$getUserId}'")->select();
        $this->assign('addr', $userAddress);
        $this->display('user_address');
    }

    /**
     * 添加用户
     * @date 2015-01-18
     * @return [type] [description]
     */
    public function doUserAdd()
    {
        $users = array();
        $users['uname'] = I('post.uname');
        $users['email'] = I('post.email');
        $users['mobile'] = I('post.mobile');
        if (empty($users['email']) || empty($users['mobile']))
        {
            $this->error('用户名或密码不能全为空');
            exit;
        }
        $mobileState = I('post.mobile_state');
        $emailState = I('post.email_state');
        $users['mobile_state'] = empty($mobileState) ? 0 : 1;
        $users['email_state'] = empty($emailState) ? 0 : 1;

        $users['password'] = I('post.password');
        $users['repassword'] = I('post.repassword');

        // 检查密码是否为空
        if (empty($users['password']))
        {
            $this->error('密码不能为空');
            exit;
        }

        // 检查密码长度
        if (strlen($users['password']) < 6)
        {
            $this->error('密码长度不够');
            exit;
        }

        // 检查密码是否一致
        if (strcmp($users['password'], $users['repassword']) != 0)
        {
            $this->error('两次密码不一致');
            exit;
        }
        $users['password'] = sha1($users['password']);
        $users['create_time'] = time();
        $users['last_login_time'] = time();
        // 获取IP
        $getIp = I('server.REMOTE_ADDR');
        $users['last_login_ip'] = ip2long($getIp);

        // 添加用户登录信息表，获取用户ID
        $modelUsers = M('users');
        if ($modelUsers->create($users))
        {
            $lastInsertId = $modelUsers->add();
        }

        if ( ! $lastInsertId)
        {
            $this->error('用户添加失败');
            exit;
        }

        // 添加用户基本信息
        $userInfo = array();
        $userInfo['user_id'] = $lastInsertId;
        $userInfo['sex'] = I('post.sex');
        $userInfo['true_name'] = I('post.true_name');
        $userInfo['birthday'] = I('post.birthday');
        $userInfo['education'] = I('post.education');
        $userInfo['province'] = I('post.province');
        $userInfo['city'] = I('post.city');
        $userInfo['area'] = I('post.area');
        $userInfo['address'] = I('post.address');
        $userInfo['job'] = I('post.job');
        $userInfo['marry'] = I('post.marry');
        $userInfo['identity'] = I('post.identity');
        $userInfo['wage'] = I('post.wage');

        // 判断有没有文件上传
        if ( ! empty($_FILES['thumb']['name']))
        {
            $upload = new \Think\Upload();                          // 实例化上传类
            // 设置附件上传类型    
            $upload->exts      = array('jpg', 'gif', 'png', 'jpeg');
            $upload->subName   = '';
            $upload->rootPath  = './Public/';
            $upload->savePath  = 'User/';                           // 设置附件上传目录    
            $upload->saveName  = date('YmdHis') . mt_rand(100, 999);

            // 上传文件     
            $info   =   $upload->uploadOne($_FILES['thumb']);
            if($info) {
                $imagePath = './Public/' . $info['savepath'] . $info['savename'];
                $image = new \Think\Image(); 
                $image->open($imagePath);
                $image->thumb(150, 150, \Think\Image::IMAGE_THUMB_CENTER)->save($imagePath);

                $userInfo['thumb'] = $info['savename'];
            }
        }
        // 执行添加操作
        $modelUserInfo = M('user_info');
        if ($modelUserInfo->create($userInfo))
        {
            $userAffectedRows = $modelUserInfo->add();
        }

        $this->success('用户添加成功', 'index');
    }

    /**
     * 批量删除用户
     * @date 2015-01-18
     * @return void 
     */
    public function batchDeleteUsers ()
    {
        $uids = I('post.uids');
        $where = implode(', ', $uids);
        $where = "id IN({$where})";

        // 实例化用户表模型
        $modelUsers = M('users');
        $affectedRows = $modelUsers->where($where)->delete();

        if ($affectedRows > 0)
        {
            $this->success('删除成功');
        }
        else
        {
            $this->error('删除失败');
        }
    }

    /**
     * 会员基本资料修改
     * @date 2015-01-18
     * @return void 
     */
    public function userEdit ()
    {
        $getUserId = I('get.id');

        $modelUserInfo = M();
        $userInfoRes = $modelUserInfo->table('__USERS__ AS u, __USER_INFO__ AS i')->where("u.id = i.user_id AND u.id='{$getUserId}'")->find();

        if ( ! empty($userInfoRes['province']))
        {
            $city = array($userInfoRes['province'], $userInfoRes['city'], $userInfoRes['area']);
        }
        else
        {
            $city = array('选择省', '选择市', '选择县');
        }
        $c = Area::city($city);

        $this->assign('user', $userInfoRes);
        $this->assign("city", $c);
        $this->display('user_edit');
    }

    /**
     * 检查Ajax传过来的值有没有出现
     * 用于会员基本资料修改，检查邮箱和手机号有没有重复出现
     * @return string 将检测结果通过Ajax传回请求页
     */
    public function checkValueExists ()
    {
        $getEmail = I('post.email');
        $modelUsers = M('users');
        $emailResult = $modelUsers->field('id')->where($_POST)->find();

        if ( ! empty($emailResult))
        {
            echo 0;     // 说明不可用
        }
        else
        {
            echo 1;     // 说说可用
        }

    }

    /**
     * 执行用户资料修改操作
     * @date 2015-01-18
     * @return void 
     */
    public function doUserEdit()
    {
        $getUserId = I('post.id');

        // 实例化数据模型
        $modelUsers = M('users');
        $modelUserInfo = M('user_info');
        // 密码
        $newPassword = I('post.password');
        $orgPassword = I('post.org_password');

        if ( ! empty ($newPassword))
        {
            // 判断两次密码输入是否一致
            if (I('post.password') != I('post.repassword'))
            {
                $this->error('两次密码不一致');
                exit;
            }

            // 判断密码是否一致
            if (sha1($newPassword) != $orgPassword)
            {
                $usersArr['password'] = sha1($newPassword);
            }
        }

        $usersArr['id'] = I('post.id');
        $usersArr['email'] = I('post.email');
        if ( ! empty($usersArr['email']))
        {
            $usersArr['email_state'] = empty($_POST['email_state']) ? 0 : 1;
        }
        $usersArr['mobile'] = I('post.mobile');
        if ( ! empty($userArr['mobile']))
        {
            $usersArr['mobile_state'] = empty($_POST['mobile_state']) ? 0 : 1;
        }
        
        // 保存用户信息
        if ($modelUsers->create($usersArr))
        {
            $affectedUsers = $modelUsers->save();
        }
        
        // 下面是user_info表信息处理
        $userInfoArr['true_name'] = I('post.true_name');
        $userInfoArr['sex'] = I('post.sex');
        $userInfoArr['birthday'] = I('post.birthday');
        $userInfoArr['marry'] = I('post.marry');
        $userInfoArr['education'] = I('post.education');
        $userInfoArr['province'] = I('post.province');
        $userInfoArr['city'] = I('post.city');
        $userInfoArr['area'] = I('post.area');
        $userInfoArr['address'] = I('post.address');
        $userInfoArr['job'] = I('post.job');
        $userInfoArr['identity'] = I('post.identity');
        $userInfoArr['wage'] = I('post.wage');

        // 判断有没有文件上传
        if ( ! empty($_FILES['thumb']['name']))
        {
            $upload = new \Think\Upload();                          // 实例化上传类
            // 设置附件上传类型    
            $upload->exts      = array('jpg', 'gif', 'png', 'jpeg');
            $upload->subName   = '';
            $upload->rootPath  = './Public/';
            $upload->savePath  = 'User/';                           // 设置附件上传目录    
            $upload->saveName  = date('YmdHis') . mt_rand(100, 999);

            // 上传文件     
            $info   =   $upload->uploadOne($_FILES['thumb']);
            if($info) {
                $imagePath = './Public/' . $info['savepath'] . $info['savename'];
                $image = new \Think\Image(); 
                $image->open($imagePath);
                $image->thumb(150, 150, \Think\Image::IMAGE_THUMB_CENTER)->save($imagePath);

                $userInfoArr['thumb'] = $info['savename'];
            }
            else
            {
                $userInfoArr['thumb'] = I('post.org_thumb');
            }
        }
        // 执行修改
        $affectedUserInfo = $modelUserInfo->where("user_id = '{$getUserId}'")->data($userInfoArr)->save();

        if ($affectedUsers || $affectedUserInfo)
        {
            $this->success('修改成功');
        }
        else
        {
            $this->error('修改失败');
        }
    }

    public function productParam(){
        $db = M('orders');
        $users = M('users');
            $map['user_id']=I('get.id');
            $count = $db->where($map)->count();    //获取总页数
            $page = new \Think\Page($count, 10);
            $showPage = $page->show();
            $res = $db->where($map)->order('id ASC')->limit($page->firstRow . ',' . $page->listRows)->select();

            $date = Date(time());

            //如果订单状态是未支付 并且 时间超过2小时 并且不是货到付款 , 则取消订单
            foreach ($res as $val) {

                $da = '';

                if (($val['order_status'] == 1) && (($val['pay_status']) == 1) && ($val['order_date'] + 7200 < $date)) {
                    $ids = $val['id'];
                    $da['order_status'] = 0;
                    $db->where("id = $ids")->save($da);    //存档
                }
            }

            //再次查询
            $res = $db->where($map)->order('id ASC')->limit($page->firstRow . ',' . $page->listRows)->select();
            foreach($res as &$vo){
                $userId = $vo['user_id'];
                $u = $users -> where("id = $userId") -> find();
                $vo['uname'] = $u['uname'];
            }


        
        $this->assign('data', $res);
        $this->assign('page', $showPage);

        $this->display('user_orderlist');
    }

    public function userRecycle(){
        $id=I('get.id');
        $modelUsers = M('users');
        $map['id']=$id;
        $data['status']=0;
        $affectedRows = $modelUsers->where($map)->save($data);

        if ($affectedRows > 0)
        {
            $this->success('禁用成功');
        }
        else
        {
            $this->error('禁用失败');
        }
    }

    public function userRecycled(){
        $id=I('get.id');
        $modelUsers = M('users');
        $map['id']=$id;
        $data['status']=1;
        $affectedRows = $modelUsers->where($map)->save($data);

        if ($affectedRows > 0)
        {
            $this->success('解除禁用成功');
        }
        else
        {
            $this->error('解除禁用失败');
        }
    }
}
