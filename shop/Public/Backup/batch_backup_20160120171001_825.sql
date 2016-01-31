/* This file is created by MySQLReback 2016-01-20 17:10:01 */
 /* 创建表结构 `im_aboutus`  */
 DROP TABLE IF EXISTS `im_aboutus`;/* MySQLReback Separation */ CREATE TABLE `im_aboutus` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `title` varchar(30) NOT NULL,
  `image` varchar(250) NOT NULL,
  `contents` text CHARACTER SET utf16 NOT NULL,
  `coordinate` varchar(250) NOT NULL COMMENT '坐标',
  `maplabel` varchar(25) NOT NULL COMMENT '地图标签',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;/* MySQLReback Separation */
 /* 插入数据 `im_aboutus` */
 INSERT INTO `im_aboutus` VALUES ('1','关于滇晟','2016-01-16/5699fede4d4f4.JPG','&lt;p&gt;\r\n	&lt;span style=&quot;font-size:32px;&quot;&gt;&lt;strong&gt;临沧滇晟农林发展有限公司&lt;/strong&gt;&lt;/span&gt; \r\n&lt;/p&gt;\r\n&lt;p&gt;\r\n	&lt;span style=&quot;font-size:24px;line-height:2.5;&quot;&gt;公司地址：&lt;/span&gt; \r\n&lt;/p&gt;\r\n&lt;p&gt;\r\n	&lt;span style=&quot;font-size:24px;line-height:2.5;&quot;&gt;公司邮箱：&lt;/span&gt; \r\n&lt;/p&gt;\r\n&lt;p&gt;\r\n	&lt;span style=&quot;font-size:24px;line-height:2.5;&quot;&gt;公司电话：&lt;/span&gt; \r\n&lt;/p&gt;\r\n&lt;p&gt;\r\n	&lt;span style=&quot;font-size:24px;line-height:2.5;&quot;&gt;公司传真：&lt;/span&gt; \r\n&lt;/p&gt;','102.676915,25.053905','滇晟公司');/* MySQLReback Separation */