<?php
 /*
  * 售后服务
  * 
  *
  *
  *
  */
 namespace Admin\Controller;
 use  Think\Controller;

 class SaleServiceController extends Controller{
 	//退货商品
 	public function returnProduct(){
 		  if($_GET['service_sn'] !=""){
 		  	  $service_sn =$_GET['service_sn'];
              $map['service_sn'] =array("like","{$service_sn}%");
 		  }
 		  if($_GET['service_status']!=""){
 		  	  $service_status =$_GET['service_status'];
 		  	  $map['service_status'] =array("eq",$service_status);
 		  }
 		  $map['service_type'] =array("eq",2);
 		  $db =M("sale_service");
 		  $product = M("product");
 		  $order =M("orders");
	  	  $count = $db->where($map)->count();
	  	   // echo $db->getlastSql();
	  	   // exit;
	  	  $page = new \Think\Page($count,6);
	  	  foreach($map as $key=>$val){
	  	  	 $page->parameter .="$key = ".urlencode($val).'&';
	  	  }
	  	  $show = $page->show();
	  	  $list = $db->where($map)->order("apply_time desc")->limit($page->firstRow.','.$page->listRows)->select();
	  	  foreach($list as &$v){
             $v['products'] = $product->where("id = ".$v['pro_id'])->find();
             $v['orders'] = $order ->where("id =".$v['order_id'])->find();
	  	  }
	  	  //dump($list);
	  	  $this->assign("page",$show);
	  	  $this->assign("list",$list);
	  	  $this->display();
 	}
 	//查看服务单详情
 	public function lookService(){
 		 $id =$_GET['id'];
 		 $db =M('sale_service');
 		 $product =M("product");
 		 $list = $db ->where("id = ".$id)->find();
         $list['pro_name'] = $product->field('pro_name')->where("id = ".$list['pro_id'])->find();
         if($list['service_type']==1){
         	$list['serive_type'] ="维修";

         }else if($list['service_type']==2){
         	$list['service_type'] ="退货";
         }else{
         	$list['service_type'] ="换货";
         }

         $this->assign("list",$list);
         $this->display();
 	}
 	//同意申请
 	public function apply(){
 		$id = $_POST['id'];
 		$db =M("sale_service");
 		$ser = M("service_details");
 		$data['service_status'] =1;
 		$rt = $db->where("id = ".$id)->save($data);
 		if($rt){
 			$data['service_id'] =$id;
 			$data['message'] ="申请通过，请等待受理";
 			$data['time'] =time();
 			$data['operator']="系统";
 			$rts = $ser->add($data);
 			if($rts){
 				echo 1;
 			    exit;
 			}else{
 				echo 3;
 				exit;
 			}
 			
 		}else{
 			echo 2;
 			exit;
 		}
 	}
 	//确认受理
 	public function accept(){
 		$id = $_POST['id'];
 		$db =M("sale_service");
 		$data['service_status'] =2;
 		$ser = M("service_details");
 		$rt = $db->where("id = ".$id)->save($data);
 		if($rt){
 			$data['service_id'] =$id;
 			$data['message'] ="受理成功，请将商品返回";
 			$data['time'] =time();
 			$data['operator']="系统";
 			$rts = $ser->add($data);
 			echo 1;
 			exit;
 		}else{
 			echo 2;
 			exit;
 		}
 	}
 	//确认完成
 	public function finish(){
 		$id = $_POST['id'];
 		$db =M("sale_service");
 		$ser = M("service_details");
 		$data['service_status'] =3;
 		$rt = $db->where("id = ".$id)->save($data);
 		if($rt){
 			$data['service_id'] =$id;
 			$data['message'] ="服务完成";
 			$data['time'] =time();
 			$data['operator']="系统";
 			$rts = $ser->add($data);
 			echo 1;
 			exit;
 		}else{
 			echo 2;
 			exit;
 		}
 	}
 	//换货
 	public function changeProduct(){
 		  if($_GET['service_sn'] !=""){
 		  	  $service_sn =$_GET['service_sn'];
              $map['service_sn'] =array("like","{$service_sn}%");
 		  }
 		  if($_GET['service_status']!=""){
 		  	  $service_status =$_GET['service_status'];
 		  	  $map['service_status'] =array("eq",$service_status);
 		  }
 		  $map['service_type'] =array("eq",3);
 		  $db =M("sale_service");
 		  $product = M("product");
 		  $order =M("orders");
	  	  $count = $db->where($map)->count();
	  	   // echo $db->getlastSql();
	  	   // exit;
	  	  $page = new \Think\Page($count,6);
	  	  foreach($map as $key=>$val){
	  	  	 $page->parameter .="$key = ".urlencode($val).'&';
	  	  }
	  	  $show = $page->show();
	  	  $list = $db->where($map)->order("apply_time desc")->limit($page->firstRow.','.$page->listRows)->select();
	  	  foreach($list as &$v){
             $v['products'] = $product->where("id = ".$v['pro_id'])->find();
             $v['orders'] = $order ->where("id =".$v['order_id'])->find();
	  	  }
	  	  //dump($list);
	  	  $this->assign("page",$show);
	  	  $this->assign("list",$list);
	  	  $this->display();
 	}
 	public function repareProduct(){
 		 if($_GET['service_sn'] !=""){
 		  	  $service_sn =$_GET['service_sn'];
              $map['service_sn'] =array("like","{$service_sn}%");
 		  }
 		  if($_GET['service_status']!=""){
 		  	  $service_status =$_GET['service_status'];
 		  	  $map['service_status'] =array("eq",$service_status);
 		  }
 		  $map['service_type'] =array("eq",1);
 		  $db =M("sale_service");
 		  $product = M("products");
 		  $order =M("orders");
	  	  $count = $db->where($map)->count();
	  	   // echo $db->getlastSql();
	  	   // exit;
	  	  $page = new \Think\Page($count,6);
	  	  foreach($map as $key=>$val){
	  	  	 $page->parameter .="$key = ".urlencode($val).'&';
	  	  }
	  	  $show = $page->show();
	  	  $list = $db->where($map)->order("apply_time desc")->limit($page->firstRow.','.$page->listRows)->select();
	  	  foreach($list as &$v){
             $v['products'] = $product->where("id = ".$v['pro_id'])->find();
             $v['orders'] = $order ->where("id =".$v['order_id'])->find();
	  	  }
	  	  //dump($list);
	  	  $this->assign("page",$show);
	  	  $this->assign("list",$list);
	  	  $this->display();

 	}
 }