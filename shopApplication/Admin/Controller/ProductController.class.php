<?php
namespace Admin\Controller;
use         Think\Controller;
header('Content-Type:text/html; charset=utf-8');
class ProductController extends MyController{
    /**
     * 构造函数，初始化
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * 普通商品列表
     */
    public function productList(){
        // 获取商品类别
        $productCats = $this->get_data();
        $modelProducts = M('product');
        $map['a.recycle'] = 0;
        // 分页处理，带关键字搜索
        if (isset($_GET)) {
            foreach ($_GET as $key => $val) {
                if ($val == '查找全部' && $key == 'cat_id') {
                    continue;
                }

                if ($val == '全部' && $key == 'is_on_sale') {
                    continue;
                }

                if ($val == '' && $key == 'pro_name') {
                    continue;
                }

                if($key=='pro_name'){
                    $val=str_replace('，',',',$val);
                    $val=trim($val,',');
                    $val=explode(',',$val);
                    foreach($val as $k=>$v){
                        $val[$k]='%'.trim($v).'%';
                        }
                     $val =array('like',$val,'OR');
                }

                if($key =='cat_id' && is_numeric($val)){
                   $ModelCat=M('product_cat');
                   $fid=$ModelCat->where(array('id'=>$val))->getField('fid');
                   if($fid ==='0'){
                    $cat_id=$ModelCat->field('id')->where(array('fid'=>$val))->select();
                    if($cat_id){
                            foreach($cat_id as $i=>$ii){
                        $lala[$i]=$ii['id'];
                                    }
                                }else{
                            $lala=$val;
                                    }
                    }else{
                        $lala=$val;
                        }
                   $val=array('in',$lala);
                }
                $map['a.'.$key] = $val;
            }
        }
        // dump($map);
        // die;
        // 查询总记录数
        $getPageCounts = $modelProducts->alias('a')->where($map)->count();
        // 每页显示 $pageSize 条数据
        $pageSize = 15;
        // 实例化分页类
        $page = new \Think\Page($getPageCounts, $pageSize, $map);

        $productsList = $modelProducts->field('a.id,a.pro_sn,a.pro_name,a.promote_price,a.is_new,a.is_on_sale,a.is_ship,a.add_time,a.is_promote,b.status,b.rate,b.promote_num,b.condition,b.gifts,b.start_time,b.end_time,b.combs')
                    ->alias('a')
                    ->where($map)
                    ->join('LEFT JOIN im_promote b ON a.id = b.product_id')
                    ->order('a.id DESC')->limit($page->firstRow, $page->listRows)
                    ->select();
        // if (!empty($map)) {
        //     foreach ($map as $k => $v) {
        //         $page->parameter .= "$k=".urlencode($v).'&';
        //     }
        // }

        $pageShow = $page->show();
        $this->assign('productCats', $productCats);
        $this->assign('page', $pageShow);
        // dump($productsList);
        // die;
        $this->assign('productsList', $productsList);
        $this->display('product_list');
    }

    /**
     * 获取商品分类，这个功能其它方法也能用到
     */
    private function _getProductCats(){
        $modelProduct = M('product_cat');
        return $modelProduct->select();
    }

    /**
     * 改变产品是否为上架状态
     */
    public function toggleOnSale(){
        $getProductId = I('get.id');
        $getPreState = I('get.state');

        $modelProduct = M('product');
        if ($getPreState) {
            $data = array('is_on_sale' => 0);
        } else {
            $data = array('is_on_sale' => 1);
        }

        $affectedRows = $modelProduct->data($data)->where("id='{$getProductId}'")->save();

        if ($affectedRows) {
            $this->success('设置成功');
        } else {
            $this->error('设置失败');
        }
    }

    /**
     * 回收站商品列表
     */
    public function productRecycle(){
        // 获取商品类别
        $productCats = $this->get_data();
        $modelProducts = M('product');
        $map['recycle'] = 1;

               if (isset($_GET)) {
            foreach ($_GET as $key => $val) {
                if ($val == '查找全部' && $key == 'cat_id') {
                    continue;
                }

                if ($val == '全部' && $key == 'is_on_sale') {
                    continue;
                }

                if ($val == '' && $key == 'pro_name') {
                    continue;
                }

                if($key=='pro_name'){
                    $val=str_replace('，',',',$val);
                    $val=trim($val,',');
                    $val=explode(',',$val);
                    foreach($val as $k=>$v){
                        $val[$k]='%'.trim($v).'%';
                        }
                     $val =array('like',$val,'OR');
                }

                if($key =='cat_id' && is_numeric($val)){
                   $ModelCat=M('product_cat');
                   $fid=$ModelCat->where(array('id'=>$val))->getField('fid');
                   if($fid ==='0'){
                    $cat_id=$ModelCat->field('id')->where(array('fid'=>$val))->select();
                    if($cat_id){
                            foreach($cat_id as $i=>$ii){
                        $lala[$i]=$ii['id'];
                                    }
                                }else{
                            $lala=$val;
                                    }
                    }else{
                        $lala=$val;
                        }
                   $val=array('in',$lala);
                }
                $map[$key] = $val;
            }
        }

        $pageCounts = $modelProducts->where($map)->count();

        $pageSize = 15;
        $page = new \Think\Page($pageCounts, $pageSize,$map);

        $productsList = $modelProducts->where($map)->limit($page->firstRow, $page->listRows)->order('id DESC')->select();
        $pageShow = $page->show();

        $this->assign('productsList', $productsList);
        $this->assign('page', $pageShow);
        $this->assign('productCats', $productCats);
        $this->display('product_recycle_list');
    }

    /**
     * Ajax获取产品类别
     */
    public function searchCatByAjax(){
        $getCatId = I('post.cat_id');
        $getCatName = I('post.pro_name');

        $where['cat_id'] = $getCatId;
        if (!empty($getCatName)) {
            $where['pro_name'] = array('LIKE', "%{$getCatName}%");
        }

        $modelProduct = M('product');
        $productRes = $modelProduct
                      ->alias('a')
                      ->field('a.id, a.pro_name, sum(b.product_number) as total')
                      ->join('LEFT JOIN im_productnum b on a.id = b.product_id')
                      ->where($where)
                      ->group('a.id')
                      ->select();
        $this->ajaxReturn($productRes);
    }

    /**
     * 将商品添加到回收站
     */
    public function addProductToRecycle(){
        $getProductId = I('get.id');
        $modelProduct = M('product');
        $affectedRows = $modelProduct->where("id='{$getProductId}'")->data(array('recycle' => '1'))->save();

        if ($affectedRows) {
            // 写入日志
            $cLog = new \Admin\Controller\LogController();
            $cLog->setLog('商品（ID:' . $getProductId . '）已成功加入回收站');
            $this->success('商品已加入回收站');
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 将产品从回收站移出
     */
    public function removeProductToRecycle(){
        $getProductId = I('get.id');
        $modelProduct = M('product');
        $affectedRows = $modelProduct->where("id='{$getProductId}'")->data(array('recycle' => '0'))->save();

        if ($affectedRows) {
            // 写入日志
            $cLog = new \Admin\Controller\LogController();
            $cLog->setLog('商品（ID:' . $getProductId . '）已从回收站移出');
            $this->success('商品移出回收站');
        } else {
            $this->error('操作失败');
        }
    }

    public function deleteProduct()
    {
        $getProductId = I('get.id');
        $modelProduct = M('product');
        $modelAttr = M('products_attr');
        $modelNum=M('productnum');
        $modelAlbum=M('product_album');
        $modelParam=M('product_param');
        $modelPromote=M('promote');
        // 删除商品表
        $affectedRows = $modelProduct->where(array('id' => $getProductId))->delete();
        //删除商品库存表
        $modelNum->where(array('product_id'=>$getProductId))->delete();
        //删除参数表
        $modelParam->where(array('pro_id'=>$getProductId))->delete();
        //删除促销表
        $modelPromote->where(array('product_id'=>$getProductId))->delete();
        // 删除属性关联表
        $whereAttr['pro_id'] = $getProductId;
        $modelAttr->where($whereAttr)->delete();

        if ($affectedRows) {
            // 写入日志
            $cLog = new \Admin\Controller\LogController();
            $cLog->setLog('商品（ID:' . $getProductId . '）删除成功');
            $this->success('商品删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    /**
     * 获取添加商品页模板
     */
    public function productAdd(){
        $cats = $this->get_data();
        // 将商品分类变量分配到模板
        $this->assign('cats', $cats);
        $this->display('product_add');
    }

    /**
     * 执行产品添加操作
     */
    public function doAddProduct(){
        // 处理基本通用信息，包含fetcher
        $post = I('post.');
        $post['feacher']=$_POST['feacher'];
        $post['pro_name']=trim($post['pro_name']);
        // 当商品名为空时，拒绝添加
        if(empty($post['pro_name'])){
            $this->error('商品名称不能为空，添加失败！');
            exit;
        }

        $post['add_time'] = time();
        $post['update_time'] = time();

        $modelProducts = M('product');
        if ($modelProducts->create($post)) {
            $getProductId = $modelProducts->add($post);
            if (!$getProductId) {
                $this->error('商品添加失败');
                exit;
            }
            // 处理商品规格特性
            $modelAttr = M('products_attr');
            // dump($modelAttr);
            // die;
            if (!empty($post['style_name'][0])) {
                $data = array();
                foreach ($post['style_name'] as $k => $v) {
                    $data[$k]['pro_id'] = $getProductId;
                    $data[$k]['prop_id'] = 2;
                    $data[$k]['prop_name'] = $v;
                    $data[$k]['prop_price'] = $post['style_price'][$k];
                    $data[$k]['prop_weight'] = $post['style_weight'][$k];
                }
                $modelAttr->addAll($data);
            }

            // 处理包装颜色特性
            if (!empty ($post['prop_name'][0])) {
                $data = array();
                foreach ($post['prop_name'] as $k => $v) {
                    $data['pro_id'] = $getProductId;
                    $data['prop_id'] = 1;
                    $data['prop_name'] = $v;
                    $data['prop_value'] = $post['prop_value'][$k];
                    $data['prop_price'] = $post['prop_price'][$k];
                    $data['prop_weight'] = $post['prop_weight'][$k];

                    $getAttrId = $modelAttr->data($data)->add();

                    // 处理颜色对应的小图
                    $albumData = array();
                    foreach ($post['advs'][$k] as $key => $val) {
                        $albumData[$key]['attr_id'] = $getAttrId;
                        $albumData[$key]['images'] = $val;
                    }

                    // 添加颜色对应的图片
                    $modelAlbum = M('product_album');
                    $modelAlbum->addAll($albumData);
                }
            }

            // 处理赠品
            // if (isset($_POST['gifts'])) {
            //     $giftsData['prop_value'] = implode(',', $_POST['gifts']);
            //     $giftsData['prop_name'] = '赠品';
            //     $giftsData['pro_id'] = $getProductId;
            //     $giftsData['prop_id'] = 3;

            //     $modelAttr->data($giftsData)->add();
            // }

            if ($getProductId) {
                // 写入日志
                $cLog = new \Admin\Controller\LogController();
                $cLog->setLog('商品（ID:' . $getProductId . '）添加成功');
                $this->success('商品添加成功', 'productList');
                exit;
            } else {
                $this->error('商品添加失败');
            }

        } else {
            $this->error('上传出错。');
        }

    }

    /**
     * 商品属性编辑
     */
    public function productEdit(){
        $productCats = $this->get_data();
        $getProductId = I('get.id');
        // 获取产品详细信息
        $modelProduct = M('product');
        $productRes = $modelProduct->find($getProductId);

        // 规格
        $modelAttr = M('products_attr');
        $memWhere['pro_id'] = $productRes['id'];
        $memWhere['prop_id'] = 2;
        $methodRes = $modelAttr->where($memWhere)->select();

        // 包装
        $modelAlbum = M('product_album');
        $ColorWhere['pro_id'] = $productRes['id'];
        $ColorWhere['prop_id'] = 1;
        $ColorRes = $modelAttr->where($ColorWhere)->select();

        // // 赠品 
        // $giftWhere['pro_id'] = $productRes['id'];
        // $giftWhere['prop_id'] = 3;
        // $giftRes = $modelAttr->where($giftWhere)->find();
        // $gifts = array();
        // if (!empty($giftRes['prop_value'])) {
        //     $giftsWhere = "id in ({$giftRes['prop_value']})";
        //     // exit;
        //     $gifts = $modelProduct->field('id,pro_name')->where($giftsWhere)->select();
        // }

        $albums = array();
        foreach ($ColorRes as $k => $v) {
            $getAlbumId = $v['id'];
            $albumRes = $modelAlbum->where("attr_id = '{$getAlbumId}'")->select();
            $arr = array();
            foreach ($albumRes as $key => $val) {
                $arr['album'][] = $val['images'];
            }
            $arr['prop_name'] = $v['prop_name'];
            $arr['prop_value'] = $v['prop_value'];
            $arr['prop_price']=$v['prop_price'];
            $arr['prop_weight']=$v['prop_weight'];
            $albums[] = $arr;
        }

        // dump($albums);
        // // dump($productRes);
        // die;
        $this->assign('album', $albums);
        $this->assign('method', $methodRes);
        $this->assign('productCats', $productCats);
        $this->assign('pro', $productRes);
        $this->display('product_edit');
    }

    /**
     * 执行商品修改操作
     */
    public function doProductEdit(){
        $getProductId = I('post.id');
        $post = I('post.');
        $post['feacher']=$_POST['feacher'];
        // dump($post);
        // die;
        $post['pro_name']=trim($post['pro_name']);
        // dump($post);
        // die;
        if (empty($post['pro_name'])) {
            $this->error('商品更改无效');
            exit;
        }

        $post['update_time'] = time();
        $post['is_new'] = isset($_POST['is_new']) ? $_POST['is_new'] : 0;
        $post['is_promote'] = isset($_POST['is_promote']) ? $_POST['is_promote'] : 0;
        $post['is_ship'] = isset($_POST['is_ship']) ? $_POST['is_ship'] : 0;


        // 修改通用信息
        $modelProduct = M('product');
        $updateProduct = 0;
        if ($modelProduct->create($post)) {
            $updateProduct = $modelProduct->where(array('id'=>$getProductId))->save();
            // $updateProduct = $modelProduct->save();
        }

        // 修改属性
        // 处理规格属性 prop_id = 2 
        // 先删除原来的，再加入现在的
        $modelAttr = M('products_attr');
        $mAffectedRows = 0;
        if (!empty($post['style_name'][0])) {
            $memoryWhere['prop_id'] = 2;
            $memoryWhere['pro_id'] = $getProductId;
            $modelAttr->where($memoryWhere)->delete();
            $data = array();
            foreach ($post['style_name'] as $k => $v) {
                $data[$k]['pro_id'] = $getProductId;
                $data[$k]['prop_id'] = 2;
                $data[$k]['prop_name'] = $v;
                $data[$k]['prop_price'] = $post['style_price'][$k];
                $data[$k]['prop_weight'] = $post['style_weight'][$k];
            }
            $mAffectedRows = $modelAttr->addAll($data);
        }else{
            $memoryWhere['prop_id'] = 2;
            $memoryWhere['pro_id'] = $getProductId;
            $modelAttr->where($memoryWhere)->delete();
        }

        // 处理包装颜色部分，没有删除album表的存放的照片信息，会造成数据冗余 
        if (!empty ($post['prop_name'][0])) {
            // 删除原来的颜色
            $colorAttr['prop_id'] = 1;
            $colorAttr['pro_id'] = $getProductId;
            // 找到原有的图片
            $getOldAblum = $modelAttr->field('id')->where($colorAttr)->select();
            $modelAlbum = M('product_album');
            foreach ($getOldAblum as $key => $val) {
                $oldId = $val['id'];
                $modelAlbum->where(array('attr_id'=>$oldId))->delete();
            }
            $modelAttr->where($colorAttr)->delete();

            $data = array();
            $getAttrId = 0;
            foreach ($post['prop_name'] as $k => $v) {
                $data['pro_id'] = $getProductId;
                $data['prop_id'] = 1;
                $data['prop_name'] = $v;
                $data['prop_value'] = $post['prop_value'][$k];
                $data['prop_price'] = $post['prop_price'][$k];
                $data['prop_weight'] = $post['prop_weight'][$k];

                $getAttrId = $modelAttr->data($data)->add();

                // 处理颜色对应的小图
                $albumData = array();
                foreach ($post['advs'][$k] as $key => $val) {
                    $albumData[$key]['attr_id'] = $getAttrId;
                    $albumData[$key]['images'] = $val;
                }

                // 添加颜色对应的图片
                $modelAlbum->addAll($albumData);
            }
        }else{
            $colorAttr['prop_id'] = 1;
            $colorAttr['pro_id'] = $getProductId;
            $getOldAblum = $modelAttr->field('id')->where($colorAttr)->select();
            $modelAlbum = M('product_album');
            foreach ($getOldAblum as $key => $val) {
                $oldId = $val['id'];
                $modelAlbum->where(array('attr_id'=>$oldId))->delete();
            }
            $modelAttr->where($colorAttr)->delete();
        }
        // 处理赠品
        // if (isset($_POST['gifts'])) {
        //     // 删除原来的赠品
        //     $colorAttr['prop_id'] = 3;
        //     $colorAttr['pro_id'] = $getProductId;
        //     $modelAttr->where($colorAttr)->delete();

        //     $giftsData['prop_value'] = implode(',', $_POST['gifts']);
        //     $giftsData['prop_name'] = '赠品';
        //     $giftsData['pro_id'] = $getProductId;
        //     $giftsData['prop_id'] = 3;

        //     $zAffectedRows = $modelAttr->data($giftsData)->add();
        // }

        if ($mAffectedRows || $updateProduct || $getAttrId) {
            // 写入日志
            $cLog = new \Admin\Controller\LogController();
            $cLog->setLog('商品（ID:' . $getProductId . '）修改成功');
            $this->success('商品属性修改成功','productList');
            exit;
        } else {
            $this->error('商品添加失败');
        }

    }


    /**
     * 添加商品参数，获取参数添加页面
     */
    public function productParamAdd(){
        $getProId = I('get.id');
        $modelParam = M('product_param');

        $paramRes = $modelParam->where("pro_id = '{$getProId}'")->select();

        $this->assign('paramRes', $paramRes);
        $this->display('product_param_add');
    }


    /**
     * 执行参数添加操作
     * 但Ajax返回HTML实体没有解决
     */
    public function doAddParamByAjax()
    {
        if (I('post.pro_id') == '' || I('post.param_title') == '' || I('post.param_content') == '') {
            $this->error('警告：非法操作');
            exit;
        }

        $pro_id = I('post.pro_id');
        $modelParam = M('product_param');
        if ($modelParam->create()) {
            if ($modelParam->add()) {
                $cLog = new \Admin\Controller\LogController();
                $cLog->setLog('商品（ID:' . $pro_id . '）参数添加成功');
                $this->success('添加成功！');
                exit;
            } else {
                $this->error('添加失败');
            }
        }
    }


    /**
     * 执行参数编辑操作
     */
    public function doEditParam()
    {
        $modelParam = M('product_param');

        if ($modelParam->create()) {
            if ($modelParam->save()) {
                $cLog = new \Admin\Controller\LogController();
                $cLog->setLog('商品（ID:' . I('post.pro_id'). '）参数编辑成功');
                $jumpURL = 'productParamAdd/id/' . I('post.pro_id');
                $this->success('修改成功', $jumpURL);
                exit;
            }
        }
        $this->error('修改失败');
    }

    /**
     * 编辑商品参数，获取参数编辑页面
     */
    public function editParam()
    {
        $getParamId = I('get.id');
        $modelParam = M('product_param');
        $paramRes = $modelParam->where("id='{$getParamId}'")->find();

        $this->assign('paramRes', $paramRes);
        $this->display('product_param_edit');
    }

    /**
     * 执行删除参数操作
     */
    public function doDeleteParam()
    {
        $getParamId = I('get.id');
        $modelParam = M('product_param');
        $affectedRows = $modelParam->where("id = {$getParamId}")->delete();

        if ($affectedRows > 0) {
            $cLog = new \Admin\Controller\LogController();
            $cLog->setLog('商品参数（ID:' . $getParamId. '）删除成功');
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }


    /**
     * 商品分类列表，获取商品列表模板
     */
    public function productCats()
    {
        $productCats=$this->get_data();
        $this->assign('productCats', $productCats);
        $this->display('product_cats');
    }

    /**
     * 获取添加商品分类界面
     */
    public function productCatAdd(){
        $catData=$this->get_data();
        $this->assign('catData',$catData);
        $this->display('product_cat_add');
    }

    /**
     * 处理商品分类数组
     */
    private function get_data(){
        $list = M('product_cat')->order('fid asc,sort asc')->select ();
        foreach ( $list as $k => $vo ) {
            if ($vo ['fid'] != 0)
                continue;
            $one_arr [$vo ['id']] = $vo;
            unset ( $list [$k] );
        }
        foreach ($one_arr as $p) {
            $data [] = $p;
            $two_arr = array ();
            foreach ( $list as $key => $l ) {
                if ($l ['fid'] != $p ['id'])
                    continue;

                $l ['cat_name'] = '├──' . $l ['cat_name'];
                $two_arr [] = $l;
                unset ( $list [$key] );
            }

            $data = array_merge ( $data, $two_arr );
        }
            return $data;
    }

    /**
     * 执行添加商品分类操作
     */
    public function doCatAdd(){
        $modelProduct = M('product_cat');
        if ($modelProduct->create($_POST)) {
            $getId=$modelProduct->add();
            if ($getId) {
                $cLog = new \Admin\Controller\LogController();
                $cLog->setLog('商品分类（CAT_ID:' . $getId . '）添加成功');
                $this->success('商品分类添加成功', 'productCats');
                exit;
            }else{
                $this->error('商品类型属性添加失败');
                }
        }
        $this->error('商品类型属性添加失败');
    }

    /**
     * 商品分类编辑
     */
    public function catEdit(){
        $Model=M('product_cat');
        $fid=$Model->field('cat_name,id')->where('fid= 0')->order ( 'sort asc' )->select();
        $data=$Model->where('id ='.I('get.id'))->find();
        $this->assign(array(
            'fid'=>$fid,
            'data'=>$data,
            ));
        $this->display('product_cat_edit');
    }


    /**
     * 执行商品分类编辑
     */
    public function doCatEdit(){
        $data=I('post.');
        $Model=M('product_cat');
                if ($Model->create($data)){
                    $res = $Model->save($data);
                    if ($res){
                        // 写入日志
                                $cLog = new \Admin\Controller\LogController();
                                $cLog->setLog('商品分类（ATTR_ID:' . $data['id'] . '）修改成功');
                                $this->success('商品分类修改成功','productCats' );
                                exit;
                            }
                            $this->error('商品分类修改失败');
                }
                $this->error('商品分类修改失败');
        }

    /**
     * 执行商品分类删除
     */
    public function catDel(){
        $id =I('get.id');
            if (empty($id)) {
                    $this->error('请选择要删除的菜单!');
            }
                $Model=M('product_cat');
                $fid=$Model->field('fid')->where('id ='.$id )->find();
                if($fid['fid']==0){
                    $idarr=$Model->field('id')->where('fid ='.$id)->select();
                    foreach($idarr as $k=>$v){
                        $ids[$k]=$v['id'];
                    }
                    if($ids){
                    array_push($ids,$id);
                    $map=array('id' => array('in', $ids) );
                        }else{
                        $map=array('id'=>$id);
                        }
                }else{
                    $map=array('id'=>$id);
                    }
            if($Model->where($map)->delete()){
                    $cLog = new \Admin\Controller\LogController();
                    $cLog->setLog('商品分类（ATTR_ID:' . $id . '）删除成功');
                    $this->success('删除成功');
                    exit;
            } else {
                    $this->error('删除失败！');
                }
    }

    /**
     * 上传图片，用于多文件上传插件
     * 此方法被商品添加页，上传缩略图所使用
     */
    public function upload(){
        
        $upload = new \Think\Upload();  // 实例化上传类
        $upload->maxSize = 3145728;             // 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');   // 设置附件上传类型
        $upload->rootPath = "Public/Product/thumb/";
        $upload->savePath = '';                         // 设置附件上传目录    // 上传文件
        $info = $upload->upload();
        $src=$info['Filedata']['savepath'] . $info['Filedata']['savename'];
        $smsrc=$info['Filedata']['savepath'] .'sm_'. $info['Filedata']['savename'];
        $image = new \Think\Image();
        $image->open('./Public/Product/thumb/'.$src);
        $image->thumb(80, 80)->save('./Public/Product/thumb/'.$smsrc);
        $this->ajaxReturn($src);
    }

    /**
     * 上传菜单图片
     * 此方法被商品分类所用
     */
    public function uploadNav(){
        
        $upload = new \Think\Upload();  // 实例化上传类
        $upload->maxSize = 3145728;             // 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');   // 设置附件上传类型
        $upload->rootPath = "Public/Product/nav/";
        $upload->savePath = '';                         // 设置附件上传目录    // 上传文件
        $info = $upload->upload();
        $src=$info['Filedata']['savepath'] . $info['Filedata']['savename'];
        $image = new \Think\Image();
        $image->open('./Public/Product/nav/'.$src);
        $this->ajaxReturn($src);
    }

    /**
     * 图片上传操作，此方法用于多文件上传插件，返回Ajax字符串
     * 此方法被商品颜色添加页所调用
     */
    public function advUpload(){
        $upload = new \Think\Upload();  // 实例化上传类
        $upload->maxSize = 3145728;             // 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');   // 设置附件上传类型
        $upload->rootPath = "Public/Product/adv/";
        $upload->savePath = 'Goods/';                          // 设置附件上传目录    // 上传文件
        $info = $upload->upload();
        $src =$info['Filedata']['savepath'] . $info['Filedata']['savename'];
        $this->ajaxReturn($src);
    }

    /**
     * 伪代码，没有此方法多文件上传插件出错
     */
    public function index()
    {

    }


    //商品评价
    public function productAssess()
    {
        $id = $_GET['id'];
        $adminComment = M('product_comment_replies');
        $userComment = M('product_comments');
        $product = M('product');
        $users = M('users');

        $count = $userComment->where("product_id = $id")->count();
        $pageSize = 15;
        $page = new \Think\Page($count, $pageSize);
        $showPage = $page->show();
        $userReplay = $userComment->where("product_id = $id")->limit($page->firstRow, $page->listRows)->order('id DESC')->select(); //用户对于商品的留言

        foreach ($userReplay as &$val) {
            $getUserId = $val['user_id'];
            $getProductId = $val['product_id'];
            $username = $users->where("id = $getUserId")->find();
            $pro_name = $product->where("id = $getProductId")->find();
            $val['uname'] = $username['uname'];
            $val['proname'] = $pro_name['pro_name'];
        }


        // 查看该留言有没有处理
        foreach ($userReplay as &$val) {
            $getId = $val['id'];
            $counts = $adminComment->where("comment_id = '{$getId}'")->count();
            if ($counts) {
                $val['is_reply'] = true;
            } else {
                $val['is_reply'] = false;
            }
        }

        $this->assign('page', $showPage);
        $this->assign('data', $userReplay);
        $this->display('product_comment');
    }

    /**
     * 批量删除留言
     */
    public function doBatchDelete()
    {
        $fids = I('post.fids');
        $whereString = implode(', ', $fids);

        $product_comments = M('product_comments');
        $product_comment_replies = M('product_comment_replies');
        // 删除所有回复内容
        $product_comment_replies->where("comment_id in ({$whereString})")->delete();
        // 删除所有留言内容
        $affectedRows = $product_comments->where("id in ($whereString)")->delete();

        if ($affectedRows) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    /**
     * 执行删除操作
     */
    public function doDeleteFeedback()
    {
        $getFId = I('get.id');
        // 删除留言
        // 同时删除回复
        $product_comments = M('product_comments');
        $product_comment_replies = M('product_comment_replies');

        $product_comment_replies->where("comment_id = '{$getFId}'")->delete();

        $affectedRows = $product_comments->where("id='{$getFId}'")->delete();

        if ($affectedRows) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    /**
     * 获取回复页面
     */
    public function product_comment_view()
    {
        $id = $_GET['id'];
        $fid = $_GET['fid'];    //父级页面的id
        //1.将评价取出
        $pro_comments = M('product_comments');
        $users = M('users');

        $userReplay = $pro_comments->where("id = $id")->find(); //用户对于商品的留言
        $getUserId = $userReplay['user_id'];
        $username = $users->where("id = $getUserId")->find();
        $userReplay['uname'] = $username['uname'];

        //2.将回复取出
        $pcr = M('product_comment_replies');
        $replay = $pcr -> where("comment_id = $id") -> find();
        $userReplay['replay_content'] = $replay['content'];
        $userReplay['replay_id'] = $replay['id'];
        $userReplay['replay_user_id'] = $replay['admin_user_id'];
        $userReplay['fid'] = $fid;


        $this->assign('data', $userReplay);
        $this->display();
    }

    /**
     * 执行回复操作
     */
    public function doFeedbackAdd()
    {
        $admin_id = $_SESSION['id'];
        $comment_id = $_POST['reply_id'];
        $content = $_POST['content'];
        $id = $_POST['id'];

        $data['content'] = $content;
        $data['reply_time'] = time();
        $data['comment_id'] = $_POST['id'];
        $data['admin_user_id'] = $admin_id;

        $reply = M('product_comment_replies');
        // 如果以前被回复过，则修改
        // 如果以前没有进行过回复 ，则插入
        if (!empty($comment_id)){

            $row = $reply->where("comment_id='{$id}'")->data($data)->save();

            if ($row) {
                $this->success('执行成功');
                exit;
            }
            $this->error('执行失败');
        } else {
            if ($reply->create($data)) {
                if ($reply->add()) {
                    $this->success('添加成功');
                    exit;
                }
            }
            $this->error('上传失败');
        }
    }

    public function batchRecycle ()
    {
        if ( ! isset($_POST['batch_option']))
        {
            $this->error('非法操作');
            exit;
        }

        if ( $_POST['batch_option'] == -1)
        {
            $this->error('请选择执行操作');
            exit;
        }

        $getIds = I('post.ids');
        $getProductIds=implode(',', $getIds);
        $getProductIds = array('in',$getProductIds);
        $modelP = M('product');

        switch ($_POST['batch_option'])
        {
            case 1:
                $data['recycle'] = 0;
                $where['id']=$getProductIds;
                // dump($where);
                // die;
                $affectedRows = $modelP->where($where)->data($data)->save();
                $jumpURL = 'productList';
                break;
            case 2:
                $modelAttr = M('products_attr');
                $modelNum=M('productnum');
                $modelAlbum=M('product_album');
                $modelParam=M('product_param');
                $modelPromote=M('promote');
                //删除商品库存表
                $where1['product_id']=$getProductIds;
                $modelNum->where($where1)->delete();
                //删除参数表
                $where2['pro_id']=$getProductIds;
                $modelParam->where($where2)->delete();
                //删除促销表
                $modelPromote->where($where1)->delete();
                // 删除属性关联表
                $modelAttr->where($where2)->delete();
                // 删除商品表
                $where['id']=$getProductIds;
                $affectedRows = $modelP -> where($where) -> delete();
                $jumpURL='';
                break;
        }

        if ($affectedRows)
        {
            $this->success('执行成功', $jumpURL);
        }
        else
        {
            $this->error('执行失败');
        }
    }

    /**
     * 批量执行产品列表
     */
    public function batchDo ()
    {
        if ( ! isset($_POST['batch_option']))
        {
            $this->error('非法操作');
            exit;
        }

        if ( $_POST['batch_option'] == -1)
        {
            $this->error('请选择执行操作');
            exit;
        }

        $getIds = I('post.ids');
        $where = "id in (" . implode(', ', $getIds) . ') ';
        $modelP = M('product');

        switch ($_POST['batch_option'])
        {
            case 1:
                $data['recycle'] = 1;
                break;
            case 2:
                $data['is_on_sale'] = 0;
                break;
            case 3:
                $data['is_on_sale'] = 1;
                break;
        }

        $affectedRows = $modelP->where($where)->data($data)->save();
        if ($affectedRows)
        {
            $this->success('执行成功');
        }
        else
        {
            $this->error('执行失败');
        }

    }

    /**
     * 商品类型列表
     */
    public function productType(){
          // 获取商品类型
        $modelType = M('type');
        //分页处理，带关键字搜索
        if (isset($_GET)){
            foreach ($_GET as $key => $val) {
                if($key=='type_name'){
                    $val=str_replace('，',',',$val);
                    $val=trim($val,',');
                    $val=explode(',',$val);
                    foreach($val as $k=>$v){
                        $val[$k]='%'.trim($v).'%';
                        }
                     $val =array('like',$val,'OR');
                }
                $map[$key] = $val;
            }
        }
        // 查询总记录数
        $getPageCounts = $modelType->where($map)->count();
        // 每页显示 $pageSize 条数据
        $pageSize = 15;
        // 实例化分页类
        $page = new \Think\Page($getPageCounts, $pageSize, $map);

        $productType = $modelType->where($map)->order('id DESC')->limit($page->firstRow, $page->listRows)->select();
        $pageShow = $page->show();
        $this->assign('page', $pageShow);
        $this->assign('productType', $productType);
        $this->display('product_type');
    }

    public function productTypeAdd(){
        $this->display('product_type_add');
    }

    /**
     * 执行产品添加操作
     */
    public function doAddProductType()
    {

        // 处理基本通用信息，包含fetcher
        $post = I('post.');
        // 当商品名为空时，拒绝添加
        if (empty($post['type_name'])) {
            $this->error('商品类型添加失败');
            exit;
        }

        $modelType = M('type');
        if ($modelType->create($post)) {
            $getTypeId = $modelType->add();
            if (!$getTypeId) {
                $this->error('商品添加失败');
                exit;
            }
            if ($getTypeId) {
                // 写入日志
                $cLog = new \Admin\Controller\LogController();
                $cLog->setLog('商品类型（TYPE_ID:' . $getTypeId . '）添加成功');
                $this->success('商品类型添加成功', 'productType');
                exit;
            } else {
                $this->error('商品类型添加失败');
            }

        } else {
            $this->error('添加出错。');
        }
    }

    /**
     * 商品分类编辑
     */
    public function productTypeEdit(){
        $getTypeId = I('get.id');
        // 获取产品详细信息
        $modelType = M('type');
        $typeRes = $modelType->find($getTypeId);
        $this->assign('type', $typeRes);
        $this->display('product_type_edit');
    }

    /**
     * 执行商品分类编辑
     */
    public function doEditProductType(){
        $modelType = M('type');
        if ($modelType->create()) {
            if ($modelType->save()) {
                $setLog = new \Admin\Controller\LogController();
                $setLog->setLog('商品类型（TYPE_ID: ' .I('post.id') . '）修改成功！');
                $this->success('商品类型修改成功', 'productType');
                exit;
                }
            $this->error('修改失败');
        }
        $this->error('修改失败');
    }

    /**
     * 商品类型属性列表
     */
    public function typeAttr(){
        $type_name=I('get.type_name');
        $type_id=I('get.id');
        $typeAttrModel=M('attribute');
        $typeAttr=$typeAttrModel->where('type_id ='.$type_id)->select();
        $this->assign(array(
            'type_id'=>$type_id,
            'type_name'=>$type_name,
            'typeAttr'=>$typeAttr
            ));
        $this->display('product_typeattr');
    }

    /**
     * 商品类型属性添加
     */
    public function typeAttrAdd(){
        $type_id=I('get.type_id');
        $type_name=I('get.type_name');
        $this->assign(array(
            'type_id'=>$type_id,
            'type_name'=>$type_name,
            ));
        $this->display('product_typeattr_add');
    }

    /**
     * 执行商品类型属性添加
     */
    public function doTypeAttrAdd(){
        $data=I('post.');
          if (empty($data['attr_name'])) {
            $this->error('商品类型属性添加失败');
            exit;
        }
        $attrModel=M('attribute');
        $data['attr_values']=str_replace('，',',',$data['attr_values']);
        $data['attr_values']=trim($data['attr_values'],',');
        $getAttrId = $attrModel->add($data);
            if (!$getAttrId) {
                $this->error('商品类型属性添加失败');
                exit;
            }
            if ($getAttrId) {
                // 写入日志
                $cLog = new \Admin\Controller\LogController();
                $cLog->setLog('商品类型属性（ATTR_ID:' . $getAttrId . '）添加成功');
                $jumpURL = 'typeAttr/id/' . $data['type_id'].'/type_name/'.$data['type_name'];
                $this->success('商品类型属性添加成功', $jumpURL);
                exit;
            }else{
                $this->error('商品类型属性添加失败');
                }
    }

     /**
     * 商品类型属性编辑
     */
    public function typeAttrEdit(){
        $id=I('get.id');
        $type_name=I('get.type_name');
        $typeAttrModel=M('attribute');
        $attr=$typeAttrModel->where('id ='.$id)->find();
        $this->assign(array(
            'attr'=>$attr,
            'type_name'=>$type_name,
            ));
        $this->display('product_typeattr_edit');
    }

    /**
     * 执行商品类型属性编辑
     */
    public function doTypeAttrEdit(){
        $data=I('post.');
          if (empty($data['attr_name'])) {
            $this->error('商品类型属性修改失败');
            exit;
        }
        $attrModel=M('attribute');
        $data['attr_values']=str_replace('，',',',$data['attr_values']);
        $data['attr_values']=trim($data['attr_values'],',');
        $getAttrId = $attrModel->save($data);
            if (!$getAttrId) {
                $this->error('商品类型属性修改失败');
                exit;
            }
            if ($getAttrId) {
                // 写入日志
                $cLog = new \Admin\Controller\LogController();
                $cLog->setLog('商品类型属性（ATTR_ID:' . $getAttrId . '）修改成功');
                $jumpURL = 'typeAttr/id/' . $data['type_id'].'/type_name/'.$data['type_name'];
                $this->success('商品类型属性修改成功', $jumpURL);
                exit;
            }else{
                $this->error('商品类型属性修改失败');
                }
    }

    /**
     * 商品类型属性删除
     */
    public function typeAttrDel(){
        $attrId = I('get.id');
        $modelAttr = M('attribute');
        // 删除类型属性
        $affectedRow = $modelAttr->where(array('id' => $attrId))->delete();
        if ($affectedRow) {
            // 写入日志
            $cLog = new \Admin\Controller\LogController();
            $cLog->setLog('商品类型属性（ATTR_ID:'. $attrId . '）删除成功');
            $this->success('商品类型属性删除成功');
        } else {
            $this->error('商品类型属性删除失败');
        }
    }

    /**
     * 商品类型删除
     */
    public function typeDel(){
        $typeId = I('get.id');
        $modelType = M('type');
        // 删除分类下的类型属性
        $modelAttr = M('attribute');
        if($modelAttr->where(array('type_id' => $typeId))->count()){
        $affectedRow1 = $modelAttr->where(array('type_id' => $typeId))->delete();
            }else{
        $affectedRow1 =true;
            }
        // 删除分类
        $affectedRow2 = $modelType->where(array('id' => $typeId))->delete();
        if ($affectedRow1 AND $affectedRow2) {
            // 写入日志
            $cLog = new \Admin\Controller\LogController();
            $cLog->setLog('商品类型（TYPE_ID:'. $typeId . '）删除成功');
            $this->success('商品类型删除成功');
        } else {
            $this->error('商品类型删除失败');
        }
    }

    /**
     * 商品类型删除
     */
    public function productNumAdd(){
        //取出商品所有的单选属性
        $proid=I('get.id');
        $Attrs1=$this->getAttrs($proid,1);
        $Attrs2=$this->getAttrs($proid,2);
        $Num=$this->getNum($proid);
        $this->assign(array(
            'proid'=>$proid,
            'Attrs1'=>$Attrs1,
            'Attrs2'=>$Attrs2,
            'Num'=>$Num
            ));
        $this->display('product_num_add');
    }

    /**
     * 商品类型删除
     */
    public function doAddNum(){
        $data=I('post.');
        $product_id=I('post.product_id');
        $this->delNum($product_id);
        $ModelNum=M('productnum');
        $number=I('post.product_number');
        $attr=I('post.product_attr');
        foreach($number as $k=>$v){
            $_attr=array();
            foreach($attr as $k1=>$v1){
                if((int)$v1[$k] <= 0)
                    continue 2;
                $_attr[]=$v1[$k];
            }
        sort($_attr);
        $_attr=implode('|',$_attr);
        $res=$ModelNum->add(array(
            'product_id'=>$product_id,
            'product_number'=>(int)$v,
            'product_attr'=>$_attr
            ));
        }
        if ($res) {
            // 写入日志
            $cLog = new \Admin\Controller\LogController();
            $cLog->setLog('商品（PRO_ID:'. $product_id . '）库存更新成功');
            $this->success('商品库存添加成功', 'productList');
            exit;
        } else {
            $this->error('商品库存添加失败');
        }
    }

    /**
     * 取商品属性方法
     */
    private function getAttrs($proid,$Attr){
        $ModelAttr=M('products_attr');
        $map['pro_id']=$proid;
        if($Attr){
            $map['prop_id']=$Attr;
        }
        $Attrs=$ModelAttr->where($map)->select();
        return $Attrs;
    }

    /**
     * 取商品所有库存
     */
    private function getNum($proid){
        $ModelNum=M('productnum');
        $data=$ModelNum->where(array('product_id'=>$proid))->select();
        return $data;
    }

    /**
     * 取商品总库存
     */
    private function getAllNum($proid){
        $ModelNum=M('productnum');
        $data=$ModelNum->field('sum(product_number) as total')->where(array('product_id'=>$proid))->find();
        return $data['total'];
    }

    private function delNum($proid){
        $ModelNum=M('productnum');
        $ModelNum->where(array('product_id'=>$proid))->delete();
    }

    /**
     * 商品促销添加
     */
    public function promoteAdd(){
        $proId=I('get.id');
        $cats = $this->get_data();
        $cats2 = $this->get_data();
        $num=$this->getAllNum($proId);
        $this->assign(array(
            'product_number'=>$num,
            'product_id'=>$proId,
            'cats'=>$cats,
            'cats2'=>$cats2,
            ));
        $this->display('promote_add');
    }

    /**
     * 执行商品促销添加
     */
    public function doPromoteAdd(){
        $post=I('post.');
        // dump($post);
        // die;
        $ModelPro=M('promote');
        $ModelMem=M('members_rate');
        $ModelPro->where(array('product_id'=>$post['product_id']))->delete();
        $ModelMem->where(array('product_id'=>$post['product_id']))->delete();
        $status=$post['status'];
        if($status == 1){
            $data['status']=$status;
            $data['promote_num']=$post['promotenum1'];
            $data['product_id']=$post['product_id'];
            $data['rate']=$post['rate1'];
            $res=$ModelPro->add($data);
        }elseif($status ==2){
            $data['status']=$status;
            $data['product_id']=$post['product_id'];
            foreach ($post['memberrate'] as $k => $v)
            {
                // 如果价格为空就跳过
                if(trim($v) == '')
                    continue ;
                $data1['rate'] =$v;
                $data1['level_id']=$k+1;
                $data1['product_id']=$data['product_id'];
                // 插入数据库
                // dump($data1);
                // die;
                $ModelMem->add($data1);
            }
            $res=$ModelPro->add($data);
        }elseif($status == 3){
            $data['status']=$status;
            $data['promote_num']=$post['promotenum3'];
            $data['product_id']=$post['product_id'];
            if(!$post['gifts']){
                $this->error('商品赠品不能为空，添加失败！');
                exit;
            }else{
               $data['gifts'] = implode(',', $post['gifts']);
            }
            $res=$ModelPro->add($data);
        }elseif($status ==4){
            $data['status']=$status;
            $data['promote_num']=$post['promotenum3'];
            $data['condition']=$post['condition'];
            $data['product_id']=$post['product_id'];
            $data['gifts'] = implode(',', $post['gifts']);
            $res=$ModelPro->add($data);
        }elseif($status ==5){
            $data['product_id']=$post['product_id'];
            $data['status']=$status;
            $data['promote_num']=$post['promotenum4'];
            $data['rate']=$post['rate2'];
            if($post['start_time']){
                $data['start_time']=strtotime($post['start_time']);
            }else{
                $data['start_time']=time();
            }
            if(!$post['end_time']){
                $this->error('截止时间不能为空，添加失败！');
                exit;
            }else{
                $data['end_time']=strtotime($post['end_time']);
            }
            $res=$ModelPro->add($data);
        }elseif($status ==6){
            $data['product_id']=$post['product_id'];
            $data['status']=$status;
            $data['rate']=$post['rate'];
            if(!$post['comb']){
                $this->error('套餐商品不能为空，添加失败！');
                exit;
            }else{
                $data['combs'] = implode(',', $post['comb']);
            }
            $res=$ModelPro->add($data);
        }

        if ($res) {
            // 写入日志
            $cLog = new \Admin\Controller\LogController();
            $cLog->setLog('商品（PRO_ID:'. $data['product_id'] . '）促销更新成功');
            $this->success('商品促销添加成功', 'productList');
            exit;
        } else {
            $this->error('商品促销添加失败');
        }
    }
}
