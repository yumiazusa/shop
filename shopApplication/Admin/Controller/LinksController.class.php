<?php
    
namespace   Admin\Controller;
use         Think\Controller;

    /**
     * 友情链接
     */ 

 class LinksController extends MyController{
     public function __construct() {

        parent::__construct();
       
    }

 	/**
     * 友情链接列表
     */
    public function linksManager(){
        
         $db = M("links");
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
     * 添加友情链接
     */
    public function addLinks(){
        $this->display();
    }

    /**
     * 执行添加友情链接
     */
    public function doAddLinks(){
        //dump($_POST);
        //dump($_FILES);
        $picture=$this->upload();
        $db = M("links");
        //dump($picture);
        $_POST['picture']=$picture;

        if($db->create()){
            $res=$db->add();
            if($res){
                $this->success("添加成功",U("Links/linksmanager"));
            }else{
                $this->error("添加失败");
            }
        }else{
            $this->error("添加失败");
        }      
    }

    
    /**
     * 单图片上传
     */
    public function upload(){
        $upload = new \Think\Upload();
        $upload->maxSize =30000;
        $upload->exts    =array('jpg','gif','png','jpeg');
        $upload->rootPath="Public/Links/"; 
        $upload->savePath="";
        $info=$upload->upload();
        if(!$info) {                                         
            $this->error($upload->getError());
        }else{                                                 // 上传成功 获取上传文件信息    
            foreach($info as $file){        
                return $file['savepath'].$file['savename'];
            }
        }
    }

    /**
     * 修改友情链接
     */
    public function editLinks(){
        $id = $_GET['id'];
        $data = M("links");
        $list = $data->where("id =".$id)->find();       
        //dump($id);
        //dump($list);
        $this->assign("list",$list);
        $this->display();
    }

    /**
     * 执行修改友情链接
     */
    public function doEditLinks(){
        $db = M("links");
        if($db->create()){
            $rt=$db->save();
             if($rt){
                $this->success("修改成功",U("Links/linksmanager"));
                exit;
            }
        }else{
            $this->error("修改失败");
        }

    }

    /**
     * 删除友情链接
     */
    public function delLinks(){
        $id = $_GET['id'];
        $db = M("links");
        $ls = $db->where("id =".$id)->delete();

        if($ls){
            $tis->success("删除成功",U("Links/linksmanager"));
        }else{
            $this->error("删除失败");
        }
    }
    



}