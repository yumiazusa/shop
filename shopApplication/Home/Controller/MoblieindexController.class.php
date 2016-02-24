<?php
namespace   Home\Controller;
use         Think\Controller;

class MoblieindexController extends MyController {
    public function __construct(){
           parent::__construct();
    }
	// 首页控制器
    public function Mobileindex(){
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