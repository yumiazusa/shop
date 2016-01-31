<?php 
namespace Admin\Model;
use Think\Model\ViewModel;
class RuleViewModel extends ViewModel{
	public $viewFields=array(
		'rule'=>array('_table'=>'im_auth_rule','id','name','title','type','condition'=>'term','status','pid'),
		//condition必须别名,否则出错
		'modules'=>array('_table'=>'im_modules','moduleName','_on'=>'rule.pid=modules.id')
		);
}
 ?>