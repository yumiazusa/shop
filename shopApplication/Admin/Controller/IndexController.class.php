<?php
namespace   Admin\Controller;
use         Think\Controller;

/**
 * 后台主页
 */
class IndexController extends MyController {

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

        // 软件有没有被安装
        $lockFile = APP_PATH . "Common/lock.locked";
        if ( ! file_exists($lockFile))
        {
            header('location:/install.php' );
            exit;
        }

        // 零点的时间戳
        
        $getNowTime = time();
        $getTodayTimeStr = date('Y-') . date('m-') . date('d ') . '00:00:00';
        $tom = date('d') + 1;
        $getTomorryTimeStr = date('Y-') . date('m-') . $tom . ' 00:00:00';
        // 今天零时的时间
        $timeStamp_01 = strtotime($getTodayTimeStr);
        // 昨天零时的时间
        $timeStamp_02 = strtotime($getTomorryTimeStr);
        $where = "order_date < '" . $timeStamp_02 . "' AND order_date > '" . $timeStamp_01 . "'";
        
        // 页面头部显示的文字及导航信息
        // 首页总访问量
        $indexmodelVisit = M('index_visit');
        $counts['indexvisit_counts'] = $indexmodelVisit->Sum('visit_count');
        $indexvisitCounts = $indexmodelVisit->order("id DESC")->field('visit_count')->find();
        $counts['indexvisit_today'] = $indexvisitCounts['visit_count'];
        // 商城总访问量
        $modelVisit = M('visit');
        $counts['visit_counts'] = $modelVisit->Sum('visit_count');
        $visitCounts = $modelVisit->order("id DESC")->field('visit_count')->find();
        $counts['visit_today'] = $visitCounts['visit_count'];

        // 会员总数
        $modelUsers = M('users');
        $counts['users']         = $modelUsers->count();
        $counts['users_today'] = $modelUsers->where("create_time < '{$timeStamp_02}' AND create_time > '{$timeStamp_01}'")
                                  ->count();

        // 订单总数
        $modelOrders = M('orders');
        $counts['orders']        = $modelOrders->count();
        $counts['orders_today']  = $modelOrders->where($where)->count();
        // 今日交易
        $modelProducts = M('product');
        $counts['product'] = $modelProducts->count();
        $counts['product_today'] = $modelProducts->where("add_time < '{$timeStamp_02}' AND add_time > '{$timeStamp_01}'")
                                                 ->count();

        // 商城留言量
        $modelFeedbacks = M('feedbacks');
        $counts['feedback'] = $modelFeedbacks->count();
        $counts['feedback_today'] = $modelFeedbacks->where("add_time < '{$timeStamp_02}' AND add_time > '{$timeStamp_01}'")
                                                   ->count();

        // 首页留言量
        $indexmodelFeedbacks = M('index_contact');
        $counts['indexfeedback'] = $indexmodelFeedbacks->count();
        $counts['indexfeedback_today'] = $indexmodelFeedbacks->where("contact_time < '{$timeStamp_02}' AND contact_time > '{$timeStamp_01}'")
                                                   ->count();

        // 商品评论量
        $productmodelFeedbacks = M('product_comments');
        $counts['productfeedback'] = $productmodelFeedbacks->count();
        $counts['productfeedback_today'] = $productmodelFeedbacks->where("comment_time < '{$timeStamp_02}' AND comment_time > '{$timeStamp_01}'")
                                                   ->count();

        // $this->assign('moduleName', '后台管理');
        $this->assign('counts', $counts);
        $this->display('index');
    }

    public function ajaxCheckmessage(){
        //商城留言
        $modelFeedbacks = M('feedbacks');
        $counts['feedback'] = $modelFeedbacks->where(array('status'=>0))->count();
        // 首页留言量
        $indexmodelFeedbacks = M('index_contact');
        $counts['indexfeedback'] = $indexmodelFeedbacks->where(array('status'=>0))->count();
        // 商品评论量
        $productmodelFeedbacks = M('product_comments');
        $counts['productfeedback'] = $productmodelFeedbacks->where(array('status'=>0))->count();
        //新生订单量
        $modelOrders = M('orders');
        $where['order_status']=array('IN',array('1,2'));
        $counts['orders']        = $modelOrders->where($where)->count();
        $this->ajaxReturn($counts);
    }
}