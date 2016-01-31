<?php
     namespace Admin\Controller;
     use   Think\Controller;
     header("content-type:text/html;charset=utf-8");
     date_default_timezone_set(PRC);
  /*
   *   
   * 下载（服务与支持）
   *
   *
   *
   */
   class SupportDownController extends MyController{
          public function __construct(){
              parent::__construct();
          }
          //添加分类页面
          public function downCate(){
               $this->display();
          }
          //执行添加分类
          public function addCate(){
               $db = M("support_download_cat");
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
          public function cateList(){
              $db = M("support_download_cat");
              $list =$db->select();
              $this->assign("list",$list);
              $this->display();
          }
          public function editCate(){
              $id = $_GET['id'];
              $db = M("support_download_cat");
              $down = $db->where("id = ".$id)->find();
             
              $this->assign("list",$down);
              $this->display();

          }
          public function cateSave(){
            $db = M("support_download_cat");
            if($db->create()){
               $rt = $db->save();
               if($rt){
                 $this->success("修改成功",U("SupportDown/cateList"));
               }else{
                  $this->error("修改失败");
               }
            }
          }
          public function delCate(){
              $id = $_GET['id'];
              $db = M("support_download_cat");
               $rt = $db->delete($id);
              if($rt){
                $this->success("删除成功");
              }else{
                $this->error("删除失败");
              }
          }
          //添加下载文件页面
          public function down(){
            $db = M("support_download_cat");
            $list = $db->select();
            //dump($list);
            $this->assign("list",$list);
            $this->display();
          }
          //执行下载文件
          public function addFile(){
             if($_POST){ 
                if($_FILES['filename']['name']!=""){    
                   $_POST['savepath'] = $this->upload();
                }
                if($_FILES['fileimg']['name']!=""){    
                   $_POST['thumbnail'] = $this->uploadImg();
                }
                $_POST['description'] = $_POST['my_content'];
                $_POST['ctime'] = time();
                $db = M("support_downloads");
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
          //上传文件
          public function upload(){
            $upload = new \Think\Upload();
            $upload->maxSize = 3145728;
            $upload->exts    =array('txt','doc','html','php','xls','rar','zip');
             $upload->rootPath="Public/download/uploadFile/"; 
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
          //上传图片
          public function uploadImg(){
            $upload = new \Think\Upload();
            $upload->maxSize = 3145728;
            $upload->exts    =array('jpg','png','jpeg');
             $upload->rootPath="Public/download/downImage/"; 
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
          //下载文件列表
          public function fileList(){
              $db = M("support_downloads");
               $da = M("support_download_cat");
               //搜索
             if($_GET['title']){
                    $map["title"] = array("like",$_GET[title]."%");
                    $count = $db->where($map)->count();
                    $page = new \Think\Page($count,10);
                    foreach($map as $key=>$val){
                       $page->parameter = "$key = ".urlencode($val);
                    }
                  
              }
              $count = $db->where($map)->count();
              $page  = new \Think\Page($count,10);
              $show = $page->show();
              $list = $db->where($map)->limit($page->firstRow.','.$page->listRows)->select();
              $data = M("support_download_cat");
              foreach($list as &$v){
                  $cname= $data->field("cat_name")->find($v['cat_id']);
                  $v['cname'] =$cname['cat_name'];
                  $v['ctime'] = date("Y-m-d H:i:s",$v['ctime']);
                  if($v['is_show']==1){
                     $v['show'] ="显示";
                  }else{
                    $v['show'] ="不显示";
                  }
              }
              // dump($list);
              // exit;
              $this->assign("page",$show);
              $this->assign("list",$list);
              $this->display();
          }
          //编辑下载文件
          public function editFile(){
              $id = I("get.id");
              $db = M("support_downloads");
              $data = M("support_download_cat");
              $list = $db->find($id);
              $cname = $data->select();
              //dump($cname);
              //dump($list);
              $this->assign("list",$list);
              $this->assign("cname",$cname);

              $this->display();
          }
          //执行修改
         public function editSave(){
            if($_FILES['filename']['name']!=""){
              $_POST['savepath']=$this->upload();
            }
            if($_FILES['fileimg']['name']!=""){
              $_POST['thumbnail']=$this->uploadImg();
            }
            $_POST['description'] =$_POST['my_content'];
            $db =M("support_downloads");
            if($db->create()){
              $rt =$db->save();
              if($rt){
                $this->success("修改成功",U("SupportDown/fileList"));
                exit;
              }
            }else{
                $this->error("修改失败");
            }

         }
         //删除文件
         public function delFile(){
            $id = I("get.id");
            $db =M("support_downloads");
            $rt = $db->delete($id);
            if($rt){
              $this->success("删除成功");
            }else{
              $this->error("删除失败");
            }
         }
   }