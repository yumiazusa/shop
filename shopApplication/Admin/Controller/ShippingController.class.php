<?php

namespace   Admin\Controller;
use         Think\Controller;

/**
 * 物流配置页，此页设置“爱查询”接口信息
 * @author Eden
 * @version 2015-01-19
 */
class ShippingController extends  MyController{

    /**
     * 构造方法，用于权限管理操作
     * @date 2015-01-19
     */
    public function __construct(){
        parent::__construct();
    }


    /**
     * 获取模板文件，查询出相关数据
     * @return void 
     */
    public function index ()
    {
        $model = M();
        // 获取总记录数
        $pageCount = $model->table('__SHIPPINGS__ AS s, __SHIPPING_COMPANY__ as c')
                      ->where('s.sid = c.id')
                      ->count();

        $pageSize = 15;
        $page = new \Think\Page($pageCount, $pageSize);
        $sRes = $model->field('s.id, s.sid, c.cname, c.com, s.shipping_desc, s.is_default')
                      ->table('__SHIPPINGS__ AS s, __SHIPPING_COMPANY__ as c')
                      ->where('s.sid = c.id')
                      ->limit($page->firstRow, $page->listRows)
                      ->order('id DESC')
                      ->select();
        $pageShow = $page->show();

        // 获取API接口信息
        // $modelShippingConfig = M('sys_config');
        // $sConfig = $modelShippingConfig->where("cname LIKE 'shipping_%'")->select();
        $arr1 = $this->_arr2ToArr1($sConfig);
        // $this->assign('sConfig', $arr1);
        $this->assign('page', $pageShow);
        $this->assign('sRes', $sRes);
        $this->display();
    }

    public function saveShippingAPI ()
    {
        $post = I('post.');

        $modelSConfig = M('sys_config');
        $counts = 0;
        foreach ($post as $key=>$val)
        {
            $where['cname'] = $key;
            $data['cvalue'] = $val;
            $counts += $modelSConfig->where($where)->data($data)->save();
        }
        if ($counts)
        {
            $this->success('操作成功');
        }
        else
        {
            $this->error('删除失败');
        }
    }

    /**
     * 执行物流信息删除操作
     * @return void 
     */
    public function doShippingDelete()
    {
        $getShippingId = I('get.id');

        $model = M('shippings');
        $affectedRows = $model->where("id='{$getShippingId}'")->delete();
        if ($affectedRows)
        {
            $this->success('执行成功');
            exit;
        }
        $this->error('执行失败');
    }

    /**
     * 执行修改操作
     * @return void 
     */
    public function doShippingEdit ()
    {
        $getShippingId = I('post.id');
        $model = M('shippings');
        if (isset($_POST['is_default']))
        {
            $where = "id not in({$getShippingId})";
            $model->where($where)->data(array('is_default'=>0))->save();
        }
        else
        {
            $_POST['is_default'] = 0;
        }

        if ($model->create($_POST))
        {
            $affectedRows = $model->where("id = {$getShippingId}")->save();
            if ($affectedRows)
            {
                $this->success('修改成功');
                exit;
            }
        }
        $this->error('修改失败');
    }

    /**
     * 获取信息修改模板
     * @return void 
     */
    public function shippingEdit ()
    {
        $getShippingId = I('get.id');
        $modelShipping = M('shippings');
        $getShippingRes = $modelShipping->where("id = {$getShippingId}")->find();
        $getShippingSId = $getShippingRes['sid'];
        $modelShippingComp = M('shipping_company');
        $shippingsExists = $modelShipping->field('sid')->where("sid  not in ({$getShippingSId})")->select();
        $arr = array();
        foreach ($shippingsExists as $val)
        {
            $arr[] = $val['sid'];
        }
        $sIdExists = implode(', ', $arr);
        $where = "id not in ($sIdExists)";
        $shippingSRes = $modelShippingComp->field('id, cname')->where($where)->select();
        $this->assign('defaultCId', $getShippingSId);
        $this->assign('sVal', $getShippingRes);
        $this->assign('sRes', $shippingSRes);
        $this->display('edit');
    }

    /**
     * 获取物流添加界面
     * @return void 
     */
    public function shippingAdd ()
    {
        $modelS = M('shippings');
        $modelSC = M('shipping_company');

        $sRes = $modelS->field('sid')->select();
        // 查询结果为一个二维数组，需要将其组合成一维数组
        if($sRes){
        $arr = array();
        foreach ($sRes as $k=>$v)
        {
            $arr[] = $v['sid'];
        }
        // 用逗号连接成字符串
        $sString = implode(', ', $arr);

        // 查询现有表中不存在的记录 
        $scRes = $modelSC->field('id, cname')->where("id not in ($sString)")->select();
        }else{
        $scRes = $modelSC->field('id, cname')->select();
        }

        $this->assign('scRes', $scRes);
        $this->display('add');
    }

    /**
     * 执行添加物流信息
     * @return void 
     */
    public function doShippingAdd ()
    {
        $post = $_POST;

        $model = M('shippings');

        // 如果设置了默认，将表中原来已经设置了默认清空
        if ($post['is_default'])
        {
            $model->where("is_default = 1")->data(array('is_default'=>0))->save();
        }

        // 插入数据表
        if ($model->create($post))
        {
            if ($lastId = $model->add())
            {
                $this->success('执行成功');
                exit;
            }
        }
        $this->error('删除失败');
    }
 
 }
