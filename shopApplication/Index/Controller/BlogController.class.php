<?php
namespace Index\Controller;
use Think\Controller;
class BlogController extends MyController {
    public function __construct() {
        parent::__construct();
    }
    
    public function ajaxBlog(){
        $blogLists=S('blogLists',null);
        $blogLists=S('blogLists');
        if(!$blogLists){
            $Blogs=M('blog');
            $blogLists=$Blogs->where(array('is_show'=>1))->order('create_time DESC')->limit(6)->select();
            S('blogLists',$blogLists,3600*6);
        }
        if($blogLists){
            $str='';
            foreach($blogLists as $k=>$v){
                $key=$k+1;
                $str.='<div class="col-sm-4">';
                $str.='<div class="team-members" data-id="'.$key.'">';
                $str.='<div class="team-avatar">';
                $str.='<img class="img-responsive" src="/Public/Index/Blog/'.$v['list_image'].'" alt=""></div>';
                $str.='<div class="team-desc"><div class="team-details">';
                $str.='<h4>'.$v['title'].'</h4><span>'.$v['keywords'].'</span>';
                $str.='</div></div></div></div>';
            }
        }
        echo !empty($str) ? compress_html($str) : 0;
    }


    public function ajaxBlogWord(){
    	$filter=I('post.filter')-1;
    	$blogLists=S('blogLists');
        if(!$blogLists){
            $Blogs=M('blog');
            $blogLists=$Blogs->where(array('is_show'=>1))->order('create_time DESC')->limit(6)->select();
            S('blogLists',$blogLists,3600*6);
        }
        if($blogLists){
        	$str='';
            $str.='<h3>'.$blogLists[$filter]['title'].'</h3>';
            $str.='<p>'.$blogLists[$filter]['description'].'</p>';
            $str.='<div class="margin-bottom-40" style="margin-left: 75%;"><a style="cursor:pointer;color:#E84D1C;" href="'.U('blog',array('id'=>$blogLists[$filter]['id'])).'" target="_blank">查看详细...</a></div>';
       }
        echo !empty($str) ? compress_html($str) : 0;
    }

    public function blogList(){
                //SEO
        $ident ="Blog";
        $idents =$this->seo($ident);
        $this->assign("title",$idents['title']);
        $this->assign("keywords",$idents['keywords']);
        $this->assign("description",$idents['description']);


        $BlogModel=M('blog');
        $mapblog['a.is_show']=array('eq',1);
        $count = $BlogModel->alias('a')->where($mapblog)->count();
        $Page  = new \Think\MyPage($count,6);
        $show  = $Page->show();
        $blogs = $BlogModel->alias('a')->field('a.id,a.type,a.title,a.create_time,a.description,a.clicktimes,a.author,b.images,b.video')
              ->join('LEFT JOIN im_blog_album b ON a.id=b.blog_id')
              ->where($mapblog)
              ->limit($Page->firstRow.','.$Page->listRows)
              ->group('a.id')
              ->order('create_time DESC')
              ->select();
        $blogrank=$BlogModel->field('id,title,list_image,description')->where(array('is_show'=>1))->order('clicktimes DESC')->limit(3)->select();
        $this->assign('blogrank',$blogrank);
        $this->assign('blogs',$blogs);
        $this->assign('page',$show);
        $this->display();
    }


    public function blog(){
        $ident ="Blogpage";
        $idents =$this->seo($ident);
        $this->assign("keywords",$idents['keywords']);
        $this->assign("description",$idents['description']);


    	$id=I('get.id');
        $blogModel=M('blog');
        $res=$blogModel->where(array('id'=>$id,'is_show'=>1))->find();
        if(!$res){
            $this->display('Public:404');
            exit;
        }else{
            $album=M('blog_album');
            if($res['type']==1){
                $album=$album->where(array('blog_id'=>$id))->select();
            }elseif($res['type']==2){
                $album=$album->where(array('blog_id'=>$id))->find();
            }
            $map['id'] =array('eq',$id);
            //点击次数加一
            $blogModel->where($map)->setInc('clicktimes',1);
            $commentModel=M('blog_comments');
            $comments=$commentModel->where(array('blog_id'=>$id))->order('comment_time DESC')->limit(2)->select();
            foreach($comments as $k=>$v){
                $replies=M('blog_comment_replies')->where(array('replyid'=>$v['id'],'blog_id'=>$id))->order('reply_time ASC')->select();
                if($replies){
                    $comments[$k]['replies']=$replies;
                }
            }
            // dump($comments);
            // die;
            $this->assign('comments',$comments);
            $this->assign('album',$album);
            $this->assign('blog',$res);
            $this->assign("title",$res['title'].'--博文中心');
        }
        $this->display();
    }

    public function ajaxComment(){
        $data=I('post.');
        $badword=M('badwords')->getField('badword',true);
        $badword1 = array_combine($badword,array_fill(0,count($badword),'*'));
        $data['username']=strtr($data['username'],$badword1);
        $data['contents']=strtr($data['contents'],$badword1);
        $data['comment_time']=time();
        $commentModel=M('blog_comments');
        if($commentModel->create($data)){
            $getComId = $commentModel->add($data);
            if($getComId){
              $com=$commentModel->where(array('id'=>$getComId))->find();
              $str ='';
              $str .='<div class="media" style="padding-bottom:10px"><a href="javascript:;" class="pull-left">';
              $str .='<img src="/Public/Index/Header/'.$com['header'].'.png" alt="" class="media-object"></a>';
              $str .='<div class="media-body" id="comment'.$com['id'].'">';
              $str .='<h4 class="media-heading">'.$com['username'].'<span>'.date('Y-m-d H:i',$com['comment_time']).' / <a class="reply" href="javascript:;" data-replyid="'.$com['id'].'">回复</a></span></h4>';
              $str .='<div style="word-break:break-all">'.$com['contents'].'</div></div></div>';
            }
        }
       echo !empty($str) ? compress_html($str) : 0;
    }


    public function ajaxCommentmore(){
        $page=I('post.page');
        $blog_id=I('blog_id');
        $commentModel=M('blog_comments');
        $perpage = 2;
        $offset = ($page - 1) * $perpage;
        $comments=$commentModel->where(array('blog_id'=>$blog_id))->order('comment_time DESC')->limit($offset,$perpage)->select();
        $str ='';
        foreach($comments as $k=>$v){
              $str .='<div class="media" style="padding-bottom:10px"><a href="javascript:;" class="pull-left">';
              $str .='<img src="/Public/Index/Header/'.$v['header'].'.png" alt="" class="media-object"></a>';
              $str .='<div class="media-body" id="comment'.$v['id'].'">';
              $str .='<h4 class="media-heading">'.$v['username'].'<span>'.date('Y-m-d H:i',$v['comment_time']).' / <a class="reply" href="javascript:;" data-replyid="'.$v['id'].'">回复</a></span></h4>';
              $str .='<div style="word-break:break-all">'.$v['contents'].'</div>';
            $replies=M('blog_comment_replies')->where(array('replyid'=>$v['id'],'blog_id'=>$blog_id))->order('reply_time ASC')->select();
            if($replies){
                foreach($replies as $k1=>$v1){
                    $str .='<div class="media" style="padding-bottom:10px"><a href="javascript:;" class="pull-left">';
                    $str .='<img src="/Public/Index/Header/'.$v1['header'].'.png" alt="" class="media-object"></a>';
                    $str .='<div class="media-body" id="comment'.$v1['id'].'">';
                    $str .='<h4 class="media-heading">'.$v1['username'].'<span>'.date('Y-m-d H:i',$v1['reply_time']).' </span></h4>';
                    $str .='<div style="word-break:break-all">'.$v1['contents'].'</div></div></div>';
                }
            }
             $str.='</div></div>';
        }
        echo !empty($str) ? compress_html($str) : 0;
    }

    public function ajaxCommentreply(){
        $post=I('post.');
        $post['reply_time']=time();
        $badword=M('badwords')->getField('badword',true);
        $badword1 = array_combine($badword,array_fill(0,count($badword),'*'));
        $data['username']=strtr($data['username'],$badword1);
        $data['contents']=strtr($data['contents'],$badword1);
        $commentModel=M('blog_comment_replies');
        if($commentModel->create($post)){
            $getComId = $commentModel->add($post);
            if($getComId){
              $com=$commentModel->where(array('id'=>$getComId))->find();
              $str ='';
              $str .='<div class="media" style="padding-bottom:10px"><a href="javascript:;" class="pull-left">';
              $str .='<img src="/Public/Index/Header/'.$com['header'].'.png" alt="" class="media-object"></a>';
              $str .='<div class="media-body" id="comment'.$com['id'].'">';
              $str .='<h4 class="media-heading">'.$com['username'].'<span>'.date('Y-m-d H:i',$com['reply_time']).'</span></h4>';
              $str .='<div style="word-break:break-all">'.$com['contents'].'</div></div></div>';
                }
            }
            echo !empty($str) ? compress_html($str) : 0;
    }
}

