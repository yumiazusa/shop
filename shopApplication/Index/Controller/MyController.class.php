<?php
namespace Index\Controller;
use    Think\Controller;
date_default_timezone_set(PRC);
/*
 *
 * 访问量
 *
 */
 class MyController extends Controller{
 	  public function __construct(){
 	  	parent::__construct();
 	  	$config=M('sys_config')->getField('cname,cvalue');
        C($config);
        if(!C('sy_web_online')){
           $this->display('Error:update');
           die;
        }
        if(C('sy_seo_rewrite')){
        	C('URL_MODEL',2);
        	C('URL_HTML_SUFFIX','html|shtml|xml');
        }else{
        	C('URL_MODEL',1);
        	C('URL_HTML_SUFFIX','html');
        }
        $this->visit();
 	  }

    public function visit(){
      $vtime = date("Y-m-d",time());
      if($_SESSION['visit1']==""){
        $db = M("index_visit");
        $rt =$db->where("vtime = '".$vtime."'")->find();
        if($rt){
           $db->where("vtime = '".$vtime."'")->setInc('visit_count',1); 
        }else{
           $data['vtime'] = $vtime;
           $data['visit_count'] =1;
           $db->add($data);
        }
        $_SESSION['visit1'] =1;
      }
    }

 	  public function seo($ident){
          $seo =M("seo");
          $map['ident'] = array("eq",$ident);
          $seolist = $seo->where($map)->find();
           // echo $seo->getlastSql();
           // exit;
          return $seolist;
 	  }
 }
