<?php
/*
 * 单页
 */
namespace Home\Controller;

use  Think\Controller;
use Think\Area;

class PageController extends MyController{
    public function __construct() {
        parent::__construct();
    }
    public function aboutUs(){
        //SEO
        $ident ="support";
        $idents =$this->seo($ident);
        $this->assign('productactive','active');
        $this->assign("title",$idents['title']);
        $this->assign("keywords",$idents['keywords']);
        $this->assign("description",$idents['description']);


         $db=M('aboutus');
         $list=$db->find();
         $this->assign('list',$list);
         $album=M('aboutus_album')->select();
         $this->assign('album',$album);
         $this->display('Html/aboutus');
    }

    public function policy(){
        //SEO
        $ident ="support";
        $idents =$this->seo($ident);
        $this->assign('productactive','active');
        $this->assign("title",$idents['title']);
        $this->assign("keywords",$idents['keywords']);
        $this->assign("description",$idents['description']);

        
         $db=M('policy');
         $list=$db->where(array('id'=>1))->find();
         $this->assign('list',$list);
         $this->display('Html/policy');
    }
}
