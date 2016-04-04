<?php
   namespace Home\Controller;
   use  Think\Controller;
   header("content-type:text/html;charset=utf8");
  date_default_timezone_set("PRC");

   class MobileproductdetailController extends MyController{
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
                if($_SESSION['user']['userlevel']>0){
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
            
            $this->assign('time',$time);
            $this->assign("title",$idents['title']);
            $this->assign("keywords",$idents['keywords']);
            $this->assign("description",$idents['description']);
            $this->assign("ls",$ls);//color
            $this->assign("ls1",$ls1);//version
            $this->assign("goodsData",$goodsDatas);//version和color组合
            $this->assign("data",$data);
            
            $this->assign("gplist",$gplist);
            // dump($gplist);
            // die;
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

        //立即购买跳转
        public function buy_now(){
            unset($_SESSION['order']);
            $id = I('post.id');
            $ordernum = intval(I('post.ordernum'));
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