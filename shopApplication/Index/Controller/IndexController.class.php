<?php
namespace Index\Controller;
use Think\Controller;
class IndexController extends MyController {
        public function __construct() {
        parent::__construct();
    }


    public function index(){
        dump(C('logo'));
        die;
        //SEO
        $ident ="Indexindex";
        $idents =$this->seo($ident);
        $this->assign("title",$idents['title']);
        $this->assign("keywords",$idents['keywords']);
        $this->assign("description",$idents['description']);

    	$FocusPos=M('indexfocus');
    	$PosList=$FocusPos->alias('a')->field('a.status,b.*')
    			->join('LEFT JOIN im_focus b on a.id =b.id')
    			->select();
    	$this->assign('PosList',$PosList);

    	$About=M('index_about');
    	$abouts=$About->select();
    	$this->assign('abouts',$abouts);

        //异步加载资讯新闻(News/ajaxNews)

        //产品列表
        S('goodLists',null);
        $goodLists=S('goodLists');
        if(!$goodLists){
            $Goods=M('index_goods');
            $goodLists=$Goods->where(array('is_show'=>1,'status'=>1))->order('id DESC')->select();
            S('goodLists',$goodLists,3600*6);
        }
        $this->assign('goodLists',$goodLists);

        //异步加载博文中心


        //友情链接
        $linksModel=M('links');
        $links=$linksModel->where(array('is_show'=>1))->select();
        $this->assign('links',$links);
    	$this->display();
        }

    public function ajaxContact(){
        // require('badword.src.php');
        $badword=M('badwords')->getField('badword',true);
        $badword1 = array_combine($badword,array_fill(0,count($badword),'*'));
        $data=I('post.');
        $data['subject']=strtr($data['subject'],$badword1);
        $data['content']=strtr($data['content'],$badword1);
        $data['contact_time']=time();
        $db=M('index_contact');
        $res=$db->add($data);
        if($res){
            $this->ajaxReturn(1);
            exit;
            }else{
            $this->ajaxReturn(0);
            exit;
            }
        }
}