<?php

namespace   Admin\Controller;
use         Think\Controller;
class ArchivesController extends MyController {

    // 获取主页列表
    public function index()
    {
        $model = M('archives');
        $pageCounts = $model->count();
        $pageSize = 15;

        $page = new \Think\Page($pageCounts, $pageSize);
        $archivesRes = $model->limit($page->firstRow, $page->listRows)->select();

        $pageShow = $page->show();
        $this->assign('page', $pageShow);
        $this->assign('archives', $archivesRes); 
        $this->display('list');
    }

    // 获取添加页面
    public function add ()
    {
        $this->display();
    }

    // 获取编辑页面
    public function edit ()
    {
        $getId = I('get.id');
        $model = M('archives');
        $archivesRes = $model->find($getId);
        $this->assign('ach', $archivesRes);
        $this->display();
    }

    // 操作编辑操作
    public function doEdit ()
    {
        $post = I('post.');
        $model = M('archives');

        if ( ! $model->create($post))
        {
            $this->error('数据对象创建失败');
            exit;
        }
        $getSymbol = $post['symbol'];

        // 生成静态文件
        // 分配变量
        $this->assign('title', $post['title']);
        $this->assign('keywords', $post['keywords']);
        $this->assign('description', $post['description']);
        $this->assign('content', $post['content']);
        $this->buildHtml($getSymbol, '', 'Archives:tmp2');

        if ($model->where("id='{$post['id']}'")->save())
        {
            $this->success('单页修改成功', 'index');
            exit;
        }
        $this->error('单页修改失败');
    }    

    // 删除单件记录
    public function delete ()
    {
        $getId = I('get.id');
        $getS  = I('get.symbol');
        $file = APP_PATH . '../Html/' . $getS . '.html';
        $model = M('archives');
        $affectedRows = $model->where("id = {$getId}")->delete();
        if ($affectedRows) {
            $file = APP_PATH . '../Html/' . $getS . '.html';
            @unlink($file);
            $this->success('执行成功');
            exit;
        }
        else
        {
            $this->error('删除失败');
        }
    }

    // 执行添加操作
    public function doAdd ()
    {
        $post = I('post.');
        $model = M('archives');

        if ( ! $model->create($post))
        {
            $this->error('数据对象创建失败');
            exit;
        }
        $getSymbol = $post['symbol'];

        // 判断数据库是否有重复存在
        $isRepeat = $model->where("symbol = '" . $getSymbol . "'")->find();

        if ($isRepeat)
        {
            $this->error('该标识已被占用');
            exit;
        }
        // 生成静态文件
        // 分配变量
        $this->assign('title', $post['title']);
        $this->assign('keywords', $post['keywords']);
        $this->assign('description', $post['description']);
        $this->assign('content', $post['content']);
        $this->buildHtml($getSymbol, '', 'Archives:tmp2');

        if ($model->add())
        {
            $this->success('单页创建成功', 'index');
            exit;
        }
        $this->error('单页创建失败');
    }

}