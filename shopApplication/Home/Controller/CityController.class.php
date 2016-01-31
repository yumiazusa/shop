<?php
namespace Home\Controller;
use Think\Controller;
class CityController extends Controller{
	//省
	public function provice(){
		$provice = M("city_class");
		$list = $provice->where($_POST)->select();
		exit(json_encode($list));

	}
}