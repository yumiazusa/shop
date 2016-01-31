<?php
namespace Index\Controller;
use Think\Controller;
class NewsController extends MyController {

    public function __construct() {
        parent::__construct();
    }

    public function ajaxNews(){
        $news=S('news',null);
        $news=S('news');
        if(!$news){
            $NewsModel=M('news_contents');
            $news=$NewsModel->field('id,title,create_time,thumbnail,description')->where(array('is_show'=>1))->order('create_time DESC')->limit(4)->select();
            S('news',$news,3600*6);
        }
        if($news){
            $str='';
            foreach($news as $k=>$v){
                $str.='<div class="col-md-6" style="margin-bottom: 20px;">';
                $str.='<div class="features"><img src="/Public/News/'.$v['thumbnail'].'">';
                $str.='<div class="features-in"><h3><a target="_blank" href="'.U('News/newspage',array('id'=>$v['id'])).'">'.$v['title'].'</a></h3>';
                $str.='<p>'.msubstr($v['description'],0,30,'utf-8',true).'</p></div></div>';
                $str.='<div style="float: right;">'.date('Y-m-d H:i',$v['create_time']).'</div></div>';
            }
        }
        echo !empty($str) ? compress_html($str) : 0;
    }

    public function newslist(){
        //SEO
        $ident ="News";
        $idents =$this->seo($ident);
        $this->assign("title",$idents['title']);
        $this->assign("keywords",$idents['keywords']);
        $this->assign("description",$idents['description']);

        $cat=I('get.cat');
        $NewsModel=M('news_contents');
        $NewsCat=M('news_cat');
        $newscat=$NewsCat->where(array('is_show'=>1))->order('id ASC')->select();
        $cats=array();
        foreach ($newscat as $k => $v) {
           $cats[$k]=$v['id'];
           $map['cat_id']=array('eq',$v['id']);
           $map['is_show']=array('eq',1);
           $newscat[$k]['amount']=$NewsModel->where($map)->count();
        }
        $newsrank=$NewsModel->field('id,title,thumbnail,description')->where(array('is_show'=>1))->order('clicktimes DESC')->limit(3)->select();
        $this->assign('newsrank',$newsrank);
        $mapnews=array();
        if($cat){
            if(in_array($cat, $cats)){
            $cat_name=$NewsCat->where(array('id'=>$cat))->getField('cat_name');
            $this->assign('cat_name',$cat_name);
            $mapnews['cat_id']=array('eq',$cat);
            }else{
            $this->display('Public:404');
            exit;
            }
        }
        $mapnews['is_show']=array('eq',1);
        $count = $NewsModel->where($mapnews)->count();// 查询满足要求的总记录数
        $Page  = new \Think\MyPage($count,4);
        $show  = $Page->show();
        $news = $NewsModel->field('id,title,create_time,thumbnail,description,clicktimes,author')->where($mapnews)->order('create_time DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('news',$news);
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('newscat',$newscat);
        $this->display();
    }

    public function newspage(){
        //SEO
        $ident ="Newspage";
        $idents =$this->seo($ident);
        $this->assign("keywords",$idents['keywords']);
        $this->assign("description",$idents['description']);


        $id=I('get.id');
        $newsModel=M('news_contents');
        $res=$newsModel->where(array('id'=>$id,'is_show'=>1))->find();
        if(!$res){
            $this->display('Public:404');
            exit;
        }else{
            $map['id'] =array('eq',$id);
            //点击次数加一
            $newsModel->where($map)->setInc('clicktimes',1);
            $NewsCat=M('news_cat');
            $newscat=$NewsCat->where(array('is_show'=>1))->order('id ASC')->select();
            foreach ($newscat as $k => $v) {
            $mapcat['cat_id']=array('eq',$v['id']);
            $mapcat['is_show']=array('eq',1);
            $newscat[$k]['amount']=$newsModel->where($mapcat)->count();
            }
            $newsrank=$newsModel->field('id,title,thumbnail,description')->where(array('is_show'=>1))->order('clicktimes DESC')->limit(3)->select();
            $this->assign('newsrank',$newsrank);
            $this->assign('new',$res);
            $this->assign('newscat',$newscat);
            $this->assign("title",$res['title'].'--资讯中心');
        }

        $this->display();
    }

}

