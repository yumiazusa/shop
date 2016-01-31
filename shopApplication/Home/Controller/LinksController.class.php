<?php

namespace Home\Controller;
use Think\Controller;

/**
 * Linkscontroller 
 * 
 * @author: Sun
 * @date: 2015-01-22
 */

class LinksController extends MyController
{
	public function __construct() {
        parent::__construct();
    }
   
    /**
     * 友情链接列表
     * @date 2015-01-22
     * @return void 
     */
	public function index(){
         //SEO
        $ident ="links";
        $idents =$this->seo($ident);
        $this->assign("title",$idents['title']);
        $this->assign("keywords",$idents['keywords']);
        $this->assign("description",$idents['description']);

        $data = M("links");
        $list = $data->where('is_show=1')->select();       

        //dump($list);
        $this->assign("list",$list);
        $this->display();

	}
}