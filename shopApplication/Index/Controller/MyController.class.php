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
