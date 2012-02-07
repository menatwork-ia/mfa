-- ********************************************************
-- *                                                      *
-- * IMPORTANT NOTE                                       *
-- *                                                      *
-- * Do not import this file manually but use the Contao  *
-- * install tool to create and maintain database tables! *
-- *                                                      *
-- ********************************************************

-- 
-- Table `tl_form`
-- 

CREATE TABLE `tl_form` (
  `mfa` char(1) NOT NULL default '',
  `mail_attachment` varchar(128) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;