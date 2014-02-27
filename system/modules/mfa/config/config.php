<?php

/**
 * Contao Open Source CMS
 *
 * @copyright  MEN AT WORK 2014
 * @package    MailFormAttachment
 * @license    GNU/LGPL
 * @filesource
 */

/*
 * Hooks
 */
$GLOBALS['TL_HOOKS']['processFormData'][] = array('MailFormAttachment', 'processFormData');