<?php
namespace   Admin\Controller;
use         Think\Controller;

/**
 * 后台主页
 */
class IndexsController extends MyController {

    /**
     * 构造方法：对该模块进行一些初始化操作
     * 实现父类的构造方法，可对用户登录进行验证
     * 设置该控制器下共有的标题
     */
    public function __construct() {
        parent::__construct();
    }

    // 后台首页
    public function index() {
        $db = M("indexfocus");
        $list = $db->select();
         $this->assign("list",$list);
         $this->display('index');
    }

    //切换焦点图显示状态
    public function statusChange(){
        $id=I('get.id');
        $status=I('get.status');
        $db = M("indexfocus");
        if($status ==1){
        $data['status']=0;
        $res=$db->where(array('id'=>$id))->save($data);
        if($res){
            $this->ajaxReturn(0);
        }
        }elseif($status==0){
        $data['status']=1;
        $res=$db->where(array('id'=>$id))->save($data);
        if($res){
            $this->ajaxReturn(1);
        }
        }
    }

    //修改焦点图
    public function editFocus(){
        $id = I("get.id");
        $db = M("focus");
        $list = $db->find($id);
        $this->assign("data",$list);
        $this->display();
    }

    //多图片上传
    public function upload(){
        $upload = new \Think\Upload();
        $upload->maxSize = 3145728*2;
        $upload->exts    =array('jpg','gif','png','jpeg','JPG','GIF','PNG','JPEG');
         $upload->rootPath="Public/Focus/";
         $upload->savePath="";
         $info=$upload->upload();
         if(!$info) {
                  $this->error($upload->getError());
          }else{                                                 // 上传成功 获取上传文件信息
            $data=array();
             foreach($info as $file=>$v){
               $data[$file]= $v['savepath'].$v['savename'];
             }
             return $data;
         }
    }

    //单图片上传
    public function uploadone(){
        $upload = new \Think\Upload();
        $upload->maxSize = 3145728;
        $upload->exts    =array('jpg','gif','png','jpeg');
         $upload->rootPath="Public/Focus/";
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

    //执行修改保存
    public function editSave(){
       if($_POST['id'] ==1){
            if($_FILES['background']['name']!="" || $_FILES['img']['name'] !=""){
             $data=$this->upload();
            }
            if($_FILES['background']!=""){
                $_POST['background']=$data['background'];
            }
            if($_FILES['img']!=""){
                $_POST['img']=$data['img'];
            }
        }elseif($_POST['id'] ==2){
            if($_FILES['icon1']['name']!="" or $_FILES['icon2']['name']!="" or $_FILES['background']['name']!="" or $_FILES['img']['name']!=""){
            $data=$this->upload();
            }
            if($_FILES['icon1']!=""){
                $_POST['icon_one']=$data['icon1'];
            }
            if($_FILES['icon2']!=""){
                $_POST['icon_two']=$data['icon2'];
            }
            if($_FILES['background']!=""){
                $_POST['background']=$data['background'];
            }
            if($_FILES['img']!=""){
                $_POST['img']=$data['img'];
            }
       }elseif($_POST['id'] ==3){
            if($_FILES['background']['name']!=""){
            $_POST['background']=$this->uploadone();
            }
       }
        if(!$_POST['background']){
            unset($_POST['background']);
        }
        if(!$_POST['icon_one']){
            unset($_POST['icon_one']);
        }
        if(!$_POST['icon_two']){
            unset($_POST['icon_two']);
        }
        if(!$_POST['img']){
            unset($_POST['img']);
        }
        $mdl = M("focus");
        if($mdl->create()){

            $rt=$mdl->save();
            if($rt){
                $this->success("修改成功",U("Indexs/index"));
                exit;
            }
        }
        $this->error("修改失败");
    }

    public function aboutList(){
        $Model=M('index_about');
        $list=$Model->select();
        $this->assign('data',$list);
        $this->display();
    }

    //修改关于我们
    public function editAbout(){
        $id = I("get.id");
        $db = M("index_about");
        $list = $db->find($id);
        $this->assign("data",$list);
        $this->display();
    }

    public function aboutSave(){
        if($_FILES['img']['name']!=""){
            $_POST['img']=$this->uploadAbout();
        }
        // dump($_POST);
         $about = M("index_about");
        if($about->create()){

            $rt=$about->save();
            if($rt){
                $this->success("修改成功",U("Indexs/aboutList"));
                exit;
            }
        }
        $this->error("修改失败");
    }

    //单图片上传
    public function uploadAbout(){
        $upload = new \Think\Upload();
        $upload->maxSize = 3145728;
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
     * 新闻类别分类列表 Category news_cat and display news_cat
     */
    public function CategoryList(){
         $db = M("news_cat");
         $count = $db->count();
         $page = new \Think\Page($count,10);
         $list = $db -> limit($page->firstRow.','.$page->listRows)->select();
         //dump($list);
         $this->assign("list",$list);
         $show  = $page->show();
         $this->assign("page",$show);
         $this->display();
    }

     /**
     * 添加新闻类别 display adding news category
     */
    public function addNewsCategory(){
        $this->display();
    }

    /**
     * 执行添加新闻类别 display adding news category
     */
    public function addCategory(){
        $db = M("news_cat");
        if($db->create()){
            $res=$db->add();
            if($res){
                $cLog = new \Admin\Controller\LogController();
                $cLog->setLog('首页新闻类别（ID:' . $res . '）添加成功');
                $this->success("添加成功");
            }else{
                $this->error("添加失败");
            }
        }else{
             $this->error("添加失败");
        }
    }

    /**
     * 修改新闻类别 editCategory as
     */
    public function editCategory(){
        $id = $_GET['id'];
        $data = M("news_cat");
        $list = $data->where("id =".$id)->find();
        //dump($id);
        //dump($list);
        $this->assign("list",$list);
        $this->display();
    }

     /**
     * 执行修改新闻类别
     */
    public function categoryUpdate(){
        $db = M("news_cat");
        $id=I('post.id');
        if($db->create()){
            $rt=$db->save();
             if($rt){
                $cLog = new \Admin\Controller\LogController();
                $cLog->setLog('首页新闻类别（ID:' . $id . '）修改成功');
                $this->success("修改成功",U("Indexs/categoryList"));
                exit;
            }
        }else{
            $this->error("修改失败");
        }
    }

    /**
     * 删除新闻类别
     */
    public function delCategory(){
        $id = $_GET['id'];
        $db = M("news_cat");
        $ls = $db->where("id =".$id)->delete();

        if($ls){
            $cLog = new \Admin\Controller\LogController();
            $cLog->setLog('首页新闻类别（ID:' . $id . '）删除成功');
            $this->success("删除成功",U("Indexs/categoryList"));
        }else{
            $this->error("删除失败");
        }
    }

    public function del(){
        $ids = array();
        $ids = $_GET['id'];
        $data = implode(",",$ids);
        $db = M("news_cat");
        $list= $db ->where("id in (".$data.")")->delete();
        if($list){
            echo 1;
        }else{
            echo 0;
        }
    }

    /**
     * 添加新闻
     */
    public function addNews(){
        $db = M("news_cat");
        $list = $db ->select();
        // dump($list);
        $this->assign("list",$list);

        $this->display();
    }

    /**
     * 执行添加新闻
     */
    public function doAddNews(){
        $_POST['thumbnail']=$this->uploadNews();
        $_POST['create_time']= time();


        $db = M("news_contents");
        if($db->create()){
            $res=$db->add();
            if($res){
                $cLog = new \Admin\Controller\LogController();
                $cLog->setLog('首页新闻（ID:' . $res . '）添加成功');
                $this->success("添加成功",U("Indexs/newsManager"));
            }else{
                $this->error("添加失败");
            }
        }else{
             $this->error("添加失败");
        }
    }

    /**
     * 单图片上传
     */
    public function uploadNews(){
        $upload = new \Think\Upload();
        $upload->maxSize = 3000000;
        $upload->exts    =array('jpg','gif','png','jpeg');
         $upload->rootPath="Public/News/";
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
     * 修改新闻
     */
    public function editNews(){
        $id = $_GET['id'];
        $db = M("news_cat");
        $listCat = $db ->select();
        $data = M("news_contents");
        $list = $data->where("id =".$id)->find();
        //dump($id);
        //dump($list);
        $this->assign("list",$list);
        $this->assign("listCat",$listCat);
        $this->display();
    }

    /**
     * 新闻List
     */
    public function newsManager(){

        $db = M("news_contents");
        $count = $db->count();
        $page = new \Think\Page($count,10); 
        $list = $db -> limit($page->firstRow.','.$page->listRows)->select();
        //dump($list);
        $this->assign("list",$list);
        $show  = $page->show();
        $this->assign("page",$show);
        $this->display();

    }

    /**
     * 执行修改新闻
     */
    public function updateNews(){
        if($_FILES['file']['name']){
            $_POST['thumbnail']=$this->uploadNews();
        }
        $id=I('post.id');
        $db = M("news_contents");
        if($db->create()){
            $rt=$db->save();
             if($rt){
                $cLog = new \Admin\Controller\LogController();
                $cLog->setLog('首页新闻（ID:' . $id . '）修改成功');
                $this->success("修改成功",U("Indexs/newsManager"));
                exit;
            }else{
                $this->error("修改失败");
            }
        }else{
            $this->error("修改失败");
        }
    }

    /**
     * 删除新闻
     */
    public function delNews(){
        $id = $_GET['id'];
        $db = M("news_contents");
        $ls = $db->where("id =".$id)->delete();

        if($ls){
            $cLog = new \Admin\Controller\LogController();
            $cLog->setLog('首页新闻（ID:' . $id . '）删除成功');
            $this->success("删除成功",U("Indexs/newsManager"));
        }else{
            $this->error("删除失败");
        }
    }

    /**
     * 友情链接列表
     */
    public function linksManager(){

         $db = M("links");
         $count = $db->count();
         $page = new \Think\Page($count,10); 
         $list = $db -> limit($page->firstRow.','.$page->listRows)->select();
         //dump($list);
         $this->assign("list",$list);
         $show  = $page->show();
         $this->assign("page",$show);
         $this->display();
    }

    /**
     * 添加友情链接
     */
    public function addLinks(){
        $this->display();
    }

    /**
     * 执行添加友情链接
     */
    public function doAddLinks(){
        //dump($_POST);
        //dump($_FILES);
         if($_FILES['file']['name']){
             $picture=$this->uploadLinks();
        }else{
             $this->error("请上传链接图片");
        }
        $db = M("links");
        //dump($picture);
        $_POST['picture']=$picture;

        if($db->create()){
            $res=$db->add();
            if($res){
                $cLog = new \Admin\Controller\LogController();
                $cLog->setLog('首页友情链接（ID:' . $res . '）添加成功');
                $this->success("添加成功",U("Indexs/linksmanager"));
            }else{
                $this->error("添加失败");
            }
        }else{
            $this->error("添加失败");
        }      
    }

    
    /**
     * 单图片上传
     */
    public function uploadLinks(){
        $upload = new \Think\Upload();
        $upload->maxSize =30000;
        $upload->exts    =array('jpg','gif','png','jpeg');
        $upload->rootPath="Public/Links/"; 
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
     * 修改友情链接
     */
    public function editLinks(){
        $id = $_GET['id'];
        $data = M("links");
        $list = $data->where("id =".$id)->find();       
        //dump($id);
        //dump($list);
        $this->assign("list",$list);
        $this->display();
    }

    /**
     * 执行修改友情链接
     */
    public function doEditLinks(){
         if($_FILES['file']['name']){
             $picture=$this->uploadLinks();
             $_POST['picture']=$picture;
        }
        $id=I('post.id');
        $db = M("links");
        if($db->create()){
            $rt=$db->save();
             if($rt){
                $cLog = new \Admin\Controller\LogController();
                $cLog->setLog('首页友情链接（ID:' . $id . '）修改成功');
                $this->success("修改成功",U("Indexs/linksmanager"));
                exit;
            }
        }else{
            $this->error("修改失败");
        }
    }

    /**
     * 删除友情链接
     */
    public function delLinks(){
        $id = $_GET['id'];
        $db = M("links");
        $ls = $db->where("id =".$id)->delete();

        if($ls){
            $cLog = new \Admin\Controller\LogController();
            $cLog->setLog('首页友情链接（ID:' . $id . '）删除成功');
            $tis->success("删除成功",U("Indexs/linksmanager"));
        }else{
            $this->error("删除失败");
        }
    }


    /**
     * 产品列表
     */
    public function goodsList(){
        $Model=M('index_goods');
        $count = $Model->count();
        $page = new \Think\Page($count,10);
        $list = $Model -> limit($page->firstRow.','.$page->listRows)->select();        
        $this->assign("list",$list);
        $show  = $page->show();
        $this->assign("page",$show);
        $this->display();
    }

    /**
     * 产品添加
     */
    public function addGoods(){
        $this->display();
    }

    public function doAddGoods(){
        $post = I('post.');
        $post['pro_name']=trim($post['pro_name']);
        if(empty($post['pro_name'])){
            $this->error('产品名称不能为空，添加失败！');
            exit;
        }
        $post['add_time'] = time();
        $post['update_time'] = time();
        $modelGoods = M('index_goods');
        if ($modelGoods->create($post)) {
            $getGoodsId = $modelGoods->add($post);
            if (!$getGoodsId) {
                $this->error('商品添加失败');
                exit;
            }else{
                $albumData = array();
                foreach ($post['imgs'] as $key => $val) {
                    $albumData[$key]['good_id'] = $getGoodsId;
                    $albumData[$key]['images'] = $val;
                    }
                $modelAlbum = M('index_album');
                $modelAlbum->addAll($albumData);
                if ($getGoodsId) {
                // 写入日志
                $cLog = new \Admin\Controller\LogController();
                $cLog->setLog('首页产品（ID:' . $getProductId . '）添加成功');
                $this->success('首页产品添加成功', 'goodsList');
                exit;
             }
            }
        }else{
            $this->error('上传出错。');
        }

    }

    /**
     * 上传图片
     *产品上传缩略图所使用
     */
    public function uploadGoods(){
        $upload = new \Think\Upload();  // 实例化上传类
        $upload->maxSize = 3145728;             // 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');   // 设置附件上传类型
        $upload->rootPath = "Public/Index/Goods/";
        $upload->savePath = '';                         // 设置附件上传目录    // 上传文件
        $info = $upload->upload();
        $src=$info['Filedata']['savepath'] . $info['Filedata']['savename'];
        $this->ajaxReturn($src);
    }

    /**
     * 图片上传操作，此方法用于多文件上传插件，返回Ajax字符串
     * 此方法被产品图片上传使用
     */
    public function goodsUpload(){
        $upload = new \Think\Upload();  // 实例化上传类
        $upload->maxSize = 3145728*6;             // 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');   // 设置附件上传类型
        $upload->rootPath = "Public/Index/Goods/";
        $info = $upload->upload();
        $src =$info['Filedata']['savepath'] . $info['Filedata']['savename'];
        $this->ajaxReturn($src);
    }

    /**
     * 产品修改
     */
    public function editGood(){
        $getGoodId = I('get.id');
        // 获取产品详细信息
        $modelGoods = M('index_goods');
        $productRes = $modelGoods->find($getGoodId);
        $modelImg=M('index_album');
        $imgs=$modelImg->where(array('good_id'=>$getGoodId))->select();
        $this->assign('good',$productRes);
        $this->assign('imgs',$imgs);
        $this->display();
    }

    /**
     * 执行产品修改
     */
    public function doEditGood(){
        $getId = I('post.id');
        $post=I('post.');
        $post['pro_name']=trim($post['pro_name']);
        if (empty($post['pro_name'])) {
            $this->error('商品更改无效');
            exit;
        }
        $post['update_time'] = time();
        $modelGoods = M('index_goods');
        $updateGoods = 0;
        if ($modelGoods->create($post)) {
            $updateGoods = $modelGoods ->where(array('id'=>$getId))->save();
        }
        $data=array();
        if($post['imgs']){
            $mAffectedRows = 0;
            foreach($post['imgs'] as $k=>$v){
                $data[$k]['good_id']=$getId;
                $data[$k]['images']=$v;
            }
        $modelImg=M('index_album');
        $modelImg->where(array('good_id'=>$getId))->delete();
        $mAffectedRows = $modelImg->addAll($data);
        }else{
            $mAffectedRows = 1;
        }
        if ($mAffectedRows || $updateGoods) {
            // 写入日志
            $cLog = new \Admin\Controller\LogController();
            $cLog->setLog('产品（ID:' . $getId . '）修改成功');
            $this->success('产品修改成功','goodsList');
            exit;
        } else {
            $this->error('商品添加失败');
        }
    }

    /**
     * 删除产品
     */
    public function delGood(){
        $getId = I('get.id');
        $modelGoods = M('index_goods');
        $affectedRows = $modelGoods->where(array('id'=>$getId))->delete();
        $modelImg=M('index_album');
        $mAffectedRows=$modelImg->where(array('good_id'=>$getId))->delete();
        if ($affectedRows and $mAffectedRows) {
            // 写入日志
            $cLog = new \Admin\Controller\LogController();
            $cLog->setLog('产品（ID:' . $getId . '）已成功删除');
            $this->success('产品已删除');
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 博文管理
     */
    public function blogList(){
        $db=M('blog');
        $count = $db->count();
        $page = new \Think\Page($count,10); 
        $list = $db -> limit($page->firstRow.','.$page->listRows)->select();
        $this->assign("list",$list);
        $show  = $page->show();
        $this->assign("page",$show);
        $this->display();
    }

    /**
     * 博文添加
     */
    public function addBlog(){
        $this->display();
    }

    /**
     * 执行博文添加
     */
    public function doAddBlog(){
        $post = I('post.');
        // dump($post);
        // die;
        $post['title']=trim($post['title']);
        if(empty($post['title'])){
            $this->error('博文名称不能为空，添加失败！');
            exit;
        }
        $post['create_time'] = time();
        $modelBlogs = M('blog');
        if ($modelBlogs->create($post)) {
            $getBlogId = $modelBlogs->add($post);
            if (!$getBlogId) {
                $this->error('商品添加失败');
                exit;
            }else{
                $albumData = array();
                $modelAlbum = M('blog_album');
                if($post['type']==1){
                foreach ($post['imgs'] as $key => $val) {
                    $albumData[$key]['blog_id'] = $getBlogId;
                    $albumData[$key]['images'] = $val;
                    }
                $modelAlbum->addAll($albumData);
                }elseif($post['type']==2){
                    $albumData['blog_id']=$getBlogId;
                    $albumData['video']=$post['video'];
                    $modelAlbum->add($albumData);
                    }
                if ($getBlogId) {
                // 写入日志
                $cLog = new \Admin\Controller\LogController();
                $cLog->setLog('首页博文（ID:' . $getBlogId . '）添加成功');
                $this->success('首页博文添加成功', 'blogList');
                exit;
             }
            }
        }else{
            $this->error('上传出错。');
        }
    }

    /**
     * 删除博文
     */
    public function delBlog(){
        $id = $_GET['id'];
        $db = M("blog");
        $ls = $db->where("id =".$id)->delete();
        $album=M('blog_album');
        $ls2=$album->where(array('blog_id'=>$id))->delete();
        if($ls and $ls2){
            $cLog = new \Admin\Controller\LogController();
            $cLog->setLog('首页博文（ID:' . $id . '）删除成功');
            $this->success("删除成功",U("Indexs/blogList"));
        }else{
            $this->error("删除失败");
        }
    }

    /**
     * 博文修改
     */
    public function editBlog(){
        $getBlogId = I('get.id');
        // 获取产品详细信息
        $modelBlog = M('blog');
        $Res = $modelBlog->find($getBlogId);
        $modelImg=M('blog_album');
        $album=$modelImg->where(array('blog_id'=>$getBlogId))->select();
        $this->assign('blog',$Res);
        $this->assign('album',$album);
        $this->display();
    }

    /**
     * 执行博文修改
     */
    public function doEditBlog(){
        $getId = I('post.id');
        $post=I('post.');
        $post['title']=trim($post['title']);
        if (empty($post['title'])) {
            $this->error('博文更改无效');
            exit;
        }
        $post['create_time'] = time();
        $modelBlog = M('blog');
        $updateBlog = 0;
        if ($modelBlog->create($post)) {
            $updateBlog = $modelBlog ->where(array('id'=>$getId))->save();
        }
        $data=array();
        if($post['imgs']){
            $mAffectedRows = 0;
            foreach($post['imgs'] as $k=>$v){
                $data[$k]['blog_id']=$getId;
                $data[$k]['images']=$v;
            }
        $modelImg=M('blog_album');
        $modelImg->where(array('blog_id'=>$getId))->delete();
        $mAffectedRows = $modelImg->addAll($data);
        }else{
            $mAffectedRows = 1;
        }
        if($post['video']){
        $modelImg=M('blog_album');
        $data['blog_id']=$getId;
        $data['video']=$post['video'];
        $mAffectedRows2 = $modelImg->where(array('blog_id'=>$data['blog_id']))->save($data);
        }
        if ($mAffectedRows || $updateGoods || $mAffectedRows2) {
            // 写入日志
            $cLog = new \Admin\Controller\LogController();
            $cLog->setLog('首页博文（ID:' . $getId . '）修改成功');
            $this->success('首页博文修改成功','blogList');
            exit;
        } else {
            $this->error('博文修改失败');
        }
    }

    /**
     * 图片上传操作，此方法用于多文件上传插件，返回Ajax字符串
     * 此方法被多图文博文图片上传使用
     */
    public function blogUpload(){
        $upload = new \Think\Upload();  // 实例化上传类
        $upload->maxSize = 3145728*6;             // 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');   // 设置附件上传类型
        $upload->rootPath = "Public/Index/Blog/";
        $info = $upload->upload();
        $src =$info['Filedata']['savepath'] . $info['Filedata']['savename'];
        $this->ajaxReturn($src);
    }


    /**
     * 上传图片
     *首页博文封面图上传
     */
    public function uploadBlog(){
        $upload = new \Think\Upload();  // 实例化上传类
        $upload->maxSize = 3145728;             // 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');   // 设置附件上传类型
        $upload->rootPath = "Public/Index/Blog/";
        $upload->savePath = '';                         // 设置附件上传目录    // 上传文件
        $info = $upload->upload();
        $src=$info['Filedata']['savepath'] . $info['Filedata']['savename'];
        $this->ajaxReturn($src);
    }
}