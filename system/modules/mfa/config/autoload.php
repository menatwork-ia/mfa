<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package Mfa
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	'MailFormAttachment' => 'system/modules/mfa/MailFormAttachment.php',
	'MfaDcaHelper'       => 'system/modules/mfa/MfaDcaHelper.php',
));
