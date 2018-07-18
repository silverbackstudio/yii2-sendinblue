<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use yii\sendinblue\transactional\SmtpMessage;
use yii\sendinblue\transactional\TemplateMessage;
use yii\base\InvalidConfigException;

use yii\mail\BaseMessage;

final class TemplateMessageTest extends TestCase
{
    
    private $message;

    protected function setUp()
    {
        $this->message = new TemplateMessage();
    }    
    
    public function testCanGetSetCharset()
    {
        $this->assertNull($this->message->getCharset()); 
        
        $message = $this->message->setCharset('utf8');
        
        $this->assertInstanceOf(TemplateMessage::class, $message); 

        $this->assertEquals('utf8', $this->message->getCharset());
        
    }
    
    public function testCanGetSetReplyTo()
    {
        $this->assertNull($this->message->getReplyTo()); 
        
        $message = $this->message->setReplyTo('testReplyTo@example.com');
        
        $this->assertInstanceOf(TemplateMessage::class, $message); 

        $this->assertEquals('testReplyTo@example.com', $this->message->getReplyTo());
    }  
    
    public function testCanGetSetTo()
    {
        $this->assertNull($this->message->getTo()); 
        
        $message = $this->message->setTo('testTo@example.com');
        
        $this->assertInstanceOf(TemplateMessage::class, $message); 

        $this->assertContains('testTo@example.com', $this->message->getTo());
        
        //test arrays
        $recipientArray = ['testTo1@example.com', 'testTo2@example.com'];
        
        $message = $this->message->setTo($recipientArray);

        $this->assertEquals($recipientArray, $this->message->getTo());        
    }      
    
    public function testCanGetSetCc()
    {
        $this->assertNull($this->message->getCc()); 
        
        $message = $this->message->setCc('testCc@example.com');
        
        $this->assertInstanceOf(TemplateMessage::class, $message); 

        $this->assertContains('testCc@example.com', $this->message->getCc());
        
        //test arrays
        $recipientArray = ['testCc1@example.com', 'testCc2@example.com'];
        
        $message = $this->message->setCc($recipientArray);

        $this->assertEquals($recipientArray, $this->message->getCc());        
    }      
    
    public function testCanGetSetBcc()
    {
        $this->assertNull($this->message->getBcc()); 
        
        $message = $this->message->setBcc('testBcc@example.com');
        
        $this->assertInstanceOf(TemplateMessage::class, $message); 

        $this->assertContains('testBcc@example.com', $this->message->getBcc());
        
        //test arrays
        $recipientArray = ['testBcc1@example.com', 'testBcc2@example.com'];
        
        $message = $this->message->setBcc($recipientArray);

        $this->assertEquals($recipientArray, $this->message->getBcc());        
    }  
    
    public function testCanGetSetTemplate()
    {
        $this->assertNull($this->message->getTemplate()); 
        
        $message = $this->message->setTemplate(99);
        
        $this->assertInstanceOf(TemplateMessage::class, $message); 

        $this->assertSame(99, $this->message->getTemplate());
        
    }  
    
    public function testCanGetSetAttributes()
    {
        $this->assertNull($this->message->getAttributes()); 
        
        $attributes = array(
            'key1' => 'val1',
            'key2' => 'val2',
            'key3' => array(
                'subkey1' => 'subvalue1',
                'subkey2' => array( 
                    'subsubkey1' => 'subsubvalue1' 
                ),
            )
        );
        
        $message = $this->message->setAttributes($attributes);
        
        $this->assertInstanceOf(TemplateMessage::class, $message); 

        $expected = array(
            'KEY1' => 'val1',
            'KEY2' => 'val2',
            'KEY3__SUBKEY1' => 'subvalue1',
            'KEY3__SUBKEY2__SUBSUBKEY1' => 'subsubvalue1',
        );

        $this->assertEquals($expected, $this->message->getAttributes());
        $this->assertEquals($attributes, $this->message->getAttributes(false));
        
    }      

    public function testCanGetSetTags()
    {
        $this->assertNull($this->message->getTags()); 
        
        $tags = ['tag1','tag2','tag3'];
        
        $message = $this->message->setTags($tags);
        
        $this->assertInstanceOf(TemplateMessage::class, $message); 

        $this->assertEquals($tags, $this->message->getTags());
    }  

    public function testCanGetSetHeaders()
    {
        $this->assertNull($this->message->getHeaders()); 
        
        $headers = [
            'header1' => 'value1',
            'header2' => 'value2',
            'header3' => 'value3',
        ];
        
        $message = $this->message->setHeaders($headers);
        
        $this->assertInstanceOf(TemplateMessage::class, $message); 

        $this->assertEquals($headers, $this->message->getHeaders());
    }  


    public function testCanAttach()
    {

        $message = $this->message->attachContent('0123456789');
        
        $this->assertInstanceOf(TemplateMessage::class, $message); 
        
        $attachments = $this->message->getSendinblueModel()->getAttachment();

        $this->assertContainsOnlyInstancesOf(SendinBlue\Client\Model\SendEmailAttachment::class, $attachments);
        $this->assertEquals(base64_encode('0123456789'), $attachments[0]->getContent());
    }

    public function testToString()
    {
         $this->assertInternalType('string', $this->message->toString());
    }

}