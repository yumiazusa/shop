<?php
    
namespace   Admin\Controller;
use         Think\Controller;

class EmailController extends MyController
{

    public function __construct ()
    {
        parent::__construct();
    }

    /**
     * 获取邮件配置界面，初始化配置信息
     * @date 2015-01-13
     * @return [type] [description]
     */
    public function index ()
    {
        $modelEmail = M('sys_config');
        $emailConfig = array();
        $map['cname'] = array('LIKE', 'email_%');
        $res = $modelEmail->where("cname LIKE 'email_%'")->select();

        // 将获取结果转化为一维数组保存
        foreach ($res as $v)
        {
            $key = $v['cname'];
            $emailConfig[$key] = $v['cvalue'];
        }

        $this->assign('emailConfig', $emailConfig);
        $this->display('config');
    }

    /**
     * 获取邮件修改模板
     * @date 2015-01-13
     * @return void
     */
    public function tmpEdit()
    {
        $tName = I('get.tname');        // 获取模板名称
        $eName = I('get.ename');        // 获取模板标识

        $modelTmp = M('email_templates');
        $tmpInfo = $modelTmp->where("ename='{$eName}'")->find();

        $this->assign('tName', $tName);
        $this->assign('tmpInfo', $tmpInfo);
        $this->display('tmp_edit');
    }

    /**
     * 执行模板编辑操作
     * @return void 
     */
    public function doTmpEdit()
    {
        $where['ename'] = I('post.ename');
        $data['subject'] = I('post.subject');
        $data['content'] = I('post.tmp_content');

        $modelTmp = M('email_templates');

        if ($modelTmp->where($where)->save($data))
        {
            $this->success('修改成功.');
        }
        else
        {
            $this->error('修改失败.');
        }
    }

    /**
     * 保存基本配置修改信息
     * @date 2015-01-13
     * @return [type] [description]
     */
    public function saveConfig ()
    {
        $configInfo = I('post.');
        $modelConfig = M('sys_config');
        $counts = 0;     // 执行成功的记录数

        foreach ($configInfo as $k=>$v)
        {
            $field = "cname = '{$k}'";          // 需要修改的行
            $value = array('cvalue' => $v);     // 需要修改的值

            $counts += $modelConfig->where($field)->save($value);
        }

        if ($counts)
        {
            $this->success('修改成功');
        }
        else
        {
            $this->error('修改失败');
        }
    }
}