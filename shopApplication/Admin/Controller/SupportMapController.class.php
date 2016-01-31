<?php

namespace Admin\Controller;

use Think\Controller;

class SupportMapController extends MyController
{

    public function __construct()
    {
        parent::__construct();
    }

    //显示所有的地址详细
    public function index()
    {
        //分页查询
        if (isset($_GET['pid'])) {
            $da = M('support_map');
            $option = $da->where('pid=0')->select();
            $db = M('support_map');

            $condition['pid'] = $_GET['pid'];
            if (!empty($_GET['name'])) {
                $condition['name'] = array('like', "%" . $_GET['name'] . "%");

            }

            $count = $db->where($condition)->count();
            $page = new \Think\Page($count, 10);
            $map['pid'] = $_GET['pid'];
            $map['name'] = $_GET['name'];
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

            //页面加载时候显示
            $da = M('support_map');
            $option = $da->where('pid=0')->select();
            $db = M('support_map');
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

    //area 修改地区(省)
    public function modifyArea()
    {
        $db = M('support_map');
        if (isset($_POST['sub'])) {
            $id = $_POST['id'];
            $data['status'] = $_POST['status'];
            $title = $_POST['title'];
            if ($title !== '') {
                $data['name'] = $title;
            }

            $row = $db->where("id=$id")->save($data);
            if ($row > 0) {
                $this->success('修改成功','index');
            } else {
                $this->error('修改失败','index');
            }
            exit();
        }
        $db = M('support_map');
        $row = $db->where('pid = 0')->select();
        $this->assign('data', $row);
        $this->display();
    }

    //删除地区及下面所以地址
    //删除全部
    function delAll()
    {
        //id=5
        $id = $_GET['id'];
        $db = M('support_map');
        $del1 = $db->where("pid=$id")->delete();
        $del = $db->where("id=$id")->delete();
        if ($del > 0) {
            $this->success('删除成功','index');
        } else {
            $this->error('删除失败','index');
        }

    }

    //添加地区
    public function addArea()
    {
        if (isset($_POST['name'])) {
            $data['name'] = $_POST['name'];
            $data['pid'] = 0;
            $data['path'] = 0;
            $db = M('support_map');
            $row = $db->add($data);
            if ($row > 0) {
                $this->success('添加成功','index');
            } else {
                $this->error('添加失败','index');
            }
            exit;
        }
        $db = M('support_map');
        $data = $db->where('pid=0')->select();
        $this->assign('data', $data);
        $this->display();
    }


    /**
     * 添加地址
     */
    function addAddress()
    {

        if (isset($_POST['name'])) {

            $d = $_POST;

            foreach ($d as $key => $value) {
                $data[$key] = $value;
            }

            //$data['path'] = '0-' . $_POST['pid'];
            $db = M('support_map');
            $row = $db->add($data);
            if ($row > 0) {
                $this->success('添加成功','index');
            } else {
                $this->error('添加失败','index');
            }
            exit;
        }

        $db = M('support_map');
        $data = $db->where('pid=0')->select();
        $this->assign('data', $data);
        $this->display();
    }

    public function modifyAddress()
    {
        $da = M('support_map');
        $option = $da->where('pid=0')->select();
        $id = $_GET['id'];
        $db = M('support_map');
        $data = $db->where("id = $id")->find();
        $this->assign('data', $data);
        $this->assign('option', $option);
        $this->display();
    }

    public function modify()
    {
        $db = M('support_map');
        $tmp = $_POST;
        $id = $_POST['id'];
        $row = $db -> where("id = $id") -> save($tmp);
        if ($row > 0) {
            $this->success('添加成功','index');
        } else {
            $this->error('添加失败','index');
        }
        exit;
    }

    public function delAddress(){
        $id = $_GET['id'];
        $db = M('support_map');

        //删除数据
        $row = $db->where("id=$id")->delete();
        if ($row > 0) {
            $this->success('删除成功','index');
        } else {
            $this->error('删除失败','index');
        }
    }
}

