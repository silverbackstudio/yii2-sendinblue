<?php
/**
 * Sendinblue Mailer Class.
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
use yii\mail\BaseMailer;
use yii\base\Model;
use yii\base\InvalidConfigException;
use yii\helpers\VarDumper;

use SendinBlue;

/**
 * Mailer is the class that consuming the Message object sends emails thorugh
 * the Sendinblue API.
 *
 * @category Yii
 * @package  Sendinblue
 * @author   Brando Meniconi <b.meniconi@silverbackstudio.it>
 * @license  BSD-3-Clause https://opensource.org/licenses/BSD-3-Clause 
 * @version  Release: 1.0
 * @link     http://www.silverbackstudio.it/
 */
class Mailer extends BaseMailer
{

    /**
     * Sendinblue API key
     * 
     * @var string 
     */
    public $apikey;

    /**
     * Message default class name.
     * 
     * @var string 
     */
    public $messageClass = 'yii\sendinblue\transactional\SmtpMessage';


    /**
     * Message default class name.
     * 
     * @var string 
     */
    public $templateMessageClass = 'yii\sendinblue\transactional\TemplateMessage';


    /**
     * Object for this instance
     * 
     * @var The SendinBlue\Client\Configuration 
     */
    public $config;
    
    /**
     * Keeps the last transaction result
     * 
     * @var object 
     */
    protected $lastError;    
    

    /**
     * Checks that the API key has indeed been set.
     *
     * @inheritdoc
     * 
     * @throws InvalidConfigException 
     * @return void
     */
    public function init()
    {

        if (! $this->hasConfig() ) {
            $this->config = SendinBlue\Client\Configuration::getDefaultConfiguration();
        }
        
        if ($this->apikey ) {
            $this->config->setApiKey('api-key', $this->apikey);
        } 
        
        if (! $this->config->getApiKey('api-key') ) {
            throw new InvalidConfigException('Sendinblue API key cannot be null.');
        }
        
    }

    /**
     * Sets the API key for Sendinblue
     *
     * @param string $apikey the Sendinblue API key
     * 
     * @throws InvalidConfigException
     * @return void
     */
    public function setApikey($apikey)
    {
        if (!is_string($apikey)) {
            throw new InvalidConfigException('"' . get_class($this) . '::apikey" should be a string, "' . gettype($apikey) . '" given.');
        }

        $trimmedApikey = trim($apikey);
        if (!strlen($trimmedApikey) > 0) {
            throw new InvalidConfigException('"' . get_class($this) . '::apikey" length should be greater than 0.');
        }

        $this->apikey = $trimmedApikey;
        
        if ($this->config instanceof SendinBlue\Client\Configuration ) {
            $this->config->setApiKey('api-key', $this->apikey);
        }
        
    }

    /**
     * Check if this instance has a config set
     * 
     * @return bool
     */
    public function hasConfig()
    {
        return ($this->config instanceof SendinBlue\Client\Configuration );
    }    

    /**
     * Return the current sendinblue configuration object
     *
     * @return SendinBlue\Client\Configuration
     * @since  1.0.0
     */
    public function getConfig()
    {
        return $this->config;
    }
    
    /**
     * Return the last error occurred
     *
     * @return object
     * @since  1.0.0
     */
    public function getLastError()
    {
        return $this->lastError;
    }    

    /**
     *
     * @inheritdoc
     */
    public function compose($view = null, array $params = [ 'PLACEHOLDER' => '' ])
    {
        
        if (is_numeric($view) ) {
            $message = $this->createMessage(true);
            $message->setTemplate($view);
            $message->setAttributes($params);
        } else {
            $message =  parent::compose($view, $params);    
        }
        
        return $message;
    }
    
    /**
     *
     * @inheritdoc
     */
    protected function createMessage( $useTemplate = false )
    {
        
        $config = $this->messageConfig;
        
        if (!array_key_exists('class', $config)) {
            $config['class'] = $useTemplate ? $this->templateMessageClass : $this->messageClass;
        }
        
        $config['mailer'] = $this;
        return Yii::createObject($config);
    }    

    /**
     *
     * @inheritdoc
     */
    protected function sendMessage($message)
    {
        Yii::debug('Sendinblue Mailer sending SMTP email for message: ' . VarDumper::export($message), __METHOD__);

        $apiInstance = new SendinBlue\Client\Api\SMTPApi(null, $this->config);
        
        try {
            
            if ($message instanceof $this->templateMessageClass ) {
                $template = $message->getTemplate();
                $result = $apiInstance->sendTemplate($template, $message->getSendinblueModel());
            } else {
                $result = $apiInstance->sendTransacEmail($message->getSendinblueModel());
            }
            
            Yii::info('Sendinblue Mailer sent SMTP email with result: ' . VarDumper::export($result), __METHOD__);
        } catch ( SendinBlue\Client\ApiException $e ) {
            $this->lastError = json_decode($e->getResponseBody());
            
            Yii::error('Sendinblue API client exception: ' . VarDumper::export($this->lastError), __METHOD__);
            return false;
        } catch ( Exception $e ) {
            Yii::error('Sendinblue API exception: ' . $e->getMessage(), __METHOD__);
            return false;            
        }

        return true;
    }

    
}
