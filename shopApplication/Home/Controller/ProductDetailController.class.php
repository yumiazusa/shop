<?php
   namespace Home\Controller;
   use  Think\Controller;
   header("content-type:text/html;charset=utf8");
  date_default_timezone_set("PRC");

   class ProductDetailController extends MyController{
   	   public function __construct(){
   	   	   parent::__construct();
   	   }
   	    public function detail(){
            //SEO
        $ident ="productDetail";
        $idents =$this->seo($ident);
        $this->assign('productactive','active');
        $this->assign("title",$idents['title']);
        $this->assign("keywords",$idents['keywords']);
        $this->assign("description",$idents['description']);


            $id =$_GET['id'];
            if($_SESSION['user']){
                $this->assign("users",$_SESSION['user']['username']);
             }
            $goodsData =array();

            $this->comment();

            //默认产品的颜色
            $list =M("products_attr");
            $da['pro_id'] =array('eq',$id);
            $da['prop_id'] =array('eq',1);
            $goodscolor = $list->where($da)->find();

            //取出默认颜色的图片
            $image = M("product_album");
            $goodimgs =$image ->where("attr_id =".$goodscolor['id'])->select();
            //echo $image->getlastSql();
            $this->assign("goodscolor",$goodscolor);
            $this->assign("goodimgs",$goodimgs);

            $map['id'] =array("eq",$id);
            //商品基本参数
            $db = M("product");
            $data = $db ->where($map)->find();
            //点击次数加一
            $db ->where('id = '.$id)->setInc('click_times',1);

            //根据商品的id取出商品的类型
            $goodsData['cat_id'] = $data['cat_id'];
            //商品的所有的基本属性(包装，规格)
            $attr = M("product_properties");
            $attrlist = $attr->select();

            //取商品的包装属性

            $arrs['pro_id'] =array("eq",$id);
            $arrs['prop_id'] =array("eq",$attrlist[0]['id']);
            $ls  =$list->where($arrs)->select();


            //取商品的规格
            $arrs1['pro_id'] =array("eq",$id);
            $arrs1['prop_id'] =array("eq",$attrlist[1]['id']);
            $ls1  =$list->field("id,prop_name,prop_price")->where($arrs1)->select();
            if($ls1){
               $this->assign("nc",$ls1);
               $goodsData['price'] =$ls1;
             }
             //取出默认商品数量
             $data['stock_num']=$this->getStoreNum($id,$ls[0]['id'],$ls1[0]['id']);

             //取出促销信息
             if($data['is_promote']){
              $ProModel=M('promote');
              $promote=$ProModel->where(array('product_id'=>$id))->find();
              if($promote['status']==2){
                if($_SESSION['user']['userlevel']){
                  $MemRate=M('members_rate');
                  $Memrate=$MemRate->where(array('product_id'=>$id,'level_id'=>$_SESSION['user']['userlevel']))->getField('rate');
                  $promote['rate']=$Memrate;
                    }
              }
              if($promote['status']==3){
                  $giftId=$ProModel->where(array('product_id'=>$id))->getField('gifts');
                  $promote['gift']=$db->field('list_image,id,pro_name')->where(array('id'=>$giftId))->find();
              }
              if($promote['status']==4){
                  $gift=$ProModel->field('condition,gifts')->where(array('product_id'=>$id))->find();
                  $promote['gift']=$db->field('list_image,id,pro_name')->where(array('id'=>$gift['gifts']))->find();
                  $promote['condition']=$gift['condition'];
              }
              if($promote['status']==5){
                  $qiang=$ProModel->field('product_id,rate,start_time,end_time')->where(array('product_id'=>$id))->find();
                  $promote['qiang']=$qiang;
                  $now=time();
                  if($qiang['end_time']>$now){
                    $promote['time']=$qiang['end_time']-$now;
                  }
                  if($qiang['start_time']>$now){
                    $promote['time']=1;
                  }
                  if($qiang['end_time']<=$now){
                    $save['is_promote']=0;
                    $db->where(array('id'=>$id))->save($save);
                    $promote['time']=0;
                  }
              }

              $this->assign('promote',$promote);
             }

           //组合成一个新的数组
            $goodsData['goodsName'] = $data['pro_name'];

            $listest =array();

            //将颜色加入新的数组，并取出相对应的图片
            foreach($ls as $key=>$v){
                $goodsData['color'][]=$v;
                $list3=$image->field("images")->where("attr_id = ".$v['id'])->select();
                 $list2 =array();
                foreach($list3 as $vo){
                     $list2[] = $vo['images'];
                }
                $listest[]=$list2;
            }

            $goodsData['images']=$listest;
            //取出商品的规格参数
            $gp = M("product_param");
            $gplist = $gp->where("pro_id = ".$id)->select();


            foreach($gplist as &$v){
                $v['param_content'] =htmlspecialchars_decode($v[param_content]);
            }
           $goodsDatas = json_encode($goodsData);
            //SEO
            switch ($data['cat_id']) {
              case 1:
                       $ident ="tablet";
                       break;
             case 2:
                       $ident ="phone";
                       break;
             case 3:
                       $ident ="wearable";
                       break;
             case 4:
                       $ident ="accessories";
                       break;

            }
            // dump($data);
            // dump($promote);
            // die;
            $idents =$this->seo($ident);
            $this->assign('time',$time);
            $this->assign("title",$idents['title']);
            $this->assign("keywords",$idents['keywords']);
            $this->assign("description",$idents['description']);
            $this->assign("ls",$ls);//color
            $this->assign("ls1",$ls1);//version
            $this->assign("goodsData",$goodsDatas);//version和color组合
            $this->assign("data",$data);
            $this->assign("gplist",$gplist);
   	    	$this->display();
   	    }

        /**
         *Ajax获取商品数量
         */
        public function ajaxGetNum(){
            $colorId=I('post.colorId');
            $versionId=I('post.versionId');
            $id=I('post.id');
            $num =$this->getStoreNum($id,$colorId,$versionId);
            $this->ajaxReturn($num);
        }

   	    //添加评论
   	    public function addComment(){
             $db = M("product_comments");
             $_POST['user_id'] =$_SESSION['user']['userid'];
             $content=I('post.content');
             $_POST['content'] =htmlspecialchars($content);
             $badword=M('badwords')->getField('badword',true);
             $badword1 = array_combine($badword,array_fill(0,count($badword),'*'));
             $_POST['content']=strtr($_POST['content'],$badword1);
             //$_POST['user_id'] =1;
             $_POST['comment_time'] =time();
             $times =date("Y-m-d H:i:s",$_POST['comment_time']);
             $str ="";
             // dump($_POST);
             // exit;
             $order = M("orders");
             $order_product = M("order_products");
             if($db->create()){
              //判断此用户是否已经评论过
                  $coms = $db->where("user_id =".$_POST['user_id']." and product_id = ".$_POST['product_id'])->find();
                  // echo $db->getlastSql();
                  //判断用户是否买过此商品
                  $ord = $order->where("user_id = ".$_POST['user_id'])->select();

                  $orderlist = array();
                  foreach($ord as $v){
                      $orderlist=$order_product->where("order_id = ".$v['id']." and product_id = ".$_POST['product_id'])->select();
                  }

                   if($coms){
                      echo 1;
                      exit;
                   }else if($orderlist ==null){
                      echo 2;
                      exit;
                   }else{
                     $rt = $db ->add();

                     if($rt){
                       $user = M("user_info");
                       $users = M("users");
                       $img = $user->field("thumb")->where("user_id = ".$_POST['user_id'])->find();
                       $username = $users->field("uname")->where("id = ".$_POST['user_id'])->find();
                       $str  .='<li class="detail-pl">';
                       $str .= '<div class="detail-list">';
                       $str .= '<div class="user-precent-left display-inline-block">';
                       if($v['thumb'] ==""){
		                  $str .='<img alt="头像" src="/Public/User/default.jpg" class="np-avatar">';
		                }else{
		                 $str .='<img alt="头像" src="'.$v['thumb'].'" class="np-avatar">';
		                }
                       $str .=' <span>'.$username['uname'].'</span>';
                       $str .=' </div>';
                       $str .='<div class="display-inline-block detail-user-comm">';
                       $str .='<div class="user-precent-header">';
                       $str .=' <span class="icon-star icon-star-'.$_POST['score'].'  display-inline-block"></span>';
                       $str .='<span class="user-date" style="font-size:14px;">'.$times.'</span>';
                       $str .=' </div>';
                       $str .='<div class="user-precent-content" style="font-size:14px;">';
                       $str .='<p>'.$_POST['content'].'</p></div>';
                       $str .='</div>';
                      $str .='</div>';
                       $str .='</li>';

                       exit($str);
                     }
                  }
             }else{
             	  echo 2;
             	  exit;
             }
   	    }

        //评论列表
        public function comment(){
            //评论表
            $db = M("product_comments");
            //回复表
            $rep = M("product_comment_replies");
            $product_id = $_GET['id'];
            //$product_id = 6;
            //全部的评论
             $count = $db->where("product_id = ".$product_id)->count();
             $fivecount=$db->where(array('product_id'=>$product_id,'score'=>5))->count();
             $fourcount=$db->where(array('product_id'=>$product_id,'score'=>4))->count();
             $threecount=$db->where(array('product_id'=>$product_id,'score'=>3))->count();
             $twocount=$db->where(array('product_id'=>$product_id,'score'=>2))->count();
             $onecount=$db->where(array('product_id'=>$product_id,'score'=>1))->count();
              $page  = new \Think\HomePage($count,4);
              $show = $page ->show();
              $comm = $db->where("product_id = ".$product_id)->order("comment_time desc")->limit($page->firstRow.','.$page->listRows)->select();
            //dump($comm);
            foreach($comm as &$v){
              $v['comment_time'] =date("Y-m-d H:i:s",$v['comment_time']);
               //回复
               $replay = $rep->where("comment_id = ".$v['id'])->find();
               if($replay){
                   $replay['replay_time'] =date("Y-m-d H:i:s",$replay['reply_time']);
                  // dump($replay);
                   $v['replay'] = $replay;
               }
               //用户头像
                $user = M("user_info");
                $img = $user->field("thumb")->where("user_id = ".$v['user_id'])->find();
                $v['thumb'] =$img['thumb'];
                //用户名
                $users= M("users");
                $username =$users->field("uname") ->where("id = ".$v['user_id'])->find();
                $v['username'] =$username['uname'];
            }
            //获得各个等级评论的总条数
            $map['product_id'] =array("eq",$product_id);
            $map['score'] =array("in","4,5");
            $this->assign(array(
                    'count'=>$count,
                    'fivecount'=>$fivecount,
                    'fourcount'=>$fourcount,
                    'threecount'=>$threecount,
                    'twocount'=>$twocount,
                    'onecount'=>$onecount,
              ));
            $this->assign("page",$show);
            $this->assign("comment",$comm);

        }
         //评论的ajax分页
       public function getPageByAjax () {
         $rep = M("product_comment_replies");
            $product_id = $_GET['id'];
            //$product_id = 6;
            $db = M("product_comments");
           if($_GET['status']){
                  $status = $_GET['status'];
                   if($status ==0){
                      $count = $db->where("product_id = ".$product_id)->count();
                      $page  = new \Think\HomePage($count,6);
                      $show = $page ->show();
                      $comm = $db->where("product_id = ".$product_id)->order("comment_time desc")->limit($page->firstRow.','.$page->listRows)->limit($page->firstRow.','.$page->listRows)->select();
                    }else if($status ==1){
                       $map['product_id'] =array("eq",$product_id);
                       $map['score'] =array("in","4,5");
                       $count = $db->where($map)->count();
                       $page  = new \Think\HomePage($count,6);
                       $show = $page ->show();
                       $comm = $db ->where($map)->order("comment_time desc")->limit($page->firstRow.','.$page->listRows)->select();

                    }else if($status ==2){
                       $map['product_id'] =array("eq",$product_id);
                       $map['score'] =array("in","3,2");
                       $count = $db->where($map)->count();
                       $page  = new \Think\HomePage($count,6);
                       $show = $page ->show();
                       $comm = $db ->where($map)->order("comment_time desc")->limit($page->firstRow.','.$page->listRows)->select();
                    }else if($status ==3){
                       $map['product_id'] =array("eq",$product_id);
                       $map['score'] =array("eq","1");
                       $count = $db->where($map)->count();
                       $page  = new \Think\HomePage($count,6);
                       $show = $page ->show();
                       $comm = $db ->where($map)->order("comment_time desc")->limit($page->firstRow.','.$page->listRows)->select();
                    }
          }else{
               $count = $db->where("product_id = ".$product_id)->count();
              $page  = new \Think\HomePage($count,4);
              $show = $page->show();
              $comm = $db->where("product_id = ".$product_id)->order("comment_time desc")->limit($page->firstRow.','.$page->listRows)->select();

          }
        $str = "";
        $str .='<ul class="acomment">';
        foreach($comm as &$v){
             $v['comment_time'] =date("Y-m-d H:i:s",$v['comment_time']);
              //回复
               $replay = $rep->where("comment_id = ".$v['id'])->find();
               if($replay){
                   $replay['replay_time'] =date("Y-m-d H:i:s",$replay['reply_time']);
                  // dump($replay);
                   $v['replay'] = $replay;
               }
               //用户头像
                $user = M("user_info");
                $img = $user->field("thumb")->where("user_id = ".$v['user_id'])->find();
                $v['thumb'] =$img['thumb'];
                //用户名
                $users= M("users");
                $username =$users->field("uname") ->where("id = ".$v['user_id'])->find();
                $v['username'] =$username['uname'];
                $str .='<li class="detail-pl">';
                $str .='<div class="detail-list">';
                $str .='<div class="user-precent-left display-inline-block">';
                if($v['thumb'] ==""){
                  $str .='<img alt="头像" src="Public/User/default.jpg" class="np-avatar">';
                }else{
                 $str .='<img alt="头像" src="'.$v['thumb'].'" class="np-avatar">';
                }

                $str .='<span>'.$v['username'].'</span>';
                $str .='</div>';
                $str .='<div class="display-inline-block detail-user-comm">';
                $str .='<div class="user-precent-header">';
                $str .=' <span class="icon-star icon-star-'.$v['score'].' display-inline-block"></span>';
                $str .='<span class="user-date" style="font-size:14px;">'.$v['comment_time'].'</span>';
                $str .='</div>';
                $str .='<div class="user-precent-content" style="font-size:14px;"><p>'.$v['content'].'</p></div>';
                $str .='</div>';
                $str .='</div>';
                if($v['replay']){
                  $str .='<div class="gl-response">管理员回复：';
                  $str .='<span style="color:red;">'.$v['replay']['content'].'</span>';
                  $str .=' <span style="color:red;">'.$v['replay']['replay_time'].'</span>';
                  $str .='</div>';
                }
                $str .='</li>';

         }
          $str .='</ul>';
          $str .='<div style="height:50px;margin-bottom:16px;">';
          $str .=$show;
          $str .='</div>';
         exit($str);
    }

        public function commentlist(){
             $rep = M("product_comment_replies");
            //$product_id = $_GET['id'];
              $db = M("product_comments");
            $product_id = $_GET['id'];
            $status =$_GET['status'];
           if($status ==0){
              $count = $db->where("product_id = ".$product_id)->count();
              $page  = new \Think\HomePage($count,6);
              $show = $page ->show();
              $comm = $db->where("product_id = ".$product_id)->order("comment_time desc")->limit($page->firstRow.','.$page->listRows)->select();
            }else if($status ==1){
               $map['product_id'] =array("eq",$product_id);
               $map['score'] =array("in","4,5");
               $count = $db->where($map)->count();
               $page  = new \Think\HomePage($count,6);
               $show = $page ->show();
               $comm = $db ->where($map)->order("comment_time desc")->limit($page->firstRow.','.$page->listRows)->select();
            }else if($status ==2){
               $map['product_id'] =array("eq",$product_id);
               $map['score'] =array("in","3,2");
               $count = $db->where($map)->count();
               $page  = new \Think\HomePage($count,6);
               $show = $page ->show();
               $comm = $db ->where($map)->order("comment_time desc")->limit($page->firstRow.','.$page->listRows)->select();
            }else if($status ==3){
               $map['product_id'] =array("eq",$product_id);
               $map['score'] =array("eq","1");
               $count = $db->where($map)->count();
               $page  = new \Think\HomePage($count,6);
               $show = $page ->show();
               $comm = $db ->where($map)->order("comment_time desc")->limit($page->firstRow.','.$page->listRows)->select();
            }
            foreach($comm as &$v){
                $v['comment_time'] =date("Y-m-d H:i:s",$v['comment_time']);
              //回复
               $replay = $rep->where("comment_id = ".$v['id'])->find();
               if($replay){
                   $replay['replay_time'] =date("Y-m-d H:i:s",$replay['reply_time']);

                   $v['replay'] = $replay;

               }
               //用户头像
                $user = M("user_info");
                $img = $user->field("thumb")->where("user_id = ".$v['user_id'])->find();
                $v['thumb'] =$img['thumb'];
                //用户名
                $users= M("users");
                $username =$users->field("uname") ->where("id = ".$v['user_id'])->find();
                $v['username'] =$username['uname'];
            }

            $comm['page']=$show;
            $comm = json_encode($comm);

            exit($comm);
        }

        //购物车跳转
        public function cart(){
            $id = I('post.id');
            $ordernum = I('post.ordernum');
            $color = I('post.color');
            $version = I('post.version');
            $versionId = I('post.versionId');
            $colorId = I('post.colorId');
            $num=$this->getStoreNum($id,$colorId,$versionId);
            if($ordernum > $num){
              //判断库存是否充足
            echo 0;
            exit;
            }else{
            $key=$id."-".$versionId."-".$colorId;
            $cart=isset($_COOKIE['cart']) ? unserialize($_COOKIE['cart']) : array();
            $cart[$key] += $ordernum;
            //写入COOKIE，保存一个月
            $aMonth=time()+30*24*3600;
            setcookie('cart',serialize($cart),$aMonth,'/');
            echo 1;
            exit;
          }
        }
        //立即购买跳转
        public function buy_now(){
            unset($_SESSION['order']);
            $id = I('post.id');
            $ordernum = intval(I('post.ordernum'));
            $color = I('post.color');
            $version = I('post.version');
            $versionId = I('post.versionId');
            $colorId = I('post.colorId');
            $ids = $id."-".$versionId."-".$colorId;
            $_SESSION['order'][$ids]=$ordernum;
            echo 1;
            exit;
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

   }