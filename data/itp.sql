-- phpMyAdmin SQL Dump
-- version 4.1.6
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2014-08-25 15:30:21
-- 服务器版本： 5.6.16
-- PHP Version: 5.5.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `itp`
--

-- --------------------------------------------------------

--
-- 表的结构 `itp_comments`
--

CREATE TABLE IF NOT EXISTS `itp_comments` (
  `comment_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `lecture_id` bigint(20) NOT NULL,
  `reviewer_uid` bigint(20) NOT NULL,
  `comment_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `comment_ranking` int(2) NOT NULL,
  `comment_detail` text,
  PRIMARY KEY (`comment_id`),
  KEY `comment_course_id` (`lecture_id`),
  KEY `reviewer_uid` (`reviewer_uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- 转存表中的数据 `itp_comments`
--

INSERT INTO `itp_comments` (`comment_id`, `lecture_id`, `reviewer_uid`, `comment_time`, `comment_ranking`, `comment_detail`) VALUES
(1, 100, 1003, '2014-06-16 05:22:55', 4, '非常赞的课程!'),
(2, 100, 1002, '2014-07-16 09:31:45', 5, '非常赞的课程!'),
(3, 100, 1001, '2014-08-05 14:24:15', 4, 'alert(''防XSS攻击测试'');');

-- --------------------------------------------------------

--
-- 表的结构 `itp_companies`
--

CREATE TABLE IF NOT EXISTS `itp_companies` (
  `uid` bigint(20) NOT NULL AUTO_INCREMENT,
  `company_name` varchar(64) NOT NULL,
  `company_region` varchar(16) NOT NULL,
  `company_province` varchar(16) NOT NULL,
  `company_city` varchar(16) NOT NULL,
  `company_address` varchar(128) NOT NULL,
  `company_field` varchar(128) NOT NULL,
  `company_scale` int(8) NOT NULL,
  `company_phone` varchar(24) NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `itp_courses`
--

CREATE TABLE IF NOT EXISTS `itp_courses` (
  `course_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `course_name` varchar(128) NOT NULL,
  `course_is_public` tinyint(1) NOT NULL DEFAULT '1',
  `course_type_id` int(4) NOT NULL,
  `teacher_id` bigint(20) NOT NULL,
  `course_cycle` int(4) NOT NULL,
  `course_audience` varchar(256) NOT NULL,
  `course_brief` text NOT NULL,
  `course_objective` text NOT NULL,
  PRIMARY KEY (`course_id`),
  KEY `uid` (`teacher_id`),
  KEY `course_type_id` (`course_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=103 ;

--
-- 转存表中的数据 `itp_courses`
--

INSERT INTO `itp_courses` (`course_id`, `course_name`, `course_is_public`, `course_type_id`, `teacher_id`, `course_cycle`, `course_audience`, `course_brief`, `course_objective`) VALUES
(100, '技术管理者训练营', 1, 21, 100, 3, '软件行业新上岗一线经理、拟提拔一线经理、二线经理、项目经理', '“猛将必发于卒伍，宰相必起于州郡”，软件企业中的技术管理者通常从优秀的技术骨干中提拔，然而对新上任的管理者来说，与升职的欣喜相伴而来的常常是大量棘手的问题： 为什么大家的主动性总是不够，怎样激励团队成员？ 怎样安排工作才更高效合理？ 碰到团队中的“牛人”该怎么处理？ 怎么处理与周边团队的争端？ 领导的指示经常变化怎么办？ 为什么我这么辛苦的工作，但下属却很轻松？ 我该怎样培养下属，让他们尽快能独当一面？ 怎么样指出下属的不足又不挫伤他的积极性？ 为什么团队总是犯相同的错误？ 杂事太多，很难静下心来做事，该怎么办？ 在新的岗位上感觉心力交瘁，我是不是不适合做管理工作？ ………… 从技术骨干成长为技术管理者并不是简单的工作年限积累的结果，期间要经历思维视角、知识技能、自身修养等多重转换，对管理者的要求与对技术人员的要求是截然不同的。', ' 本培训课程将诠释优秀经理们的成长规律，对学员进行有针对性的、系统化的指导和培训，可以显著加快管理者的成长，降低试错成本，有效的帮助新上岗主管完成从技术到管理的“华丽转身”。 '),
(101, '软件测试方法与策略高级培训', 1, 7, 1003, 2, '测试工程师', '系统地讲解软件测试的概念、方法及其应用，帮助学员全面掌握软件测试的理论及方法。\n将业界先进的软件测试理念和国际一流的软件测试流程、最佳实践，与知识点融合在一起进行介绍，帮助学员提升对软件测试的认识，并在将来实践中能把握正确的方向，不断深入，获得长足进步。', '更好地理解软件测试和软件开发、软件测试和质量管理等之间的关系。\n更好地和其它团队合作，开发出更高质量的软件产品。\n更好地开展自动化测试工作，更有效地进行软件测试的管理，提供测试效率。'),
(102, '软件测试分析、设计与流程', 1, 6, 1003, 2, '高级测试工程师', '本课程侧重软件测试的需求分析和测试用例设计，兼顾测试思想、流程与方法；\n本课程起点较高，不介绍软件测试的基本概念和方法，而是讲解如何将测试方法应用于实际的项目中，注重培养学员的逻辑思维能力，即授之以渔；\n着重通过具体的案例来讨论和分析所涉及的主题，手把手地给学员一些辅导，力争达到教练式培训的效果，真正能够解决实际工作中的问题；\n加强与学员的交互，力争使课程生动，使学员轻视学习、理解所学的内容；\n分享多年在国际一流企业的管理实践和经验，深入浅出地分析软件测试实际工作中所遇到的问题，使学员少走弯路，力争使学员一步到位，达到较高的测试业务水准；\n不仅讲解要做好测试管理需要做什么，更注重讲解怎么做、为什么这样做。', '系统地理解软件需求的不同层次和不同方面，掌握测试需求的分析方法，并能应用于实际的工作之中，能够有效地将软件需求转化为测试需求。 \n能够针对测试需求以及可能存在的测试风险，制定出有效的测试策略，降低测试风险，并能缩短测试周期或降低测试的成本 \n能够构建结构合理的、易维护的测试用例框架，并利用有效的测试方法设计出高质量的测试用例。 \n掌握软件测试涉及的关键技术，包括静态测试技术和动态测试技术、功能测试和非功能测试、持续测试等。 \n掌握测试用例设计的不同层次方法，能真正提高测试的有效性和效率， \n有效地监控测试过程, 及时对执行结果进行分析,持续改进测试活动,最终达到事先预定的目标。 \n掌握国际化标准测试流程的建立思路，高效率软件测试的标准及规范，从而有效地进行软件测试过程改进，持续改进企业内部的测试流程。 \n能够发现团队的问题，激励团队士气，做好团队和个人发展的规划，构建优秀的团队。\n打造一个好的工作平台，这个工作平台能给团队中的成员带来综合能力的提升。');

-- --------------------------------------------------------

--
-- 表的结构 `itp_course_composition`
--

CREATE TABLE IF NOT EXISTS `itp_course_composition` (
  `course_id` bigint(20) NOT NULL,
  `course_module_id` bigint(20) NOT NULL,
  PRIMARY KEY (`course_id`,`course_module_id`),
  KEY `course_module_id` (`course_module_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `itp_course_composition`
--

INSERT INTO `itp_course_composition` (`course_id`, `course_module_id`) VALUES
(100, 100),
(100, 101),
(100, 102),
(101, 103),
(101, 104),
(102, 104),
(101, 105);

-- --------------------------------------------------------

--
-- 表的结构 `itp_course_modules`
--

CREATE TABLE IF NOT EXISTS `itp_course_modules` (
  `course_module_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `course_module_name` varchar(128) NOT NULL,
  `teacher_id` bigint(20) NOT NULL,
  `course_module_cycle` int(4) NOT NULL,
  `course_module_brief` text NOT NULL,
  `course_module_outline` text NOT NULL,
  PRIMARY KEY (`course_module_id`),
  KEY `teacher_id` (`teacher_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=106 ;

--
-- 转存表中的数据 `itp_course_modules`
--

INSERT INTO `itp_course_modules` (`course_module_id`, `course_module_name`, `teacher_id`, `course_module_cycle`, `course_module_brief`, `course_module_outline`) VALUES
(100, '技术管理者的角色认知', 100, 120, '角色是社会中存在的对个体行为的期望系统，角色承载着组织对管理者的期望，通过这个章节的学习，可以帮助管理者反思：组织对我的期望是什么？员工对我的期望是什么？哪些是是管理者需要做好的？哪些事是管理者不应该去做的？', '什么是角色？\n角色意味着周边群体的期待，管理者角色意味着组织对管理者的哪些期待？\n项目管理者角色\n作为项目管理者，主管要做好哪些工作？\n团队建设者角色\n作为团队建设者，主管要做好哪些工作？\n技术领头人角色\n作为技术领头人，主管要做好哪些工作？\n角色自检\n在实际工作中，哪三项工作是我做得最好的？哪三项工作是我最需要改进的？\n研讨分析：林陆的一天\n作为技术管理者，案例中的主人公林陆有哪些方面是做得好的？哪些方面需要改进？'),
(101, '管理者的转身', 100, 120, '有研究显示，在最优秀、成熟的企业和组织中，一个“业务骨干”转变为“合格的管理者”通常需要3-5年，在这段时间里，哪些方面要发生变化？怎样才能主动的促成这些变化发生？', '转身的概念\r\n什么叫管理者的转身，从独立贡献者到不同层级的管理者，一般来说要经历哪些转身？\r\n转身意味着什么？\r\n转身的三要素。\r\n管理者的工作观念\r\n成为管理者，我们的工作观念要发生怎样的改变？哪些事情变得更重要一些了？\r\n管理者的时间管理\r\n管理者应该怎样分配自己的时间？\r\n管理者的工作技能\r\n管理者需要掌握哪些管理技能，才能胜任新岗位的需要？\r\n技术管理者的常见误区\r\n事必躬亲 \r\n完美主义 \r\n不决策 \r\n害怕失去“饭碗” \r\n全能妄想\r\n案例分析：林陆的困惑\r\n如果您是林陆的主管，您如何给林陆解答他在日记中提到的困惑？'),
(102, '团队建设', 100, 180, '关于团队，有一个经典的表述：Team means together everyone achieve more.作为管理者，管理着一定数目的员工，他们中可能有刚刚走出校门意气风发的年轻人，可能也有技术精湛、老练沉着的老员工，把大家团队到一起，达成组织对团队的期待，常常是一项艰巨的任务。本章将分享一些团队建设方法和工具，并介绍一些主管进行团队建设的案例。但要成为优秀的团队建设者，除了要掌握这些工具和方法，更重要的是在实际工作中不断实践、总结。', '团队的定义\r\n什么是团队？团队与群体有什么区别？\r\n研讨：成功的团队有什么特点？\r\n从团队目标，团队能力，团队中的个体三个角度总结成功团队的特征。\r\n建立成功团队的GPRI模型\r\n如何建立团队目标？ \r\nSMART原则 \r\n如何定义团队的角色与职责？ \r\n如何建立团队的工作流程？ \r\n如何建立良好的人际关系？\r\n团队发展的四个阶段\r\n形成期团队的管理要点 \r\n磨合期团队的管理要点 \r\n规范期团队的管理要点 \r\n执行期团队的管理要点\r\n演练：买打火机的游戏\r\n挑战性的目标如何激发团队的潜能\r\n有效授权\r\n有效授权的基础是什么？ \r\n常见的授权障碍有哪些？如何克服？ \r\n如何选择被授权的人？ \r\n如何选择被授权的事？ \r\n如何控制与跟进授权？ \r\n如何对对授权进行回顾评估？ \r\n如何防止“反授权”？\r\n案例分析：林陆的一次授权\r\n案例中主人公林陆对下属的授权有哪些不妥的地方。\r\n团队建设工作自检\r\n在团队建设方面，我哪些工作做得好？哪些工作还需要提升？'),
(103, '深入理解软件测试', 1003, 100, '提倡全过程的软件测试，即在整个软件生命周期开展测试活动', '软件测试过程全景图\n需求和设计的评审\n代码规范和评审\n单元测试和持续集成测试\n功能测试\n非功能性测试（性能测试、安全性测试、兼容性测试等）\n回归测试\n验收测试'),
(104, '贯穿软件生命周期的测试活动', 1003, 100, '提倡全过程的软件测试，即在整个软件生命周期开展测试活动', '软件测试过程全景图\n需求和设计的评审\n代码规范和评审\n单元测试和持续集成测试\n功能测试\n非功能性测试（性能测试、安全性测试、兼容性测试等）\n回归测试\n验收测试'),
(105, '软件测试方法', 1003, 120, '讲解日常测试中各类测试方法, 从白盒测试方法到黑盒测试方法, 从安全性测试方法到性能测试方法, 涵盖各种测试方法.', '白盒测试方法，包括分支/条件覆盖、组合覆盖、基本路径覆盖\n黑盒测试方法：等价类、边界值分析、判定表方法等，以及方法练习\n常用的安全性测试方法\n负载测试方法\n故障转移测试方法\n方法的综合运用');

-- --------------------------------------------------------

--
-- 表的结构 `itp_course_types`
--

CREATE TABLE IF NOT EXISTS `itp_course_types` (
  `course_type_id` int(4) NOT NULL AUTO_INCREMENT,
  `course_type_slug` varchar(32) NOT NULL,
  `course_type_name` varchar(32) NOT NULL,
  `course_type_parent` int(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`course_type_id`),
  UNIQUE KEY `course_type_id` (`course_type_id`),
  KEY `course_type_parent` (`course_type_parent`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25 ;

--
-- 转存表中的数据 `itp_course_types`
--

INSERT INTO `itp_course_types` (`course_type_id`, `course_type_slug`, `course_type_name`, `course_type_parent`) VALUES
(1, 'web-development', 'Web开发', 0),
(2, 'ios-development', 'iOS开发', 0),
(3, 'android-development', 'Android开发', 0),
(4, 'programming-language', '编程语言', 0),
(5, 'unit-testing', '编程与单元测试', 0),
(6, 'testing-automation', '自动化测试', 0),
(7, 'test-management', '测试管理', 0),
(8, 'agile-testing', '敏捷测试', 0),
(9, 'performance-testing-and-tuning', '性能测试与调优', 0),
(10, 'requirements-analysis', '需求分析', 0),
(11, 'user-experience', '用户体验', 0),
(12, 'architecture-design', '架构设计', 0),
(13, 'database', '数据库', 0),
(14, 'big-data', '大数据', 0),
(15, 'virtualization', '虚拟技术', 0),
(16, 'cloud-computing', '云计算', 0),
(17, 'refactory', '重构', 0),
(18, 'product-definition', '产品定义', 0),
(19, 'product-management', '项目管理', 0),
(20, 'agile-management', '敏捷管理', 0),
(21, 'team-management', '团队管理', 0),
(22, 'software-examination', '软考', 0),
(23, 'certification-examination', '认证考试', 0),
(24, 'others', '其他', 0);

-- --------------------------------------------------------

--
-- 表的结构 `itp_lectures`
--

CREATE TABLE IF NOT EXISTS `itp_lectures` (
  `lecture_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `course_id` bigint(20) NOT NULL,
  `lecture_create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `lecture_start_time` datetime NOT NULL,
  `lecture_end_time` datetime NOT NULL,
  `lecture_region` varchar(8) NOT NULL,
  `lecture_province` varchar(16) NOT NULL,
  `lecture_city` varchar(16) NOT NULL,
  `lecture_address` varchar(256) NOT NULL,
  `lecture_min_capcity` int(8) NOT NULL,
  `lecture_max_capcity` int(8) NOT NULL,
  `lecture_expense` int(8) NOT NULL,
  `lecture_precautions` text NOT NULL,
  PRIMARY KEY (`lecture_id`),
  KEY `course_id` (`course_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=104 ;

--
-- 转存表中的数据 `itp_lectures`
--

INSERT INTO `itp_lectures` (`lecture_id`, `course_id`, `lecture_create_time`, `lecture_start_time`, `lecture_end_time`, `lecture_region`, `lecture_province`, `lecture_city`, `lecture_address`, `lecture_min_capcity`, `lecture_max_capcity`, `lecture_expense`, `lecture_precautions`) VALUES
(100, 100, '2014-07-21 01:38:51', '2014-07-16 08:30:00', '2014-07-18 17:00:00', '华东地区', '浙江省', '杭州市', '浙江大学紫金港校区', 20, 200, 5800, '请自行解决住宿问题'),
(101, 100, '2014-07-21 01:38:57', '2014-08-16 08:30:00', '2014-08-18 17:00:00', '华北地区', '北京市', '', '清华大学', 20, 200, 5800, '请自行解决住宿问题'),
(102, 101, '2014-07-21 01:40:45', '2014-07-22 08:00:00', '2014-07-24 17:00:00', '华东地区', '上海市', '', '同济大学嘉定校区', 20, 100, 5800, '参加培训的注意事项'),
(103, 102, '2014-08-06 07:14:22', '2014-08-29 08:00:00', '2014-08-31 17:00:00', '华东地区', '山东省', '济南市', '山东大学', 20, 200, 5800, '请自理食宿.');

-- --------------------------------------------------------

--
-- 表的结构 `itp_lecture_attendance`
--

CREATE TABLE IF NOT EXISTS `itp_lecture_attendance` (
  `uid` bigint(20) NOT NULL,
  `lecture_id` bigint(20) NOT NULL,
  `serial_code` varchar(32) NOT NULL,
  `total_times` int(4) NOT NULL DEFAULT '1',
  `remain_times` int(4) NOT NULL,
  PRIMARY KEY (`uid`,`lecture_id`),
  KEY `lecture_id` (`lecture_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `itp_lecture_attendance`
--

INSERT INTO `itp_lecture_attendance` (`uid`, `lecture_id`, `serial_code`, `total_times`, `remain_times`) VALUES
(1001, 100, '110153c9edc9954e8', 1, 0),
(1001, 101, '110253ca5016cbe4b', 1, 1),
(1001, 103, '110453fae59a65f9e', 1, 1),
(1003, 101, '110453cb7ef36d9ff', 1, 0),
(1004, 103, '110753fae36f73065', 100, 100);

-- --------------------------------------------------------

--
-- 表的结构 `itp_lecture_schedule`
--

CREATE TABLE IF NOT EXISTS `itp_lecture_schedule` (
  `lecture_id` bigint(20) NOT NULL,
  `course_module_id` bigint(20) NOT NULL,
  `course_module_start_time` datetime NOT NULL,
  `course_module_end_time` datetime NOT NULL,
  PRIMARY KEY (`lecture_id`,`course_module_id`),
  KEY `course_module_id` (`course_module_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `itp_lecture_schedule`
--

INSERT INTO `itp_lecture_schedule` (`lecture_id`, `course_module_id`, `course_module_start_time`, `course_module_end_time`) VALUES
(100, 100, '2014-07-16 08:30:00', '2014-07-16 10:30:00'),
(100, 101, '2014-07-16 10:30:00', '2014-07-16 12:30:00'),
(100, 102, '2014-07-16 14:30:00', '2014-07-16 17:30:00'),
(101, 100, '2014-08-16 08:30:00', '2014-08-16 10:30:00'),
(103, 104, '2014-08-29 08:00:00', '2014-08-29 09:40:00');

-- --------------------------------------------------------

--
-- 表的结构 `itp_messages`
--

CREATE TABLE IF NOT EXISTS `itp_messages` (
  `message_id` bigint(20) NOT NULL,
  `message_from_uid` bigint(20) NOT NULL,
  `message_to_uid` bigint(20) NOT NULL,
  `message_send_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `message_content` text NOT NULL,
  PRIMARY KEY (`message_id`),
  KEY `message_from_uid` (`message_from_uid`,`message_to_uid`),
  KEY `message_to_uid` (`message_to_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `itp_people`
--

CREATE TABLE IF NOT EXISTS `itp_people` (
  `uid` bigint(20) NOT NULL AUTO_INCREMENT,
  `person_name` varchar(32) NOT NULL,
  `person_region` varchar(16) NOT NULL,
  `person_province` varchar(16) NOT NULL,
  `person_city` varchar(16) NOT NULL,
  `person_position_id` int(4) NOT NULL,
  `person_phone` varchar(24) NOT NULL,
  PRIMARY KEY (`uid`),
  KEY `person_position_id` (`person_position_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `itp_positions`
--

CREATE TABLE IF NOT EXISTS `itp_positions` (
  `position_id` int(4) NOT NULL AUTO_INCREMENT,
  `position_slug` varchar(32) NOT NULL,
  `position_name` varchar(32) NOT NULL,
  PRIMARY KEY (`position_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- 转存表中的数据 `itp_positions`
--

INSERT INTO `itp_positions` (`position_id`, `position_slug`, `position_name`) VALUES
(1, 'programmer', '程序员'),
(2, 'advanced-programmer', '高级程序员'),
(3, 'technical-manager', '技术主管'),
(4, 'project-manager', '项目经理'),
(5, 'product-designer', '产品经理'),
(6, 'ui-designer', '前端设计师'),
(7, 'architect', '架构师'),
(8, 'test-engineer', 'QA/测试工程师'),
(9, 'system-administrator', '系统管理员'),
(10, 'database-administrator', '数据库管理员');

-- --------------------------------------------------------

--
-- 表的结构 `itp_posts`
--

CREATE TABLE IF NOT EXISTS `itp_posts` (
  `post_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `post_category_id` int(4) NOT NULL,
  `post_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `post_title` varchar(128) NOT NULL,
  `post_content` text NOT NULL,
  PRIMARY KEY (`post_id`),
  KEY `news_category_id` (`post_category_id`),
  KEY `news_category_id_2` (`post_category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `itp_posts`
--

INSERT INTO `itp_posts` (`post_id`, `post_category_id`, `post_date`, `post_title`, `post_content`) VALUES
(1, 1, '2014-07-05 15:59:15', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry''s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry''s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.');

-- --------------------------------------------------------

--
-- 表的结构 `itp_post_categories`
--

CREATE TABLE IF NOT EXISTS `itp_post_categories` (
  `post_category_id` int(4) NOT NULL AUTO_INCREMENT,
  `post_category_slug` varchar(32) NOT NULL,
  `post_category_name` varchar(64) NOT NULL,
  PRIMARY KEY (`post_category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `itp_post_categories`
--

INSERT INTO `itp_post_categories` (`post_category_id`, `post_category_slug`, `post_category_name`) VALUES
(1, 'uncatalogued', '未分类');

-- --------------------------------------------------------

--
-- 表的结构 `itp_requirements`
--

CREATE TABLE IF NOT EXISTS `itp_requirements` (
  `requirement_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `requirement_is_accepted` tinyint(1) NOT NULL DEFAULT '0',
  `requirement_from_uid` bigint(20) NOT NULL,
  `requirement_to_uid` bigint(20) DEFAULT NULL,
  `requirement_course_id` bigint(20) DEFAULT NULL,
  `requirement_create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `requirement_participants` int(4) NOT NULL,
  `requirement_start_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `requirement_end_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `requirement_region` varchar(16) NOT NULL,
  `requirement_province` varchar(16) NOT NULL,
  `requirement_city` varchar(16) NOT NULL,
  `requirement_place` varchar(128) NOT NULL,
  `requirement_expense` int(8) NOT NULL,
  `requirement_detail` text NOT NULL,
  PRIMARY KEY (`requirement_id`),
  KEY `requirement_course_id` (`requirement_course_id`),
  KEY `requirement_from_uid` (`requirement_from_uid`,`requirement_to_uid`),
  KEY `requirement_to_uid` (`requirement_to_uid`),
  KEY `requirement_from_uid_2` (`requirement_from_uid`,`requirement_to_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `itp_teachers`
--

CREATE TABLE IF NOT EXISTS `itp_teachers` (
  `uid` bigint(20) NOT NULL,
  `teacher_is_approved` tinyint(1) NOT NULL DEFAULT '0',
  `teacher_name` varchar(16) NOT NULL,
  `teacher_brief` varchar(640) NOT NULL,
  `teacher_avatar` varchar(64) NOT NULL,
  `teacher_region` varchar(16) NOT NULL,
  `teacher_province` varchar(16) NOT NULL,
  `teacher_city` varchar(16) NOT NULL,
  `teacher_company` varchar(64) NOT NULL,
  `teacher_phone` varchar(24) NOT NULL,
  `teacher_weibo` varchar(32) NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `itp_teachers`
--

INSERT INTO `itp_teachers` (`uid`, `teacher_is_approved`, `teacher_name`, `teacher_brief`, `teacher_avatar`, `teacher_region`, `teacher_province`, `teacher_city`, `teacher_company`, `teacher_phone`, `teacher_weibo`) VALUES
(100, 1, '杨锋镝', '杨锋镝是一位有着十余年从业经验的软件工程专家、资深技术管理者、培训师、咨询师. \r\n他先后在UTStarcom、华为等企业从事过软件研发过程中涉及的大部分岗位：需求、设计、开发、测试、实施、维护、项目管理、团队管理、敏捷教练、人力资源管理, 同时还是多项技术专利的发明人. 丰富的职业跨界经历使他深刻体会各岗位的痛点, 能贴合客户需求提供务实的咨询服务. \r\n近年来, 他研发了敏捷软件开发、从技术到管理等系列课程, 曾为三星电子、招商信诺、风行网和拓维信息等企业提供咨询服务, 辅导客户实施敏捷组织转型, 并曾为顺丰航空、国航、中国移动、Oracle、东软集团、普华永道等数十家企业提供培训服务. \r\n此外, 他还受邀担任中国过程改进大会、敏捷中国大会、Scrum Gathering等大会的评委和主持人, 并经常在全球软件案例研究峰会、亚太软件研发管理峰会、中国软件工程大会、中国软件技术大会、中国过程改进大会、CSDN CTO俱乐部、QClub、敏捷之旅等发表演讲. ', '', '', '上海市', '', '麦思博有限公司资深敏捷咨询顾问', '', ''),
(101, 1, '王剑', '毕业后加盟微软雷德蒙总部。1999年1月-2000年5月在Windows CE组工作。2000年5月-2001年5月在NetDocs组工作。2001年5月-2003年6月 在Windows RTC 组工作。2001年7月加入微软亚洲研究院筹备微软亚洲工程院。2001年11月微软亚洲工程院成立他是20多创始人之一。工程院成立后他领导多个团队成功的发布多个产品，包括网络模拟器（Network Emulator），Office Communicator Web Access, Live Meeting Recording。', '', '', '北京市', '', '微软技术资深专家 微软亚洲院创始人之一', '', ''),
(102, 1, '刘大双', '清华大学国际工程项目管理研究院特聘教授，国家职业技能鉴定专家委员会委员。\r\n微软项目管理顾问及Microsoft MVP，PMP（PMI，2003年）。\r\n北京大学理学士，美国Fordham 大学商学院工商管理硕士。\r\n1989年8月至1992年8月于北京大学任教。1992年8月加入IT行业，先后服务于美国SAS软件研究所、中国惠普有限公司等全球著名的IT企业。2001年10月至2004年9月担任微软（中国）有限公司Microsoft Project产品经理。', '', '', '北京市', '', 'Microsoft MVP, PMP', '', ''),
(103, 1, '吴淏', '现任微软（中国）平台及开发合作部开发经理。目前专注于研究WCF,Office 2007 System,SaaS(Software as a Service)架构;负责微软SaaS架构技术在中国的应用推广，苏州SaaS孵化期，SaaS商务平台等。', '', '', '北京市', '', '微软开发合作部SaaS开发经理', '', ''),
(1003, 1, '朱少民', '中国科技大学软件学院教指委委员、中国软件测试认证委员会(CSTQB )资深专家、Certified ScrumMaster. 从事教育、研发和技术管理近二十年, 其中从事软件测试已有十多年, 参与多项重大科研项目的研究和几十个软件项目的产品开发, 获得多项省、市科技进步奖, 发表 多部著作, 其中有六本大学教材. 并且具有丰富的软件开发、测试的工程实践经验, 包括在海外工作和国际性一流软件企业的工作经历, 以及具有良好的教学和培训经验.', '', '华东地区', '上海市', '', '曾任思科—网迅 (中国)软件有限公司资深QA总监', '13909596672', 'kerryzhu');

-- --------------------------------------------------------

--
-- 表的结构 `itp_teaching_fields`
--

CREATE TABLE IF NOT EXISTS `itp_teaching_fields` (
  `teacher_id` bigint(20) NOT NULL,
  `course_type_id` int(4) NOT NULL,
  PRIMARY KEY (`teacher_id`,`course_type_id`),
  KEY `course_type_id` (`course_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `itp_teaching_fields`
--

INSERT INTO `itp_teaching_fields` (`teacher_id`, `course_type_id`) VALUES
(1003, 6),
(1003, 7),
(1003, 8),
(100, 19),
(100, 20);

-- --------------------------------------------------------

--
-- 表的结构 `itp_users`
--

CREATE TABLE IF NOT EXISTS `itp_users` (
  `uid` bigint(20) NOT NULL AUTO_INCREMENT,
  `username` varchar(16) NOT NULL,
  `email` varchar(64) NOT NULL,
  `password` varchar(32) NOT NULL,
  `user_group_id` int(4) NOT NULL,
  `last_time_signin` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `last_time_change_password` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `username` (`username`,`email`),
  UNIQUE KEY `email` (`email`),
  KEY `user_group_id` (`user_group_id`),
  KEY `user_group_id_2` (`user_group_id`),
  KEY `user_group_id_3` (`user_group_id`),
  KEY `user_group_id_4` (`user_group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1005 ;

--
-- 转存表中的数据 `itp_users`
--

INSERT INTO `itp_users` (`uid`, `username`, `email`, `password`, `user_group_id`, `last_time_signin`, `last_time_change_password`) VALUES
(100, 'user-100', 'user-100@zjhzxhz.com', '785ee107c11dfe36de668b1ae7baacbb', 2, '2014-07-21 02:39:23', '0000-00-00 00:00:00'),
(101, 'user-101', 'user-101@zjhzxhz.com', 'password', 2, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(102, 'user-102', 'user-102@zjhzxhz.com', 'password', 2, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(103, 'user-103', 'user-103@zjhzxhz.com', 'password', 2, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(104, 'user-104', 'user-104@zjhzxhz.com', 'password', 2, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(105, 'user-105', 'user-105@zjhzxhz.com', 'password', 2, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(106, 'user-106', 'user-106@zjhzxhz.com', 'password', 2, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(107, 'user-107', 'user-107@zjhzxhz.com', 'password', 2, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(108, 'user-108', 'user-108@zjhzxhz.com', 'password', 2, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(109, 'user-109', 'user-109@zjhzxhz.com', 'password', 2, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(110, 'user-110', 'user-110@zjhzxhz.com', 'password', 2, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(111, 'user-111', 'user-111@zjhzxhz.com', 'password', 2, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(112, 'user-112', 'user-112@zjhzxhz.com', 'password', 2, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(113, 'user-113', 'user-113@zjhzxhz.com', 'password', 2, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(114, 'user-114', 'user-114@zjhzxhz.com', 'password', 2, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(115, 'user-115', 'user-115@zjhzxhz.com', 'password', 2, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(116, 'user-116', 'user-116@zjhzxhz.com', 'password', 2, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(117, 'user-117', 'user-117@zjhzxhz.com', 'password', 2, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(118, 'user-118', 'user-118@zjhzxhz.com', 'password', 2, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(119, 'user-119', 'user-119@zjhzxhz.com', 'password', 2, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(1000, 'Administrator', 'zjhzxhz@example.com', '785ee107c11dfe36de668b1ae7baacbb', 4, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(1001, 'zjhzxhz', 'zjhzxhz@gmail.com', '785ee107c11dfe36de668b1ae7baacbb', 1, '2014-07-11 07:09:33', '0000-00-00 00:00:00'),
(1002, 'kelliany', 'a965526122@gmail.com', '25f9e794323b453885f5181f1b624d0b', 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(1003, 'kerryzhu', 'zhu.kerry@gmail.com', '785ee107c11dfe36de668b1ae7baacbb', 2, '2014-07-05 03:58:30', '0000-00-00 00:00:00'),
(1004, 'cisco_webex', 'service@webex.com', '785ee107c11dfe36de668b1ae7baacbb', 3, '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- 表的结构 `itp_user_groups`
--

CREATE TABLE IF NOT EXISTS `itp_user_groups` (
  `user_group_id` int(4) NOT NULL AUTO_INCREMENT,
  `user_group_slug` varchar(24) NOT NULL,
  `user_group_name` varchar(24) NOT NULL,
  PRIMARY KEY (`user_group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- 转存表中的数据 `itp_user_groups`
--

INSERT INTO `itp_user_groups` (`user_group_id`, `user_group_slug`, `user_group_name`) VALUES
(1, 'person', '个人'),
(2, 'teacher', '讲师'),
(3, 'company', '企业'),
(4, 'administrator', 'Administrators');

--
-- 限制导出的表
--

--
-- 限制表 `itp_comments`
--
ALTER TABLE `itp_comments`
  ADD CONSTRAINT `itp_comments_ibfk_1` FOREIGN KEY (`lecture_id`) REFERENCES `itp_lectures` (`lecture_id`),
  ADD CONSTRAINT `itp_comments_ibfk_2` FOREIGN KEY (`reviewer_uid`) REFERENCES `itp_users` (`uid`);

--
-- 限制表 `itp_companies`
--
ALTER TABLE `itp_companies`
  ADD CONSTRAINT `itp_companies_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `itp_users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 限制表 `itp_courses`
--
ALTER TABLE `itp_courses`
  ADD CONSTRAINT `itp_courses_ibfk_1` FOREIGN KEY (`course_type_id`) REFERENCES `itp_course_types` (`course_type_id`),
  ADD CONSTRAINT `itp_courses_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `itp_teachers` (`uid`);

--
-- 限制表 `itp_course_composition`
--
ALTER TABLE `itp_course_composition`
  ADD CONSTRAINT `itp_course_composition_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `itp_courses` (`course_id`),
  ADD CONSTRAINT `itp_course_composition_ibfk_2` FOREIGN KEY (`course_module_id`) REFERENCES `itp_course_modules` (`course_module_id`);

--
-- 限制表 `itp_course_modules`
--
ALTER TABLE `itp_course_modules`
  ADD CONSTRAINT `itp_course_modules_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `itp_teachers` (`uid`);

--
-- 限制表 `itp_lectures`
--
ALTER TABLE `itp_lectures`
  ADD CONSTRAINT `itp_lectures_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `itp_courses` (`course_id`);

--
-- 限制表 `itp_lecture_attendance`
--
ALTER TABLE `itp_lecture_attendance`
  ADD CONSTRAINT `itp_lecture_attendance_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `itp_users` (`uid`),
  ADD CONSTRAINT `itp_lecture_attendance_ibfk_2` FOREIGN KEY (`lecture_id`) REFERENCES `itp_lectures` (`lecture_id`);

--
-- 限制表 `itp_lecture_schedule`
--
ALTER TABLE `itp_lecture_schedule`
  ADD CONSTRAINT `itp_lecture_schedule_ibfk_1` FOREIGN KEY (`lecture_id`) REFERENCES `itp_lectures` (`lecture_id`),
  ADD CONSTRAINT `itp_lecture_schedule_ibfk_2` FOREIGN KEY (`course_module_id`) REFERENCES `itp_course_modules` (`course_module_id`);

--
-- 限制表 `itp_messages`
--
ALTER TABLE `itp_messages`
  ADD CONSTRAINT `itp_messages_ibfk_1` FOREIGN KEY (`message_from_uid`) REFERENCES `itp_users` (`uid`),
  ADD CONSTRAINT `itp_messages_ibfk_2` FOREIGN KEY (`message_to_uid`) REFERENCES `itp_users` (`uid`);

--
-- 限制表 `itp_people`
--
ALTER TABLE `itp_people`
  ADD CONSTRAINT `itp_people_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `itp_users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `itp_people_ibfk_2` FOREIGN KEY (`person_position_id`) REFERENCES `itp_positions` (`position_id`);

--
-- 限制表 `itp_posts`
--
ALTER TABLE `itp_posts`
  ADD CONSTRAINT `itp_posts_ibfk_1` FOREIGN KEY (`post_category_id`) REFERENCES `itp_post_categories` (`post_category_id`);

--
-- 限制表 `itp_requirements`
--
ALTER TABLE `itp_requirements`
  ADD CONSTRAINT `itp_requirements_ibfk_1` FOREIGN KEY (`requirement_from_uid`) REFERENCES `itp_users` (`uid`),
  ADD CONSTRAINT `itp_requirements_ibfk_2` FOREIGN KEY (`requirement_to_uid`) REFERENCES `itp_users` (`uid`),
  ADD CONSTRAINT `itp_requirements_ibfk_3` FOREIGN KEY (`requirement_course_id`) REFERENCES `itp_courses` (`course_id`);

--
-- 限制表 `itp_teachers`
--
ALTER TABLE `itp_teachers`
  ADD CONSTRAINT `itp_teachers_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `itp_users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 限制表 `itp_teaching_fields`
--
ALTER TABLE `itp_teaching_fields`
  ADD CONSTRAINT `itp_teaching_fields_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `itp_teachers` (`uid`),
  ADD CONSTRAINT `itp_teaching_fields_ibfk_2` FOREIGN KEY (`course_type_id`) REFERENCES `itp_course_types` (`course_type_id`);

--
-- 限制表 `itp_users`
--
ALTER TABLE `itp_users`
  ADD CONSTRAINT `itp_users_ibfk_1` FOREIGN KEY (`user_group_id`) REFERENCES `itp_user_groups` (`user_group_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
