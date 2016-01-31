<?php
namespace Home\Controller;
use Think\Controller;

class ProductController extends MyController{
     public function __construct(){
           parent::__construct();
      }
    //商品列表
    public function productList(){
        //SEO
        $ident ="productlist";
        $idents =$this->seo($ident);
        $this->assign('productactive','active');
        $this->assign("title",$idents['title']);
        $this->assign("keywords",$idents['keywords']);
        $this->assign("description",$idents['description']);
        if(C('ad5')){
        // 列表页广告
        $modelAdv = M('advs');
        $getListLeftAdv = $modelAdv->field('product_id,is_connect, thumbnail')->where("position_id = '13'")->find();
        $getListRightAdv = $modelAdv->field('product_id,is_connect, thumbnail')->where("position_id = '14'")->find();
        $this->assign('leftAdv', $getListLeftAdv);
        $this->assign('rightAdv', $getListRightAdv);
        }
        $Cats=$this->get_data();
        $this->assign('Cats',$Cats);
        //获取赠品类ID
        $giftId=$this->get_unshow();
        // 获取商铺
        $getProductType = I('get.type');
        $getProductCat =I('get.cat');
        $where = array();
        switch ($getProductType)
        {
            case 'shop':
                $where['shop_id'] = array('eq',1);
                break;
            case 'market':
                $where['shop_id'] = array('gt',I('get.shopid'));
                break;
            default:
                $this->error('非法操作');
                exit;
        }

        $cats=$this->get_cats();
        if($getProductCat =='all'){
            $where['shop_id'] = '1';
            if($giftId){
            $where['cat_id']=array('not in',$giftId);
            }
        }elseif(in_array($getProductCat,$cats)){
            $cat=$this->checkcat($getProductCat);
            $where['cat_id']=array('in',$cat['id']);
        }else{
            $this->error('非法操作');
            exit;
        }

        $order = "update_time DESC, id DESC";

        if (isset($_GET['by']))
        {
            switch ($_GET['by'])
            {
                case 'all':
                    break;
                case 'time':
                    $order = "add_time DESC";
                    break;
                case 'promote':
                    $where['is_promote'] = 1;
                    break;
                case 'priceup':
                    $order = "promote_price ASC";
                    break;
                case 'pricedown':
                    $order = "promote_price DESC";
                    break;
                case 'new':
                    $where['is_new'] = 1;
                    break;
            }
        }

        $modelProduct = M('product');
        $where['is_on_sale'] = 1;
        $where['recycle'] = 0;
        // $where['cat_id']=array('not in',$giftId);


        $getPageCount = $modelProduct->where($where)->count();
        $getPageSize = 16;

        // 分页处理
        $page = new \Think\MyPage($getPageCount, $getPageSize);

        $productRes = $modelProduct->field('id,cat_id,pro_name,pro_subname,market_price,promote_price,list_image,is_new,is_promote')
                                   ->where($where)
                                   ->order($order)
                                   ->limit($page->firstRow, $page->listRows)
                                   ->select();

        $pageShow = $page->show();




        $this->assign('type', $_GET['type']);
        $this->assign('page', $pageShow);
        $this->assign('productList', $productRes);
        $this -> display();
    }

     /**
     * 处理商品分类数组，供导航链接用
     */
    private function get_data(){
        $Model=M('product_cat');
        $list =$Model->where(array('fid'=>0,'is_show'=>1))->order('fid asc,sort asc')->select();
        foreach($list as $k=>$v){
            $list[$k]['children']=$Model->where(array('fid'=>$v['id']))->order('fid asc,sort asc')->select();
        }
            return $list;
        }

     /**
     * 获取不可见分类ID
     */
    private function get_unshow(){
        $Model=M('product_cat');
        $list =$Model->field('id')->where(array('fid'=>0,'is_show'=>0))->select();
        $data=array();
        foreach($list as $k=>$v){
            $data[$k]=$v['id'];
        }
            return $data;
        }

     /**
     * 处理商品分类数组,供导航跳转用
     */
    private function get_cats(){
        $Modelcat=M('product_cat');
        $map['is_show']=array('eq',1);
        $cat=$Modelcat->field('cat_name')->where($map)->select();
        $cats=array();
        foreach($cat as $k=>$v){
            $cats[$k]=$v['cat_name'];
        }
        return $cats;
    }

    /**
     * 处理商品分类,供搜索条件用
     */
    private function checkcat($catname){
        $Modelcat=M('product_cat');
        $cat=$Modelcat->field('id,fid')->where(array('cat_name'=>$catname))->find();
        $cats=array();
        if($cat['fid'] == 0){
            $subcat=$Modelcat->field('id')->where(array('fid'=>$cat['id']))->select();
            $cats['id']=array();
            foreach($subcat as $k=>$v){
                $cats['id'][$k]=$v['id'];
            }
                array_push($cats['id'], $cat['id']);
        }else{
                $cats['id'][]=$cat['id'];
        }
        return $cats;
    }

    public function promoteList(){
       //SEO
        $ident ="promoteList";
        $idents =$this->seo($ident);
        $this->assign('productactive','active');
        $this->assign("title",$idents['title']);
        $this->assign("keywords",$idents['keywords']);
        $this->assign("description",$idents['description']);
        if(C('ad5')){
        // 列表页广告
        $modelAdv = M('advs');
        $getListLeftAdv = $modelAdv->field('product_id,is_connect, thumbnail')->where("position_id = '13'")->find();
        $getListRightAdv = $modelAdv->field('product_id,is_connect, thumbnail')->where("position_id = '14'")->find();
        $this->assign('leftAdv', $getListLeftAdv);
        $this->assign('rightAdv', $getListRightAdv);
        }


        $order = "update_time DESC, id DESC";

        if (isset($_GET['by']))
        {
            switch ($_GET['by'])
            {
                case 'all':
                    break;
                case 'time':
                    $order = "add_time DESC";
                    break;
                case 'priceup':
                    $order = "promote_price ASC";
                    break;
                case 'pricedown':
                    $order = "promote_price DESC";
                    break;
                case 'new':
                    $where['is_new'] = 1;
                    break;
            }
        }

        $modelProduct = M('product');
        $where['a.is_on_sale'] = 1;
        $where['a.recycle'] = 0;
        $where['a.is_promote']=1;
        $where['b.status']=5;

        $getPageCount = $modelProduct->alias('a')
                        ->join('LEFT JOIN im_promote b ON a.id = b.product_id')
                        ->where($where)
                        ->count();
        $getPageSize = 16;

        // 分页处理
        $page = new \Think\MyPage($getPageCount, $getPageSize);

        $productRes = $modelProduct->alias('a')
                                   ->field('a.id,a.cat_id,a.pro_name,a.pro_subname,a.market_price,a.promote_price,a.list_image,a.is_new,a.is_promote,b.status,b.rate,b.start_time,b.end_time')
                                   ->join('LEFT JOIN im_promote b ON a.id = b.product_id')
                                   ->where($where)
                                   ->order($order)
                                   ->limit($page->firstRow, $page->listRows)
                                   ->select();
        $time=array();
        foreach($productRes as $k=>$v){
            $now=time();
            if($v['end_time']>$now){
                $time[$v['id']]=$v['end_time']-$now;
            }
            if($v['start_time']>$now){
                $time[$v['id']]=0;
            }
            if($v['end_time'] <= $now){
                $data['is_promote']=0;
                $modelProduct->where(array('id'=>$v['id']))->save($data);
                unset($productRes[$k]);
            }
        }
        $time=json_encode($time);
        // dump($productRes);
        // dump($time);
        // die;
        $pageShow = $page->show();
        $this->assign('time', $time);
        $this->assign('page', $pageShow);
        $this->assign('productList', $productRes);
        $this -> display();
    }
}
