<?php 
namespace Admin\Model;
use Think\Model\ViewModel;
class GroupMemberViewModel extends ViewModel{
	public $viewFields=array(
		'member'=>array('_table'=>'im_admin_users','id'=>'mid','username','last_login_time'),
		'groups'=>array('_table'=>'im_auth_group_access','uid','group_id','_on'=>'member.id=groups.uid')
		);
}
 ?>