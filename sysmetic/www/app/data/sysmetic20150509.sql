-- phpMyAdmin SQL Dump
-- version 4.3.13
-- http://www.phpmyadmin.net
--
-- Host: sysmetic.ckz9pwf0k9qx.ap-northeast-1.rds.amazonaws.com
-- 생성 시간: 15-05-10 14:34
-- 서버 버전: 5.6.22-log
-- PHP 버전: 5.3.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 데이터베이스: `sysmetic`
--

-- --------------------------------------------------------

--
-- 테이블 구조 `api_tool`
--

CREATE TABLE IF NOT EXISTS `api_tool` (
  `tool_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `broker_id` int(11) NOT NULL,
  `reg_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- 테이블 구조 `auth_token`
--

CREATE TABLE IF NOT EXISTS `auth_token` (
  `token_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `reg_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- 테이블 구조 `broker`
--

CREATE TABLE IF NOT EXISTS `broker` (
  `broker_id` int(11) NOT NULL,
  `company` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `logo_s` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `company_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '증권사',
  `domestic` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `overseas` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fx` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `dma` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_open` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `sorting` int(11) NOT NULL DEFAULT '1',
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- 테이블 구조 `following_strategy`
--

CREATE TABLE IF NOT EXISTS `following_strategy` (
  `following_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `strategy_id` int(11) NOT NULL,
  `reg_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- 테이블 구조 `item`
--

CREATE TABLE IF NOT EXISTS `item` (
  `item_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `icon` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `reg_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- 테이블 구조 `mail_history`
--

CREATE TABLE IF NOT EXISTS `mail_history` (
  `mail_id` int(11) NOT NULL,
  `subject` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `contents` text COLLATE utf8_unicode_ci NOT NULL,
  `mail_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'normal',
  `status` enum('queued','sent') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'queued',
  `reserve_at` int(11) NOT NULL,
  `reg_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- 테이블 구조 `notice`
--

CREATE TABLE IF NOT EXISTS `notice` (
  `notice_id` int(11) NOT NULL,
  `subject` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `contents` text COLLATE utf8_unicode_ci NOT NULL,
  `is_open` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `reg_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- 테이블 구조 `portfolio`
--

CREATE TABLE IF NOT EXISTS `portfolio` (
  `portfolio_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `amount` int(11) NOT NULL DEFAULT '0',
  `total_profit_rate` double NOT NULL DEFAULT '0',
  `result_amount` double NOT NULL DEFAULT '0',
  `start_date` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `end_date` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `reg_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- 테이블 구조 `portfolio_strategy`
--

CREATE TABLE IF NOT EXISTS `portfolio_strategy` (
  `portfolio_strategy_id` int(11) NOT NULL,
  `portfolio_id` int(11) NOT NULL,
  `strategy_id` int(11) NOT NULL,
  `percents` int(11) NOT NULL DEFAULT '0',
  `reg_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- 테이블 구조 `qna`
--

CREATE TABLE IF NOT EXISTS `qna` (
  `qna_id` int(11) NOT NULL,
  `target` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `target_value` int(11) NOT NULL,
  `target_value_text` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `strategy_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `uid` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `question` text COLLATE utf8_unicode_ci NOT NULL,
  `answer` text COLLATE utf8_unicode_ci NOT NULL,
  `answer_at` int(11) NOT NULL,
  `reg_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- 테이블 구조 `strategy`
--

CREATE TABLE IF NOT EXISTS `strategy` (
  `strategy_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `strategy_type` enum('N','S','M') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'M',
  `strategy_term` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'day',
  `broker_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `broker_id` int(11) NOT NULL,
  `tool_id` int(11) NOT NULL,
  `currency` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'KRW',
  `investment` int(11) NOT NULL DEFAULT '0',
  `intro` text COLLATE utf8_unicode_ci NOT NULL,
  `is_operate` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `is_open` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `developer_uid` int(11) NOT NULL,
  `developer_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `total_profit_rate` double NOT NULL DEFAULT '0',
  `mdd` double NOT NULL DEFAULT '0',
  `sharp_ratio` double NOT NULL DEFAULT '0',
  `c_price` double NOT NULL DEFAULT '1000',
  `followers_count` int(11) NOT NULL DEFAULT '0',
  `is_delete` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `reg_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- 테이블 구조 `strategy_account`
--

CREATE TABLE IF NOT EXISTS `strategy_account` (
  `account_id` int(11) NOT NULL,
  `strategy_id` int(11) NOT NULL,
  `target_date` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `reg_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- 테이블 구조 `strategy_daily`
--

CREATE TABLE IF NOT EXISTS `strategy_daily` (
  `daily_id` int(11) NOT NULL,
  `strategy_id` int(11) NOT NULL,
  `target_date` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `balance` int(11) NOT NULL DEFAULT '0',
  `flow` int(11) NOT NULL DEFAULT '0',
  `reg_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=12839 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- 테이블 구조 `strategy_funding`
--

CREATE TABLE IF NOT EXISTS `strategy_funding` (
  `funding_id` int(11) NOT NULL,
  `strategy_id` int(11) NOT NULL,
  `target_date` int(11) NOT NULL,
  `money` int(11) NOT NULL DEFAULT '0',
  `investor` int(11) NOT NULL DEFAULT '0',
  `reg_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- 테이블 구조 `strategy_item`
--

CREATE TABLE IF NOT EXISTS `strategy_item` (
  `strategy_item_id` int(11) NOT NULL,
  `strategy_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `reg_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=129 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- 테이블 구조 `strategy_review`
--

CREATE TABLE IF NOT EXISTS `strategy_review` (
  `review_id` int(11) NOT NULL,
  `strategy_id` int(11) NOT NULL,
  `writer_uid` int(11) NOT NULL,
  `writer_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `rating` int(11) NOT NULL DEFAULT '1',
  `contents` text COLLATE utf8_unicode_ci NOT NULL,
  `reg_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- 테이블 구조 `system_trading_tool`
--

CREATE TABLE IF NOT EXISTS `system_trading_tool` (
  `tool_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `broker_id` int(11) NOT NULL,
  `reg_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- 테이블 구조 `univ_value`
--

CREATE TABLE IF NOT EXISTS `univ_value` (
  `value_id` int(11) NOT NULL,
  `target_date` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `value` double NOT NULL,
  `reg_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- 테이블 구조 `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `uid` int(11) NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `platform` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `platform_uid` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password_code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `nickname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `picture` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `picture_s` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `mobile` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `birthday` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `sido` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `gugun` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `gender` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'M',
  `alarm_feeds` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `alarm_all` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `user_type` enum('N','T','B','A') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `is_verify` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `is_delete` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `reg_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 덤프된 테이블의 인덱스
--

--
-- 테이블의 인덱스 `api_tool`
--
ALTER TABLE `api_tool`
  ADD PRIMARY KEY (`tool_id`), ADD KEY `broker_id` (`broker_id`);

--
-- 테이블의 인덱스 `auth_token`
--
ALTER TABLE `auth_token`
  ADD PRIMARY KEY (`token_id`), ADD KEY `uid` (`uid`), ADD KEY `token` (`token`);

--
-- 테이블의 인덱스 `broker`
--
ALTER TABLE `broker`
  ADD PRIMARY KEY (`broker_id`);

--
-- 테이블의 인덱스 `following_strategy`
--
ALTER TABLE `following_strategy`
  ADD PRIMARY KEY (`following_id`), ADD KEY `uid` (`uid`), ADD KEY `strategy_id` (`strategy_id`);

--
-- 테이블의 인덱스 `item`
--
ALTER TABLE `item`
  ADD PRIMARY KEY (`item_id`);

--
-- 테이블의 인덱스 `mail_history`
--
ALTER TABLE `mail_history`
  ADD PRIMARY KEY (`mail_id`);

--
-- 테이블의 인덱스 `notice`
--
ALTER TABLE `notice`
  ADD PRIMARY KEY (`notice_id`);

--
-- 테이블의 인덱스 `portfolio`
--
ALTER TABLE `portfolio`
  ADD PRIMARY KEY (`portfolio_id`), ADD KEY `uid` (`uid`);

--
-- 테이블의 인덱스 `portfolio_strategy`
--
ALTER TABLE `portfolio_strategy`
  ADD PRIMARY KEY (`portfolio_strategy_id`), ADD KEY `portfolio_id` (`portfolio_id`);

--
-- 테이블의 인덱스 `qna`
--
ALTER TABLE `qna`
  ADD PRIMARY KEY (`qna_id`), ADD KEY `target_value` (`target_value`);

--
-- 테이블의 인덱스 `strategy`
--
ALTER TABLE `strategy`
  ADD PRIMARY KEY (`strategy_id`), ADD KEY `mdd` (`mdd`), ADD KEY `sharp_ratio` (`sharp_ratio`), ADD KEY `followers_count` (`followers_count`);

--
-- 테이블의 인덱스 `strategy_account`
--
ALTER TABLE `strategy_account`
  ADD PRIMARY KEY (`account_id`), ADD KEY `strategy_id` (`strategy_id`);

--
-- 테이블의 인덱스 `strategy_daily`
--
ALTER TABLE `strategy_daily`
  ADD PRIMARY KEY (`daily_id`), ADD KEY `strategy_id` (`strategy_id`), ADD KEY `target_date` (`target_date`);

--
-- 테이블의 인덱스 `strategy_funding`
--
ALTER TABLE `strategy_funding`
  ADD PRIMARY KEY (`funding_id`), ADD KEY `strategy_id` (`strategy_id`);

--
-- 테이블의 인덱스 `strategy_item`
--
ALTER TABLE `strategy_item`
  ADD PRIMARY KEY (`strategy_item_id`), ADD KEY `item_id` (`item_id`), ADD KEY `strategy_id` (`strategy_id`);

--
-- 테이블의 인덱스 `strategy_review`
--
ALTER TABLE `strategy_review`
  ADD PRIMARY KEY (`review_id`), ADD KEY `strategy_id` (`strategy_id`);

--
-- 테이블의 인덱스 `system_trading_tool`
--
ALTER TABLE `system_trading_tool`
  ADD PRIMARY KEY (`tool_id`), ADD KEY `broker_id` (`broker_id`);

--
-- 테이블의 인덱스 `univ_value`
--
ALTER TABLE `univ_value`
  ADD PRIMARY KEY (`value_id`);

--
-- 테이블의 인덱스 `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`uid`), ADD KEY `email` (`email`);

--
-- 덤프된 테이블의 AUTO_INCREMENT
--

--
-- 테이블의 AUTO_INCREMENT `api_tool`
--
ALTER TABLE `api_tool`
  MODIFY `tool_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=18;
--
-- 테이블의 AUTO_INCREMENT `auth_token`
--
ALTER TABLE `auth_token`
  MODIFY `token_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=29;
--
-- 테이블의 AUTO_INCREMENT `broker`
--
ALTER TABLE `broker`
  MODIFY `broker_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=33;
--
-- 테이블의 AUTO_INCREMENT `following_strategy`
--
ALTER TABLE `following_strategy`
  MODIFY `following_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=56;
--
-- 테이블의 AUTO_INCREMENT `item`
--
ALTER TABLE `item`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=12;
--
-- 테이블의 AUTO_INCREMENT `mail_history`
--
ALTER TABLE `mail_history`
  MODIFY `mail_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- 테이블의 AUTO_INCREMENT `notice`
--
ALTER TABLE `notice`
  MODIFY `notice_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- 테이블의 AUTO_INCREMENT `portfolio`
--
ALTER TABLE `portfolio`
  MODIFY `portfolio_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=16;
--
-- 테이블의 AUTO_INCREMENT `portfolio_strategy`
--
ALTER TABLE `portfolio_strategy`
  MODIFY `portfolio_strategy_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=31;
--
-- 테이블의 AUTO_INCREMENT `qna`
--
ALTER TABLE `qna`
  MODIFY `qna_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=11;
--
-- 테이블의 AUTO_INCREMENT `strategy`
--
ALTER TABLE `strategy`
  MODIFY `strategy_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=54;
--
-- 테이블의 AUTO_INCREMENT `strategy_account`
--
ALTER TABLE `strategy_account`
  MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- 테이블의 AUTO_INCREMENT `strategy_daily`
--
ALTER TABLE `strategy_daily`
  MODIFY `daily_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=12839;
--
-- 테이블의 AUTO_INCREMENT `strategy_funding`
--
ALTER TABLE `strategy_funding`
  MODIFY `funding_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=10;
--
-- 테이블의 AUTO_INCREMENT `strategy_item`
--
ALTER TABLE `strategy_item`
  MODIFY `strategy_item_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=129;
--
-- 테이블의 AUTO_INCREMENT `strategy_review`
--
ALTER TABLE `strategy_review`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=41;
--
-- 테이블의 AUTO_INCREMENT `system_trading_tool`
--
ALTER TABLE `system_trading_tool`
  MODIFY `tool_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=32;
--
-- 테이블의 AUTO_INCREMENT `univ_value`
--
ALTER TABLE `univ_value`
  MODIFY `value_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=16;
--
-- 테이블의 AUTO_INCREMENT `user`
--
ALTER TABLE `user`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=18;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
