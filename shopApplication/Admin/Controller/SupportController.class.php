<?php
namespace Admin\Controller;
use   Think\Controller;
header("content-type:text/html;charset=utf-8");
  /*
   * 服务与支持
   */
class SupportController extends MyController{
    	public function __construct(){
    		parent::__construct();
    	}

      /**
       * 单页（关于我们）
       */
      public function aboutUs(){
         $db=M('aboutus');
         $list=$db->find();
         $this->assign('list',$list);
         $album=M('aboutus_album')->select();
         $this->assign('album',$album);
         $this->display();
      }

      public function editAboutus(){
        $db=M('aboutus');
        $list=$db->find();
        $this->assign('list',$list);
        $this->display();
      }

      public function doEditaboutus(){
       if($_FILES['file']['name']){
            $_POST['image']=$this->uploadAboutus();
        }
        $id=I('post.id');
        $db = M("aboutus");
        if($db->create()){
            $rt=$db->save();
             if($rt){
                $cLog = new \Admin\Controller\LogController();
                $cLog->setLog('单页（关于我们）（ID:' . $id . '）修改成功');
                $this->success("修改成功",U("aboutUs"));
                exit;
            }else{
                $this->error("修改失败",U("aboutUs"));
            }
        }else{
            $this->error("修改失败",U("aboutUs"));
        }
      }

      public function editCertification(){
        $db=M('aboutus_album');
        $list=$db->select();
        $this->assign('album',$list);
        $this->display();
      }

    /**
     * 上传图片
     *资质文件上传
     */
    public function uploadZizhi(){
        $upload = new \Think\Upload();  // 实例化上传类
        $upload->maxSize = 3145728;             // 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');   // 设置附件上传类型
        $upload->rootPath = "Public/About/license/";
        $upload->savePath = '';                         // 设置附件上传目录    // 上传文件
        $info = $upload->upload();
        $src=$info['Filedata']['savepath'] . $info['Filedata']['savename'];
        $this->ajaxReturn($src);
    }

    /**
     * 执行资质文件修改
     */
    public function doEditZizhi(){
        $post=I('post.');
        $data=array();
        $id=array();
        if($post['images']){
        $mAffectedRows = 0;
        $modelImg=M('aboutus_album');
        $ids=$modelImg->field('id')->select();
        foreach($ids as $k=>$v){
          $id[$k]=$v['id'];
        }
        $map['id']=array('IN',$id);
        $modelImg->where($map)->delete();
            foreach($post['images'] as $k=>$v){
                $data[$k]['images']=$v;
            }
        $mAffectedRows = $modelImg->addAll($data);
        }else{
            $mAffectedRows = 1;
        }
        if ($mAffectedRows) {
            // 写入日志
            $cLog = new \Admin\Controller\LogController();
            $cLog->setLog('产品资质修改成功');
            $this->success('产品资质修改成功','aboutUs');
            exit;
        } else {
            $this->error('产品资质修改失败','aboutUs');
        }
    }

       /**
     * 单图片上传
     */
    public function uploadAboutus(){
        $upload = new \Think\Upload();
        $upload->maxSize = 8000000;
        $upload->exts    =array('jpg','gif','png','jpeg');
         $upload->rootPath="Public/About/";
         $upload->savePath="";
         $info=$upload->upload();
         if(!$info) {
                  $this->error($upload->getError());
          }else{                                                 // 上传成功 获取上传文件信息
             foreach($info as $file){
               return $file['savepath'].$file['savename'];
             }
         }
    }

       /**
       * 单页（法律声明）
       */
      public function policy(){
         $db=M('policy');
         $list=$db->where(array('id'=>1))->find();
         $this->assign('list',$list);
         $this->display();
      }

      public function editPolicy(){
        $db=M('policy');
        $list=$db->where(array('id'=>1))->find();
        $this->assign('list',$list);
        $this->display();
      }

      public function doEditpolicy(){
        $id=I('post.id');
        $db = M("policy");
        if($db->create()){
            $rt=$db->save();
             if($rt){
                $cLog = new \Admin\Controller\LogController();
                $cLog->setLog('单页（法律声明）修改成功');
                $this->success("修改成功",U("policy"));
                exit;
            }else{
                $this->error("修改失败",U("policy"));
            }
        }else{
            $this->error("修改失败",U("policy"));
        }
      }

      /**
       * 单页（注册服务协议）
       */
      public function servicepolicy(){
         $db=M('policy');
         $list=$db->where(array('id'=>3))->find();
         $this->assign('list',$list);
         $this->display();
      }

      public function editservicePolicy(){
        $db=M('policy');
        $list=$db->where(array('id'=>3))->find();
        $this->assign('list',$list);
        $this->display();
      }

      public function doEditservicepolicy(){
        $id=I('post.id');
        $db = M("policy");
        if($db->create()){
            $rt=$db->save();
             if($rt){
                $cLog = new \Admin\Controller\LogController();
                $cLog->setLog('单页（法律声明）修改成功');
                $this->success("修改成功",U("servicepolicy"));
                exit;
            }else{
                $this->error("修改失败",'servicepolicy');
            }
        }else{
            $this->error("修改失败",'servicepolicy');
        }
      }


    }
