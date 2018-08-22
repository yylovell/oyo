SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS `oyo_benefit`;
CREATE TABLE `oyo_benefit` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `title` varchar(60) NOT NULL DEFAULT '' COMMENT '标题',
  `des` varchar(15) NOT NULL DEFAULT '' COMMENT '描述',
  `tag` varchar(20) NOT NULL DEFAULT '' COMMENT '图标',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='福利待遇列表';

insert into `oyo_benefit`(`id`,`title`,`des`,`tag`,`create_time`,`update_time`) values('2','带薪年假','正式员工拥有带薪年假、病假','headphones','1513221114','1513221114');
insert into `oyo_benefit`(`id`,`title`,`des`,`tag`,`create_time`,`update_time`) values('3','国外旅游','每年国外游2次，不定期国内游啊','plane','1513221139','1513228242');
insert into `oyo_benefit`(`id`,`title`,`des`,`tag`,`create_time`,`update_time`) values('4','追星福利','工作追星两不误','camera-retro','1513221165','1513221165');
insert into `oyo_benefit`(`id`,`title`,`des`,`tag`,`create_time`,`update_time`) values('5','定期体检','每年全员体检一次','heart','1513221189','1513221189');
insert into `oyo_benefit`(`id`,`title`,`des`,`tag`,`create_time`,`update_time`) values('6','员工培训','为员工制定个性培训课程 ','film','1513221213','1513221213');
