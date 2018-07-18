<?php
/**
 * Sendinblue Contacts Class.
 *
 * @category   Yii
 * @package    Sendinblue
 * @subpackage Campaigns
 * @author     Brando Meniconi <b.meniconi@silverbackstudio.it>
 * @license    BSD-3-Clause https://opensource.org/licenses/BSD-3-Clause 
 * @link       http://www.silverbackstudio.it/
 */

namespace yii\sendinblue\campaigns;

use Yii;
use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use SendinBlue;

/**
 * Contacts is the class to manage contacts in Sendinblue CRM
 *
 * @category Yii
 * @package  Sendinblue
 * @author   Brando Meniconi <b.meniconi@silverbackstudio.it>
 * @license  BSD-3-Clause https://opensource.org/licenses/BSD-3-Clause 
 * @version  Release: 1.0
 * @link     http://www.silverbackstudio.it/
 */
class Contacts extends Component
{
    /**
     * Sendinblue Api Key 
     *
     * @var string
     */
    public $apikey;
    
    /**
     * The list where new users should be subscribed by default
     *
     * @var string
     */    
    public $defaultList;
    
    /**
     * The Sendinblue configuration object
     *
     * @var SendinBlue\Client\Configuration
     */       
    public $config;
    
    /**
     * The Sendinblue contacts API client
     *
     * @var SendinBlue\Client\Api\ContactsApi
     */       
    protected $client;
    
      
    const DATE_FORMAT = 'Y-m-d';

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
            throw new InvalidConfigException('"' . get_class($this) . '::apikey" cannot be null.');
        }
        
        $this->client = new SendinBlue\Client\Api\ContactsApi(null, $this->config);
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
        
        if ($this->hasConfig() ) {
            $this->config->setApiKey('api-key', $this->apikey);
        }
        
    }   
   
    /**
     * Creates a contact
     *
     * @param string $email 
     * @param array  $attributes 
     * @param array  $list_ids 
     * @param bool   $update 
     * 
     * @return int|false The user ID
     */   
    public function createContact( $email, $attributes = array(), $list_ids = array(), $update = true )
    {
        $contact_attributes =  array();
        
        if (! empty($attributes) ) {
            $contact_attributes['attributes'] = $attributes;
        }
    
        if (!$email ) {
            return false;
        }
        
        $contact_attributes['email'] = $email;
        $contact_attributes['updateEnabled'] = $update;
        
        $createContact = new SendinBlue\Client\Model\CreateContact($contact_attributes);
        
        if (! empty($list_ids) ) {
            $createContact->setListIds($list_ids);
        }
        
        Yii::debug('SendinBlue creating new contact with params ' . VarDumper::export($contact_attributes),  __METHOD__);
        
        try {     
            
            $raw_result = $this->client->createContact($createContact);

        } catch ( SendinBlue\Client\ApiException $e ) {
            
            $error = json_decode($e->getResponseBody());
            
            if ('duplicate_parameter' === $error->code  ) {
                
                Yii::info('Sendinblue duplicate contact found: ' . VarDumper::export($error),  __METHOD__);

                return $update;
            }   
            
            Yii::error('Sendinblue API request error: ' . VarDumper::export($error),  __METHOD__);
            
            return false;

        } catch ( Exception $e ) {
            
            Yii::error('Sendinblue API request general error: ' . $e->getMessage(),  __METHOD__);
            
            return false;
        }
        
        $user_id = empty($raw_result->id) ? null : $raw_result->getId();
        
        Yii::debug('Sendinblue contact creation successful ' . VarDumper::export($raw_result),  __METHOD__);
        
        return $user_id;
    }
    
    /**
     * Obtains a contact
     *
     * @param string $email The email for the contact to get
     * 
     * @return SendinBlue\Client\Model\GetExtendedContactDetails|false The user info object or false in case of failure
     */     
    public function getContact( $email )
    {
        
        Yii::debug('SendinBlue getContact with params ' . VarDumper::export($email),  __METHOD__);
        
        if (! $email ) {
            Yii::warning('SendinBlue getContact with no email specified ' . VarDumper::export($email),  __METHOD__);
            return false;
        }
        
        try {
            $raw_result = $this->client->getContactInfo($email);
        } catch ( SendinBlue\Client\ApiException $e ) {
            
            $error = json_decode($e->getResponseBody());
            
            if ('document_not_found' === $error->code ) {
                Yii::warning('Sendinblue getContact() API contact not found: ' . VarDumper::export($email),  __METHOD__);            
                return false;
            }
            
            Yii::error('Sendinblue API request error: ' . VarDumper::export($error),  __METHOD__);                
            
            return false;

        } catch ( Exception $e ) {
            Yii::error('Sendinblue API request general error: ' . $e->getMessage(),  __METHOD__);
            
            return false;
        }

        Yii::debug('Sendinblue getContact()successful ' . VarDumper::export($raw_result),  __METHOD__);
    
        return $raw_result;
    }    
    
    
} 