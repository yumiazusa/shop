<?php
    
namespace   Admin\Controller;
use         Think\Controller;

    /**
     * SEO
     */

 class SeoController extends MyController{
     
    /**
     * 构造函数，初始化
     * @date 2015-01-22
     */
    public function index(){
	$this->display('seoList');
    }

    public function __construct() {
        parent::__construct();
       
    }

    /**
     * seoList SEO列表
     */

    public function seoList(){
		$db = M("seo");
		$count = $db->count();
		$page = new \Think\Page($count,10); 
		$list = $db -> limit($page->firstRow.','.$page->listRows)->select();
		//dump($list);
		$this->assign("list",$list);
		$show  = $page->show();
		$this->assign("page",$show);
		$this->display();

    }

    /**
     * addseo 添加seo
     * @return void 
     */

    public function addseo(){
    	$this->display();
    }

    /**
     * doaddseo 执行添加seo
     * @return void 
     */

    public function doaddSeo(){
    	//dump($_POST);
    	$_POST['time']= time();

        $db = M("seo");
        if($db->create()){
            $res=$db->add();
            if ($res) {
                // 写入日志
                $cLog = new \Admin\Controller\LogController();
                $cLog->setLog('SEO（SEO_IDENT:' . $_POST['ident'] . '）添加成功');
                $this->success($_POST['ident'].'页面SEO添加成功', 'seoList');
                exit;
            } else {
                $this->error('SEO添加失败');
            }
        }else{
             $this->error("添加失败");
               exit;
        }

    	$this->display('');
    }

    /**
     * editSeo 修改seo
     * @return void 
     */
    public function editSeo(){
        $id = $_GET['id'];
        $data = M("seo");
        $list = $data->where("id =".$id)->find();
        //dump($id);
        //dump($list);
        $this->assign("list",$list);
        $this->display();
    }

    
     /**
     * doeditSeo 执行修改Seo
     * @return void 
     */
    public function doeditSeo(){
        $db = M("seo");
        if($db->create()){
            $rt=$db->save();
                if ($rt) {
                // 写入日志
                $cLog = new \Admin\Controller\LogController();
                $cLog->setLog('SEO（SEO_ID:' . $_POST['id'] . '）修改成功');
                $this->success($_POST['id'].'SEO修改成功', 'seoList');
                exit;
            } else {
                $this->error('SEO修改失败');
                }
        }else{
            $this->error("SEO修改失败");
        }
    }

    
    /**
     * delSeo 删除Seo
     * @return void 
     */
    public function delSeo(){
        $id = $_GET['id'];
        $db = M("seo");
        $ls = $db->where("id =".$id)->delete();

        if($ls){
            $cLog = new \Admin\Controller\LogController();
                $cLog->setLog('SEO（SEO_ID:' . $id . '）删除成功');
                $this->success($id.'SEO删除成功');
                exit;
            } else {
                $this->error('SEO删除失败');
                }
    }





}