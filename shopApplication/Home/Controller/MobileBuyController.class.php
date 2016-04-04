<?php

namespace Home\Controller;
use Think\Controller;
use Think\Area;
class MobileBuyController extends MyController{
    public function __construct() {
        parent::__construct();
    }

    //购物车
    public function cart(){
        //SEO
        $ident ="cart";
        $idents =$this->seo($ident);
        $this->assign('productactive','active');
        $this->assign("title",$idents['title']);
        $this->assign("keywords",$idents['keywords']);
        $this->assign("description",$idents['description']);


        if($_SESSION['user']){
            $this->assign("users",$_SESSION['user']['username']);
        }

        $cart = isset($_COOKIE['cart']) ? unserialize($_COOKIE['cart']) : array();
        // 循环每一个商品取出详情信息
        $db = M("product");
        $list = M("products_attr");
        $_cart = array();
        foreach ($cart as $k => $v){
            $_attr = explode('-', $k);
            $_cart[$k]['id']=$_attr[0];
            $_cart[$k]['versionId'] = $_attr[1];
            $_cart[$k]['colorId'] = $_attr[2];
            $img = $db->field("list_image,pro_name,promote_price,unit,is_promote")->where(array('id'=>$_cart[$k]['id']))->find();
            $_cart[$k]['img'] = $img['list_image'];
            $_cart[$k]['pro_name'] =$img['pro_name'];
            $_cart[$k]['promote_price'] = $img['promote_price'];
            $_cart[$k]['color']=$list->field('prop_name,prop_price')->where(array('id'=>$_cart[$k]['colorId']))->find();
            $_cart[$k]['version']=$list->field('prop_name,prop_price')->where(array('id'=>$_cart[$k]['versionId']))->find();
            //取库存
            $_cart[$k]['num']=$this->getStoreNum($_cart[$k]['id'],$_cart[$k]['colorId'],$_cart[$k]['versionId']);
            $_cart[$k]['price']=$_cart[$k]['promote_price']+ $_cart[$k]['color']['prop_price']+$_cart[$k]['version']['prop_price'];
            $_cart[$k]['ordernum']=$v;
            //取促销
            if($img['is_promote']){
              $ProModel=M('promote');
              $_cart[$k]['promote']=$ProModel->where(array('product_id'=>$_cart[$k]['id']))->find();
              if($_cart[$k]['promote']['status']==1){
                 $_cart[$k]['price']= ($_cart[$k]['price'])*($promote['rate']/100);
              }
              if($_cart[$k]['promote']['status']==2){
                if($_SESSION['user']['userlevel']){
                  $MemRate=M('members_rate');
                  $Memrate=$MemRate->where(array('product_id'=>$_cart[$k]['id'],'level_id'=>$_SESSION['user']['userlevel']))->getField('rate');
                  $promote['rate']=$Memrate;
                  $_cart[$k]['price']= ($_cart[$k]['price'])*($promote['rate']/100);
                    }
              }
              if($_cart[$k]['promote']['status']==3){
                  $giftId=$ProModel->where(array('product_id'=>$_cart[$k]['id']))->getField('gifts');
                  $_cart[$k]['promote']['gift']=$db->field('list_image,id,pro_name,market_price')->where(array('id'=>$giftId))->find();
              }
              if($_cart[$k]['promote']['status']==4){
                  $gift=$ProModel->field('condition,gifts')->where(array('product_id'=>$_cart[$k]['id']))->find();
                  if($_cart[$k]['ordernum']>=$gift['condition']){
                  $_cart[$k]['promote']['gift']=$db->field('list_image,id,pro_name,market_price')->where(array('id'=>$gift['gifts']))->find();
                    }
              }
            }
        }
        // dump($_cart);
        // die;
        $this->assign("goods",$_cart);
        $this -> display();
    }
    //删除购物车的商品
    public function del(){
        $ids = $_POST['id'];
        $cart=isset($_COOKIE['cart']) ? unserialize($_COOKIE['cart']) : array();
        //从数组中先删除，再存回数组
        unset($cart[$ids]);
        $aMonth=time()+30*24*3600;
        setcookie('cart',serialize($cart),$aMonth,'/');
        echo 1;
        exit;
    }
    //修改购物车的商品的数量
    public function changeNum(){
        $ids = $_POST['id'];
        $num = $_POST['num'];
        $cart=isset($_COOKIE['cart']) ? unserialize($_COOKIE['cart']) : array();
        $cart[$ids] = (int)$num;
        //写入COOKIE，保存一个月
        $aMonth=time()+30*24*3600;
        setcookie('cart',serialize($cart),$aMonth,'/');
    }
    //订单确认
    public function collect(){
      //SEO
        $ident ="collect";
        $idents =$this->seo($ident);
        $this->assign('productactive','active');
        $this->assign("title",$idents['title']);
        $this->assign("keywords",$idents['keywords']);
        $this->assign("description",$idents['description']);



    	$buy_now=I('get.buy');
        $user_id = $_SESSION['user']['userid'];
        if($user_id ==""){
             $this->redirect("Login/login");
        }else{

          $user_confirmation = $_SESSION['user']['userconfirmation'];
        if(!$user_confirmation){
             $this->error('购买前，请先补全个人信息！',U('MobileMember/information'),3);
             exit;
        }

            //物流信息
            $shipping = M("shippings");
            $ship_company = M("shipping_company");
            $ships = $shipping ->order('is_default desc')->select();
            foreach($ships as &$v1){
            	$v1['cname'] = $ship_company->field("cname")->where("id = ".$v1['sid'])->find();
            }
            // dump($ships);
            // die;
            $this->assign("ships",$ships);
            //收货地址
            //$user_id = $_SESSION['user']['userid'];
            $user = $_SESSION['user']['username'];
            $add = M("user_address");
            $address = $add->where("user_id = ".$user_id)->order('is_default desc')->select();
        if($buy_now == 'now'){
        $cart =$_SESSION['order'];
        $this->assign('buy_now',$buy_now);
        }else{
        $cart = isset($_COOKIE['cart']) ? unserialize($_COOKIE['cart']) : array();
        }

        // 循环每一个商品取出详情信息
        $db = M("product");
        $list = M("products_attr");
        $_cart = array();
        $sum = "";
        $num="";
        foreach ($cart as $k => $v){
            $_attr = explode('-', $k);
            $_cart[$k]['id']=$_attr[0];
            $_cart[$k]['versionId'] = $_attr[1];
            $_cart[$k]['colorId'] = $_attr[2];
            $img = $db->field("list_image,pro_name,promote_price,is_ship,is_promote,unit,unit_weight,weight_unit")->where(array('id'=>$_cart[$k]['id']))->find();
            $_cart[$k]['img'] = $img['list_image'];
            $_cart[$k]['pro_name'] =$img['pro_name'];
            $_cart[$k]['is_ship'] =$img['is_ship'];
            $_cart[$k]['unit'] =$img['unit'];
            $_cart[$k]['weight_unit'] =$img['weight_unit'];
            if($_cart[$k]['is_ship']){
                $_cart[$k]['unit_weight'] = 0;
            }else{
                $_cart[$k]['unit_weight'] =$img['unit_weight'];
            }
            if($_cart[$k]['weight_unit'] =="克"){
                $weightper =$_cart[$k]['unit_weight']/1000;
            }elseif($_cart[$k]['weight_unit'] =="千克"){
                $weightper =$_cart[$k]['unit_weight'];
            }
            $_cart[$k]['promote_price'] = $img['promote_price'];
            $_cart[$k]['color']=$list->field('prop_name,prop_price')->where(array('id'=>$_cart[$k]['colorId']))->find();
            $_cart[$k]['version']=$list->field('prop_name,prop_price')->where(array('id'=>$_cart[$k]['versionId']))->find();
            //取库存
            $ModelNum=M('productnum');
            $attrls=array();
            $attrls=array($_cart[$k]['colorId'],$_cart[$k]['versionId']);
            sort($attrls);
            $attrls=implode('|',$attrls);
            $map['product_id']=$_cart[$k]['id'];
            $map['product_attr']=$attrls;
            $_cart[$k]['num']=$ModelNum->where($map)->getfield('product_number');
            $_cart[$k]['price']=$_cart[$k]['promote_price']+ $_cart[$k]['color']['prop_price']+$_cart[$k]['version']['prop_price'];
            $_cart[$k]['ordernum']=$v;
            //取出促销信息
            if($img['is_promote']){
              $ProModel=M('promote');
              $_cart[$k]['promote']=$ProModel->where(array('product_id'=>$_cart[$k]['id']))->find();
              if($_cart[$k]['promote']['status']==1){
                 $_cart[$k]['price']= ($_cart[$k]['price'])*($promote['rate']/100);
              }
              if($_cart[$k]['promote']['status']==2){
                if($_SESSION['user']['userlevel']){
                  $MemRate=M('members_rate');
                  $Memrate=$MemRate->where(array('product_id'=>$_cart[$k]['id'],'level_id'=>$_SESSION['user']['userlevel']))->getField('rate');
                  $promote['rate']=$Memrate;
                  $_cart[$k]['price']= ($_cart[$k]['price'])*($promote['rate']/100);
                    }
              }
              if($_cart[$k]['promote']['status']==3){
                  $giftId=$ProModel->where(array('product_id'=>$_cart[$k]['id']))->getField('gifts');
                  $_cart[$k]['promote']['gift']=$db->field('list_image,id,pro_name,market_price')->where(array('id'=>$giftId))->find();
              }
              if($_cart[$k]['promote']['status']==4){
                  $gift=$ProModel->field('condition,gifts')->where(array('product_id'=>$_cart[$k]['id']))->find();
                  if($_cart[$k]['ordernum']>=$gift['condition']){
                  $_cart[$k]['promote']['gift']=$db->field('list_image,id,pro_name,market_price')->where(array('id'=>$gift['gifts']))->find();
                    }
              }
              if($_cart[$k]['promote']['status']==5){
                  $qiang=$ProModel->field('id,status,start_time,end_time,rate')->where(array('product_id'=>$_cart[$k]['id']))->find();
                  $now=time();
                  if($qiang['start_time'] <=$now and $qiang['end_time']>$now){
                    $_cart[$k]['price']=($_cart[$k]['price'])*($qiang['rate']/100);
                  }
              }
            }


            $sum +=  $_cart[$k]['price']*$_cart[$k]['ordernum'];
            $num += $_cart[$k]['ordernum'];
            $weight += $weightper*$_cart[$k]['ordernum'];
        }
            // dump($_cart);
            // die;
            $this->assign('weight',$weight);
            $this->assign("num",$num);
            $this->assign("sum",$sum);
            $this->assign("goods",$_cart);
            $this->assign("address",$address);


            //三级联动
            $city = array("--请选择省--", "--市--", "--区县--");
            $c = Area::city($city);
            // dump($c);
            // die;
            $this->assign("city", $c);
            $this->display();
         }
    }

    //订单去支付
    public function pay(){
      //SEO
        $ident ="pay";
        $idents =$this->seo($ident);
        $this->assign('productactive','active');
        $this->assign("title",$idents['title']);
        $this->assign("keywords",$idents['keywords']);
        $this->assign("description",$idents['description']);



        $buy_now=I('post.buy_now');
        $data=I('post.');
        // dump($data);
        // die;
        if($buy_now == 'now'){
        $cart =$_SESSION['order'];
        $this->assign('buy_now',$buy_now);
        }else{
        $cart = isset($_COOKIE['cart']) ? unserialize($_COOKIE['cart']) : array();
        }
        if(!$cart){
            $this->error('订单失效，请前往个人中心查看！',U('Mobileindex/mobileindex'));
            exit;
        }
        $_POST['user_id'] = $_SESSION['user']['userid'];
        $user = $_SESSION['user']['username'];
        $addr  = M("user_address");
        if($_POST['shipping_addr_id'] ==0){
          $data2['user_id']=$_POST['user_id'];
          $data2['reciver_user']=$_POST['reciver_user'];
          $data2['province']=$_POST['province'];
          $data2['city']=$_POST['city'];
          $data2['area']=$_POST['area'];
          $data2['street']=$_POST['street'];
          $data2['zipcode']=$_POST['zipcode'];
          $data2['reciver_phone']=$_POST['reciver_phone'];
          $useraddid=$addr->add($data2);
          $_POST['shipping_addr_id']=$useraddid;
        }else{
          $useraddid = $addr->where(array('user_id'=>$_POST['user_id'],'id'=>$_POST['shipping_addr_id']))->find();
            }

        if(!$useraddid){
            $this->error('收货地址不存在，请检查！');
            exit;
        }

        if(!in_array($_POST['pay_type'],array(0,1))){
            $this->error('支付方式错误，请检查！');
            exit;
        }
        $commentnum=mb_strlen($_POST['comment']);
        if($commentnum>50){
            $this->error('备注超出字数限定，请检查！');
            exit;
        }
        $ship_way=M('shippings')->field('id')->select();
        $shipping_way=array();
        foreach($ship_way as $k=>$v){
            $shipping_way[$k]=$v['id'];
        }
        if(!in_array($_POST['shipping_way'],$shipping_way)){
            $this->error('物流方式有误，请检查！');
            exit;
        }
        
        $_POST['order_sn']     = date("YmdHis").mt_rand(1,1000);
        $_POST['order_date']   = time();
        $_POST['order_status'] = 1;
        $_POST['pay_status']   = 0;
        $db = M("orders");
        $order = M("order_products");
        $product = M("product");
        $list=M('products_attr');

        foreach ($cart as $k => $v){
            $_attr = explode('-', $k);
            $_cart[$k]['id']=$_attr[0];
            $_cart[$k]['versionId'] = $_attr[1];
            $_cart[$k]['colorId'] = $_attr[2];
            $_cart[$k]['ordernum']=$v;
            $storenum=$this->getStoreNum($_cart[$k]['id'],$_cart[$k]['colorId'],$_cart[$k]['versionId']);
            if($_cart[$k]['ordernum'] > $storenum){
                $this->error('不好意思,可能您下手晚了，你购买的商品库存不够!');
                exit;
            }
            $qiangstatus=$product->where(array('id'=>$_cart[$k]['id']))->getField('is_promote');
            if($qiangstatus){
                $qiangtime=M('promote')->where(array('product_id'=>$_cart[$k]['id'],'status'=>5))->getField('end_time');
                if($qiangtime){
                $now=time();
                if($qiangtime<$now){
                $this->error('不好意思,可能您下手晚了，抢购已经结束！');
                exit;
                }
               }
            }


        }
        $fp=fopen('./shoporder.lock','r');
        flock($fp,LOCK_EX);
        if($db->create()){
            $rt = $db->add();
            if($rt){
            foreach ($cart as $k => $v){
            $data=array();
            $_attr =   explode('-', $k);
            $data['product_id']=$_attr[0];
            $data['versionId'] = $_attr[1];
            $data['colorId'] = $_attr[2];
            $data['ordernum']=$v;
            $img = $product->field("pro_name,promote_price,is_ship,is_promote,unit_weight,weight_unit")->where(array('id'=>$data['product_id']))->find();
            $data['pro_name'] =$img['pro_name'];
            $data['is_ship'] =$img['is_ship'];
            $data['weight_unit'] =$img['weight_unit'];
            if($data['is_ship']){
                $_cart[$k]['unit_weight'] = 0;
            }else{
                $_cart[$k]['unit_weight'] =$img['unit_weight'];
            }
            if($data['weight_unit'] =="克"){
                $weightper =$data['unit_weight']/1000;
            }elseif($_cart[$k]['weight_unit'] =="千克"){
                $weightper =$data['unit_weight'];
            }
            $data['promote_price'] = $img['promote_price'];
            $data['color']=$list->field('prop_name,prop_price')->where(array('id'=>$_cart[$k]['colorId']))->find();
            $data['version']=$list->field('prop_name,prop_price')->where(array('id'=>$_cart[$k]['versionId']))->find();
            $data['price']=$data['promote_price']+ $data['color']['prop_price']+$data['version']['prop_price'];
            $data['ordernum']=$v;
            //取出促销信息
            if($img['is_promote']){
              $ProModel=M('promote');
              $promote=$ProModel->where(array('product_id'=>$data['product_id']))->find();
              if($promote['status']==1){
                 $data['price']= ($data['price'])*($promote['rate']/100);
                 $data['promote_status']=1;
              }
              if($promote['status']==2){
                if($_SESSION['user']['userlevel']){
                  $MemRate=M('members_rate');
                  $Memrate=$MemRate->where(array('product_id'=>$data['product_id'],'level_id'=>$_SESSION['user']['userlevel']))->getField('rate');
                  $promote['rate']=$Memrate;
                  $data['price']= ($data['price'])*($promote['rate']/100);
                  $data['promote_status']=2;
                    }
              }
              if($promote['status']['status']==3){
                  $data['gift_id']=$ProModel->where(array('product_id'=>$data['product_id']))->getField('gifts');
                  $data['promote_status']=3;
              }
              if($promote['status']['status']==4){
                  $gift=$ProModel->field('condition,gifts')->where(array('product_id'=>$data['product_id']))->find();
                  if($data['ordernum']>=$gift['condition']){
                  $data['gift_id']=$gift['gifts'];
                  $data['promote_status']=4;
                    }
              }
              if($promote['status']['status']==5){
                  $qiang=$ProModel->field('id,status,start_time,end_time,rate')->where(array('product_id'=>$data['product_id']))->find();
                  $now=time();
                  if($qiang['start_time'] <=$now and $qiang['end_time']>$now){
                    $data['price']=($data['price'])*($qiang['rate']/100);
                    $data['promote_status']=5;
                  }
              }
            }

            //存入订单表
            $data['order_id'] = $rt;
            $data['product_price'] = $data['price'];
            $data['product_number'] =$data['ordernum'];
            $data['product_color'] =$data['color']['prop_name'].'|'.$data['version']['prop_name'];
            $res=$order->add($data);
            if($res){
            $this->reduceStoreNum($data['product_id'],$data['colorId'],$data['versionId'],$data['ordernum']);
            if($data['gift_id']){
            $this->reduceGift($data['gift_id']);
                }
            }else{
            $this->error('不好意思,下单失败!');
            exit;
            }
        }
      }
    }
    flock($fp,LOCK_UN);
    fclose('./shoporder.lock');

        if ($rt){
            $name=array();
            foreach($cart as $k=>$v){
                $_attr = explode('-', $k);
                $_cart[$k]['id']=$_attr[0];
                $name[]= $product ->where(array('id'=>$_cart[$k]['id']))->getField('pro_name');
            }
            unset($_SESSION['order']);
            setcookie('cart','',1,'/');
             //dump($_POST['shipping_addr_id']);
            $id = $_POST['shipping_addr_id'];
            $addrs = $addr->where("id = ".$id)->find();

            // 获取支付信息
            // -------------------------------------
            $modelConfig = M('sys_config');
            $cRes = $modelConfig->where("cname LIKE 'paypal_%'")->select();
            $cRes = $this->_arr2ToArr1($cRes);
            // -------------------------------------
            // var_dump($cRes);
            // exit;

            $address = $addrs['province'].$addrs['city'].$addrs['area'].$addrs['street'];
            $this->assign('cRes', $cRes);
            $this->assign("address",$address);
            $this->assign("name",$name);
            $this->assign("order_sn",$_POST['order_sn']);
            $this->assign('orderId', $rt);
            $this->assign("real_price",$_POST['real_price']);
            $this -> display();
        }
        else
        {
            $this->error('订单生成失败');
        }

    }

    protected function _arr2ToArr1 ($arr2)
    {
        $array = array();
        foreach ($arr2 as $key=>$val)
        {
            $k = $val['cname'];
            $array[$k] = $val['cvalue'];
        }
        return $array;
    }

    //添加地址
    public function addressAdd()
    {
        $db = M('user_address');

        $user_id = $_SESSION['user']['userid'];
        $_POST['user_id'] = $user_id;


        if ($_POST['is_default'] == 1) {
            $data['is_default'] = 0;
            $db->where("user_id = $user_id")->save($data);
        }

        $row = $db->where("user_id = $user_id")->add($_POST);
        echo $row;


    }

    //删除地址
    public function addressDelete(){
        $user_id = $_SESSION['user']['userid'];
        $is_default=I('get.is_default');
        $id=I('get.id');
        $db = M('user_address');
        if ($is_default){
            $data['is_default'] = 1;
            $req=$db->where(array('id'=>$id))->delete();
            $ids = $db->where("user_id = $user_id")->order('`id` asc ')->getField('id');
            $row = $db->where("id = $ids")->save($data);
            if($req){
            $this->ajaxReturn(1);
            }else{
            $this->ajaxReturn(0);
            }
        } else {
            $req = $db->where(array('id'=>$id))->delete();
            if($req){
            $this->ajaxReturn(1);
            }else{
            $this->ajaxReturn(0);
            }
        }
    }

    public function addressUpdate(){
        $db = M('user_address');
        $id = $_POST['id'];
        unset($_POST['id']);
        $row = $db -> where("id = $id") -> save($_POST);
        echo $row;
    }

    public function shipfee(){
        $weight=I('post.weight');
        if($weight == 0){
            $this->ajaxReturn(0);
            exit;
        }
        $ship=I('post.ship');
        $shipping = M("shippings");
        $ship_company = M("shipping_company");
        $shipname=$shipping->alias('a')
                 ->join('LEFT JOIN im_shipping_company b ON a.sid =b.id')
                 ->where(array('a.sid'=>$ship))
                 ->getField('b.cname');
        switch ($shipname)
            {
                case '圆通快递':
                    $shipfee = 10 + ($this->checkweight()*2);
                    break;
                case '申通快递':
                    $shipfee = 10 + ($this->checkweight()*2);
                    break;
        }
        $this->ajaxReturn($shipfee);
    }

    private function checkweight($weight){
        if($weight < 1){
            return 0;
        }else{
            return ceil($weight) - 1;
        }
    }

    private function getStoreNum($id,$colorId,$versionId){
            $ModelNum=M('productnum');
            $attrls=array();
            $attrls=array($colorId,$versionId);
            sort($attrls);
            $attrls=implode('|',$attrls);
            $map['product_id']=$id;
            $map['product_attr']=$attrls;
            $num=$ModelNum->where($map)->getfield('product_number');
            return $num;
        }

    private function reduceStoreNum($id,$colorId,$versionId,$ordernum){
            $ModelNum=M('productnum');
            $attrls=array();
            $attrls=array($colorId,$versionId);
            sort($attrls);
            $attrls=implode('|',$attrls);
            $map['product_id']=$id;
            $map['product_attr']=$attrls;
            $res = $ModelNum->where($map)->setDec('product_number',$ordernum);
            return $res;
        }

    private function reduceGift($id){
            $ModelNum=M('productnum');
            $map['product_id']=$id;
            $res = $ModelNum->where($map)->setDec('product_number',1);
            return $res;
    }
}
