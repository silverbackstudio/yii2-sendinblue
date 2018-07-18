<?php
/**
 * Sendinblue Message Class.
 *
 * @category   Yii
 * @package    Sendinblue
 * @subpackage Transactional
 * @author     Brando Meniconi <b.meniconi@silverbackstudio.it>
 * @license    BSD-3-Clause https://opensource.org/licenses/BSD-3-Clause 
 * @link       http://www.silverbackstudio.it/
 */

namespace yii\sendinblue\transactional;

use Yii;
use yii\mail\BaseMessage;
use SendinBlue;

/**
 * Message is the class that is used to store the data of the email message that
 * will be sent through Sendinblue API.
 *
 * @category Yii
 * @package  Sendinblue
 * @author   Brando Meniconi <b.meniconi@silverbackstudio.it>
 * @license  BSD-3-Clause https://opensource.org/licenses/BSD-3-Clause 
 * @version  Release: 1.0
 * @link     http://www.silverbackstudio.it/
 */
class SmtpMessage extends BaseMessage
{

    /**
     * The charset placeholder
     *
     * @var   string
     * @since 1.0.0
     */
    public $charset = null;
    
    /**
     * The Sendinblue email class.
     *
     * @var   string
     * @since 1.0.0
     */
    protected $sendinblueModel;        


    /**
     *
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        
        $this->sendinblueModel = new SendinBlue\Client\Model\SendSmtpEmail();
    }

    /**
     * Return the Sendinblue Email Model
     * 
     * @return SendinBlue\Client\Model\SendSmtpEmail
     */
    public function getSendinblueModel()
    {
        return $this->sendinblueModel;
    }


    /**
     * Apply a specific recipient field from Yii Message to Sendinblue email object
     *
     * @param yii\mail\BaseMessage                  $message   to get the recipient from
     * @param SendinBlue\Client\Model\SendSmtpEmail $smtpEmail The email object to set the field to
     * @param string                                $field     The field to map (es. to, cc, bcc, replyTo, ..)
     * 
     * @return void
     */    
    protected function castRecipients( $recipients, $class )
    {
        
        if (empty($recipients) ) { 
            return;
        }
        
        if (! is_array($recipients) ) {
            $recipients = array( $recipients );
        }
        
        $emailRecipients = array();
        
        foreach ( $recipients as $i => $recipient ) {
            $emailRecipients[$i] = new $class;
            $emailRecipients[$i]->setEmail($recipient);
        }
        
        return $emailRecipients;
    }
    
    /**
     * Apply a specific recipient field from Yii Message to Sendinblue email object
     *
     * @param yii\mail\BaseMessage                  $message   to get the recipient from
     * @param SendinBlue\Client\Model\SendSmtpEmail $smtpEmail The email object to set the field to
     * @param string                                $field     The field to map (es. to, cc, bcc, replyTo, ..)
     * 
     * @return void
     */    
    protected function extractRecipientsEmail( $recipients )
    {
        if (empty($recipients) ) { 
            return array();
        }
        
        if (! is_array($recipients) ) {
            $recipients = array( $recipients );
        }
        
        $recipientsEmail = array();
        
        foreach ( $recipients as $i => $recipient ) {
            $recipientsEmail[$i] = $recipient->getEmail();
        }
        
        return $recipientsEmail;
    }    


    /**
     *
     * @inheritdoc
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     *
     * @inheritdoc
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;

        return $this;
    }

    /**
     *
     * @inheritdoc
     */
    public function getFrom()
    {
        $container = $this->sendinblueModel->getSender();

        if ($container ) {
            return $container->getEmail();
        }

        return null;
    }

    /**
     *
     * @inheritdoc
     */
    public function setFrom($from)
    {
        $container = new SendinBlue\Client\Model\SendSmtpEmailSender();
        $container->setEmail($from);
        
        $this->sendinblueModel->setSender($container);

        return $this;
    }

    /**
     *
     * @inheritdoc
     */
    public function getReplyTo()
    {
        $container = $this->sendinblueModel->getReplyTo();

        if ($container ) {
            return $container->getEmail();
        }
        
        return null;        
    }

    /**
     *
     * @inheritdoc
     */
    public function setReplyTo($replyTo)
    {

        $container = new SendinBlue\Client\Model\SendSmtpEmailReplyTo();
        $container->setEmail($replyTo);
        
        $this->sendinblueModel->setReplyTo($container);

        return $this;
    }

    /**
     *
     * @inheritdoc
     */
    public function getTo()
    {
        $container = $this->sendinblueModel->getTo();

        if ($container ) {
            return $this->extractRecipientsEmail($container);
        }        
        
        return null;
    }

    /**
     *
     * @inheritdoc
     */
    public function setTo($to)
    {
        $recipients = $this->castRecipients($to, SendinBlue\Client\Model\SendSmtpEmailTo::class);

        $this->sendinblueModel->setTo($recipients);

        return $this;
    }

    /**
     *
     * @inheritdoc
     */
    public function getCc()
    {
        $container = $this->sendinblueModel->getCc();

        if ($container ) {
            return $this->extractRecipientsEmail($container);
        }            
    }

    /**
     *
     * @inheritdoc
     */
    public function setCc($cc)
    {
        $recipients = $this->castRecipients($cc, SendinBlue\Client\Model\SendSmtpEmailCc::class);

        $this->sendinblueModel->setCc($recipients);        

        return $this;
    }

    /**
     *
     * @inheritdoc
     */
    public function getBcc()
    {
        $container = $this->sendinblueModel->getBcc();

        if ($container ) {
            return $this->extractRecipientsEmail($container);
        }   
    }

    /**
     *
     * @inheritdoc
     */
    public function setBcc($bcc)
    {
        $recipients = $this->castRecipients($bcc, SendinBlue\Client\Model\SendSmtpEmailBcc::class);

        $this->sendinblueModel->setBcc($recipients);        

        return $this;
    }

    /**
     *
     * @inheritdoc
     */
    public function getSubject()
    {
        return $this->sendinblueModel->getSubject();
    }

    /**
     *
     * @inheritdoc
     */
    public function setSubject($subject)
    {
        $this->sendinblueModel->setSubject($subject);        

        return $this;
    }

    /**
     *
     * @inheritdoc
     */
    public function setTextBody($text)
    {
        $this->sendinblueModel->setTextContent($text); 

        return $this;
    }

    /**
     *
     * @inheritdoc
     */
    public function setHtmlBody($html)
    {
        $this->sendinblueModel->setHtmlContent($html); 

        return $this;
    }

    /**
     *
     * @inheritdoc
     */
    public function attach($fileName, array $options = [])
    {
        
        if (!array_key_exists('fileName', $options) ) {
            $options['fileName'] = basename($fileName);
        }
        
        $this->attachContent(file_get_contents($fileName), $options);
        
        return $this;
    }

    /**
     *
     * @inheritdoc
     */
    public function attachContent($content, array $options = [])
    {
        
        $attachments = $this->sendinblueModel->getAttachment();
        
        if (empty($attachments) ) {
            $attachments = array();
        }
        
        $attachment = new SendinBlue\Client\Model\SendSmtpEmailAttachment();
        $attachment->setContent(base64_encode($content));
        
        if (array_key_exists('fileName', $options) ) {
            $attachment->setName($option['fileName']);
        }
        
        $attachments[] = $attachment;
        
        $this->sendinblueModel->setAttachment($attachments);
        
        return $this;        
    }

    /**
     *
     * @inheritdoc
     */
    public function embed($fileName, array $options = [])
    {
        $this->attach($fileName, $options);
    }

    /**
     *
     * @inheritdoc
     */
    public function embedContent($content, array $options = [])
    {
        $this->attachContent($content, $options);
    }
    
    /**
     *
     * @inheritdoc
     */
    public function toString()
    {
        return $this->sendinblueModel->__toString();
    }    


}
