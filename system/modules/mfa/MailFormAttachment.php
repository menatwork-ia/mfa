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
 * Class MailFormAttachment
 */
class MailFormAttachment extends Frontend
{

    /**
     * Process the form data and send mail
     * HOOK: processFormData
     *
     * @param array $arrPost
     * @param array $arrForm
     * @param array $arrFiles
     * @param array $arrLabels
     */
    public function processFormData($arrPost, $arrForm, $arrFiles, $arrLabels)
    {
        // Send form data via e-mail
        if ($arrForm['mfa'])
        {
            $keys = array();
            $values = array();
            $fields = array();
            $message = '';

            foreach ($arrPost as $k => $v)
            {
                if (in_array($k, array('cc','FORM_SUBMIT','REQUEST_TOKEN','MAX_FILE_SIZE','password','password_confirm')))
                {
                    continue;
                }

                $v = deserialize($v);

                // Skip empty fields
                if ($arrForm['skipEmpty'] && !is_array($v) && !strlen($v))
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

            $recipients = \Contao\StringUtil::splitCsv($arrForm['recipient']);

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
            if (strlen($arrPost['cc']))
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
                $email->attachFileFromString(\Contao\StringUtil::decodeEntities('"' . implode('";"', $keys) . '"' . "\n" . '"' . implode('";"', $values) . '"'), 'form.csv', 'text/comma-separated-values');
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
            $email->text = \Contao\StringUtil::decodeEntities(trim($message)) . $uploaded . "\n\n";
            $email->sendTo($recipients);
        }
    }

}
