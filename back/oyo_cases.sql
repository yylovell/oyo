SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS `oyo_cases`;
CREATE TABLE `oyo_cases` (
  `id` int(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `title` varchar(16) NOT NULL DEFAULT '' COMMENT '标题',
  `customer` varchar(20) NOT NULL DEFAULT '' COMMENT '植入品牌',
  `product` varchar(20) NOT NULL DEFAULT '' COMMENT '植入产品',
  `platform` varchar(20) NOT NULL DEFAULT '' COMMENT '首播平台',
  `actors` varchar(20) NOT NULL DEFAULT '' COMMENT '演员',
  `time` datetime DEFAULT NULL COMMENT '首播时间',
  `rating` varchar(10) DEFAULT NULL COMMENT '收视率',
  `web_rating` varchar(10) NOT NULL DEFAULT '' COMMENT '网络播放量',
  `rank` varchar(10) NOT NULL DEFAULT '' COMMENT '内容等级',
  `content` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  `prize` varchar(20) NOT NULL DEFAULT '' COMMENT '奖项',
  `photo` varchar(120) NOT NULL DEFAULT '' COMMENT '图',
  `photo_thumb` varchar(120) NOT NULL DEFAULT '' COMMENT '缩略图',
  `photo1` varchar(120) NOT NULL DEFAULT '' COMMENT '详情图1',
  `photo2` varchar(120) NOT NULL DEFAULT '' COMMENT '详情图2',
  `photo3` varchar(120) NOT NULL DEFAULT '' COMMENT '详情图3',
  `photo4` varchar(120) NOT NULL DEFAULT '' COMMENT '详情图4',
  `is_classical` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否经典案例：0-否，1-是',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '修改时间',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否经典案例：0-消费电子，1-',
  PRIMARY KEY (`id`),
  KEY `time` (`time`) USING BTREE,
  KEY `is_classical` (`is_classical`) USING BTREE,
  KEY `type` (`type`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8 COMMENT='案例';

insert into `oyo_cases`(`id`,`title`,`customer`,`product`,`platform`,`actors`,`time`,`rating`,`web_rating`,`rank`,`content`,`prize`,`photo`,`photo_thumb`,`photo1`,`photo2`,`photo3`,`photo4`,`is_classical`,`create_time`,`update_time`,`type`) values('3','夏至未至','力士','小晶钻洗发水/水润丝滑洗发水/臻油修护洗','湖南卫视','郑爽、陈学冬','2017-06-11 00:00:00','','','深度定制','品牌代言人陈学冬作为该剧男主角联合女主郑爽与力士品牌和产品深度互动，并推动剧情发展，更有力士冠名定制时尚颁奖典礼的定制场景。','','20171221/e71351300b3de1a668b4c48c89975e19.jpg','4fa7f47a1e3e18875bf0ffc13d35f134.jpeg','20170724/03e91d34726fe9b7aadb6790e367ef44.jpg','20170724/d107bffafe0d073715dc90aebfc7152d.jpg','20170724/6b4fa3043d8c75dd6467ad0312cd7543.jpg','20170724/1e806f6962ea11ca7e9ae4eef666d07a.jpg','1','1499325354','1513825126','1');
insert into `oyo_cases`(`id`,`title`,`customer`,`product`,`platform`,`actors`,`time`,`rating`,`web_rating`,`rank`,`content`,`prize`,`photo`,`photo_thumb`,`photo1`,`photo2`,`photo3`,`photo4`,`is_classical`,`create_time`,`update_time`,`type`) values('4','致青春','花皙蔻','Miss Alice彩妆/护肤','东方卫视/安徽卫视','陈瑶、杨玏','2016-07-11 00:00:00','','','常规植入','出彩的情节创意增加观众记忆点及好感度，产品销量翻10倍。','','20170706/c8a3e61918a3b52cbb459d820eb820ea.jpg','bfbf3ab360b951a646b8921773a01f07.jpeg','20170728/9a82d664d0cd1f53f466336eae9b1399.jpg','20170728/f0960b9f5483f07d14910f9975806124.jpg','20170728/b169e266be85a759973ab064cf7dc334.jpg','20170728/8e052190c5b4c38d407891459d5a289f.jpg','0','1499328965','1501214979','1');
insert into `oyo_cases`(`id`,`title`,`customer`,`product`,`platform`,`actors`,`time`,`rating`,`web_rating`,`rank`,`content`,`prize`,`photo`,`photo_thumb`,`photo1`,`photo2`,`photo3`,`photo4`,`is_classical`,`create_time`,`update_time`,`type`) values('6','大闹天竺','百草味','夏威夷果','全国院线','王宝强、白客、柳岩','2017-01-28 00:00:00','','','常规植入','百草味首次尝试电影植入，并配合《大闹天竺》和贺岁喜剧风推出年货版坚果礼盒，合家欢的风格和电影契合度极高。','','20170706/fa92609bb8e57cff98f7a073ed6270a2.jpg','664ef3d786a5b6496d13c3482b23806a.jpeg','20170726/516df30745d886188e5b85af1a11ea9e.jpg','20170726/34bbed1fb62eb3ade87ac9b547dd6772.jpg','20170726/57307ff5b7c343df4fa3d9b757a34138.jpg','20170726/fc22554d6a0460618524d890ea721d8f.jpg','0','1499330652','1501058056','2');
insert into `oyo_cases`(`id`,`title`,`customer`,`product`,`platform`,`actors`,`time`,`rating`,`web_rating`,`rank`,`content`,`prize`,`photo`,`photo_thumb`,`photo1`,`photo2`,`photo3`,`photo4`,`is_classical`,`create_time`,`update_time`,`type`) values('7','繁星四月','思念','汤圆/水饺','江苏卫视','吴奇隆、戚薇','2017-04-18 00:00:00','','','深度植入','“思念”与本剧核心的“寻回亲情、爱情”的主题不谋而合，通过对剧中女二打工职业的绑定、多场思念餐厅的设计、以及剧中主演对产品的配合展现，为品牌提升整体形象。','','20170718/b38c617053500349b53586221b028192.jpg','d8d4ca6af4ab1dba2d8e56d465c564f5.jpeg','20170726/c6839f8b37b898f3fa0f803c22da27ea.jpg','20170726/d06e16b6ebf283c666a3ddb3e8811ef5.jpg','20170726/4691f037b77c0b20423933b6cef10e4e.jpg','20170726/d985c6517fdb64413ab144faa8ab9809.jpg','0','1499330795','1511776372','2');
insert into `oyo_cases`(`id`,`title`,`customer`,`product`,`platform`,`actors`,`time`,`rating`,`web_rating`,`rank`,`content`,`prize`,`photo`,`photo_thumb`,`photo1`,`photo2`,`photo3`,`photo4`,`is_classical`,`create_time`,`update_time`,`type`) values('8','三生三世十里桃花','泸州老窖','桃花醉','浙江卫视/东方卫视','杨幂、赵又廷','2017-01-30 00:00:00','','','定制级','桃花醉作为剧中关键道具，直接推动故事情节发展，泸州老窖借势推出同名产品桃花醉抢占市场，有初期产品知名度提升优势；和片方宣传同步，整合全渠道营销，发挥传播价值最大化。','','20170706/805046917f44964751e8ca9c20fdd9fb.jpg','a90eb6c8d9a8b7519cab6c56a4d18176.jpeg','20170726/d7b33a8ec293e4666834f2496375936e.jpg','20170726/48736ee5e49cdfa57b7c5b49d9aca9e0.jpg','20170726/e16ebf309cc4c9175b11c73fa55733b0.jpg','20170726/bbad7748e9480ae956da619d9fecd654.jpg','1','1499332455','1501039674','3');
insert into `oyo_cases`(`id`,`title`,`customer`,`product`,`platform`,`actors`,`time`,`rating`,`web_rating`,`rank`,`content`,`prize`,`photo`,`photo_thumb`,`photo1`,`photo2`,`photo3`,`photo4`,`is_classical`,`create_time`,`update_time`,`type`) values('9','好先生','江小白','白酒','浙江卫视/江苏卫视','孙红雷、张艺兴','2016-07-17 00:00:00','','','常规植入','在《好先生》中，江小白白酒勇敢直面自我、忠于内心的品牌调性，在几位主演身上得到了自然的演绎。配合创意性的植入形式，给观众留下了深刻的印象。','','20170706/c6463412bb206f2d0f4bd6de62f738a4.jpg','84dde5d38c0f25ec77577b10d0800433.jpeg','20170727/7ddf564f798f6cf81566aa4a970e53be.jpg','20170727/ae367d8985c7cea2faaa216a12934412.jpg','20170727/aabad36c5e383e5cfa9c3246c4740d10.jpg','20170727/2215ed25f91d2e9360bb05d4b271c915.jpg','1','1499333895','1501148924','3');
insert into `oyo_cases`(`id`,`title`,`customer`,`product`,`platform`,`actors`,`time`,`rating`,`web_rating`,`rank`,`content`,`prize`,`photo`,`photo_thumb`,`photo1`,`photo2`,`photo3`,`photo4`,`is_classical`,`create_time`,`update_time`,`type`) values('12','小别离','精锐教育','精锐教育课程','浙江卫视/北京卫视','黄磊、海清','2016-08-15 00:00:00','','','深度植入','精锐教育将自身的教学理念深度植入，和《小别离》传达的观点相同，直面中国教育弊端，和观众一起共话中国教育喜怒哀乐。','','20170706/1617d1ca20f1f834c4f36117034f1209.jpg','b9f740857a374a3b01d4611d4105739d.jpeg','20170726/c2d4848728d346585a04267e0b9763cd.jpg','20170726/a92311b1d5c23267a178046268eff84a.jpg','20170726/6ea7eb4be90e30c6c46ce1e946d205ce.jpg','20170726/5c73fd870ab07121813f658cdaaee3a6.jpg','1','1499335090','1501054979','4');
insert into `oyo_cases`(`id`,`title`,`customer`,`product`,`platform`,`actors`,`time`,`rating`,`web_rating`,`rank`,`content`,`prize`,`photo`,`photo_thumb`,`photo1`,`photo2`,`photo3`,`photo4`,`is_classical`,`create_time`,`update_time`,`type`) values('14','火锅英雄','江小白','白酒','全国院线','陈坤、白百合','2016-04-01 00:00:00','','','常规植入','《火锅英雄》故事发生于重庆的火锅店，与江小白主要铺货渠道高度吻合。在影片中有大量自然的植入空间，对于观众有简单直接的代入感，高度还原了品牌的消费场景。','','20170726/6ddc806aba91f2cb719b9cdfdf7afab0.jpg','875159b37f1ba7a89605097e75b1b73d.jpeg','20170726/10c132546b84b8a901b37e0c8952844b.jpg','20170726/59fe839bfdcab9b0692e3ee0bdf2cb10.jpg','20170726/e12fee174215701c89358eeaf1bc6779.jpg','20170726/df42bda34c9273a9b337ef0c45db993b.jpg','1','1501055915','1501216855','3');
insert into `oyo_cases`(`id`,`title`,`customer`,`product`,`platform`,`actors`,`time`,`rating`,`web_rating`,`rank`,`content`,`prize`,`photo`,`photo_thumb`,`photo1`,`photo2`,`photo3`,`photo4`,`is_classical`,`create_time`,`update_time`,`type`) values('15','十五年等待候鸟','娃哈哈','轻透小檬','湖南卫视','张若昀、孙怡','2016-03-16 00:00:00','','','常规植入','项目播出时间与品牌新品上架时间高度吻合，通过设计剧中“便利店打工”、“校园路演推广”等品牌推广渠道展现，结合品牌线下宣传，助力新品造势。','','20170726/eb7f382141b35d533e606f9ed313e346.jpg','94c286bc3e60e021e996a633d7dc26c5.jpeg','20170726/30a8928f2f9a5859914bf3b5003d0be1.jpg','20170726/b53bb311eb188b6e971ae3524ee459ba.jpg','20170726/da554516fa4e0390321867f69fe315ff.jpg','20170726/f48f0f86d9867c356f02717365a9e145.jpg','1','1501057996','1501058439','2');
insert into `oyo_cases`(`id`,`title`,`customer`,`product`,`platform`,`actors`,`time`,`rating`,`web_rating`,`rank`,`content`,`prize`,`photo`,`photo_thumb`,`photo1`,`photo2`,`photo3`,`photo4`,`is_classical`,`create_time`,`update_time`,`type`) values('16','相爱穿梭千年','欧莱雅','男女士护肤/彩妆/Hair','湖南卫视','郑爽、井柏然','2015-02-15 00:00:00','','','定制级','利用代言人优势全程为品牌打造定制级别植入；韩式植入法提升品牌美誉度，大获观众好评；配套的线下营销提升品牌各维度搜索指数。','','20170727/696a37614bab71cd8dc1826f0b2d250b.jpg','c20c9e3ca80aceaf0371f20f74578cfb.jpeg','20170727/af75adb03b639b885c077d4bfca245a7.jpg','20170727/c941b28e3a08650bd8c2905b8c14cde0.jpg','20170727/0bb3d27af245c81c6f821ecc91141165.jpg','20170727/ce77b4e79a69eab200e6c0a4268e7542.jpg','0','1501136317','1501136317','1');
insert into `oyo_cases`(`id`,`title`,`customer`,`product`,`platform`,`actors`,`time`,`rating`,`web_rating`,`rank`,`content`,`prize`,`photo`,`photo_thumb`,`photo1`,`photo2`,`photo3`,`photo4`,`is_classical`,`create_time`,`update_time`,`type`) values('17','两生花','美宝莲','眉笔/CC霜','浙江卫视、江苏卫视','刘恺威、王丽坤','2015-06-12 00:00:00','','','深度植入','美宝莲在中国的首次植入，通过对产品的深度挖掘，叠加“泰坦尼克画像”式的创意，以及专柜购买等情节设计，为观众完整展现美宝莲品牌形象。','','20170727/43e86d0cedc80846fcabcd7252c0bd22.jpg','264989454a3a89674a727421abb0e3c4.jpeg','20170727/3e21e388fd02479e941a11de2cd5088d.jpg','20170727/ae2bd4361a15f38d7cf2657a272abee2.jpg','20170727/8ef66675fe409a2f4652e5cd0eceeb3f.jpg','20170727/46491cc6f8f4aaf25f0053eb1ba63a18.jpg','0','1501136959','1501136959','1');
insert into `oyo_cases`(`id`,`title`,`customer`,`product`,`platform`,`actors`,`time`,`rating`,`web_rating`,`rank`,`content`,`prize`,`photo`,`photo_thumb`,`photo1`,`photo2`,`photo3`,`photo4`,`is_classical`,`create_time`,`update_time`,`type`) values('19','老九门','携程','携程APP','爱奇艺、东方卫视','陈伟霆、赵丽颖、张艺兴','2016-07-04 00:00:00','','','软中插','欧游娱乐携手爱奇艺共同打造全新“软中插”合作形式，与片方编创团队共同进行头脑风暴，结合剧中人物关系，为品牌量身打造专属软中插。','','20170727/2cacf30cd415265de10bcc3519b0782e.jpg','bcb43eac6a6e8588eaead33fabe803f2.jpeg','20170727/ef7c9efb610a243a3868227b1f0ee56c.jpg','20170727/a0e08adbdf92892978ec946109d59e51.jpg','20170727/e684a1b858b9c4b39ad2f86437ccc073.jpg','20170727/01f0c7bc49b3fd7eb153709d58568d31.jpg','1','1501144053','1501151438','4');
insert into `oyo_cases`(`id`,`title`,`customer`,`product`,`platform`,`actors`,`time`,`rating`,`web_rating`,`rank`,`content`,`prize`,`photo`,`photo_thumb`,`photo1`,`photo2`,`photo3`,`photo4`,`is_classical`,`create_time`,`update_time`,`type`) values('22','test','test','test','test','test','0000-00-00 00:00:00','','','','test','','20171127/4942244e4bd43d62d26fef1d6db92212.jpg','21941160dd3f5f6080f37b05e6c56fd6.jpeg','','','','','1','1511774272','1511776380','2');
insert into `oyo_cases`(`id`,`title`,`customer`,`product`,`platform`,`actors`,`time`,`rating`,`web_rating`,`rank`,`content`,`prize`,`photo`,`photo_thumb`,`photo1`,`photo2`,`photo3`,`photo4`,`is_classical`,`create_time`,`update_time`,`type`) values('23','yy','','','','','0000-00-00 00:00:00','','','','','','20171221/3778006e14a71f034e61a1116457634a.jpg','261264811de5c30aa426b9085dcea128.jpeg','','','','','1','1511777127','1513934702','1');
insert into `oyo_cases`(`id`,`title`,`customer`,`product`,`platform`,`actors`,`time`,`rating`,`web_rating`,`rank`,`content`,`prize`,`photo`,`photo_thumb`,`photo1`,`photo2`,`photo3`,`photo4`,`is_classical`,`create_time`,`update_time`,`type`) values('27','特特特','3','3','3','3','2018-06-13 00:00:00','','','','3','','20171222/3f56f17b60a3165305dfaa12f3cd4193.jpg','591ddfcb5224d40dc6e6df9d177ef2d9.jpeg','20180612/e387bd230e3480c4e32537c6492b759d.jpg','20180612/69697ec2b95b221819b256c7e4b4d753.jpg','20180612/efc1adb9576935da3c17ccb31deb9192.jpg','20180612/3daea73735462e883fecf9fc59d534d7.png','0','1513949494','1528784124','1');
