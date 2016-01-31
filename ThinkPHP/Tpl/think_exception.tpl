<?php
    if(C('LAYOUT_ON')) {
        echo '{__NOLAYOUT__}';
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
<title>系统发生错误</title>
<style type="text/css">
*{ padding: 0; margin: 0; }
html{ overflow-y: scroll; }
body {
	background: #f9fee8;
	margin: 0;
	padding: 20px;
	font-family:Arial, Helvetica, sans-serif;
	font-size:14px;
	color:#666666;
}
/*body{ background: #fff; font-family: '微软雅黑'; color: #333; font-size: 16px; }
*/img{ border: 0; }
.error{ padding: 24px 48px; }
.face{ font-size: 100px; font-weight: normal; line-height: 120px; margin-bottom: 12px; }
h1{ font-size: 32px; line-height: 48px; }
.error .content{ padding-top: 10px}
.error .info{ margin-bottom: 12px; }
.error .info .title{ margin-bottom: 3px; }
.error .info .title h3{ color: #000; font-weight: 700; font-size: 16px; }
.error .info .text{ line-height: 24px; }
.copyright{ padding: 12px 48px; color: #999; }
.copyright a{ color: #000; text-decoration: none; }
.error_page {
	text-align:center;
	width: 600px;
	padding: 50px;
	margin: auto;
}
.error_page h1 {
	margin: 20px 0 0;
}
.error_page p {
	margin: 10px 0;
	padding: 0;
}
a {
	color: #9caa6d;
	text-decoration:none;
}
a:hover {
	color: #9caa6d;
	text-decoration:underline;
}
</style>
</head>
<body>
<div class="error">
<!-- <p class="face">:(</p> -->
<div class="error_page"> <img alt="" src="/Main.jpg">
          <h1>我们非常抱歉...</h1>
          <p>你访问的页面不存在！</p>
          <p><a href="">返回网站主页</a></p>
        </div>
<div class="content">
<?php if(isset($e['file'])) {?>
<h1><?php echo strip_tags($e['message']);?></h1>
	<div class="info">
		<div class="title">
			<h3>错误位置</h3>
		</div>
		<div class="text">
			<p>FILE: <?php echo $e['file'] ;?> &#12288;LINE: <?php echo $e['line'];?></p>
		</div>
	</div>
<?php }?>
<?php if(isset($e['trace'])) {?>
	<div class="info">
		<div class="title">
			<h3>TRACE</h3>
		</div>
		<div class="text">
			<p><?php echo nl2br($e['trace']);?></p>
		</div>
	</div>
<?php }?>
</div>
</div>
</body>
</html>
