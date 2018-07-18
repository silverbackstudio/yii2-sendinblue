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
class TemplateMessage extends BaseMessage
{

    /**
     * The name of the template inside Sendinblue.
     *
     * @var   string
     * @since 1.0.0
     */
    public $template = null;
    
    /**
     * The name of the template inside Sendinblue.
     *
     * @var   string
     * @since 1.0.0
     */
    public $attributes;

    /**
     * The name of the template inside Sendinblue.
     *
     * @var   string
     * @since 1.0.0
     */
    public $charset = null;
    
    /**
     * The Sendinblue email model class.
     *
     * @var   SendinBlue\Client\Model\SendEmail|null
     * @since 1.0.0
     */
    public $sendinblueModel;       
    

    /**
     *
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        
        $this->sendinblueModel = new SendinBlue\Client\Model\SendEmail();
    }
    

    /**
     * Return the Sendinblue Email Model
     * 
     * @return SendinBlue\Client\Model\SendEmail
     */
    public function getSendinblueModel()
    {
        return $this->sendinblueModel;
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
        return null;
    }

    /**
     *
     * @inheritdoc
     */
    public function setFrom($from)
    {
        return $this;
    }

    /**
     *
     * @inheritdoc
     */
    public function getReplyTo()
    {
        return $this->sendinblueModel->getReplyTo();
    }

    /**
     *
     * @inheritdoc
     */
    public function setReplyTo($replyTo)
    {
        $this->sendinblueModel->setReplyTo($replyTo);
        
        return $this;
    }

    /**
     *
     * @inheritdoc
     */
    public function getTo()
    {
        return $this->sendinblueModel->getEmailTo();
    }

    /**
     *
     * @inheritdoc
     */
    public function setTo($to)
    {
        $this->sendinblueModel->setEmailTo((array)$to);
        
        return $this;
    }

    /**
     *
     * @inheritdoc
     */
    public function getCc()
    {
        return $this->sendinblueModel->getEmailCc();
    }

    /**
     *
     * @inheritdoc
     */
    public function setCc($cc)
    {
        $this->sendinblueModel->setEmailCc((array)$cc);

        return $this;
    }

    /**
     *
     * @inheritdoc
     */
    public function getBcc()
    {
        return $this->sendinblueModel->getEmailBcc();
    }

    /**
     *
     * @inheritdoc
     */
    public function setBcc($bcc)
    {
        $this->sendinblueModel->setEmailBcc((array)$bcc);

        return $this;
    }

    /**
     *
     * @inheritdoc
     */
    public function getSubject()
    {
        return null;
    }

    /**
     *
     * @inheritdoc
     */
    public function setSubject($subject)
    {

        return $this;
    }

    /**
     *
     * @inheritdoc
     */
    public function setTextBody($text)
    {
        return $this;
    }

    /**
     *
     * @inheritdoc
     */
    public function setHtmlBody($html)
    {
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
        
        $attachment = new SendinBlue\Client\Model\SendEmailAttachment();
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
     * Return the active template
     *
     * @since 1.0.0
     *
     * @return int
     */
    public function getTemplate()
    {
        return $this->template;
    }
    
    /**
     * Sets the active template
     *
     * @param int|string $template The template ID
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function setTemplate( $template )
    {
        $this->template = intval($template);
        
        return $this;
    }
    
    /**
     * Return the message attributes
     *
     * @param array $flattened Return the Sendinblue flattned attributes or the original ones
     * 
     * @since 1.0.0
     *
     * @return array|null
     */
    public function getAttributes( $flattened = true )
    {
        if ($flattened ) {
            return $this->sendinblueModel->getAttributes();
        } else {
            return $this->attributes;
        }
    }
    
    /**
     * Sets the message attributes
     *
     * @param array $attributes The attributes to set in the message
     *
     * @since  1.0.0 
     * @return $this
     */
    public function setAttributes( $attributes )
    {
        $this->attributes = $attributes;
        $this->sendinblueModel->setAttributes(self::flatten($attributes));        
        
        return $this;
    }    

    /**
     * Return the message tags
     *
     * @since 1.0.0
     *
     * @return array|null
     */
    public function getTags()
    {
        return $this->sendinblueModel->getTags();
    }

    /**
     * Sets the message tags
     *
     * @param array $tags The tags to set in the message
     *
     * @since  1.0.0 
     * @return $this
     */
    public function setTags($tags)
    {
        $this->sendinblueModel->setTags((array)$tags);

        return $this;
    }

    /**
     * Return the message headers
     *
     * @since 1.0.0
     *
     * @return array|null
     */
    public function getHeaders()
    {
        if (! empty($this->sendinblueModel->getHeaders()) ) {
            return (array)$this->sendinblueModel->getHeaders();
        }
        
        return null;
    }

    /**
     * Sets the message headers
     *
     * @param array $headers The headers to set in the message
     *
     * @since  1.0.0 
     * @return $this
     */
    public function setHeaders($headers)
    {
        $this->sendinblueModel->setHeaders((object)$headers);

        return $this;
    }


    /**
     * Flatten an array for email attibutes
     *
     * @param array  $array  The multidimensional array to be flattened
     * @param string $prefix The prefix to prepend to every record
     * 
     * @return array
     * @since  1.0.0
     */
    public static function flatten( $array, $prefix = '' )
    {
        $output = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $output = array_merge($output, self::flatten($value, $prefix . $key . '__'));
            } elseif ($value instanceof Model ) {
                $output = array_merge($output, self::flatten($value->toArray(), $prefix . $key . '__'));
            } else {
                $output[ strtoupper($prefix . $key) ] = $value;
            }
        }
    
        return $output;
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
