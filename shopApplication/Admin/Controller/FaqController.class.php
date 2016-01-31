<?php
     namespace Admin\Controller;
     use   Think\Controller;
     header("content-type:text/html;charset=utf-8");
  /*
   *   
   * 常见问题（服务与支持）
   *
   *
   *
   */
    class FaqController extends MyController{
    	public function __construct(){
    		parent::__construct();
    	}
      //添加分类
    	public function faqCate(){
    		$this->display();
    	}
      //执行保存操作
      public function cateSave(){
         $db = M("support_faq_cate");
         if($db->create()){
            $rt = $db->add();
            if($rt){
              $this->success("添加成功",'cateList');
              exit;
            }
         }else{
            $this->error("添加失败");
         }
      }
      //FAQ分类列表
  public function cateList(){
        $db =M("support_faq_cate");
        $data = $db->select();

        $this->assign("data",$data);

        $this->display();
  }
  //编辑分类
  public function editCate(){
    $id = I("get.id");
    $db = M("support_faq_cate");
    $data = $db ->find($id);

    $this->assign("data",$data);
    $this->display();
  }
  //执行修改分类
  public function editcateSave(){
    $data=I('post.');
    $id=I('post.id');
     $db =M("support_faq_cate");
      $rt = $db->where(array('id'=>$id))->save($data);
      if($rt){
        $this->success("修改成功",'cateList');
      }else{
        $this->error("修改失败",'cateList');

     }
  }

  //删除分类
  public function delCate(){
    $id = I("get.id");
     $db = M("support_faq_cate");
     $rt = $db->delete($id);
     if($rt){
      $this->success("删除成功");
     }else{
      $this->error("删除失败");
     }
  }
      //添加文章
      public function faqArticles(){
          $db = M("support_faq_cate");
          $list = $db->select();
          $this->assign("list",$list);
          $this->display();
      }
      //执行添加文章
      public function addFaq(){
          // $_POST['faq_content'] = htmlspecialchars($_POST['faq_content']);
           // dump($_POST);
           $_POST['create_time'] =time();
           $db = M("faq_articles");

           if($db->create()){
              $rt = $db->add();
              if($rt){
                $this->success("添加成功",'articleList');
                exit;
              }
           }else{
               $this->error("修改失败");
           }
      }
      //文章列表
      public function articleList(){
              $db = M("faq_articles");
              $data = M("support_faq_cate");
              $datalist = $data->select();
              $this->assign("datalist",$datalist);
              $count = $db->count();
              $page = new \Think\Page($count,8);
              $show = $page->show();
              $list = $db->limit($page->firstRow.','.$page->listRows)->select();
              foreach($list as &$v){
                   $cname = $data->where("id = ".$v['cid'])->find();
                   $v['cname'] = $cname['cate_name'];
              }
              $this->assign("page",$show);
              $this->assign("list",$list);
              $this->display();     
      }
      //查看文章
      public function lookArticle(){
           $id = I("get.id");
           $db = M("faq_articles");
           $data = M("support_faq_cate");
           $user=$db->find($id);
           $cname=$data->field("cate_name")->find($user['cid']);
           $this->assign("list",$user);
           $this->assign("cname",$cname);
           $this->display();
      }
      //编辑文章
      public function editArticle(){
           $id = I("get.id");
           $db = M("faq_articles");
           $data = M("support_faq_cate");
           $user=$db->find($id);
           $cname=$data->select();
           $this->assign("list",$user);
           $this->assign("cname",$cname);
           $this->display();
      }
      //执行修改
      public function editSave(){
            $db = M("faq_articles");
            if($db->create()){
              $rt = $db->save();
               if($rt){
                $this->success("修改成功",U("faq/articleList"));
               }
            }else{
               $this->error("修改失败");
            }
          
      }
      //删除文章
      public function delArticle(){
          $id = I("get.id");
          $db = M("faq_articles");
          $rt = $db ->delete($id);
          if($rt){
            $this->success("删除成功");
          }else{
            $this->error("删除失败");
          }
      }

     
    }
