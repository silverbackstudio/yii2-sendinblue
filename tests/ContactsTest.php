<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use yii\sendinblue\campaigns\Contacts;
use yii\base\InvalidConfigException;

use yii\mail\BaseMessage;

final class ContactsTest extends TestCase
{
    
    protected $contacts;

    protected function setUp()
    {
        $this->contacts = new Contacts(['apikey' => SENDINBLUE_API_KEY ]);
    }    
    
    public function testCanSetApiKey()
    {
        $this->assertEquals($this->contacts->config->getApiKey('api-key'), SENDINBLUE_API_KEY);
        
        $this->contacts->setApiKey('baz');
        $this->assertEquals($this->contacts->config->getApiKey('api-key'), 'baz');
        
        $this->contacts->setApiKey(SENDINBLUE_API_KEY);
    }    
    
    public function testCreateContact()
    {
        $attributes = [ 'SURNAME' => 'Test Name' ];
        
        $result = $this->contacts->createContact(SENDINBLUE_TO, $attributes, array());
        $this->assertNotFalse($result);
        
        $result = $this->contacts->createContact(SENDINBLUE_TO, $attributes, array(), false);
        $this->assertFalse($result, 'Creating a duplicate contact with $update=false shoud return false');        
        
        $result = $this->contacts->getContact(SENDINBLUE_TO);
        $this->assertArraySubset($attributes, $result->getAttributes());      
        
    }
    
    public function testAddToList()
    {
        
        $lists = array( 2 );
        
        $result = $this->contacts->createContact(SENDINBLUE_TO, array(), $lists, true);
        $this->assertNotFalse($result);
        
        $result = $this->contacts->getContact(SENDINBLUE_TO);
        $this->assertArraySubset($lists, $result->getListIds());        
        
    }    

}