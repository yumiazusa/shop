<?php

namespace Admin\Controller;

use Think\Controller;

class OrderInfoController extends MyController
{

    public function __construct()
    {
        parent::__construct();
    }

    //首页显示
    function index()
    {


        $db = M('orders');
        $users = M('users');

        // 订单号查询
        if (isset($_GET['sn_sub'])) {
            $sn = '%' . $_GET['order_sn'] . '%';
            $count = $db->where("order_sn LIKE '$sn'")->count();
            $page = new \Think\Page($count, 10);
            $showPage = $page->show();
            $res = $db->where("order_sn LIKE '$sn'")->order('id ASC')->limit($page->firstRow . ',' . $page->listRows)->select();

            //物流单号查询
        } else if (isset($_GET['num_sub'])) {
            $num = '%' . $_GET['order_num'] . '%';
            $count = $db->where("shipping_num LIKE '$num'")->count();
            $page = new \Think\Page($count, 10);
            $showPage = $page->show();
            $res = $db->where("shipping_num LIKE '$num'")->order('id ASC')->limit($page->firstRow . ',' . $page->listRows)->select();

            //按订单状态查询
        } else if ((isset($_GET['status_sub'])) && (($_GET['order_status']) != 999)) {
            $status = $_GET['order_status'];
            $count = $db->where("order_status = '$status'")->count();
            $page = new \Think\Page($count, 10);
            $showPage = $page->show();
            $res = $db->where("order_status  = '$status'")->order('id ASC')->limit($page->firstRow . ',' . $page->listRows)->select();

        } //查询全部
        else {
            $count = $db->count();    //获取总页数
            $page = new \Think\Page($count, 10);
            $showPage = $page->show();
            $res = $db->order('id ASC')->limit($page->firstRow . ',' . $page->listRows)->select();

            $date = Date(time());

            //如果订单状态是未支付 并且 时间超过2小时 并且不是货到付款 , 则取消订单
            foreach ($res as $val) {

                $da = '';

                if (($val['order_status'] == 1) && (($val['pay_status']) == 1) && ($val['order_date'] + 7200 < $date)) {
                    $ids = $val['id'];
                    $da['order_status'] = 0;
                    $db->where("id = $ids")->save($da);    //存档
                }
            }

            //再次查询
            $res = $db->order('id ASC')->limit($page->firstRow . ',' . $page->listRows)->select();
            foreach($res as &$vo){
                $userId = $vo['user_id'];
                $u = $users -> where("id = $userId") -> find();
                $vo['uname'] = $u['uname'];
            }

        }

        $dc = M('shippings');
        $dd = M('shipping_company');
        $shipping = $dc->field('sid')->select();
        foreach ($shipping as &$vo) {
            $company[] = $dd->find($vo['sid']);
        }


        $this->assign('company', $company);
        // dump($res);
        // die;
        $this->assign('data', $res);
        $this->assign('page', $showPage);

        $this->display();

    }

    //获得订单的收货地址
    public function getUserAddress()
    {
        $getAddId = I('post.id');
        $db = M('user_address');
        $Address = $db->where("id = '{$getAddId}'")->find();
        $this->assign('addr', $Address);

        $this->ajaxReturn($Address);
    }


    //订单信息
    public function OrderInfo()
    {

        $id = $_GET['id'];
        $price = $_GET['price'];
        $user_id = $_GET['addid'];

        //1 通过$id 获取购物信息
        //2 通过购物信息的物品id 获取物品的信息

        $order_products = M('order_products');
        $address = M('user_address');
        $products = M('product');
        $order = M('orders');
        $orders = $order->where("id = $id")->find();
        //查询买家的地址信息
        $dress = $address->where("id = $user_id")->find();
        //查询买的商品列表
        $order_prodects_list = $order_products->where("order_id = $id")->select();
        //查询买的商品详情

        $worth = 0;   //商品总价值
        foreach ($order_prodects_list as &$or) {

            $row = $products->find($or['product_id']);
            foreach ($row as $key => $value) {
                $or[$key] = $value;

            }
            $worth += $or['product_price']*$or['product_number'];
            if($or['gift_id']){
                $or['gift']=$products->where(array('id'=>$or['gift_id']))->getField('pro_name');
            }
        }
        // dump($order_prodects_list);
        // die;
$worth = sprintf("%01.2f", $worth);
        $this -> assign('worth',$worth);
        $this->assign('address', $dress);
        $this->assign('price', $price);
        $this->assign('data', $order_prodects_list);
        $this->assign('order', $orders);
        $this->display();
    }

    //添加物流信息
    public function shippingAdd()
    {
        $admin_id = $_SESSION['id'];
        $db = M('orders');
        $row = $db->save($_POST);
        if ($row > 0) {
            echo $row;

            // 写入日志
            $log = new \Admin\Controller\LogController();
            $log->setLog('用户（UID：' . $admin_id . '）对订单id为' . $_POST['id'] . '的订单增加了' . $_POST['shipping_way'] . '物流' . $_POST['shipping_num']);

        }
    }

    //获得物流信息
    public function shippingGet()
    {
        $db = M('orders');
        $row = $db->where($_POST)->find();
        $this->ajaxReturn($row);
    }

    //取消订单
    public function OrderCancel()
    {


        $admin_id = $_SESSION['id'];    //当前登陆的管理员
        $order_products = M('order_products');
        $products = M('products');
        $db = M('orders');
        $id = $_POST['id'];    //要操作的订单id
        //1.如果已经支付成功 且没发货 则将钱返还,物品入库,后将订单状态设为已取消0
        $row = $db->where("id = $id")->find();

        if (!empty($row)) {  //数据库查询到订单信息

            //查询买的商品id和数量
            $order_prodects_list = $order_products->where("order_id = $id")->select();
            if ($row['order_status'] == 0) {    //订单是已经 取消状态
                echo '订单已经是取消状态!';
            } else if ($row['order_status'] == 5) {    //订单是 完成状态
                echo '订单已经完成!无法取消';
            }

            else if ($row['order_status'] == 2) {    //查询订单状态是 未发货 状态
                if ($row['pay_status'] == 0) {    //如果是未付款的订单

                    //入库
                    foreach ($order_prodects_list as $or) {    //查询订单下所有的商品id和数量
                        $productsId = $or['product_id'];
                        $productsNum = $or['product_number'];
                        $products->where("id = $productsId")->setInc('stock_num', $productsNum);    //入库

                    }

                    $data['order_status'] = 0;
                    $db->where("id = $id")->save($data);    //订单状态设为取消状态

                } else if ($row['pay_status'] == 1) {    //如果是支付的订单
                    $pay = $row['deail_price'];    //获得所消费的金额
                    //返还的方法  debug 未实现

                    //入库
                    foreach ($order_prodects_list as $or) {    //查询订单下所有的商品id和数量
                        $productsId = $or['product_id'];
                        $productsNum = $or['product_number'];
                        $result = $products->where("id = $productsId")->setInc('stock_num', $productsNum);    //入库
                    }
                    if ($result > 0) {

                        $data['order_status'] = 0;
                        $closeStatus = $db->where("id = $id")->save($data);    //订单状态设为取消状态
                        if ($closeStatus > 0) {
                            echo '订单取消成功!';
                            // 写入日志
                            $log = new \Admin\Controller\LogController();
                            $log->setLog('用户（UID：' . $admin_id . '）取消了id为' . $_POST['id'] . '的订单增');
                        } else {
                            echo '订单取消失败!';
                        }

                    } else {
                        echo '订单取消失败!';
                    }

                }else{
                    echo '订单取消失败!';
                }

            } else if ($row['order_status'] == 1) {    //未支付
                //入库
                foreach ($order_prodects_list as $or) {    //查询订单下所有的商品id和数量
                    $productsId = $or['product_id'];
                    $productsNum = $or['product_number'];
                    $result = $products->where("id = $productsId")->setInc('stock_num', $productsNum);    //入库
                }
                if ($result > 0) {

                    $data['order_status'] = 0;
                    $closeStatus = $db->where("id = $id")->save($data);    //订单状态设为取消状态
                    if ($closeStatus > 0) {
                        echo '订单取消成功!';
                        // 写入日志
                        $log = new \Admin\Controller\LogController();
                        $log->setLog('用户（UID：' . $admin_id . '）取消了id为' . $_POST['id'] . '的订单增');
                    } else {
                        echo '订单取消失败!';
                    }

                } else {
                    echo '订单取消失败!';
                }


            } else if ($row['order_status'] == 3) {    //已发货
                echo '订单已发货,无法取消!';
            }
        }



    }

    //无效订单列表
    public function invalidOrder(){
        $db = M('orders');
        $users = M('users');
        $count = $db->where("order_status = 0")->count();    //获取总页数
        $page = new \Think\Page($count, 10);
        $showPage = $page->show();
        $res = $db->where("order_status = 0")->order('id ASC')->limit($page->firstRow . ',' . $page->listRows)->select();
        foreach($res as &$vo){
            $userId = $vo['user_id'];
            $u = $users -> where("id = $userId") -> find();
            $vo['uname'] = $u['uname'];
        }
        $this->assign('company', $company);

        $this->assign('data', $res);
        $this->assign('page', $showPage);

        $this->display();

    }




}
