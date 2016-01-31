<?php

namespace Admin\Controller;

use         Think\Controller;

class FeedbackController extends MyController
{
    /**
     * 获取列表模板
     * @return void
     */
    public function index()
    {
        $modelFeedback = M();
        $modelReply = M('feedback_replies');

        // 分页参数
        $feedbackCounts = $modelFeedback->table('__FEEDBACKS__ AS f, __USERS__ AS u')
            ->where('f.user_id = u.id')
            ->count();
        $pageSize = 15;
        $page = new \Think\Page($feedbackCounts, $pageSize);
        $showPage = $page->show();

        $feedbackRes = $modelFeedback->field('u.uname, f.*')
            ->table('__FEEDBACKS__ AS f, __USERS__ AS u')
            ->where('f.user_id = u.id')
            ->limit($page->firstRow, $page->listRows)
            ->order('f.id DESC')
            ->select();

        // 查看该留言有没有处理 
        foreach ($feedbackRes as &$val) {
            $getId = $val['id'];
            $counts = $modelReply->where("feedback_id = '{$getId}'")->count();
            if ($counts) {
                $val['is_reply'] = true;
            } else {
                $val['is_reply'] = false;
            }
        }

        $this->assign('page', $showPage);
        $this->assign('feedbackRes', $feedbackRes);
        $this->display('feedback_list');
    }

    /**
     * 批量删除留言
     * @return void
     */
    public function doBatchDelete()
    {
        $fids = I('post.fids');
        $whereString = implode(', ', $fids);

        $modelFeedbacks = M('feedbacks');
        $modelReplies = M('feedback_replies');
        // 删除所有回复内容
        $modelReplies->where("feedback_id in ({$whereString})")->delete();
        // 删除所有留言内容
        $affectedRows = $modelFeedbacks->where("id in ($whereString)")->delete();

        if ($affectedRows) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    /**
     * 执行删除操作
     * @return void
     */
    public function doDeleteFeedback()
    {
        $getFId = I('get.id');
        // 删除留言
        // 同时删除回复
        $modelFeedback = M('feedbacks');
        $modelReplies = M('feedback_replies');

        $modelReplies->where("feedback_id = '{$getFId}'")->delete();

        $affectedRows = $modelFeedback->where("id='{$getFId}'")->delete();

        if ($affectedRows) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    /**
     * 获取回复页面
     * @return void
     */
    public function view()
    {
        $getFeedbackId = I('get.id');
        $modelFeedback = M();
        $feedbackRes = $modelFeedback->field('u.uname, f.id, f.user_id, f.content, f.add_time')
            ->table('__FEEDBACKS__ AS f, __USERS__ AS u')
            ->where('f.user_id = u.id AND f.id=' . $getFeedbackId)
            ->find();
        $modelReply = M('feedback_replies');
        $replyRes = $modelReply->where("feedback_id='{$getFeedbackId}'")->find();
        $this->assign('replyRes', $replyRes);
        $this->assign('feedbackRes', $feedbackRes);
        $this->display('feedback_view');
    }

    /**
     * 执行回复操作
     */
    public function doFeedbackAdd()
    {
        $post = $_POST;
        // dump($post);
        // die;
        $post['reply_time'] = time();

        $modelReply = M('feedback_replies');
        // 如果以前被回复过，则修改
        // 如果以前没有进行过回复 ，则插入
        if (!empty ($post['reply_id'])) {
            $data['content'] = $post['content'];
            $data['reply_time'] = time();
            $data['admin_id'] = $post['admin_id'];

            $getFId = $post['reply_id'];

            $affectedRows = $modelReply->where("id='{$getFId}'")->data($data)->save();

            if ($affectedRows) {
                $this->success('执行成功');
                exit;
            }
            $this->error('执行失败');
        } else {
            if ($modelReply->create($post)) {
                if ($modelReply->add()) {
                    $this->success('添加成功');
                    exit;
                }
            }
            $this->error('上传失败');
        }
    }

    public function indexContact(){
         $db = M("index_contact");
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
     * 获取回复页面
     */
    public function viewContact()
    {
        $getFeedbackId = I('get.id');
        $modelFeedback = M('index_contact');
        $feedbackRes = $modelFeedback->where(array('id'=>$getFeedbackId))->find();
        $this->assign('data', $feedbackRes);
        $this->display('contactDes');
    }

    public function ajaxRead(){
        $id=I('post.id');
        $modelFeedback = M('index_contact');
        $data['status']=1;
        $res=$modelFeedback->where(array('id'=>$id))->save($data);
        if($res){
            $this->ajaxReturn(1);
        }else{
            $this->ajaxReturn(0);
        }
    }

    public function doDeleteContact(){
        $getFId = I('get.id');
        $modelFeedback = M('index_contact');
        $affectedRows=$modelFeedback->where(array('id'=>$getFId))->delete();
        if ($affectedRows) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }
}
