<?php
namespace Admin\Controller;
use  \Think\Contoller;
date_default_timezone_set(PRC);
/*
 *服务与支持 政策文章
 *
 */
class SupportArticlesController extends MyController{
	public function __construct(){
		parent::__construct();
	}
	public function articleCate(){
        $this->display();
	}
//执行添加分类
  public function addCate(){
       $db = M("support_article_cat");
       if($db->create()){
          $rt = $db->add();
          if($rt){
            $this->success("添加成功");
            exit;
          }
       }else{
           $this->error("添加失败");
       }
  }
  //文章分类列表
  public function cateList(){
  	    $db =M("support_article_cat");
  	    $data = $db->select();
  	    foreach($data as &$v){
  	    	if($v['is_show']==1){
  	    		$v['show'] ="显示";
  	    	}else{
  	    		$v['show']="不显示";
  	    	}
  	    }

  	    $this->assign("data",$data);

  	    $this->display();
  }
  //编辑分类
  public function editCate(){
  	$id = I("get.id");
  	$db = M("support_article_cat");
  	$data = $db ->find($id);

  	$this->assign("data",$data);
  	$this->display();
  }
  //执行修改分类
  public function editSave(){
  	 $db =M("support_article_cat");
  	 if($db->create()){
  	 	$rt = $db->save();
  	 	if($rt){
  	 		$this->success("修改成功");
  	 	}else{
  	 		$this->success("修改失败");
  	 	}
  	 }
  }
  //删除分类
  public function delCate(){
  	$id = I("get.id");
  	 $db = M("support_article_cat");
  	 $rt = $db->delete($id);
  	 if($rt){
  	 	$this->success("删除成功");
  	 }else{
  	 	$this->error("删除失败");
  	 }
  }
  //添加文章详情
  public function article(){
  	   $db = M("support_article_cat");
  	   $data =$db->select();
  	   $this->assign("data",$data);
  	   $this->display();
  }
  //执行添加
  public function addArticle(){
  	   if($_POST){
	  	   $_POST['content'] = $_POST['my_content'];
	  	   $_POST['create_time'] = time();
	  	   $db = M("support_article_detail");
	  	   if($db->create()){
	  	   	  $rt = $db->add();
	  	   	  if($rt){
	  	   	  	$this->success("添加成功");
	  	   	  	exit;
	  	   	  }
	  	   }else{
	  	   	  $this->error("添加失败");
	  	   }
	  	}
  }
  //文章列表
  public function articleList(){
  	  $db = M("support_article_detail");
  	  $data = M("support_article_cat");
      if($_GET['cat_id']){
          $da   =$data->select();
         $map['cat_id'] = array("eq",$_GET['cat_id']);
         $count = $db->where($map)->count();
         $page  = new \Think\Page($count,10);
         foreach($map as $key =>$val){
            $page->parmeter = "$key=".urldecode($val);
         }
         $show =$page->show();
         $list = $db->where($map)->order("create_time desc")->limit($page->firstRow.','.$page->listRows)->select();
         foreach($list as &$v){
             $cname = $data->find($v['cat_id']);
             if($v['is_show'] ==1){
              $v['show'] ="显示";
             }else{
              $v['show'] ="不显示";
             }
             $v['creat_time'] =date("Y-m-d H:i:s");
             $v['cname'] = $cname['cat_name'];
          }
           $this->assign("page",$show);
           $this->assign("data",$da);
           $this->assign("list",$list);
           $this->display();
           exit;
      }
  	  $da   =$data->select();
  	  $count = $db->count();
  	  $page = new \Think\Page($count,10);
  	  $show = $page->show();
  	  $list = $db->order("create_time desc")->limit($page->firstRow.','.$page->listRows)->select();
  	  foreach($list as &$v){
  	  	 $cname = $data->find($v['cat_id']);
  	  	 if($v['is_show'] ==1){
  	  	 	$v['show'] ="显示";
  	  	 }else{
  	  	 	$v['show'] ="不显示";
  	  	 }
  	  	 $v['creat_time'] =date("Y-m-d H:i:s");
  	  	 $v['cname'] = $cname['cat_name'];
  	  }
  	  $this->assign("page",$show);
      $this->assign("data",$da);
  	  $this->assign("list",$list);
  	  $this->display();
  }
  //文章编辑
  public function editArticle(){
  	 $id = I("get.id");
  	 $db = M("support_article_detail");
  	 $data = M("support_article_cat");
  	 $list = $db->find($id);
     $da = $data->select();
     $this->assign("list",$list);
     $this->assign("data",$da);
     $this->display();
  }
  //修改文章
   public function articleSave(){
     $_POST['content'] = $_POST['my_content'];
     $db = M("support_article_detail");
     if($db ->create()){
        $rt = $db->save();
        if($rt){
          $this->success("修改成功",U("SupportArticles/articleList"));
          exit;
        }
     }else{
        $this->error("修改失败");
     }
  }
  //删除文章
  public function delArticle(){
     $id = I("get.id");
     $db = M("support_article_detail");
     $rt = $db->delete($id);
     if($rt){
       $this->success("删除成功");
     }else{
      $this->error("删除成功");
     }
  }


}
 