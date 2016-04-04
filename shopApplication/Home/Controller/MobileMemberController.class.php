<?php
namespace Home\Controller;
use  Think\Controller;
use Think\Area;

class MobileMemberController extends MyController{
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


        $id = $_SESSION['user']['userid'];

        if(!$id){
            $this->error('请先登录！',U('Mobileindex/mobileindex'),3);
            exit;
        }else{
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
        }
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

        if(!$id){
            $this->error('请先登录！',U('Mobileindex/mobileindex'),3);
            exit;
        }else{

        $db = M('user_info');
        $dc = M('users');

        $info = $db->where("user_id =".$id)->find();
        $users = $dc->where("id = ".$id)->find();

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
    }

    //保存个人信息
    public function saveInformation()
    {
        $id = $_SESSION['user']['userid'];
        $db = M('user_info');
        $dc = M('users');

        $d = $_POST;
        $uname['uname'] = $d['uname'];

        $identity=$this->checkIdCard($d['identity']);

        if($identity){
            $data['identity']=$d['identity'];
            $uname['confirmation']=1;
            session(array('name'=>'userLogin', 'prefix'=>'user'));
            session('userconfirmation',$uname['confirmation']);
        }else{
            echo 400;
            exit;
        }

        $data['sex'] = $d['sex'];
        $data['true_name'] = $d['true_name'];
        $dc->where("id = ".$id)->save($uname);
        $row = $db->where("user_id =".$id)->save($data);
        if($row){
             echo 1;
         }else{
            echo 401;
         }
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
        if(!$user_id){
            $this->error('请先登录！',U('Mobileindex/mobileindex'),3);
            exit;
        }else{
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
        if ($_POST['is_default']) {
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

   

        //身份证号验证
    protected function checkIdCard($patient_card){
        if(empty($patient_card)){
            return false;
        }
        $idcard = $patient_card;
        $City = array(11=>"北京",12=>"天津",13=>"河北",14=>"山西",15=>"内蒙古",21=>"辽宁",22=>"吉林",23=>"黑龙江",31=>"上海",32=>"江苏",33=>"浙江",34=>"安徽",35=>"福建",36=>"江西",37=>"山东",41=>"河南",42=>"湖北",43=>"湖南",44=>"广东",45=>"广西",46=>"海南",50=>"重庆",51=>"四川",52=>"贵州",53=>"云南",54=>"西藏",61=>"陕西",62=>"甘肃",63=>"青海",64=>"宁夏",65=>"新疆",71=>"台湾",81=>"香港",82=>"澳门",91=>"国外");
        $iSum = 0;
        $idCardLength = strlen($idcard);
        //长度验证
        if(!preg_match('/^\d{17}(\d|x)$/i',$idcard) and !preg_match('/^\d{15}$/i',$idcard))
        {
            return false;
        }
        //地区验证
        if(!array_key_exists(intval(substr($idcard,0,2)),$City))
        {
           return false;
        }
        // 15位身份证验证生日，转换为18位
        if ($idCardLength == 15)
        {
            $sBirthday = '19'.substr($idcard,6,2).'-'.substr($idcard,8,2).'-'.substr($idcard,10,2);
            $dd = date( 'Y-m-d', strtotime( $sBirthday ) );
            // echo $sBirthday;die;
            // $d = new DateTime($sBirthday);
            // $dd = $d->format('Y-m-d');
            if($sBirthday != $dd)
            {
                return false;
            }
            $idcard = substr($idcard,0,6)."19".substr($idcard,6,9);//15to18
            $Bit18 = $this->getVerifyBit($idcard);//算出第18位校验码
            $idcard = $idcard.$Bit18;
        }
        // 判断是否大于2078年，小于1900年
        $year = substr($idcard,6,4);
        if ($year<1900 || $year>2078 )
        {
            return false;
        }

        //18位身份证处理
        $sBirthday = substr($idcard,6,4).'-'.substr($idcard,10,2).'-'.substr($idcard,12,2);
        $dd = date( 'Y-m-d', strtotime( $sBirthday ) );
        // echo $sBirthday;die;
        // $d = new DateTime($sBirthday);
        // $dd = $d->format('Y-m-d');
        if($sBirthday != $dd)
         {
            return false;
         }
        //身份证编码规范验证
        $idcard_base = substr($idcard,0,17);
        if(strtoupper(substr($idcard,17,1)) != $this->getVerifyBit($idcard_base))
        {
           return false;
        }
        return true;
    }

    // 计算身份证校验码，根据国家标准GB 11643-1999
    function getVerifyBit($idcard_base)
    {
        if(strlen($idcard_base) != 17)
        {
            return false;
        }
        //加权因子
        $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
        //校验码对应值
        $verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
        $checksum = 0;
        for ($i = 0; $i < strlen($idcard_base); $i++)
        {
            $checksum += substr($idcard_base, $i, 1) * $factor[$i];
        }
        $mod = $checksum % 11;
        $verify_number = $verify_number_list[$mod];
        return $verify_number;
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
