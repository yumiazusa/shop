<?php
/*
 * 会员中心订单管理
 */

namespace Home\Controller;
use  Think\Controller;
class OrderController extends MyController{
     public function __construct() {
        parent::__construct();
    }

    //未完成的订单(待付款|取消订单 0,1)
    public function untreatedOrders(){
        //SEO
        $ident ="order";
        $idents =$this->seo($ident);
        $this->assign("title",$idents['title']);
        $this->assign("keywords",$idents['keywords']);
        $this->assign("description",$idents['description']);



        $db = M("orders");
        $order = M("order_products");
        $order_detail = M('products_attr');
        $product = M("product");
        $user_id = $_SESSION['user']['userid'];
        $map['order_status'] = array("in", "0,1");
        $map['user_id'] = array("eq", $user_id);
        $count = $db->where($map)->count();
        $page = new \Think\MyPage($count, 3);
        $show = $page->show();
        $orderlist = $db->where($map)->order("order_date desc")->limit($page->firstRow . ',' . $page->listRows)->select();
        foreach ($orderlist as &$v) {
            $v['list'] = $order->where("order_id = " . $v['id'])->select();
            foreach ($v['list'] as &$vo) {
                //取商品的信息
                $vo['prolist'] = $product->field('pro_name,id,list_image')->where("id = " . $vo['product_id'])->find();
                //取赠品的信息
                if($vo['gift_id']){
                $vo['prolist']['gift'] = $product->field('pro_name,id,list_image')->where('id = ' . $vo['gift_id'] )->find();
                }
            }
        }
        // dump($orderlist);
        // die;
        $this->assign("page", $show);
        $this->assign("orderlist", $orderlist);
        $this->display();
    }

    //处理中的订单(已发货 等待发货 2,3)
    public function processingOrder()
    {
        //SEO
        $ident ="order";
        $idents =$this->seo($ident);
        $this->assign("title",$idents['title']);
        $this->assign("keywords",$idents['keywords']);
        $this->assign("description",$idents['description']);


        $db = M("orders");
        $order = M("order_products");
        $order_detail = M('products_attr');
        $product = M("product");
        $user_id = $_SESSION['user']['userid'];
        $map['order_status'] = array("in", "2,3");
        $map['user_id'] = array("eq", $user_id);
        $count = $db->where($map)->count();
        $page = new \Think\MyPage($count, 3);
        $show = $page->show();
        $orderlist = $db->where($map)->order("order_date desc")->limit($page->firstRow . ',' . $page->listRows)->select();
        foreach ($orderlist as &$v) {
            $v['list'] = $order->where("order_id = " . $v['id'])->select();
            foreach ($v['list'] as &$vo) {
                //取商品的信息
                $vo['prolist'] = $product->field('pro_name,id,list_image')->where("id = " . $vo['product_id'])->find();
                //取赠品的信息
                if($vo['gift_id']){
                $vo['prolist']['gift'] = $product->field('pro_name,id,list_image')->where('id = ' . $vo['gift_id'] )->find();
                }
            }
            //dump($v['list']);

        }
        // dump($orderlist);
        // die;
        $this->assign("page", $show);
        $this->assign("orderlist", $orderlist);
        $this->display();
    }

    //完成的订单
    public function completeOrder()
    {
        //SEO
        $ident ="order";
        $idents =$this->seo($ident);
        $this->assign("title",$idents['title']);
        $this->assign("keywords",$idents['keywords']);
        $this->assign("description",$idents['description']);


        $db = M("orders");
        $order = M("order_products");
        $order_detail = M('products_attr');
        $product = M("product");
        $user_id = $_SESSION['user']['userid'];
        $map['order_status'] = array("eq", "4");
        $map['user_id'] = array("eq", $user_id);
        $count = $db->where($map)->count();
        $page = new \Think\MyPage($count, 3);
        $show = $page->show();
        $orderlist = $db->where($map)->order("order_date desc")->limit($page->firstRow . ',' . $page->listRows)->select();
        foreach ($orderlist as &$v) {
            $v['list'] = $order->where("order_id = " . $v['id'])->select();
            foreach ($v['list'] as &$vo) {
                //取商品的信息
                $vo['prolist'] = $product->field('pro_name,id,list_image')->where("id = " . $vo['product_id'])->find();
                //取赠品的信息
               if($vo['gift_id']){
                $vo['prolist']['gift'] = $product->field('pro_name,id,list_image')->where('id = ' . $vo['gift_id'] )->find();
                }
            }
            //dump($v['list']);

        }
        // dump($orderlist);
        $this->assign("page", $show);
        $this->assign("orderlist", $orderlist);
        $this->display();
    }

    //查看订单详情
    public function viewOrder()
    {
        //SEO
        $ident ="order";
        $idents =$this->seo($ident);
        $this->assign("title",$idents['title']);
        $this->assign("keywords",$idents['keywords']);
        $this->assign("description",$idents['description']);
        $id = $_GET['id'];
        $db = M("orders");
        $order = M("order_products");
        $order_detail = M('products_attr');

        $product = M("product");
        $orderlist = $db->where("id = " . $id)->find();
        //echo $db->getlastSql();
        //dump($orderlist);
        $orderlist['order_status'] = $this->status($orderlist['order_status']);
        //echo $orderlist['order_status'];
        //exit;
        $orderlist['pay_type'] = $this->pay_type($orderlist['pay_type']);
        $orderlist['address'] = $this->address($orderlist['user_id'], $orderlist['shipping_addr_id']);
        $list = $order->where("order_id = " . $id)->select();
        foreach ($list as &$vo) {
            //取商品的信息
            $vo['prolist'] = $product->field('pro_name,id,list_image')->where("id = " . $vo['product_id'])->find();
            //取赠品的信息
            $prop = $order_detail->field("prop_value")->where('pro_id = ' . $vo['product_id'] . ' and  prop_id = 3')->find();
            //echo $order_detail->getlastSql();
            if ($prop['prop_value']) {
                $vo['prolist']['zp'] = $product->where("id in (" . $prop['prop_value'] . ")")->select();

            }

        }
        // dump($list);
        // dump($orderlist);
        // die;
        $this->assign("orderlist", $orderlist);
        $this->assign("list", $list);
        $this->display();
    }


    //取消订单
    public function cancel()
    {
        $db = M("orders");
        $id = $_POST['id'];
        $data['order_status'] = 0;
        $rt = $db->where("id = " . $id)->save($data);
        if ($rt) {
            echo 1;
            exit;
        } else {
            echo 2;
            exit;
        }
    }

    //订单状态
    public function status($status)
    {

        if ($status == 0) {
            $order_status = "已取消";
        } else if ($status == 1) {
            $order_status = "等待付款";
        } else if ($status == 2) {
            $order_status = "等待发货";

        } else if ($status == 3) {
            $order_status = "已发货";
        } else if ($status == 4) {
            $order_status = "已完成";
        }
        //echo $order_status;
        return $order_status;
    }

    //发货方式
    public function pay_type($pay)
    {
        if ($pay == 0) {
            $pay_type = "在线支付";
        } else {
            $pay_type = "货到付款";
        }
        return $pay_type;
    }

    //订单的收货地址
    public function address($user_id, $addr_id)
    {
        $address = M("user_address");
        $map['user_id'] = array("eq", $user_id);
        $map['id'] = array("eq", $addr_id);
        $add = $address->where($map)->find();
        $addr = $add['province'] . $add['city'] . $add['area'] . $add['street'] . "(" . $add['reciver_user'] . "收)" . $add['reciver_phone'];
        return $addr;
    }

    public function checkShipping()
    {
        $getShippingId = I('post.order_id');

        $modelOrder = M('orders');
        $orderRes = $modelOrder->field('shipping_way, shipping_num')->where("id='{$getShippingId}'")->find();
        $shippingWay = $orderRes['shipping_way'];

        if (empty ($shippingWay) || empty ($orderRes['shipping_num'])) {
            die(0);
        }

        // 查找物流码
        $modelShippingCompany = M('shipping_company');
        $comInfo = $modelShippingCompany->field('com')->where("id='{$shippingWay}'")->find();


        // 查找配置参数
        $modelConfig = M('sys_config');
        $shippingRes = $modelConfig->where("cname LIKE 'shipping_%'")->select();
        $sConfig = $this->_arr2ToArr1($shippingRes);

        // 获取请求数据所需要的参数
        $id = $sConfig['shipping_id'];          //API KEY
        $secret = $sConfig['shipping_secret'];      //36e82eb79043f8d450d75f6ff45ed11a;
        $com = $comInfo['com'];                  //快递公司
        $nu = $orderRes['shipping_num'];        //快递单号
        $type = $sConfig['shipping_type'];
        $encode = $sConfig['shipping_encode'];
        $ord = $sConfig['shipping_ord'];

        // 请求远程数据
        $url = "http://api.ickd.cn/?id={$id}&secret={$secret}&com={$com}&nu={$nu}&encode={$encode}&type={$type}";

        $content = file_get_contents($url);
        $this->ajaxReturn($content);
    }

    //将二维数组转为一维数组
    protected function _arr2ToArr1($arr2)
    {
        $array = array();
        foreach ($arr2 as $key => $val) {
            $k = $val['cname'];
            $array[$k] = $val['cvalue'];
        }
        return $array;
    }

    public function change()
    {
        $id = $_POST['id'];
        $data['order_status'] = 4;
        $orders = M("orders");
        $orders->where("id =" . $id)->save($data);
        if ($orders) {
            echo 1;
            exit;
        } else {
            echo 2;
            exit;
        }
    }

}
