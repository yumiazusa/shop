<?php

namespace   Admin\Controller;
use         Think\Controller;

/**
 * 支付配置，此页设置Paypal API接口信息
 * @author Eden
 * @version 2015-01-19
 */
class PayController extends  MyController{

    /**
     * 构造方法，用于权限管理操作
     * @date 2015-01-19
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * 获取支付配置模板，读取支付相关参数
     * @return void 
     */
    public function index(){
        $modelPaypal = M('sys_config');
        $paypalRes = $modelPaypal->where("cname LIKE 'paypal_%'")->select();

        // 将获取结果转化成一维数组
        $arr = array();
        foreach ($paypalRes as $key=>$val)
        {
            $k = $val['cname'];
            $arr[$k] = $val['cvalue'];
        }

        $this->assign('paypalRes', $arr);
        $this->display('index');
    }

    /**
     * 保存信息
     * @return void
     */
    public function savePaypal(){
        if(isset($_POST)){
            $db =M("sys_config");
            $affectedRows = 0;

            foreach ($_POST as $key=>$val)
            {
                $where['cname'] = $key;
                $value['cvalue'] = $val;

                $affectedRows += $db->where($where)->save($value);
            }


            if($affectedRows){
                $this->success("添加成功");
                exit;
            }else{
                $this->error("添加失败");
                exit;
            }
        }
        $this->error('非法操作');
    }
 }
