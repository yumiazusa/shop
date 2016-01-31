<?php
namespace Home\Controller;
use  Think\Controller;
use Think\Area;

class MemberController extends MyController{
    public function __construct(){
           parent::__construct();
    }
    //会员首页
    public function member(){
         //SEO
        $ident ="member";
        $idents =$this->seo($ident);
        $this->assign("title",$idents['title']);
        $this->assign("keywords",$idents['keywords']);
        $this->assign("description",$idents['description']);
        $user = $_SESSION['user'];
        if ($user != "") {
            $user_id =$_SESSION['user']['userid'];
            $user_info =M("users");
            $users = M("user_info");
            $order = M("orders");
            $product = M("product");
            $ulist = $user_info->field("mobile_state,email_state,uname")->where("id =".$user_id)->find();
            $uimg = $users->field("thumb")->where("user_id =".$user_id)->find();

            //查寻待付款的订单
            $count = $order->where("order_status = 1 and user_id = {$user_id}")->count();
            $this->assign("order",$count);

            //查询处理中的订单(等待发货和已发货的)
            $orderlist = $order->where("order_status in (2,3) and user_id ={$user_id}")->count();
            //投诉与意见的站内信

            $feedBack = M("feedback_replies");
            $map['reply_uid'] =array("eq",$user_id);
            $map['status'] =array("eq",0);
            $feed = $feedBack->where($map)->count();
            $this->assign("feed",$feed);
            //查询浏览次数高的商品
            $productlist = $product->order("click_times desc")->limit(4)->select();
            $this->assign("product",$productlist);
            $this->assign("orderlist",$orderlist);
            $this->assign("uimg",$uimg);
            $this->assign("ulist",$ulist);
            $this->display();
        }else {
            $this->redirect("Login/login");
        }
    }
    //修改信息状态
    public function modstatus(){
        $uid = $_SESSION['user']['userid'];
        $feedBack = M("feedback_replies");
        //修改回复状态改为已读
        $data['status'] =1;
         $rt = $feedBack->where("reply_uid = ".$uid)->save($data);
            if($rt){
                echo 1;
                exit;
            }else{
                echo 2;
                exit;
            }
        }

    //个人基本信息
    public function information()
    {
        //SEO
        $ident ="member";
        $idents =$this->seo($ident);
        $this->assign("title",$idents['title']);
        $this->assign("keywords",$idents['keywords']);
        $this->assign("description",$idents['description']);


        $id = $_SESSION['user']['userid'];

        $db = M('user_info');
        $dc = M('users');

        $info = $db->where("user_id = $id")->find();
        $users = $dc->where("id = $id")->find();

        //三级联动
        if($info['province'] || $info['city'] || $info['area']){
          $city = array($info['province'], $info['city'], $info['area']);
        }else{
          $city = array("--省--","--市--","--县--"); 
        }
        $c = Area::city($city);
        $this->assign("city", $c);
        $this->assign('info', $info);
       
        $this->assign('users', $users);

        $this->display();
    }

    //保存个人信息
    public function saveInformation()
    {
        $id = $_SESSION['user']['userid'];
        $db = M('user_info');
        $dc = M('users');

        $d = $_POST;
        $uname['uname'] = $d['uname'];

        $time = $d['birthday_year'] . '-' . $d['birthday_month'] . '-' . $d['birthday_day'];
        $data['sex'] = $d['sex'];
        $data['true_name'] = $d['true_name'];
        $data['province'] = $d['province'];
        $data['city'] = $d['city'];
        $data['area'] = $d['area'];
        $data['birthday'] = $time;

        $dc->where("id = $id")->save($uname);
        $row = $db->where("user_id = $id")->save($data);

        echo $row;

    }

    //保存个人信息2
    public function saveInformation2()
    {

        $id = $_SESSION['user']['userid'];
        $db = M('user_info');
        $row = $db->where("user_id = $id")->save($_POST);
        echo $row;
    }


    //修改密码
    public function modPwd()
    {   
        //SEO
        $ident ="member";
        $idents =$this->seo($ident);
        $this->assign("title",$idents['title']);
        $this->assign("keywords",$idents['keywords']);
        $this->assign("description",$idents['description']);


        $id = $_SESSION['user']['userid'];
        if (isset($_POST['password'])) {

            $db = M('users');
            $password = sha1($_POST['password']);
            $npwd = sha1($_POST['npwd']);

            $data['password'] = $npwd;
            $where = "id = '{$id}' AND password = '{$password}'";
            $row = $db->where($where)->find();
            if ($row != null) {
                $row = $db->where("id = $id")->save($data);
                if ($row > 0) {
                    $msg = '密码修改成功';
                } else {
                    $msg = '密码修改失败!';
                }
            } else {
                $msg = '旧密码不正确!';
            }

            $this->ajaxReturn($msg);
        }


        $this->display();
    }

    //修改邮箱
    public function modEmail()
    {
        //SEO
        $ident ="member";
        $idents =$this->seo($ident);
        $this->assign("title",$idents['title']);
        $this->assign("keywords",$idents['keywords']);
        $this->assign("description",$idents['description']);


        $this->display();
    }

    //收货地址
    public function address()
    {   
        //SEO
        $ident ="member";
        $idents =$this->seo($ident);
        $this->assign("title",$idents['title']);
        $this->assign("keywords",$idents['keywords']);
        $this->assign("description",$idents['description']);

        
        $user_id = $_SESSION['user']['userid'];
        $db = M('user_address');
        $count = $db->where("user_id = $user_id")->count("`user_id`");
        $data = $db->where("user_id = $user_id")->order('is_default desc')->select();

        $this->assign('data', $data);
        $this->assign('count', $count);

        //三级联动
        $city = array("--请选择省--", "--市--", "--区县--");
        $c = Area::city($city);
        $this->assign("city", $c);
        $this->display();
    }

    //添加地址
    public function addressAdd()
    {
        $db = M('user_address');
        //修改
        if (($_POST['id']) > 0) {
            unset($_POST['is_default']);
            $row = $db->save($_POST);
            if ($row > 0) {
                echo '修改成功!';
            } else {
                echo '修改失败!';
            }
        } else {
            $user_id = $_SESSION['user']['userid'];
            $_POST['user_id'] = $user_id;


            if ($_POST['is_default'] == 1) {
                $data['is_default'] = 0;
                $db->where("user_id = $user_id")->save($data);
            }

            $row = $db->where("user_id = $user_id")->add($_POST);
            if ($row > 0) {
                echo '添加成功!';
            } else {
                echo '添加失败!';
            }
        }

    }


    //删除地址
    public function addressDel()
    {
        $user_id = $_SESSION['user']['userid'];
        $db = M('user_address');
        $id = $_POST['id'];
        if (isset($_POST['is_default'])) {
            $data['is_default'] = 1;
            $db->where("id = $id")->delete();
            $ids = $db->where("user_id = $user_id")->order('`id` asc ')->getField('id');
            $row = $db->where("id = $ids")->save($data);
            echo $row;
        } else {
            $row = $db->where("id = $id")->delete();
            echo $row;
        }
    }

    //获得地址信息
    public function addressGet()
    {
        $id = $_POST['id'];
        $db = M('user_address');
        $row = $db->where("id = $id")->find();
        $province = $row['province'];
        $city = $row['city'];
        $area = $row['area'];
        $city = array($province, $city, $area);
        $c = Area::city($city);

        $this->assign("o", $c);
        $this->ajaxReturn($row);
        $this->display('address');
    }

    public function addressDefault()
    {
        $db = M('user_address');
        $id = $_POST['id'];
        $user_id = $_SESSION['user']['userid'];
        $data['is_default'] = 0;
        $result = $db->where("user_id = $user_id")->save($data);
        if ($result > 0) {
            $data['is_default'] = 1;
            $row = $db->where("id = $id")->save($data);
            if ($row > 0) {
                echo $row;
            } else {
                echo 0;
            }
        } else {
            echo 0;
        }

    }

    //上传头像
    public function upimg()
    {

        $user_id = $_SESSION['user']['userid'];

        $result = array();
        $result['success'] = false;
        $success_num = 0;

    //上传目录
        $dir1 = $_SERVER['DOCUMENT_ROOT']."/Public/User";
        $dir ="/Public/User";
        $filename = date("YmdHis") . '_' . floor(microtime() * 1000) . '_' . createRandomCode(8);
        $avatars = array("__avatar1");
        $avatars_length = count($avatars);
        for ($i = 0; $i < $avatars_length; $i++) {
            $avatar = $_FILES[$avatars[$i]];
            $avatar_number = $i + 1;
            if ($avatar['error'] > 0) {
                $result['type'] = 1;
            } else {
                $savePath = "$dir1/php_avatar" . $avatar_number . "_$filename.jpg";
                $savePath1 = "$dir/php_avatar" . $avatar_number . "_$filename.jpg";
                $result['avatarUrls'][$i] = toVirtualPath($savePath);

               move_uploaded_file($avatar["tmp_name"], $savePath);
                $success_num++;

                //写入数据库
                $db = M('user_info');
                $data['thumb'] = $savePath1;
                $row = $db->where("user_id = $user_id")->save($data);
                if ($row) {
                    $result['type'] = 0;
                } else {
                    $result['type'] = 1;
                }

            }
        }


        if ($success_num > 0) {
            $result['success'] = true;
        }
        $this->ajaxReturn($result);
    }


}

/**************************************************************
 *  生成指定长度的随机码。
 * @param int $length 随机码的长度。
 * @access public
 **************************************************************/
function createRandomCode($length)
{
    $randomCode = "";
    $randomChars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    for ($i = 0; $i < $length; $i++) {
        $randomCode .= $randomChars{mt_rand(0, 35)};
    }
    return $randomCode;
}

/**************************************************************
 *  生成指定长度的随机码。
 * @param int $length 随机码的长度。
 * @access public
 **************************************************************/
function toVirtualPath($physicalPpath)
{
    $virtualPath = str_replace($_SERVER['DOCUMENT_ROOT'], "", $physicalPpath);
    $virtualPath = str_replace("\\", "/", $virtualPath);
    return $virtualPath;
}


?>
