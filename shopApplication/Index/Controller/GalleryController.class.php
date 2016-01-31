<?php
namespace Index\Controller;
use Think\Controller;
class GalleryController extends MyController {
    public function __construct() {
        parent::__construct();
    }
    
    public function ajaxGalleryword(){
    	$filter=I('post.filter');
    	$goodLists=S('goodLists');
        if(!$goodLists){
            $Goods=M('index_goods');
            $goodLists=$Goods->where(array('is_show'=>1,'status'=>1))->order('id DESC')->select();
            S('goodLists',$goodLists,3600*6);
        }
        if($goodLists){
        	$str='';
        	if(!$filter){
        		$str.='<h2><strong>'.$goodLists[0]['pro_name'].'</strong><br>';
        		$str.=$goodLists[0]['pro_subnamee'].'</h2>';
        		$str.='<p>'.$goodLists[0]['description'].'</p><br>';
        		$str.='<a target="_blank" href="'.U('galleryList',array('id'=>$goodLists[0]['id'])).'" class="btn-brd-primary">详细查看</a>';
        	}else{
        		$filter=$filter-1;
        		$str.='<h2><strong>'.$goodLists[$filter]['pro_name'].'</strong><br>';
        		$str.=$goodLists[$filter]['pro_subnamee'].'</h2>';
        		$str.='<p>'.$goodLists[$filter]['description'].'</p><br>';
        		$str.='<a target="_blank" href="'.U('galleryList',array('id'=>$goodLists[$filter]['id'])).'" class="btn-brd-primary">详细查看</a>';
        	}
       }
        echo !empty($str) ? compress_html($str) : 0;
    }

    public function galleryList(){
        //SEO
        $ident ="Gallerylist";
        $idents =$this->seo($ident);
        $this->assign("keywords",$idents['keywords']);
        $this->assign("description",$idents['description']);

    	$id=I('get.id');
    	$goodModel=M('index_goods');
    	$good=$goodModel->where(array('id'=>$id,'is_show'=>1))->find();
    	if(!$good){
    		$this->display('Public:404');
            exit;
    	}else{
            $goodModel->where(array('id'=>$id,'is_show'=>1))->setInc('click_times',1);
    	    $img=M('index_album')->where(array('good_id'=>$id))->select();
    	    $this->assign('good',$good);
            $this->assign("title",$good['pro_name'].'--产品中心');
    	    $this->assign('img',$img);
    	}
        $this->display();
    }

    public function gallery(){
        //SEO
        $ident ="Gallery";
        $idents =$this->seo($ident);
        $this->assign("title",$idents['title']);
        $this->assign("keywords",$idents['keywords']);
        $this->assign("description",$idents['description']);


    	$goodLists=S('goodLists');
        if(!$goodLists){
            $Goods=M('index_goods');
            $goodLists=$Goods->where(array('is_show'=>1,'status'=>1))->order('id DESC')->select();
            S('goodLists',$goodLists,3600*6);
        }
        if($goodLists){
        	$this->assign('goods',$goodLists);
        }
        $this->display();
    }
}

