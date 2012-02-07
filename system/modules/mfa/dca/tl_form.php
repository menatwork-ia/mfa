<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  MEN AT WORK 2012
 * @package    MailFormAttachment
 * @license    GNU/LGPL
 * @filesource
 */

/**
 * Add to palette
 */
$GLOBALS['TL_DCA']['tl_form']['palettes']['__selector__'][] = 'mfa'; 
$GLOBALS['TL_DCA']['tl_form']['palettes']['default'] = str_replace('sendViaEmail', 'mfa', $GLOBALS['TL_DCA']['tl_form']['palettes']['default']);

$GLOBALS['TL_DCA']['tl_form']['subpalettes']['mfa'] = $GLOBALS['TL_DCA']['tl_form']['subpalettes']['sendViaEmail'] . ',mail_attachment';

$GLOBALS['TL_DCA']['tl_form']['fields']['mfa'] = $GLOBALS['TL_DCA']['tl_form']['fields']['sendViaEmail'];

$GLOBALS['TL_DCA']['tl_form']['fields']['mail_attachment'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_form']['mail_attachment'],
    'exclude' => TRUE,    
    'inputType' => 'select',
    'options' => array('mail_attach', 'link_path', 'attach_mail_link_path'),
    'reference' => &$GLOBALS['TL_LANG']['MFA'],    
    'eval' => array('includeBlankOption' => TRUE, 'tl_class' => 'clr w50')
);

?>
