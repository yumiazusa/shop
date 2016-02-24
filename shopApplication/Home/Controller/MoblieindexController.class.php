<?php
namespace   Home\Controller;
use         Think\Controller;

class MoblieindexController extends MyController {
    public function __construct(){
           parent::__construct();
    }
	// 首页控制器
    public function Mobileindex(){
       
        $this->display();
    }

    private function _two2one($arr2)
    {
        $arr1 = array();
        foreach ($arr2 as $val)
        {
            $arr1[] = $val['images'];
        }
        return $arr1;
    }
}