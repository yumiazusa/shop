<?php


namespace Admin\Controller;
use Think\Controller;

class JobController extends MyController{

    public function __construct ()
    {
        parent::__construct();
    }

    //显示 job 列表
    public function index(){

        $db = M('jobs');
        $dc = M('job_cat');
        $count = $db->count();    //获取总页数
        $page = new \Think\Page($count, 10);
        $showPage = $page->show();
        $res = $db->order('id ASC')->limit($page->firstRow . ',' . $page->listRows)->select();
        foreach ($res as &$val) {
            $cat_id = $val['cat_id'];
            $result = $dc -> where("id = $cat_id") -> find();
            $val['cat_name'] = $result['cat_name'];
        }

        $this -> assign('data',$res);
        $this -> assign('page',$showPage);

        $this -> display();
    }


    //分类首页
    public function catIndex(){
        $db = M('job_cat');
        $dc = M('jobs');
        $list = $db -> select();
        foreach($list as &$li){
            $id = $li['id'];
            $count = $dc -> where("cat_id = $id") -> count();
            $li['count'] = $count;
        }

        $this -> assign('data',$list);
        $this -> display();

    }

    //查看分类下信息---------------
    public function catShow(){
        $db = M('jobs');
        $dc = M('job_cat');
        $id = $_GET['id'];
        $count = $db->where("cat_id = $id")->count();    //获取总页数
        $page = new \Think\Page($count, 10);
        $showPage = $page->show();
        $res = $db->where("cat_id = $id")->order('id ASC')->limit($page->firstRow . ',' . $page->listRows)->select();
        foreach ($res as &$val) {
            $cat_id = $val['cat_id'];
            $result = $dc -> where("id = $cat_id") -> find();
            $val['cat_name'] = $result['cat_name'];
        }

        $this -> assign('data',$res);
        $this -> assign('page',$showPage);

        $this -> display('index');
    }


    //添加类别
    public function addCat(){
        $db = M('job_cat');

        if(isset($_POST['sub'])){

            $post['cat_name'] = $_POST['cat_name'];
            $post['cat_desc'] = $_POST['cat_desc'];
            $post['is_show'] = 0;
            $result = $db -> add($post);
            if($result>0){
                $this -> success('添加成功!','index');
            }else{
                $this -> error('添加失败!','index');
            }
            exit;
        }
        $list = $db -> select();
        $this -> assign('data',$list);
        $this -> display();
    }

    //修改某类别
    public function editCat(){
        $db = M('job_cat');
        $id = $_GET['id'];

        if (isset($_POST['sub'])) {

            unset($_POST['sub']);

            //如果没填写新的类名,使用旧的
            if($_POST['cat_name'] ==''){
                $_POST['cat_name'] = $_POST['oldcat'];
            }

            $row = $db->save($_POST);
            if ($row > 0) {
                $this->success('修改成功','index');
            } else {
                $this->error('修改失败','index');
            }
            exit;
        }

        $data = $db -> where("id = $id") -> find();
        $this -> assign('data',$data);

        $this->display();

    }



    //删除某个类别
    public function catDel(){
        $db = M('jobs');
        $dc = M('job_cat');
        $id = $_POST['id'];
        $db -> where("cat_id = $id") -> delete();
        $rows = $dc -> where("id = $id") -> delete();
        echo $rows;
    }

    //删除某个工作
    public function jobDel(){
        $db = M('jobs');
        $id = $_POST['id'];
        $row = $db -> where("id = $id") -> delete();
        echo $row;
    }


    //添加工作
    public function addJob(){
        $db = M('job_cat');
        $cat_list = $db -> select();
        $this -> assign('cat_list',$cat_list);

        if(isset($_POST['sub'])){
            $post = $_POST;

            foreach($post as $k =>$v){
                $data[$k] = $v;
            }

            $data['content'] = $data['contents'];
            $data['add_time'] = time();
            unset($data['sub']);
            unset($data['contents']);

            $dc = M('jobs');
            $result = $dc ->data($data)->add();
            if($result>0){
                $this -> success('添加成功!','index');

            }else{
                $this -> error('添加失败!','index');
            }
            exit;
        }


        $this -> display();
    }

    //编辑工作信息
    public function editJob(){
        $db = M('jobs');
        $id = $_GET['id'];
        $dc = M('job_cat');
        $cat_list = $dc -> select();
        $data = $db -> where("id = $id") -> find();
        $this -> assign('cat_list',$cat_list);
        $this -> assign('data',$data);

        $this -> display();
    }

    //执行编辑工作信息
    public function doeditJob(){
        if(isset($_POST['sub'])){
            unset($_POST['sub']);
            $db = M('jobs');
            $row = $db->where(array('id'=>$_POST['id']))-> save($_POST);

            if($row>0){
                $this -> success('更新成功!','index');
            }else{
                $this -> error('更新失败!','index');
            }
            exit;
        }
    }



    //求职首页
    public function applyIndex(){

        $db = M('job_applies');
        $dc = M('jobs');
        $count = $db->count();    //获取总页数
        $page = new \Think\Page($count, 10);
        $showPage = $page->show();
        $res = $db->order('id ASC')->limit($page->firstRow . ',' . $page->listRows)->select();
        foreach($res as &$r){
            $id = $r['job_name'];
            $n = $dc -> where("id = $id") -> find();
            $r['job_name'] = $n['job_name'];
        }
        $this -> assign('data',$res);
        $this -> assign('page',$showPage);
        $this -> display();
    }

    //删除求职信息
    public function applyDel(){
        $db = M('job_applies');
        $id = $_POST['id'];
        $row = $db -> where("id = $id") -> delete();
        echo $row;
    }

    //查看求职信息
    public function applyInfo(){
        $db = M('job_applies');
        $id = $_GET['id'];
        $row = $db -> where("id = $id") -> find();
        $this -> assign('data',$row);

        $this -> display();
    }
}
