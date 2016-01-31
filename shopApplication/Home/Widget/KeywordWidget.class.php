<?php
namespace   Home\Widget;
use         Think\Controller;

/**
 *  搜索关键字获取
 *  @author Eden
 *  @date   2015-01-24
 */
class KeywordWidget extends Controller 
{
    /**
     * 获取关键字的方法
     * 获取最热十个关键字
     * @return void 
     */
    public function keyword()
    {
        $modelKeywords = M('keywords');
        $keywordsRes   = $modelKeywords->field('keyword')->order("times DESC")->limit(10)->select();
        $this->assign('k', $keywordsRes);
        $this->display('Public:keyword');
    }
}
