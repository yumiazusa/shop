<?php
    
namespace   Admin\Controller;
use         Think\Controller;

class SmsController extends MyController
{

    public function __construct ()
    {
        parent::__construct();
    }

    /**
     * 获取短信配置界面，初始化配置信息
     * @date 2015-01-13
     * @return void
     */
    public function index ()
    {
        $modelSms = M('sys_config');
        $modelTmp = M('sms_templates');
        $smsConfig = array();
        $res = $modelSms->where("cname LIKE 'sms_%'")->select();

        $pageCounts = $modelTmp->count();       // 总记录数
        $pageSize   = 10;                        // 每页数量，需要与getPageByAjax里的分页一致

        // Ajax分页类
        $page = new \Think\AjaxPage($pageCounts, $pageSize);

        // 获取数据
        $tmpArr = $modelTmp->limit($page->firstRow, $page->listRows)->select();

        // 将获取结果转化为一维数组保存
        foreach ($res as $v)
        {
            $key = $v['cname'];
            $smsConfig[$key] = $v['cvalue'];
        }

        // 分页信息赋值给变量
        $pageShow = $page->show();

        $this->assign('pageShow', $pageShow);
        $this->assign('tmpArr', $tmpArr);
        $this->assign('smsConfig', $smsConfig);
        $this->display('config');
    }


    /**
     * Ajax 请求分页
     * @date 2015-01-13
     * @return void
     */
    public function getPageByAjax()
    {
        $modelTmp = M('sms_templates');

        $pageCounts = $modelTmp->count();           // 总记录数
        $pageSize   = 10;                           // 每页数量，需要与index里面的方法保持一致

        // Ajax分页类
        $page = new \Think\AjaxPage($pageCounts, $pageSize);

        // 获取数据
        $tmpArr = $modelTmp->limit($page->firstRow, $page->listRows)->select();

        $string = '';
        foreach($tmpArr as $val)
        {
            $string .= '<tr>';
            $string .= '<td style="text-align:center;">'.$val['id'].'</td>';
            $string .= '<td>' . $val['tmp_title'] . '</td>';
            $string .= '<td style="text-align:center;">' . $val['tmp_no'] . '</td>';
            $string .= '<td>' . $val['tmp_content'] . '</td>';
            $string .= '<td style="text-align:center;">';
            $string .= '    <a href="editTmp/id/' . $val['id'] . '"><i class="icon-pencil"></i> 编辑</a>&nbsp;&nbsp;|&nbsp;&nbsp;';
            $string .= '    <a href="deleteTmp/id/' . $val['id'] . '" onclick="return confirm(\'您确定删除该模板吗？\');"><i class="icon-trash"></i> 删除</a>';
            $string .= '</td>';
            $string .= '</tr>';

            $string .= '<tr>';
            $string .= '    <td colspan="5">';
            $string .= '        <div class="pagination alternate text-right" style="margin:0">';
            $string .=              $page->show();
            $string .= '        </div>  ';
            $string .= '    </td>';
            $string .= '</tr>';
        }

        $this->ajaxReturn($string);
    }

    /**
     * 执行删除操作
     * @date 2015-01-13
     * @return [type] [description]
     */
    public function deleteTmp()
    {
        $where['id'] = I('get.id');
        $modelTmp = M('sms_templates');
        if ($modelTmp->where($where)->delete())
        {
            $this->success('删除成功.');
        }
        else
        {
            $this->error('删除失败.');
        }
    }

    /**
     * 获取添加模板
     * @date 2015-01-13
     */
    public function addTmp ()
    {
        $this->display('tmp_add');
    }

    /**
     * 执行添加操作
     * @return void
     */
    public function doAddTmp ()
    {
        $modelSms = M('sms_templates');

        if ($modelSms->create())
        {
            if ($modelSms->add())
            {
                $this->success('模板添加成功.', 'index');
                exit;
            }
        }
        $this->error('添加失败.');
    }

    /**
     * 获取短信修改模板
     * @date 2015-01-13
     * @return void
     */
    public function editTmp()
    {
        $tID = I('get.id');        // 获取模板id

        $modelTmp = M('sms_templates');
        $tmpInfo = $modelTmp->where("id='{$tID}'")->find();

        $this->assign('tmpInfo', $tmpInfo);
        $this->display('tmp_edit');
    }

    /**
     * 执行编辑操作
     * @return void
     */
    public function doEditTmp()
    {
        $modelTmp = M('sms_templates');
        if ($modelTmp->create())
        {
            if ($modelTmp->save())
            {
               $this->success('修改成功.', 'index');
               exit;
            }
        }
        $this->error('修改失败.');
    }

    /**
     * 保存基本配置修改信息
     * @date 2015-01-13
     * @return void
     */
    public function saveConfig ()
    {
        $configInfo = I('post.');
        $modelConfig = M('sys_config');
        $counts = 0;     // 执行成功的记录数

        foreach ($configInfo as $k=>$v)
        {
            $field = "cname = '{$k}'";          // 需要修改的行
            $value = array('cvalue' => $v);     // 需要修改的值

            $counts += $modelConfig->where($field)->save($value);
        }

        if ($counts)
        {
            $this->success('修改成功');
        }
        else
        {
            $this->error('修改失败');
        }
    }
}