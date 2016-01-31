<?php
namespace   Home\Controller;
use         Think\Controller;
/**
 * 支付模块
 * @version 2015-01-20
 */
class PayController extends MyController {
    public function __construct() {
        parent::__construct();
    }
    public function doPaypal ()
    {
        $modelConfig = M('sys_config');
        $cRes = $modelConfig->field('cvalue')->where("cname = 'paypal_env'")->find();
        $paypal = new \Home\Controller\PaypalController($cRes['cvalue']);
        $paypal->run();
    }

    public function complete ()
    {
        $getTx = I('get.tx');               // 验证码，和IPN发送过来的一样，保存在session('pay_tx');中
        $getPaymentStatus = I('get.st');

        if ($getPaymentStatus == 'Completed' || $getPaymentStatus == 'Pending')
        {
            // 说明支付成功
            $url  = U('Member/member');
            $png  = 'reg_ok.png';
            $title = '恭喜您，支付成功！';
        }
        else
        {
            $url  = U('Member/member');
            $png = 'reg_error.png';
            $title = '对不起，支付失败！';
        }

        $this->assign('url', $url);
        $this->assign('png', $png);
        $this->assign('title', $title);
        $this->display('complete');
        
    }
}