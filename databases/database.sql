CREATE DATABASE db161140_tro;

CREATE USER 'db161140_2go'@'localhost' IDENTIFIED BY 'where2GO';

GRANT ALL PRIVILEGES ON *.* TO 'db161140_2go'@'localhost' WITH GRANT OPTION;


Users

CREATE TABLE `user_emails` (
  `email_id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `email` varchar(256) NOT NULL,
  `password` varchar(20) NOT NULL,
  `host` varchar(256) NOT NULL,
  `port` int(100) DEFAULT NULL,
  `host_service` varchar(45) DEFAULT NULL,
  `inbox` int(100) DEFAULT NULL,
  `outbox` int(100) DEFAULT NULL,
  `archived` int(100) DEFAULT NULL,
  `active` int(1) DEFAULT '1',
  PRIMARY KEY (`email_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='emails for the system';

INSERT INTO user_emails(user_id, email, password, host, port, host_service)
VALUES(13, 'it@hhluxuryinvestments.com', 'zonaxgoogle', 'imap.gmail.com', 993, 'gmail');


CREATE TABLE `email_messages` (
  `message_id` int(50) NOT NULL AUTO_INCREMENT,
  `message_system_id` varchar(256) DEFAULT NULL,
  `date` date NOT NULL,
  `hour` time NOT NULL,
  `from_email` varchar(256) NOT NULL,
  `to_email` varchar(256) NOT NULL,
  `personal_name` varchar(256) DEFAULT NULL,
  `subject` varchar(500) DEFAULT NULL,
  `message` text,
  `attachment` int(1) DEFAULT NULL,
  `status` int(1) DEFAULT NULL,
  `folder` int(1) DEFAULT NULL,
  `inbox` int(1) DEFAULT '1',
  `member_id` int(10) DEFAULT NULL,
  `user_sender` varchar(256) DEFAULT NULL,
  `template_id` int(100) DEFAULT NULL,
  `user_id` int(6) DEFAULT NULL,
  PRIMARY KEY (`message_id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE TABLE `email_attachments` (
  `attachment_id` int(100) NOT NULL AUTO_INCREMENT,
  `message_system_id` varchar(256) DEFAULT NULL,
  `email` varchar(256) NOT NULL,
  `attachment_name` varchar(256) NOT NULL,
  PRIMARY KEY (`attachment_id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COMMENT='emails attachments';

CREATE TABLE `brokers` (
  `broker_id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `name` varchar(250) NOT NULL DEFAULT '',
  `last_name` varchar(250) DEFAULT '',
  `address` varchar(250) DEFAULT '',
  `city` varchar(250) DEFAULT '',
  `state` varchar(250) DEFAULT '',
  `country` varchar(250) DEFAULT '',
  `website` varchar(250) DEFAULT '',
  `notes` text,
  `active` int(1) NOT NULL,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`broker_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;

CREATE TABLE `broker_emails` (
  `email_id` int(10) NOT NULL AUTO_INCREMENT,
  `broker_id` int(10) NOT NULL,
  `email` varchar(250) NOT NULL,
  `active` int(1) NOT NULL,
  PRIMARY KEY (`email_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;

CREATE TABLE `broker_phones` (
  `phone_id` int(10) NOT NULL AUTO_INCREMENT,
  `broker_id` int(10) NOT NULL,
  `phone` varchar(250) NOT NULL DEFAULT '',
  `active` int(1) NOT NULL,
  PRIMARY KEY (`phone_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;

CREATE TABLE `broker_history` (
  `history_id` int(100) NOT NULL AUTO_INCREMENT,
  `user_id` int(100) NOT NULL,
  `broker_id` int(100) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `history` text NOT NULL,
  PRIMARY KEY (`history_id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


CREATE TABLE `room_types` (
	`room_type_id` int(100) NOT NULL AUTO_INCREMENT,
	`room_type` varchar(250) NOT NULL,
	`notes` text NULL,
	PRIMARY KEY (`room_type_id`)
)  ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE TABLE `rooms` (
	`room_id` int(100) NOT NULL AUTO_INCREMENT,
	`room_type_id` int(100) NOT NULL,
	`room` varchar(256) NOT NULL,
	`capacity` int(2) NULL,
	`notes` text NULL,
	PRIMARY KEY (`room_id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE TABLE `reservations`(
	`reservation_id` INT(100) NOT NULL AUTO_INCREMENT,
	`member_id` INT(100) NOT NULL,
	`room_id` INT(100) NOT NULL,
	`check_in`	date NOT NULL,
	`check_out` date NOT NULL,
	`date` date NOT NULL,
	`agency` INT(2) NULL,
	`price_per_night` INT(100) NULL,
	`price` INT(100) NULL,
	`status` INT(100) NOT NULL DEFAULT '0',
	`paid` INT(100) NOT NULL DEFAULT '0',
	`note` text NULL,
	PRIMARY KEY (`reservation_id`)
)  ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=UTF8;

ALTER TABLE `room_types` ADD COLUMN `abbr` varchar(64);

ALTER TABLE `reservations` ADD COLUMN `adults` int(2) NOT NULL;
ALTER TABLE `reservations` ADD COLUMN `children` int(2) NULL;
ALTER TABLE `reservations` ADD COLUMN `agency` INT(2) NULL;
ALTER TABLE `reservations` ADD COLUMN `price_per_night` INT(100) NULL;

CREATE TABLE `agencies` (	
	`agency_id` INT(100) NOT NULL AUTO_INCREMENT,
	`agency` VARCHAR(256) NOT NULL,
	PRIMARY KEY (`agency_id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=UTF8;

































