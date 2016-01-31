<?php

namespace   Admin\Controller;
use         Think\Controller;


/**
 * 日志类
 * 记录网站后台重要操作，可供其它控制器调用
 *
 * @author Eden
 * @date 2015-01-14
 */
class LogController extends MyController {

    /**
     * 构造方法，初始化自定义公共类的数据
     * @author Eden
     * @date 2015-01-14
     */
    public function __construct () {
        parent::__construct();
    }

    /**
     * 日志写入类，为其它操作器写入的接口
     * @date 2015-01-14
     * @param string $content 需要记录的日志信息
     */
    public function setLog($content = '')
    {
        // 如果设置的内容为空，则返回
        if ($content == '')
        {
            return ;
        }
        $getRemoteIp        = I('server.REMOTE_ADDR');

        $data['user_id']    = I('session.id');
        $data['username']   = I('session.username');
        $data['content']    = $content;
        $data['ctime']      = time();
        $data['cip']        = ip2long($getRemoteIp);

        $modelLog = M('admin_logs');
        if ($modelLog->create($data))
        {
            $modelLog->add();
        }
    }


    /**
     * 获取日志列表模板
     * @return void
     */
    public function index()
    {
        $modelLog = M('admin_logs');

        $map = array();
        if ( ! empty($_GET))
        {
            foreach ($_GET as $key=>$val)
            {
                if ( ! empty($val))
                {
                    $map[$key] = array('LIKE', '%' . $val . '%');
                }
            }
        }
        // 分页
        $pageCount = $modelLog->where($map)->count();
        $pageSize  = 15;

        // 引分页类
        $page = new \Think\Page($pageCount, $pageSize);

        foreach($map as $key=>$val) {    
            $Page->parameter   .=   "$key=".urlencode($val).'&';
        }

        $res = $modelLog->where($map)->limit($page->firstRow, $page->listRows)->order('id DESC')->select();

        $pageShow = $page->show();
        $this->assign('pageShow', $pageShow);
        $this->assign('res', $res);
        $this->display();
    }

    public function logDelete ()
    {
        $getLogId = I('get.id');

        $modelLog = M('admin_logs');
        if ($modelLog->where("id='{$getLogId}'")->delete())
        {
            $this->success('日志删除成功');
        }
        else
        {
            $this->error('日志删除失败');
        }
    }

    /**
     * 批量删除操作
     * @return void
     */
    public function batchDelete()
    {
        $deleIds = I('post.log_ids');
        if (empty ($deleIds))
        {
            $this->error('选择数据不能为空');
            exit;
        }

        $logIds = 'id in (' . implode(', ', $deleIds) . ')';

        $modelLogs = M('admin_logs');

        $affectedRows = $modelLogs->where($logIds)->delete();
        if ($affectedRows)
        {
            $this->success('日志删除成功');
        }
        else
        {
            $this->error('日志删除成功');
        }
    }
}