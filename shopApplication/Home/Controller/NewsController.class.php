<?php
    
namespace   Home\Controller;
use         Think\Controller;

    /**
     * 新闻类别
     * 
     * @author: Sun
     * @date: 2015-01-12
     */

 class NewsController extends MyController{

    /**
     * 构造函数，初始化
     * @date 2015-01-22
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * 新闻 index display
     * @date 2015-01-22
     * @return void 
     */
    public function index(){

        $db = M("news_contents");
        $count = $db->where('is_show=1')->count();
        $page = new \Think\Page($count,2); 
        $list = $db-> where('is_show=1')->limit($page->firstRow.','.$page->listRows)->select();
        
        //dump($list);
        //dump($page);
        $this->assign("list",$list);
        $show  = $page->show();
        $this->assign("page",$show);
        $this->display();
    }

    /**
     * 新闻  display individual news from id
     * @date 2015-01-22
     * @return void 
     */
    public function news(){
        $id = $_GET['id'];
        $data = M("news_contents");
        $list = $data->where("id =".$id)->find();       
        //dump($id);
        //dump($list);

        $this->assign("list",$list);
        $this->display();
    }   


    public function tmp()
    {
        $this->display();
    }



}