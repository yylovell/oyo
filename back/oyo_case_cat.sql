SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS `oyo_case_cat`;
CREATE TABLE `oyo_case_cat` (
  `id` int(12) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(10) NOT NULL DEFAULT '' COMMENT '分类名称',
  `des` varchar(100) NOT NULL DEFAULT '' COMMENT '描述',
  `weight_val` tinyint(1) NOT NULL DEFAULT '1' COMMENT '权值，越小优先级越高',
  `is_send` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否启用：0-否，1-是',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='案例分类';

insert into `oyo_case_cat`(`id`,`name`,`des`,`weight_val`,`is_send`,`create_time`,`update_time`) values('1','日化类','test','1','1','1513135236','1513135645');
insert into `oyo_case_cat`(`id`,`name`,`des`,`weight_val`,`is_send`,`create_time`,`update_time`) values('2','食品饮料','','2','1','1513135681','1513135681');
insert into `oyo_case_cat`(`id`,`name`,`des`,`weight_val`,`is_send`,`create_time`,`update_time`) values('3','酒类','','3','1','1513135694','1513135694');
insert into `oyo_case_cat`(`id`,`name`,`des`,`weight_val`,`is_send`,`create_time`,`update_time`) values('4','其他','','4','1','1513135704','1513135704');
insert into `oyo_case_cat`(`id`,`name`,`des`,`weight_val`,`is_send`,`create_time`,`update_time`) values('5','test','','5','1','1513155599','1513649745');
insert into `oyo_case_cat`(`id`,`name`,`des`,`weight_val`,`is_send`,`create_time`,`update_time`) values('6','hsh','','45','1','1513648324','1513649791');
insert into `oyo_case_cat`(`id`,`name`,`des`,`weight_val`,`is_send`,`create_time`,`update_time`) values('7','haha','6','55','0','1513648799','1513649771');
