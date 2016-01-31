<?php

namespace Home\Controller;

use Think\Controller;

date_default_timezone_set('PRC');

class JobController extends MyController
{
    public function __construct()
    {
        parent::__construct();
    }

    //job 列表
    public function index()
    {
        $db = M('job_cat');
        $dc = M('jobs');
        $id = $_GET['id'];
        $data = $db->where("is_show = 1")->select();
        if ($id == 0) {
            $where = '';
        } else {
            $where = " AND cat_id = {$id}";
        }
        $count = $dc->where("is_show = 1 $where")->count();
        $page = new \Think\Page($count, 2);
        $showPage = $page->show();

        $job = $dc->where("is_show = 1 $where")->order('id desc')->limit($page->firstRow . ',' . $page->listRows)->select();
        foreach ($job as &$jo) {
            $cat_id = $jo['cat_id'];
            $row = $db->where("id = $cat_id")->find();
            $jo['cat_name'] = $row['cat_name'];
        }


        //获得9条最新新闻
        $news = M('news_contents');
        $n = $news -> limit(9) -> field('id,title')->select();
        $this -> assign('news',$n);


        $this->assign('page', $showPage);
        $this->assign('data', $data);
        $this->assign('job', $job);


        $this->display();
    }

    //上传简历
    public function apply(){

        $this -> display();
    }

    public function upApply(){
        $db = M('job_applies');
        $row = $db ->data($_POST)->add();
        echo $row;
    }

}
