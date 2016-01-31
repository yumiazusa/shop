<?php
return array(
	//'配置项'=>'配置值'
    /* 数据库设置 */
    'DB_TYPE'               =>  'mysql',            // 数据库类型
    'DB_HOST' => 'localhost',   // 服务器地址
    'DB_NAME' => 'imagery',             // 数据库名
    'DB_USER' => 'root',             // 用户名
    'DB_PWD' => 'root',            // 密码
    'DB_PORT'               =>  '3306',             // 端口
    'DB_PREFIX' => 'im_',            // 数据库表前缀
    'DB_PARAMS'             =>  array(),            // 数据库连接参数
    'DB_DEBUG'              =>  TRUE,               // 数据库调试模式 开启后可以记录SQL日志
    'DB_FIELDS_CACHE'       =>  true,               // 启用字段缓存
    'DB_CHARSET'            =>  'utf8',             // 数据库编码默认采用utf8
    'TMPL_L_DELIM'          =>  '<{',
    'TMPL_R_DELIM'          =>  '}>',
    'TMPL_ACTION_ERROR'     =>  'Index:jump',      // 错误跳转页
    'TMPL_ACTION_SUCCESS'   =>  'Index:jump',      // 成功页跳转
);