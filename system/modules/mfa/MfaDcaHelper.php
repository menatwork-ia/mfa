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
 * Class MfaDCAHelper
 */
class MfaDcaHelper extends tl_form
{
    /**
     * Set the old sendViaEmail field in the database to FALSE
     * 
     * @param DataContainer $dc 
     */
    public function disableSendViaEmail(DataContainer $dc)
    {
        $this->Database->prepare("UPDATE tl_form SET sendViaEmail = ? WHERE id = ?")->execute(FALSE, $dc->id);
    }
}