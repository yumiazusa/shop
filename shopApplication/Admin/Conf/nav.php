<?php
	
$navConfig = array(
	1 => array(
		'title' 	=> '系统设置',
		'items' 	=> array(
			2 => array('c' => 'Config', 'a' => 'web', 't' => '网站配置'),
			3 => array('c' => 'Config', 'a' => 'logo', 't' => 'Logo设置' ),
			4 => array('c' => 'Administrator', 'a'=>'admin', 't' => '管理员管理'),
			5 => array('c' => 'Config', 'a' => 'email', 't' => '邮件管理'),
			6 => array('c' => 'Pay', 'a' => 'list', 't' => '支付管理'),
			7 => array('c' => 'Keyword', 'a' => 'list', 't' => '关键词管理'),
			8 => array('c' => 'Config', 'a' => 'integral', 't' => '积分管理'),
			9 => array('c' => 'Config', 'a' => 'seo', 't' => 'SEO优化'),
			10 => array('c' => 'Database', 'a' => 'show', 't' => '数据管理')
		)
	),
	11 => array(
		'title' 	=> '用户管理',
		'items' 	=> array(
			12 => array('c' => 'User', 'a' => 'list', 't' => '用户管理'),
			13 => array('c' => 'Message', 'a' => 'list', 't' => '留言管理')
		)
	),
	14 => array(
		'title' 	=> '商品管理',
		'items' 	=> array(
			15 => array('c' => 'Product', 'a' => 'list', 't' => '商品列表'),
			16 => array('c' => 'Product', 'a' => 'add', 't' => '添加商品'),
			17 => array('c' => 'Product', 'a' => 'presell', 't' => '预售商品'),
			18 => array('c' => 'Product', 'a' => 'soldout', 't' => '下架商品')
		)
	),
	19 => array(
		'title' 	=> '订单管理',
		'items' 	=> array(
			20 => array('c' => 'Order', 'a' => 'list', 't' => '订单列表'),
			21 => array('c' => 'Order', 'a' => 'forsell', 't' => '预售商品'),
			22 => array('c' => 'Order', 'a' => 'invalid', 't' => '无效订单')
		)
	),
	23 => array(
		'title' => '广告管理',
		'items' => array(
			24 => array('c' => 'Adv', 'a' => 'list', 't' => '广告管理'),
			25 => array('c' => 'Link', 'a' => 'list', 't' => '友情链接')
		)
	),
	26 => array(
		'title' 	=> '售后管理',
		'items' 	=> array(
			27 => array('c' => 'Support', 'a' => 'download', 't' => '相关下载'),
			28 => array('c' => 'Support', 'a' => 'regist', 't' => '产品注册'),
			29 => array('c' => 'Support', 'a' => 'find', 't' => '网站查询'),
			30 => array('c' => 'Support', 'a' => 'faq', 't' => 'FAQ'),
			31 => array('c' => 'Support', 'a' => 'returned', 't' => '退货商品'),
			32 => array('c' => 'Support', 'a' => 'exchange', 't' => '换货商品'),
			33 => array('c' => 'Support', 'a' => 'repair', 't' => '维修商品')
		)
	),
	34 => array(
		'title' 	=> '新闻管理',
		'items' 	=> array(
			35 => array('c' => 'News', 'a' => 'category', 't' => '新闻分类'),
			36 => array('c' => 'News', 'a' => 'list', 't' => '新闻列表')
		)
	),
	37 => array(
		'title' 	=> '单页管理',
		'items' 	=> array(
			38 => array('c' => 'Archive', 'a' => 'list', 't' => '单页列表')
		)
	),
	39 => array(
		'title' 	=> '系统设置',
		'items' 	=> array(
			40 => array('c' => 'Service', 'a' => 'useful', 't' => '常用语设置'),
			41 => array('c' => 'Service', 'a' => 'client', 't' => '客户管理'),
			42 => array('c' => 'Service', 'a' => 'chat', 't' => '聊天窗口')
		)
	),
	43 => array(
		'title' 	=> '日志管理',
		'items' 	=> array(
			44 => array('c' => 'Log', 'a' => 'list', 't' => '日志列表')
		)
	)
);

return array(
	'NAV_CONFIG' => $navConfig
);