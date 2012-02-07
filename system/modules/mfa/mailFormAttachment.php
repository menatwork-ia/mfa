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
 
class mailFormAttachment extends Frontend
{

    public function processFormData($arrPost, $arrForm, $arrFiles, $arrLabels)
    {        
        // Send form data via e-mail
        if ($arrForm['mfa'])
        {
            $this->import('String');

            $keys = array();
            $values = array();
            $fields = array();
            $message = '';

            foreach ($arrPost as $k => $v)
            {
                if ($k == 'cc')
                {
                    continue;
                }

                $v = deserialize($v);

                // Skip empty fields
                if ($this->skipEmpty && !is_array($v) && !strlen($v))
                {
                    continue;
                }

                // Add field to message
                $message .= (isset($arrLabels[$k]) ? $arrLabels[$k] : ucfirst($k)) . ': ' . (is_array($v) ? implode(', ', $v) : $v) . "\n";

                // Prepare XML file
                if ($arrForm['format'] == 'xml')
                {
                    $fields[] = array
                        (
                        'name' => $k,
                        'values' => (is_array($v) ? $v : array($v))
                    );
                }

                // Prepare CSV file
                if ($arrForm['format'] == 'csv')
                {
                    $keys[] = $k;
                    $values[] = (is_array($v) ? implode(',', $v) : $v);
                }
            }

            $recipients = $this->String->splitCsv($arrForm['recipient']);

            // Format recipients
            foreach ($recipients as $k => $v)
            {
                $recipients[$k] = str_replace(array('[', ']', '"'), array('<', '>', ''), $v);
            }

            $email = new Email();

            // Get subject and message
            if ($arrForm['format'] == 'email')
            {                
                $message = $_SESSION['FORM_DATA']['message'];
                $email->subject = $_SESSION['FORM_DATA']['subject'];
            }

            // Set the admin e-mail as "from" address
            $email->from = $GLOBALS['TL_ADMIN_EMAIL'];
            $email->fromName = $GLOBALS['TL_ADMIN_NAME'];

            // Get the "reply to" address
            if (strlen($this->Input->post('email', true)))
            {
                $replyTo = $this->Input->post('email', true);

                // Add name
                if (strlen($this->Input->post('name')))
                {
                    $replyTo = '"' . $this->Input->post('name') . '" <' . $replyTo . '>';
                }

                $email->replyTo($replyTo);
            }

            // Fallback to default subject
            if (!strlen($email->subject))
            {
                $email->subject = $this->replaceInsertTags($arrForm['subject']);
            }

            // Send copy to sender
            //TODO VAR ISNT'T SET
            if (strlen($arrSubmitted['cc']))
            {
                $email->sendCc($this->Input->post('email', true));
                unset($_SESSION['FORM_DATA']['cc']);
            }

            // Attach XML file
            if ($arrForm['format'] == 'xml')
            {
                $objTemplate = new FrontendTemplate('form_xml');

                $objTemplate->fields = $fields;
                $objTemplate->charset = $GLOBALS['TL_CONFIG']['characterSet'];

                $email->attachFileFromString($objTemplate->parse(), 'form.xml', 'application/xml');
            }

            // Attach CSV file
            if ($arrForm['format'] == 'csv')
            {
                $email->attachFileFromString($this->String->decodeEntities('"' . implode('";"', $keys) . '"' . "\n" . '"' . implode('";"', $values) . '"'), 'form.csv', 'text/comma-separated-values');
            }

            $uploaded = '';

            // Attach uploaded files           
            if (count($arrFiles))
            {
                foreach ($arrFiles as $file)
                {
                    switch ($arrForm['mail_attachment'])
                    {
                        case 'mail_attach':
                            $email->attachFileFromString(file_get_contents($file['tmp_name']), $file['name'], $file['type']);
                            break;
                        case 'attach_mail_link_path':                                             
                            $email->attachFileFromString(file_get_contents($file['tmp_name']), $file['name'], $file['type']);
                        case 'link_path':
                            // Add a link to the uploaded file
                            if ($file['uploaded'])
                            {
                                $uploaded .= "\n" . $this->Environment->base . str_replace(TL_ROOT . '/', '', dirname($file['tmp_name'])) . '/' . rawurlencode($file['name']);
                            }                                                
                            break;                            
                    }                     
                }
            }

            $uploaded = strlen(trim($uploaded)) ? "\n\n---\n" . $uploaded : '';

            // Send e-mail
            $email->text = $this->String->decodeEntities(trim($message)) . $uploaded . "\n\n";
            $email->sendTo($recipients);
        }
    }

}

?>
