<?php
    
namespace   Admin\Controller;
use         Think\Controller;

    /**
     * 新闻类别
     */

 class NewsController extends MyController{
     public function __construct() {

        parent::__construct();
       
    }

    /**
     * 新闻类别分类列表 Category news_cat and display news_cat
     * @date 2015-01-22
     * @return void 
     */
    public function CategoryList(){
         $db = M("news_cat");
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
     * 添加新闻类别 display adding news category
     * @date 2015-01-22
     * @return void 
     */
    public function addNewsCategory(){
        $this->display();
    }

    /**
     * 执行添加新闻类别 display adding news category
     * @date 2015-01-22
     * @return void 
     */
    public function addCategory(){
        //dump($_POST);
        
        $db = M("news_cat");
        if($db->create()){
            $res=$db->add();
            if($res){
                $this->success("添加成功");
            }else{
                $this->error("添加失败");
            }
        }else{
             $this->error("添加失败");
        }
        
    }

    /**
     * 修改新闻类别 editCategory as
     * @date 2015-01-22
     * @return void 
     */
    public function editCategory(){
        $id = $_GET['id'];
        $data = M("news_cat");
        $list = $data->where("id =".$id)->find();       
        //dump($id);
        //dump($list);
        $this->assign("list",$list);
        $this->display();
    }

    /**
     * 执行修改新闻类别
     * @date 2015-01-22
     * @return void 
     */
    public function categoryUpdate(){
        $db = M("news_cat");
        if($db->create()){
            $rt=$db->save();
             if($rt){
                $this->success("修改成功",U("News/categoryList"));
                exit;
            }
        }else{
            $this->error("修改失败");
        }

    }

    /**
     * 删除新闻类别
     * @date 2015-01-22
     * @return void 
     */
    public function delCategory(){
        $id = $_GET['id'];
        $db = M("news_cat");
        $ls = $db->where("id =".$id)->delete();

        if($ls){
            $this->success("删除成功",U("News/categoryList"));
        }else{
            $this->error("删除失败");
        }
    }

    public function del(){
        $ids = array();
        $ids = $_GET['id'];
        $data = implode(",",$ids);
        $db = M("news_cat");
        $list= $db ->where("id in (".$data.")")->delete();
        if($list){
            echo 1;
        }else{
            echo 0;
        }
    }

    /**
     * 添加新闻
     * @date 2015-01-22
     * @return void 
     */
    public function addNews(){
		$db = M("news_cat");
        $list = $db ->select();
        // dump($list);
        $this->assign("list",$list);

        $this->display();
    }

    /**
     * 执行添加新闻
     * @date 2015-01-22
     * @return void 
     */
    public function doAddNews(){
        $_POST['thumbnail']=$this->upload();
        $_POST['create_time']= time();


        $db = M("news_contents");
        if($db->create()){
            $res=$db->add();
            if($res){
                $this->success("添加成功",U("News/newsManager"));
            }else{
                $this->error("添加失败");
            }
        }else{
             $this->error("添加失败");
        }      
    }

    
    /**
     * 单图片上传
     * @date 2015-01-22
     * @return void 
     */
 	public function upload(){
 		$upload = new \Think\Upload();
 		$upload->maxSize = 3000000;
 		$upload->exts    =array('jpg','gif','png','jpeg');
 		 $upload->rootPath="Public/News/"; 
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
     * 修改新闻
     */
    public function editNews(){
        $id = $_GET['id'];
        $db = M("news_cat");
        $listCat = $db ->select();
        $data = M("news_contents");
        $list = $data->where("id =".$id)->find();
        //dump($id);
        //dump($list);
        $this->assign("list",$list);
        $this->assign("listCat",$listCat);
        $this->display();
    }

    /**
     * 新闻List
     * @date 2015-01-22
     * @return void 
     */
    public function newsManager(){

    	$db = M("news_contents");
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
     * 执行修改新闻
     * @date 2015-01-22
     * @return void 
     */
    public function updateNews(){
    	// dump($_POST);
    	// die;
        $db = M("news_contents");
        if($db->create()){
            $rt=$db->save();
             if($rt){
                $this->success("修改成功",U("News/newsManager"));
                exit;
            }else{
            	$this->error("修改失败");
            }
        }else{
            $this->error("修改失败");
        }

    }

    /**
     * 删除新闻
     * @date 2015-01-22
     * @return void 
     */
    public function delNews(){
        $id = $_GET['id'];
        $db = M("news_contents");
        $ls = $db->where("id =".$id)->delete();

        if($ls){
            $this->success("删除成功",U("News/categoryList"));
        }else{
            $this->error("删除失败");
        }
    }


}
