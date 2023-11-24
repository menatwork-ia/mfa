<?php

/**
 * Contao Open Source CMS
 *
 * @copyright  MEN AT WORK 2014
 * @package    MailFormAttachment
 * @license    GNU/LGPL
 * @filesource
 */

/**
 * Config
 */
$GLOBALS['TL_DCA']['tl_form']['config']['onload_callback'][] = array('MfaDcaHelper', 'disableSendViaEmail');

/**
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_form']['palettes']['__selector__'][] = 'mfa';
$paletteParts                                               = explode(
    ';', $GLOBALS['TL_DCA']['tl_form']['palettes']['default']
);
foreach ($paletteParts as $k => $v) {
    if (strpos($v, 'sendViaEmail') !== false) {
        array_insert($paletteParts, $k + 1, array('mfa;'));
        break;
    }
}
$GLOBALS['TL_DCA']['tl_form']['palettes']['default'] = implode(';', $paletteParts);
$GLOBALS['TL_DCA']['tl_form']['subpalettes']['mfa']  = $GLOBALS['TL_DCA']['tl_form']['subpalettes']['sendViaEmail'] . ',mail_attachment';

/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_form']['fields']['mfa'] = $GLOBALS['TL_DCA']['tl_form']['fields']['sendViaEmail'];

$GLOBALS['TL_DCA']['tl_form']['fields']['mail_attachment'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_form']['mail_attachment'],
    'exclude'   => true,
    'inputType' => 'select',
    'options'   => array
    (
        'mail_attach',
        'link_path',
        'attach_mail_link_path'
    ),
    'reference' => &$GLOBALS['TL_LANG']['MFA'],
    'eval'      => array
    (
        'includeBlankOption' => true,
        'tl_class'           => 'clr w50'
    )
);
