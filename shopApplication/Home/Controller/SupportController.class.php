<?php


namespace Home\Controller;
use Think\Controller;
date_default_timezone_set(PRC);
class SupportController extends MyController{
    public function __construct(){
        parent::__construct();
    }

    // 服务与支持模块主页
    public function index() {
        //SEO
        $ident ="support";
        $idents =$this->seo($ident);
        $this->assign('productactive','active');
        $this->assign("title",$idents['title']);
        $this->assign("keywords",$idents['keywords']);
        $this->assign("description",$idents['description']);


        $db = M("support_download_cat");
        $down = M("support_downloads");
        $list = $db->field("id,cat_name")->select();
        foreach($list as &$v){
        	$v['img'] = $down->where("cat_id = ".$v['id'])->limit(8)->select();
        }
       // dump($list);

        $this->assign("list",$list);
        
        $this->display();
    }

    // 服务与支持模块“FAQ”
    //
    public function faq() {
        //SEO
        $ident ="support";
        $idents =$this->seo($ident);
        $this->assign('productactive','active');
        $this->assign("title",$idents['title']);
        $this->assign("keywords",$idents['keywords']);
        $this->assign("description",$idents['description']);



        $db = M("support_faq_cate");
        $data = M("faq_articles");
        $list = $db->select();
        if($_GET['cid']==""){
           $datalist = $data->limit(6)->select();
          
        }else{
           $datalist =$data->where("cid = ".$_GET['cid'])->select();

        }

        foreach($datalist as &$v){
            $v['faq_content'] =htmlspecialchars_decode($v['faq_content']);
        }
         $this->assign("datalist",$datalist);
        $this->assign("list",$list);
        $this->display();
    }

    // 服务与支持模块“服务政策”
    // 
    public function policy() {
        //SEO
        $ident ="support";
        $idents =$this->seo($ident);
        $this->assign('productactive','active');
        $this->assign("title",$idents['title']);
        $this->assign("keywords",$idents['keywords']);
        $this->assign("description",$idents['description']);


        $db =M("support_article_cat");
        $data = M("support_article_detail");
        $list = $db ->where("is_show = 1")->select();
        if($_GET['cat_id'] ==""){
            $datalist = $data->find();
        }else{
            $datalist = $data->where("cat_id = ".$_GET['cat_id'])->find();
        }
        $datalist['content'] = htmlspecialchars_decode($datalist['content']);
        $this->assign("list",$list);
        $this->assign("datalist",$datalist);
        $this->display();
    }

    // 服务与支持模块 “相关下载”
    public function download(){
        //SEO
        $ident ="support";
        $idents =$this->seo($ident);
        $this->assign('productactive','active');
        $this->assign("title",$idents['title']);
        $this->assign("keywords",$idents['keywords']);
        $this->assign("description",$idents['description']);



        $db =M("support_downloads");
        $list = M("support_download_cat");
        $datalist =$list->select();
        if($_GET['title'] !=""){
            $map['title'] =array('like','%'.$_GET['title'].'%');
            foreach($map as $key=>$val){
                $page ->parameter = "$key =".urlencode($val);
            }
        }
        if($_GET['id'] !=""){
            $map['cat_id']=array("eq",$_GET['id']);
        }

        $count = $db->where($map)->count();

        $page  = new \Think\HomePage($count,9);
        $show = $page ->show();
        $data = $db->where($map)->order("ctime desc")->limit($page->firstRow.','.$page->listRows)->select();
        foreach($data as &$v){
             $v['ctime'] = date("Y.m.d",$v['ctime']); 
        }
        $this->assign("page",$show);
        $this->assign("data",$data);
        $this->assign("datalist",$datalist);
        $this->display();
    }
    //下载的页面并ajax分页
    public function getPageByAjax () {
        $db = M("support_downloads");
        $count = $db->count();
        $page  = new \Think\HomePage($count,9);
        $show = $page->show();
        $data = $db ->order("ctime desc")->limit($page->firstRow.','.$page->listRows)->select();
        $str = "";
        $str .='<div class="row download-lists" id="pages">';
        foreach($data as &$v){
            $v['ctime'] = date("Y.m.d",$v['ctime']); 
            $str .='<div class="col-sm-6 col-md-4">';
            $str .='<div class="thumbnail">';
            $str .='<img src="/Public/download/downImage/'.$v['thumbnail'].'" style="width:210px;height:228px;">';
            $str .='<div class="caption">';
            $str .='<h3>'.$v['title'].'</h3>';
            $str .='<p>';
            $str .='<span>文件大小：'.$v['filesize'].'</span>';
            $str .='<span>更新时间：'.$v['ctime'].'</span>';
            $str .='</p>';
            $str .='<p>';
            if($v['linkurl'] !=""){
                  $str .='<a href="'.$v[linkurl].'" class="btn btn-primary" role="button">立即下载</a>&nbsp;';
            }else{
                 $str .='<a href="'.U('Support/fileDown/id/'.$v['id']).'" class="btn btn-primary" role="button">立即下载</a>&nbsp';
            }
            $str .='<a href="'.U('Support/downloaddetail/id/'.$v['id']).'" class="btn btn-default" role="button">详细</a>';
            $str .='</p>';
            $str .='</div>';
            $str .='</div>';
            $str .='</div>';
             
         }
          $str .='</div>';
           $str .='<div class="row page-padding-right">';
           $str .=$show;
           $str .='</div>';
         exit($str);
    } 

    // 服务与支持模块 “下载详细页”
    // 
    public function downloadDetail(){
        //SEO
        $ident ="support";
        $idents =$this->seo($ident);
        $this->assign('productactive','active');
        $this->assign("title",$idents['title']);
        $this->assign("keywords",$idents['keywords']);
        $this->assign("description",$idents['description']);



        $id = I("get.id");
        $db = M("support_downloads");
        $datalist = M("support_download_cat");
        $ld  = $datalist->select();
        $list = $db->find($id);
        $list['description'] =htmlspecialchars_decode($list['description']);
        $list['ctimes'] = date("Y.m.d",$list['ctime']);
        //获取历史版本
        $map['ctime'] =array("lt",$list['ctime']);
        $map['title'] =array("eq",$list['title']);
        $data =$db ->where($map)->select();
        foreach($data as &$v){
            $v['ctime'] =date('Y-m-d',$v['ctime']);
        }
       // echo $db->getlastSql();
        $this->assign("datalist",$ld);
        $this->assign("data",$data);
        $this->assign("list",$list);
        $this->display();
    }

    //人才招聘
    // public function joinUs(){
    //     $this -> display();
    // }

    public function jobs(){
        //SEO
        $ident ="support";
        $idents =$this->seo($ident);
        $this->assign('productactive','active');
        $this->assign("title",$idents['title']);
        $this->assign("keywords",$idents['keywords']);
        $this->assign("description",$idents['description']);


        $db = M('jobs');
        $res = $db->where(array('is_show'=>1))->order('id ASC')->select();
        $this -> assign('data',$res);
        $this -> display('job');
    }

    //填写简历
    public function recruitment(){
        $this -> display();
    }


    //视频展示
    public function video(){
            //SEO
        $ident ="support";
        $idents =$this->seo($ident);
        $this->assign('productactive','active');
        $this->assign("title",$idents['title']);
        $this->assign("keywords",$idents['keywords']);
        $this->assign("description",$idents['description']);

            $db = M('support_video');
            $list = $db->where('pid>0')->order('id DESC')->select();
            $this->assign('list', $list);
            $this->display();
    }

    //老服务网点
    public function serviceCenter(){
        //SEO
        $ident ="support";
        $idents =$this->seo($ident);
        $this->assign('productactive','active');
        $this->assign("title",$idents['title']);
        $this->assign("keywords",$idents['keywords']);
        $this->assign("description",$idents['description']);


        $db = M('support_map');

        if(isset($_POST['id'])){
            $id = $_POST['id'];
            $row = $db -> where("pid = $id") -> select();
            $this -> ajaxReturn($row);

        }else{
            $row = $db -> where('pid =0') -> select();
        }
        $this -> assign('data',$row);
        $this -> display();
    }

    //新 服务网点
    public function supportMap(){
        //SEO
        $ident ="support";
        $idents =$this->seo($ident);
        $this->assign('productactive','active');
        $this->assign("title",$idents['title']);
        $this->assign("keywords",$idents['keywords']);
        $this->assign("description",$idents['description']);


        $da = M('support_map');
        $option = $da->where('pid>0')->select();
        $this->assign('option',$option);
        $this -> display();
    }
    //下载
    public function fileDown(){
        //SEO
        $ident ="support";
        $idents =$this->seo($ident);
        $this->assign('productactive','active');
        $this->assign("title",$idents['title']);
        $this->assign("keywords",$idents['keywords']);
        $this->assign("description",$idents['description']);


        $id =I("get.id");
        $db = M("support_downloads");
        $rt =$db->find($id);
        $file_path = "__PUBLIC__/download/uploadFile".$rt['savepath'];
        $fp =fopen($file_path,"r");
        $file = pathinfo($file_path);
        $file_size = filesize($file_path);
        Header("Content-type:application/octet-stream");
        Header("Accept-Ranges:bytes");
        Header("Accept-length:".$file_size);
        Header("Content-Disposition:attachment;filename=".$file['basename']);
        $buffer = 1024;
        $file_count =0;
        while(!feof($fp) && $file_count<$file_size){
             $file_con =fread($fp,$buffer);
             $file_count +=$buffer;
             echo $file_con;
         }
        //点击次数加一
         $db ->where('id = '.$id)->setInc('clicktimes',1);
    }
}
