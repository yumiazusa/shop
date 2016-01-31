<?php
 namespace Admin\Controller;
use Think\Controller;

 /*
  *  广告管理
  *
  *
  */
 class AdvPositionController extends MyController{
 	 public function __construct() {

        parent::__construct();

    }
    //添加广告类别页
 	public function adv(){
 		$this->display();
 	}
 	//执行添加广告类别
 	public function addCate(){

 		$db = M("adv_position");
 		if($db->create()){
 			$rt=$db->add();
            if($rt){
            	$this->success("添加成功",U("AdvPosition/cateList"));
            }else{
            	$this->error("添加失败");
            }
 		}else{
             $this->error("添加失败");
 		}
 	}
 	//添加广告页面
 	public function addAdv(){
 		$db = M("adv_position");
 		$result = $db->select();
 		foreach($result as &$v){
            $v["catename"]=$v['p_name']."规格: 宽".$v['adv_width']."高".$v['adv_height'];
 		}
        $product =M("product");
        $list = $product->field("id,pro_name")->select();
        $this->assign("list",$list);
 		$this->assign("data",$result);
 		$this->display();
 	}
 	//执行添加广告
 	public function insertAdv(){
 		$_POST['thumbnail']=$this->upload();
 		$db =M("advs");

 		$_POST['add_time'] = time();

 		if($db->create($_POST)){
 			$rt = $db->add();
 			 if($rt){
            	$this->success("添加成功",U("AdvPosition/advManager"));
            	exit;
            }


 		}
 		$this->error("添加失败");

 	}
 	//单图片上传
 	public function upload(){
 		$upload = new \Think\Upload();
 		$upload->maxSize = 3145728;
 		$upload->exts    =array('jpg','gif','png','jpeg');
 		 $upload->rootPath="Public/Adv/";
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
 	//广告管理
 	public function advManager(){
 		 $model = M();
 		 //分页
 		 $count = $model->table("__ADVS__ as adv,__ADV_POSITION__ as advposition")->where("adv.position_id=advposition.id")->count();
 		 $page  = new \Think\Page($count,10);
 		 $show  = $page->show();
 		 $this->assign("page",$show);

 		 $list  = $model->field("adv.id,adv.position_id,adv.adv_name,adv.is_show,advposition.id as pid,advposition.p_name,advposition.adv_width,advposition.adv_height")
                        ->table("__ADVS__ as adv,__ADV_POSITION__ as advposition")
                        ->where("adv.position_id=advposition.id")
                        ->limit($page->firstRow.','.$page->listRows)
                        ->order('adv.add_time ASC')
                        ->select();

 		 foreach($list as &$v){
 		 	$v['catename'] = $v['p_name']."规格: 宽".$v['adv_width']."高".$v['adv_height'];
 		 }
 		 $this->assign("data",$list);
 		 $this->display();
 	}

 	//编辑广告位
 	public function editAdv(){
 		$id = $_GET['id'];
 		$data = M("advs");
 		$list = $data->where("id =".$id)->find();
 		$db = M("adv_position");
 		$result = $db->select();
 		foreach($result as &$v){
            $v["catename"]=$v['p_name']."规格: 宽".$v['adv_width']."高".$v['adv_height'];
 		}
        $product =M("product");
        $productlist = $product->field("id,pro_name")->select();
        $this->assign("product",$productlist);
 		$this->assign("res",$result);
 		$this->assign("data",$list);
 		$this->display();
 	}
 	//执行修改保存
    public function editSave(){
    	if($_FILES['file']['name']!=""){
    		$_POST['thumbnail']=$this->upload();
    	}
    	$mdl = M("advs");
    	if($mdl->create()){
    		$rt=$mdl->save();
    		if($rt){
            	$this->success("修改成功",U("AdvPosition/advManager"));
            	exit;
            }
    	}
        $this->error("修改失败");
    }
    //删除广告
    public function delAdv(){
    	$id = $_GET['id'];
    	$db = M("advs");
    	$ls = $db->where("id =".$id)->delete();

    	if($ls){
    		$this->success("删除成功",U("AdvPosition/advManager"));
    	}else{
    		$this->error("删除失败");
    	}
    }
    //广告分类列表
    public function cateList(){

         $db = M("adv_position");
         $count = $db->count();
          $page = new \Think\Page($count,10);
         $list = $db -> limit($page->firstRow.','.$page->listRows)->select();
         foreach($list as &$v){
            $v["catename"]=$v['p_name']."规格: 宽".$v['adv_width']."高".$v['adv_height'];
 		 }
 		 $this->assign("list",$list);
 		 $show  = $page->show();
 		 $this->assign("page",$show);
 		 $this->display();
    }
    //修改广告分类
    public function editCate(){
    	$id = I("get.id");
    	$db = M("adv_position");
    	$list = $db->find($id);
    	$this->assign("list",$list);
    	$this->display();
    }
    //执行修改广告分类
    public function cateSave(){
    	$db = M("adv_position");
    	if($db->create()){
    		$rt=$db->save();
    		 if($rt){
            	$this->success("修改成功",U("AdvPosition/cateList"));
            	exit;
            }
    	}else{
    		$this->error("修改失败");
    	}

    }
    //删除广告分类
    public function delCate(){
    	$id = I("get.id");
    	$db = M("adv_position");
    	$ls = $db->delete($id);

    	if($ls){
    		$this->success("删除成功",U("AdvPosition/cateList"));
    	}else{
    		$this->error("删除失败");
    	}
    }
    public function delall(){
    	$ids = array();
        $ids = $_GET['id'];
    	$data = implode(",",$ids);
    	$db = M("adv_position");
    	$list= $db ->where("id in (".$data.")")->delete();
    	if($list){
    		echo 1;
    	}else{
    		echo 0;
    	}
    }
 }