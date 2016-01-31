<?php

namespace   Home\Controller;
use         Think\Controller;

class SearchController extends MyController {
    public function __construct() {
        parent::__construct();
    }

    public function index () {
        //SEO
        $ident ="search";
        $idents =$this->seo($ident);
        $this->assign("title",$idents['title']);
        $this->assign("keywords",$idents['keywords']);
        $this->assign("description",$idents['description']);


        $getKeyword = I('get.keyword');
        if (empty ($getKeyword))
        {
            $this->error('非法操作...');
        }
        // 先处理关键字，查询数据库，如果数据库中存在关键字，那么对关键字户数，如果不存在，加入关键字
        $this->_collect($getKeyword);

        // 查询产品
        $modelProducts = M('product');
        $where = "pro_name LIKE '%{$getKeyword}%'";
        $proInfo['counts'] = $modelProducts->where($where)->count();
        $proInfo['product'] = $modelProducts
                                        ->field('id, pro_name, pro_subname, market_price, promote_price, list_image')
                                        ->where($where)
                                        ->order('update_time DESC')
                                        ->limit(5)
                                        ->select();
        // 查询新闻
        $modelNews = M('news_contents');
        $where = "title LIKE '%{$getKeyword}%'";
        $newsInfo['counts'] = $modelNews->where($where)->count();
        $newsInfo['news']   = $modelNews->where($where)
                                        ->field('id, title')
                                        ->order('create_time DESC')
                                        ->limit(5)
                                        ->select();

        // 查询视频
        $modelVideo = M('support_video');
        $where = "title LIKE '%{$getKeyword}%' AND pid != 0";
        $videoInfo['counts'] = $modelVideo->where($where)->count();
        $videoInfo['video']  = $modelVideo->where($where)
                                          ->field('id, title, simg, weburl')
                                          ->order('id DESC')
                                          ->limit(6)
                                          ->select();

        $this->assign('videoInfo', $videoInfo);
        $this->assign('keyword', $getKeyword);
        $this->assign('newsInfo', $newsInfo);
        $this->assign('proInfo', $proInfo);
        $this->display('index');
    }

    private function _collect($keyword)
    {
        $modelKey = M('keywords');
        $keywordsRes = $modelKey->field("id, times")->where("keyword LIKE '%{$keyword}%'")->find();
        if ($keywordsRes) 
        {
            $times = $keywordsRes['times'] + 1;
            $modelKey->where("id = '{$keywordsRes['id']}'")->data(array('times'=>$times))->save();
        }
        else
        {
            $modelKey->data($_POST)->add();
        }
    }

}