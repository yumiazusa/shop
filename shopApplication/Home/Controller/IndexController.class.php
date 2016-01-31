<?php
namespace   Home\Controller;
use         Think\Controller;

class IndexController extends MyController {
    public function __construct(){
           parent::__construct();
    }
	// 首页控制器
    public function index(){
        //SEO
        $ident ="shopindex";
        $idents =$this->seo($ident);
        $this->assign('indexactive','active');
        $this->assign("title",$idents['title']);
        $this->assign("keywords",$idents['keywords']);
        $this->assign("description",$idents['description']);

        // 大图 1366 x 580
        $modelAdv = M('advs');
        $m = M();
        $bannerRes = $modelAdv->field('id, adv_name, thumbnail, product_id,is_connect')
                              ->where(array('position_id'=>1, 'is_show'=>1))
                              ->order('id DESC')
                              ->limit(3)
                              ->select();
        if(C('ad2')){
        // 手机广告左
        $mobileLeft = $modelAdv->alias('a')->field('a.id, a.adv_name, a.thumbnail, a.adv_desc, a.product_id,a.is_connect, p.market_price, p.promote_price')
                        ->join('LEFT JOIN im_product p ON a.product_id = p.id')
                        ->where("a.position_id=3 AND a.is_show=1")
                        ->order('a.id DESC')
                        ->find();
        if($mobileLeft['is_connect']){
        $mobileLeftAlbum = $modelAdv->alias('a')
                            ->field('album.images')
                            ->join('LEFT JOIN im_products_attr pa ON a.product_id = pa.pro_id')
                            ->join('LEFT JOIN im_product_album album ON pa.id = album.attr_id')
                             ->where("a.id = {$mobileLeft['id']} AND pa.prop_id = 1")
                             ->limit(4)
                             ->select();
        $mobileLeft['album'] = $this->_two2one($mobileLeftAlbum);
                 }
        // 手机广告右上2
        $mobileRightTop2 = $modelAdv->alias('a')->field('a.id, a.adv_name, a.thumbnail, a.adv_desc, a.product_id,a.is_connect, p.market_price, p.promote_price')
                        ->join('LEFT JOIN im_product p ON a.product_id = p.id')
                        ->where("a.position_id=4 AND a.is_show=1")
                        ->order('a.id DESC')
                        ->limit(2)
                        ->select();
        foreach ($mobileRightTop2 as &$v) 
        {
            if($v['is_connect']){
            $mobileLeftAlbum = $modelAdv->alias('a')
                            ->field('album.images')
                            ->join('LEFT JOIN im_products_attr pa ON a.product_id = pa.pro_id')
                            ->join('LEFT JOIN im_product_album album ON pa.id = album.attr_id')
                             ->where("a.id = {$v['id']} AND pa.prop_id = 1")
                             ->limit(4)
                             ->select();
            $v['album'] = $this->_two2one($mobileLeftAlbum);
            }
        }

        // 手机底部
        $mobileRightBtm = $modelAdv->alias('a')->field('a.id, a.adv_name, a.thumbnail, a.adv_desc, a.product_id,a.is_connect, p.market_price, p.promote_price')
                        ->join('LEFT JOIN im_product p ON a.product_id = p.id')
                        ->where("a.position_id=5 AND a.is_show=1")
                        ->order('a.id DESC')
                        ->find();
        if($mobileRightBtm['is_connect']){
        $mobileRightAlbum = $modelAdv->alias('a')
                            ->field('album.images')
                            ->join('LEFT JOIN im_products_attr pa ON a.product_id = pa.pro_id')
                            ->join('LEFT JOIN im_product_album album ON pa.id = album.attr_id')
                             ->where("a.id = {$mobileRightBtm['id']} AND pa.prop_id = 1")
                             ->limit(4)
                             ->select();
        $mobileRightBtm['album'] = $this->_two2one($mobileRightAlbum);
            }
        $this->assign('mobileLeft', $mobileLeft);
        $this->assign('mobileRightTop2', $mobileRightTop2);
        $this->assign('mobileRightBtm', $mobileRightBtm);
        // 手机广告结束 =====================================================
             }

        if(C('ad3')){
        // 平板广告开始
        // 左侧广告
        $tabletLeft = $modelAdv->field('id,is_connect,product_id, thumbnail')->where("position_id = 6")->find();
        // 右侧小四侧广告
        $tabletRight = $modelAdv->field('id,is_connect,product_id, thumbnail')->where('position_id = 7')->limit(4)->select();
        // 平板广告结束 =====================================================
        $this->assign('tabletRight', $tabletRight);
        $this->assign('tabletLeft', $tabletLeft);
                }

        if(C('ad4')){
        // 穿戴开始
        $wearableHigh = $modelAdv->alias('a')
                        ->join('LEFT JOIN im_product p ON a.product_id = p.id')
                          ->field('a.product_id, a.is_connect,a.thumbnail, p.pro_name, p.market_price, p.promote_price')
                          ->where("a.position_id = 8")
                          ->order('a.id DESC')
                          ->limit(2)
                          ->select();
        $wearableWidth = $modelAdv->alias('a')
                        ->join('LEFT JOIN im_product p ON a.product_id = p.id')
                          ->field('a.product_id, a.is_connect,a.thumbnail, p.pro_name, p.market_price, p.promote_price')
                          ->where("a.position_id = 9")
                          ->order('a.id DESC')
                          ->limit(2)
                          ->select();
        $wearableSmall= $modelAdv->alias('a')
                        ->join('LEFT JOIN im_product p ON a.product_id = p.id')
                          ->field('a.product_id, a.is_connect,a.thumbnail, p.pro_name, p.market_price, p.promote_price')
                          ->where("a.position_id = 10")
                          ->order('a.id DESC')
                          ->limit(4)
                          ->select();
        // 穿戴产品广告结束
        $this->assign('wS', $wearableSmall);
        $this->assign('wW', $wearableWidth);
        $this->assign('wH', $wearableHigh);
            }
        // // 配件广告开始
        // $access_01 = $modelAdv->where("position_id = 11")
        //                       ->field('product_id, thumbnail')
        //                       ->order('id DESC')
        //                       ->find();
        // $access_02 = $modelAdv->where("position_id = 12")
        //                       ->field('product_id, thumbnail')
        //                       ->order('id DESC')
        //                       ->select();
        // $modelProduct = M('product');
        // $accessSort = $modelProduct->field('id, pro_name, market_price, promote_price, list_image')
        //                            ->where('cat_id = 4 AND is_on_sale = 1 AND recycle = 0')
        //                            ->order('click_times DESC')
        //                            ->limit(5)
        //                            ->select();

        $this->assign('aS', $accessSort);
        $this->assign('aD', $access_02);
        $this->assign('aB', $access_01);
        $this->assign('bannerRes', $bannerRes);
        $this->display();
    }

    private function _two2one($arr2)
    {
        $arr1 = array();
        foreach ($arr2 as $val)
        {
            $arr1[] = $val['images'];
        }
        return $arr1;
    }
}