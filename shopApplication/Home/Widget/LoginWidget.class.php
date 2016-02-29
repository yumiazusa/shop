<?php

namespace   Home\Widget;
use         Think\Controller;

class LoginWidget extends Controller 
{
    public function topLogin ()
    {
        $userSession = session('?user');

        if ( ! $userSession)
        {
            $this->display('Public:topLogin');
        }
        else
        {
            $modelUser = M();
            $userId = $_SESSION['user']['userid'];
            $userInfo = $modelUser->table("__USERS__ as u, __USER_INFO__ as i")
                                  ->field('i.thumb')
                                  ->where("u.id = i.user_id AND u.id = '{$userId}'")
                                  ->find();
            $this->assign('thumb', $userInfo['thumb']);
            $this->display('Public:topAfterLogin');
        }
    }

   public function cart(){
            // if($_SESSION['gws'] !=""){
            //     $goods = $_SESSION['gws'];
            //     $db = M("products");
            //     $list = M("product_attr");
            //      $sum = "";
            //     foreach($goods as &$v){
            //         $img = $db->field("list_image,pro_name")->where("id = ".$v['id'])->find();
            //         $v['img'] = $img['list_image'];
            //         $v['pro_name'] =$img['pro_name'];
            //         $zp  = $list->field("prop_value")->where("pro_id = ".$v['id']." and prop_id = 3")->find();
            //         if($zp){
            //            $zplist = $db ->where("id in (".$zp['prop_value'].")")->select();
            //            $v['zplist'] = $zplist;
            //         }
            //         $sum +=$v['price']*$v['num'];
            //         $num +=$v['num'];
            //     }
            //     $this->assign("num",$num);
            //     $this->assign("sum",$sum);
            //     $this->assign("goods",$goods);
            // }
             //dump($goods);
            // exit;
        $cart = isset($_COOKIE['cart']) ? unserialize($_COOKIE['cart']) : array();
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
            $img = $db->field("list_image,pro_name,promote_price,unit,is_promote")->where(array('id'=>$_cart[$k]['id']))->find();
            $_cart[$k]['img'] = $img['list_image'];
            $_cart[$k]['pro_name'] =$img['pro_name'];
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
              $promote=$ProModel->where(array('product_id'=>$_cart[$k]['id']))->find();
              if($promote['status']==1){
                 $_cart[$k]['price']= ($_cart[$k]['price'])*($promote['rate']/100);
              }
              if($promote['status']==2){
                if($_SESSION['user']['userlevel']){
                  $MemRate=M('members_rate');
                  $Memrate=$MemRate->where(array('product_id'=>$_cart[$k]['id'],'level_id'=>$_SESSION['user']['userlevel']))->getField('rate');
                  $promote['rate']=$Memrate;
                  $_cart[$k]['price']= ($_cart[$k]['price'])*($promote['rate']/100);
                    }
              }
            }




            $sum +=  $_cart[$k]['price']*$_cart[$k]['ordernum'];
            $num += $_cart[$k]['ordernum'];
        }
            $this->assign("num",$num);
            $this->assign("sum",$sum);
            $this->assign("goods",$_cart);
            $this->display('Public:cart');
        }

     public function img(){
         $user_id =$_SESSION['user']['userid'];
         $user = M("user_info");
         $thumb = $user->field("thumb")->where("user_id = ".$user_id)->find();
         $username =$_SESSION['user']['username'];
         $this->assign("username",$username);
         $this->assign("thumb",$thumb);
         $this->display('Public:img');
     }


     public function topmobileLogin ()
    {
        $userSession = session('?user');

        if ( ! $userSession)
        {
            $this->display('Public:topmobileLogin');
        }
        else
        {
            $this->display('Public:topmobileAfterLogin');
        }
    }
}
