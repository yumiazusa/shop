<?php
/*
 * 会员中心:售后管理
 */


namespace Home\Controller;

use  Think\Controller;
use Think\Area;

class CustomController extends MyController
{
    public function __construct() {
        parent::__construct();
    }

    //维修单
    public function repairProduct(){
        //SEO
        $ident ="service";
        $idents =$this->seo($ident);
        $this->assign("title",$idents['title']);
        $this->assign("keywords",$idents['keywords']);
        $this->assign("description",$idents['description']);


        $sale =M("sale_service");
        $order = M("orders");
        $product = M("product");
        $user_id =$_SESSION['user']['userid'];
        $map['user_id'] =array("eq",$user_id);
        $map['service_type'] =array("eq",1);
        $count =$sale->where($map)->count();
        $page = new \Think\MyPage($count,6);
        $show =$page->show();
        $list =$sale->where($map)->order("apply_time")->limit($page->firstRow.','.$page->listRows)->select();
        foreach($list as &$v){
            $v['pro_name'] =$product->field("pro_name")->where("id = ".$v['pro_id'])->find();
            $v['order_sn'] =$order->field("order_sn")->where("id = ".$v['order_id'])->find();
        }
        $this->assign("page",$show);
        $this->assign("list",$list);
        $this->display();
    }

    //退货单
    public function returnProduct(){
        //SEO
        $ident ="service";
        $idents =$this->seo($ident);
        $this->assign("title",$idents['title']);
        $this->assign("keywords",$idents['keywords']);
        $this->assign("description",$idents['description']);


        $sale =M("sale_service");
        $order = M("orders");
        $product = M("product");
        $user_id =$_SESSION['user']['userid'];
        $map['user_id'] =array("eq",$user_id);
        $map['service_type'] =array("eq",2);
        $count =$sale->where($map)->count();
        $page = new \Think\MyPage($count,6);
        $show =$page->show();
        $list =$sale->where($map)->order("apply_time")->limit($page->firstRow.','.$page->listRows)->select();
        // echo $sale->getlastSql();
        foreach($list as &$v){
            $v['pro_name'] =$product->field("pro_name")->where("id = ".$v['pro_id'])->find();
            $v['order_sn'] =$order->field("order_sn")->where("id = ".$v['order_id'])->find();
        }
        // dump($list);
        $this->assign("page",$show);
        $this->assign("list",$list);
        $this->display();
    }

    //换货单
    public function changeProduct(){
        //SEO
        $ident ="service";
        $idents =$this->seo($ident);
        $this->assign("title",$idents['title']);
        $this->assign("keywords",$idents['keywords']);
        $this->assign("description",$idents['description']);


         $sale =M("sale_service");
        $order = M("orders");
        $product = M("product");
         $user_id =$_SESSION['user']['userid'];
        $map['user_id'] =array("eq",$user_id);
        $map['service_type'] =array("eq",3);
        $count =$sale->where($map)->count();
        $page = new \Think\MyPage($count,6);
        $show =$page->show();
        $list =$sale->where($map)->order("apply_time")->limit($page->firstRow.','.$page->listRows)->select();
        foreach($list as &$v){
            $v['pro_name'] =$product->field("pro_name")->where("id = ".$v['pro_id'])->find();
            $v['order_sn'] =$order->field("order_sn")->where("id = ".$v['order_id'])->find();
        }
        $this->assign("page",$show);
        $this->assign("list",$list);
        $this->display();
    }


    public function suggest(){
        //SEO
        $ident ="service";
        $idents =$this->seo($ident);
        $this->assign("title",$idents['title']);
        $this->assign("keywords",$idents['keywords']);
        $this->assign("description",$idents['description']);


        //获取用户的id
        $id = $_SESSION['user']['userid'];

        $feedbacks = M('feedbacks');
        $model = M();

        // 分页参数
        $feedbackCounts = $feedbacks->where("user_id = $id")->count();    //获取总页数
        $page = new \Think\Page($feedbackCounts, 5);    //每页5条数据
        $showPage = $page->show();

        //分页查询 获取id的所有的发言 保存到feedbacks中
        $res = $feedbacks
            ->where("user_id = {$id}")
            ->limit($page->firstRow, $page->listRows)
            ->order("id desc")
            ->select();

        //对分页查询的结果进行2次查询  获取对应id的回复信息
        foreach ($res as &$val) {
            $val['r_content'] = '';
            $val['r_time'] = '';
            $val['u_name'] = '';

            //再次查询回复表中的admin_id 对应的admin_username
            $arr = $model->table('__FEEDBACK_REPLIES__ as r, __ADMIN_USERS__ as u')
                ->field("r.content as r_content, r.reply_time, u.username")
                ->where("r.feedback_id = {$val['id']} AND r.admin_id = u.id")
                ->find();

            //结果存储到feedbacks的查询结果中
            if ($arr) {
                $val['r_content'] = $arr['r_content'];
                $val['r_time'] = $arr['reply_time'];
                $val['u_name'] = $arr['username'];
            }
        }

        $this->assign('page', $showPage);
        $this->assign('data', $res);

        $this->display();
        exit;


    }


    //进行留言
    public function suggestAdd(){

        //登陆了才可以留言
        if (isset($_SESSION['user'])) {
            $db = M('feedbacks');
            $data['content'] = $_POST['count'];
            $badword=M('badwords')->getField('badword',true);
            $badword1 = array_combine($badword,array_fill(0,count($badword),'*'));
            $data['content']=strtr($data['content'],$badword1);
            $data['user_id'] = $_SESSION['user']['userid'];
            $data['add_time'] = date(time());
            $row = $db->add($data);
            echo $row;
        } else {
            echo 0;
        }


    }


    //申请退货过程
    public function applyProcess() {
        //SEO
        $ident ="service";
        $idents =$this->seo($ident);
        $this->assign("title",$idents['title']);
        $this->assign("keywords",$idents['keywords']);
        $this->assign("description",$idents['description']);

        $pro_attr = M("products_attr");
        $product = M("product");
        $order_product = M("order_products");
        $order = M("orders");

        $product_id = $_GET['id'];
        $order_id = $_GET['ids'];

        $map['order_id'] = $map2['a.id'] = array("eq", $order_id);
        $user_id = $_SESSION['user']['userid'];
        $map['user_id'] = $map2['a.user_id']= array("eq", $user_id);
        $map['product_id'] = $map2['b.product_id'] = array("eq", $product_id);

        //判断是否存在订单
        $orders=$order->alias('a')->join('LEFT JOIN im_order_products b ON a.id = b.order_id')
                      ->where($map2)
                      ->find();
        if(!$orders){
            $this->error('订单不存在请检查!');
            exit;
        }


        //查找商品是否有赠品
        $zp = $pro_attr->field("prop_value")->where("pro_id = " . $product_id . " AND prop_id =3")->find();
         if ($zp) {
             $list = $product->field("pro_name")->where("id IN (" . $zp['prop_value'] . ")")->select();
            $this->assign("zplist", $list);
        }
        //查找商品的购买数量
        $number = $order_product->field("product_number")->where($map)->find();
        $this->assign("number", $number);
        //查找商品的信息
        $pro = $product->field("id,pro_name,pro_subname,list_image")->where("id = " . $product_id)->find();
        $this->assign("product", $pro);
        //查找收货人的信息
        $addr = $order->field(shipping_addr_id)->where("id = " . $order_id)->find();
        $user_addr = M("user_address");
        $address = $user_addr->where("id = " . $addr['shipping_addr_id'])->find();
        $city = array($address['province'], $address['city'], $address['area']);
        $c = Area::city($city);
        $this->assign("city", $c);
        $this->assign("order_id", $order_id);
        $this->assign("address", $address);
        $this->display();

    }

    //可以申请退换的商品列表
    public function applyProduct(){
        //SEO
        $ident ="service";
        $idents =$this->seo($ident);
        $this->assign("title",$idents['title']);
        $this->assign("keywords",$idents['keywords']);
        $this->assign("description",$idents['description']);


        $order = M("orders");
        $order_product = M("order_products");
        $product = M("product");
        $sale =M("sale_service");
        $user_id = $_SESSION['user']['userid'];
        $map['user_id'] = array("eq", $user_id);
        $map['order_status'] = array("eq", 4);
        if ($user_id) {
            $count = $order->where($map)->count();
            $page = new \Think\MyPage($count, 6);
            $show = $page->show();
            $orderlist = $order->where($map)->order("order_date desc")->limit($page->firstRow . ',' . $page->listRows)->select();
            foreach ($orderlist as &$v) {
                $v['list'] = $order_product->where("order_id = " . $v['id'])->select();
                foreach ($v['list'] as &$vo) {
                    //取商品的信息
                    $vo['prolist'] = $product->field('pro_name,id,list_image')->where("id = " . $vo['product_id'])->find();
                    $sales =$sale->where("pro_id = ".$vo['product_id']." and order_id = ".$v['id'])->find();
                    if($sale){
                        $vo['sale'] =$sales;
                    }
                }
                //dump($v['list']);
            }
            // dump($orderlist);
        }
        $this->assign("page", $show);
        $this->assign("orderlist", $orderlist);
        $this->display();

    }

    //申请成功的页面
    public function applySuccess(){
        //SEO
        $ident ="service";
        $idents =$this->seo($ident);
        $this->assign("title",$idents['title']);
        $this->assign("keywords",$idents['keywords']);
        $this->assign("description",$idents['description']);


        if($_POST['ordernumber'] < $_POST['applyNum']){
            $this->error('提交数量有误，请检查！');
            exit;
        }
        $service = M("sale_service");
        $res=$service->where(array('pro_id'=>$_POST['pro_id'],'order_id'=>$_POST['order_id']))->find();
        if($res){
            $this->error('此订单已进行过退换货，请检查！');
            exit;
        }
        if ($_FILES['image']['name'] != "") {
            $_POST['images'] = $this->upload();
        }
        $_POST['service_sn'] = "S".date("Ymd").time();
        $_POST['user_id'] = $_SESSION['user']['userid'];
        $_POST['apply_time'] = time();
        if ($service->create()) {
            $rt = $service->add();
            if ($rt) {
                $this->display();
            }
        }

    }


    //售后服务详情页
    public function returnDetail(){
        //SEO
        $ident ="service";
        $idents =$this->seo($ident);
        $this->assign("title",$idents['title']);
        $this->assign("keywords",$idents['keywords']);
        $this->assign("description",$idents['description']);

        $user_id = $_SESSION['user']['userid'];
        $map['user_id'] = array("eq", $user_id);
        $product_id = rtrim($_GET['id'], ".html");
        $order_id = $_GET['ids'];
        $map['pro_id'] = array("eq",$product_id);
        $map['order_id'] =array("eq",$order_id);
        $product = M("product");
        $sale = M("sale_service");
        $service = M("service_details");
        $sales = $sale->where($map)->find();
        if(!$sales){
            $this->error('服务单号不存在请检查!');
            exit;
        }
        $list = $product->field("pro_name,pro_subname")->where("id = {$product_id}")->find();
        $details = $service->where("service_id = ".$sales['id'])->order("time ASC")->select();
        $this->assign("sale",$sales);
        $this->assign("list",$list);
        $this->assign("details",$details);
        $this->display();
    }


    public function upload(){
        $upload = new \Think\Upload();
        $upload->maxSize = 3145728;
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');
        $upload->rootPath = "Public/Service/";
        $upload->savePath = "";
        $info = $upload->upload();
        if (!$info) {
            $this->error($upload->getError());
        } else {                                                 // 上传成功 获取上传文件信息
            foreach ($info as $file) {
                return $file['savepath'] . $file['savename'];
            }
        }
    }
    //取消服务单
    public function cancle(){
        $id = $_POST['id'];
        $db =M("sale_service");
        $user = $_SESSION['user']['username'];
        $service = M("service_details");
        $data['service_status'] =4;
        $times = time();
        $rt = $db->where("id =".$id)->save($data);
        if($rt){
            $da['service_id'] =$id;
            $da['time'] =$times;
            $da['operator'] =$user;
            $da['message'] ="服务单已由".$user."取消";
            $result = $service->add($da);
            if($result){
                $str  = '<tr>';
                $str .= '<td><span class="ftx01">'.date("Y-m-d H:i:s",$times).'</span></td>';
                $str .= '<td><div class="al"><span class="ftx01">您的服务单已经由'.$user.'取消</span></div></td>';                           
                $str .= '<td><span class="ftx01">'.$user.'</span></td>';                           
                $str .= '</tr>';

                echo $str;
                exit;                       
            }         
        }
    }
}
