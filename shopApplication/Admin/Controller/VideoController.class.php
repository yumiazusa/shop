<?php
/**
 * Created by PhpStorm.
 * User: YJG
 * Date: 2015/1/14
 * Time: 1:55
 */
namespace Admin\Controller;

use Think\Controller;

class VideoController extends MyController
{
    public function __construct()
    {
        parent::__construct();
    }

    //获得视频信息
    public function setVideo()
    {

        if (isset($_GET['pid'])) {
            $da = M('support_video');
            $option = $da->where('pid=0')->select();
            $db = M('support_video');

            $condition['pid'] = $_GET['pid'];
            if (!empty($_GET['title'])) {
                $condition['title'] = array('like', "%" . $_GET['title'] . "%");

            }
            $count = $db->where($condition)->count();
            $page = new \Think\Page($count, 10);
            $map['pid'] = $_GET['pid'];
            $map['title'] = $_GET['title'];
            foreach ($map as $key => $val) {
                $page->parameter .= "$key=" . urlencode($val) . '$';
            }
            $show = $page->show();
            $list = $db->where($condition)->order('id')->limit($page->firstRow . ',' . $page->listRows)->select();
            $this->assign('option', $option);
            $this->assign('list', $list);
            $this->assign("page", $show);
            $this->display();
            exit;
        } else {
            $da = M('support_video');
            $option = $da->where('pid=0')->select();
            $db = M('support_video');
            $count = $db->where('pid>0')->count();
            $page = new \Think\Page($count, 10);
            $show = $page->show();
            $list = $db->where('pid>0')->order('id')->limit($page->firstRow . ',' . $page->listRows)->select();
            $this->assign('option', $option);
            $this->assign('list', $list);
            $this->assign("page", $show);
            $this->display();
        }

    }

    //视频信息
    public function infoVideo()
    {
        $da = M('support_video');
        $option = $da->where('pid=0')->select();
        $id = $_GET['id'];
        $db = M('support_video');
        $data = $db->where("id = $id")->find();
        $this->assign('data', $data);
        $this->assign('option', $option);
        $this->display();
    }

    //修改视频信息
    public function modifyVideo()
    {

        $db = M('support_video');

        $g = $_POST;    //获取post表单的值
        $id = $g['id'];    //获取要修改的id
        $old = $db->where("id=$id")->find();    //获取修改钱的信息
        $old_simg = $old['simg'];       //老图片地址
        $old_bimg = $old['bimg'];
        $old_url = $old['url'];


        //组合post的数据
        foreach ($g as $key => $value) {
            $data[$key] = $value;
        }

        //如果长传了图片
        if (isset($_FILES)) {
            //如果上传了小图

            if (($_FILES['simg']['error']) == 0) {
                //删除老的小图
                unlink('Public/Home' . $old_simg);


                $upload = new \Think\Upload();// 实例化上传类
                $upload->maxSize = 3145728;// 设置附件上传大小
                $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                $upload->rootPath = 'Public/Home';
                $upload->savePath = '/move/simg/'; // 设置附件上传目录
                // 上传单个文件
                $info = $upload->uploadOne($_FILES['simg']);
                if (!$info) {
                    // 上传错误提示错误信息
                    //$this->error($upload->getError());
                } else {
                    // 上传成功 获取上传文件信息,组合到data集合
                    $data['simg'] = $info['savepath'] . $info['savename'];

                }

            }
            if (($_FILES['bimg']['error']) == 0) {

                unlink('Public/Home' . $old_bimg);


                $upload = new \Think\Upload();// 实例化上传类
                $upload->maxSize = 3145728;// 设置附件上传大小
                $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                $upload->rootPath = 'Public/Home';
                $upload->savePath = '/move/bimg/'; // 设置附件上传目录
                $info = $upload->uploadOne($_FILES['bimg']);
                if (!$info) {
                    // 上传错误提示错误信息
                    // $this->error($upload->getError());
                } else {
                    // 上传成功 获取上传文件信息
                    $data['bimg'] = $info['savepath'] . $info['savename'];
                }

            }
            if (($_FILES['url']['error']) == 0) {

                unlink('Public/Home' . $old_url);

                $upload = new \Think\Upload();// 实例化上传类
                $upload->maxSize = 9983145728;// 设置附件上传大小
                $upload->exts = array('mp4', 'swf', 'flv');// 设置附件上传类型
                $upload->rootPath = 'Public/Home';
                $upload->savePath = '/move/'; // 设置附件上传目录
                // 上传单个文件
                $info = $upload->uploadOne($_FILES['url']);
                if (!$info) {
                    // 上传错误提示错误信息
                    // $this->error($upload->getError());
                } else {
                    // 上传成功 获取上传文件信息
                    $data['url'] = $info['savepath'] . $info['savename'];
                }

            }

        }

        //提交保存
        $row = $db->where("id=$id")->save($data);

        if ($row > 0) {
            $this->success('修改成功',"setVideo");
        } else {
            $this->error('修改失败');
        }
    }


    //修改类别
    public function modifyType()
    {
        $db = M('support_video');
        if (isset($_POST['sub'])) {

            $id = $_POST['id'];
            $data['status'] = $_POST['status'];
            $title = $_POST['title'];
            if($title !== ''){
                $data['title'] = $title;
            }

            $row = $db->where("id=$id")->save($data);
            if ($row > 0) {
                $this->success('修改成功',"setVideo");
            } else {
                $this->error('修改失败');
            }
            exit();
        }
        $db = M('support_video');
        $row = $db->where('pid = 0')->select();
        $this->assign('data', $row);
        $this->display();
    }

    //隐藏
    public function hiddenVideo()
    {
        $id = $_POST['id'];
        $status = $_POST['status'];
        $db = M('support_video');
        if ($status == 1) {
            $data['status'] = 0;
        } else if ($status == 0) {
            $data['status'] = 1;
        }

        $row = $db->where("id=$id")->save($data);
        echo $row;
    }

    //删除单个文件
    public function delVideo()
    {
        $id = $_GET['id'];
        $db = M('support_video');
        $row = $db->where("id=$id")->find();
        $simg = $row['simg'];
        $bimg = $row['bimg'];
        $url = $row['url'];

        //删除文件

        unlink('Public/Home' . $simg);
        unlink('Public/Home' . $bimg);
        unlink('Public/Home' . $url);
        //删除数据
        $row = $db->where("id=$id")->delete();
        if ($row > 0) {
            $this->success('删除成功',"setVideo");
        } else {
            $this->error('删除失败');
        }


    }

    //添加类别
    public function addType()
    {
        if (isset($_POST['title'])) {
            $data['title'] = $_POST['title'];
            $data['pid'] = 0;
            $data['path'] = 0;
            $db = M('support_video');
            $row = $db->add($data);
            if ($row > 0) {
                $this->success('添加成功',"setVideo");
            } else {
                $this->error('添加失败');
            }
            exit;
        }
        $db = M('support_video');
        $data = $db->where('pid=0')->select();
        $this->assign('data', $data);
        $this->display();
    }

    //添加视频
    public function addVideo()
    {

        if (isset($_POST['title'])) {
            $simg = '';

            if (isset($_FILES)) {
                if (($_FILES['simg']['error']) == 0) {
                    $upload = new \Think\Upload();// 实例化上传类
                    $upload->maxSize = 3145728;// 设置附件上传大小
                    $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                    $upload->rootPath = 'Public/Home';
                    $upload->savePath = '/move/simg/'; // 设置附件上传目录
                    // 上传单个文件
                    $info = $upload->uploadOne($_FILES['simg']);
                    if (!$info) {
                        // 上传错误提示错误信息
                        $this->error($upload->getError());
                    } else {
                        // 上传成功 获取上传文件信息
                        $simg = $info['savepath'] . $info['savename'];
                    }

                }


            }
            $d = $_POST;

            foreach ($d as $key => $value) {
                $data[$key] = $value;
            }
            $data['simg'] = $simg;
            $data['path'] = '0-' . $_POST['pid'];
            $db = M('support_video');
            $row = $db->add($data);
            if ($row > 0) {
                $this->success('添加成功',"setVideo");
            } else {
                $this->error('添加失败');
            }
            exit;
        }

        $db = M('support_video');
        $data = $db->where('pid=0')->select();
        $this->assign('data', $data);
        $this->display();
    }

    //删除全部
    function delAll()
    {
        //id=5
        $id = $_GET['id'];
        $db = M('support_video');
        $row = $db->where("pid=$id")->select();


        foreach ($row as $key => $value) {
            foreach ($value as $k => $v) {
                unlink('Public/Home' . $v);
            }

        }
        $del1 = $db->where("pid=$id")->delete();

        $del = $db->where("id=$id")->delete();
        if ($del > 0) {
            $this->success('删除成功',"setVideo");
        } else {
            $this->error('删除失败');
        }

    }
}
