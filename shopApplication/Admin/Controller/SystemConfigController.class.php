<?php
/**
 * Created by PhpStorm.
 * User: YJG
 * Date: 2015/1/12
 * Time: 19:53
 */
namespace Admin\Controller;
use Think\Controller;


class SystemConfigController extends MyController{
	
	public function __construct() {
        parent::__construct();
    }
    
    function webConfig(){
        $sysConfig = M('sys_config');
        $row = $sysConfig -> select();
        //dump($row);
        $contracts=M('policy');
        $contract=$contracts->where(array('id'=>3))->find();
        $this->assign('contract',$contract);
        $this -> assign('data',$row);

        $this -> display();
    }

    //保存配置
    function uploadConfig()
    {
        $data = $_POST;
        $save = M('sys_config');

        foreach($data as $key =>$value){
            $d['cvalue'] = $value;
            $save ->where("cname='$key'") -> save($d);
        }
    if($data['sy_fkeyword']){
        str_replace("，",",",$data['sy_fkeyword']);
        $data['sy_fkeyword']=explode(',', $data['sy_fkeyword']);
        foreach($data['sy_fkeyword'] as $v){
            $data['badword']=trim($v);
            M('badwords')->add($data);
        }
    }

    }

    public function uploadLogo(){
        dump($_FILES);
        die;
    }

    public function upLogo()
    {
        $upload             =     new \Think\Upload();  // 实例化上传类
        $upload->maxSize    =     3145728 ;             // 设置附件上传大小
        $upload->exts       =     array('jpg', 'gif', 'png', 'jpeg','ico');   // 设置附件上传类型
        $upload->rootPath   =     "Public/Admin/img/Logo/";
        $upload->savePath   =      '';                          // 设置附件上传目录    // 上传文件
        $info   =   $upload->upload();
        // dump($info);
        // die;
        $id = $_POST['id'];
        $url = "/Public/Admin/img/Logo/".$info['file_upload'.$id]['savepath'].$info['file_upload'.$id]['savename'];
        $db = M('sys_config');
        $data['cvalue'] = $url;
        $id = $_POST['id'];

        if($id == 1){
            $res =$db ->where("cname='logo'") -> save($data);
        }else if($id == 2){
            $res = $db ->where("cname='logo2'") -> save($data);
        }else if($id == 3){
            $res = $db ->where("cname='logo3'") -> save($data);
        }else if($id == 4){
            $res = $db ->where("cname='logo4'") -> save($data);
        }else if($id == 5){
            $res = $db ->where("cname='logo5'") -> save($data);
        }else{}
        if($res){
            $this->success('Logo修改成功','webConfig');
        }else{
            $this->error('Logo修改失败','webConfig');
        }

    }




}
