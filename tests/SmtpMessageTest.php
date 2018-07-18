<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use yii\sendinblue\transactional\SmtpMessage;
use yii\sendinblue\transactional\TemplateMessage;
use yii\base\InvalidConfigException;

use yii\mail\BaseMessage;

final class SmtpMessageTest extends TestCase
{
    
    private $message;

    protected function setUp()
    {
        $this->message = new SmtpMessage();
    }    
    
    public function testCanGetSetCharset()
    {
        $this->assertNull($this->message->getCharset()); 
        
        $message = $this->message->setCharset('utf8');
        
        $this->assertInstanceOf(SmtpMessage::class, $message); 

        $this->assertEquals('utf8', $this->message->getCharset());
        
    }       
    
    public function testCanGetSetFrom()
    {
        $this->assertNull($this->message->getFrom()); 
        
        $message = $this->message->setFrom('testFrom@example.com');

        $this->assertInstanceOf(SmtpMessage::class, $message); 
        
        $this->assertInstanceOf(SendinBlue\Client\Model\SendSmtpEmailSender::class, $this->message->getSendinblueModel()->getSender());
        $this->assertEquals($this->message->getFrom(), 'testFrom@example.com');
    }
    
    public function testCanGetSetReplyTo()
    {
        $this->assertNull($this->message->getReplyTo()); 
        
        $message = $this->message->setReplyTo('testReplyTo@example.com');
        
        $this->assertInstanceOf(SmtpMessage::class, $message); 

        $this->assertInstanceOf(SendinBlue\Client\Model\SendSmtpEmailReplyTo::class, $this->message->getSendinblueModel()->getReplyTo());
        $this->assertEquals($this->message->getReplyTo(), 'testReplyTo@example.com');
    }  
    
    public function testCanGetSetTo()
    {
        $this->assertNull($this->message->getTo()); 
        
        $message = $this->message->setTo('testTo@example.com');
        
        $this->assertInstanceOf(SmtpMessage::class, $message); 

        $this->assertContainsOnlyInstancesOf(SendinBlue\Client\Model\SendSmtpEmailTo::class, $this->message->getSendinblueModel()->getTo());
        $this->assertContains('testTo@example.com', $this->message->getTo());
        
        //test arrays
        $recipientArray = ['testTo1@example.com', 'testTo2@example.com'];
        
        $message = $this->message->setTo($recipientArray);

        $this->assertContainsOnlyInstancesOf(SendinBlue\Client\Model\SendSmtpEmailTo::class, $this->message->getSendinblueModel()->getTo());
        $this->assertEquals($this->message->getTo(), $recipientArray);        
    }      
    
    public function testCanGetSetCc()
    {
        $this->assertNull($this->message->getCc()); 
        
        $message = $this->message->setCc('testCc@example.com');
        
        $this->assertInstanceOf(SmtpMessage::class, $message); 

        $this->assertContainsOnlyInstancesOf(SendinBlue\Client\Model\SendSmtpEmailCc::class, $this->message->getSendinblueModel()->getCc());
        $this->assertContains('testCc@example.com', $this->message->getCc());
        
        //test arrays
        $recipientArray = ['testCc1@example.com', 'testCc2@example.com'];
        
        $message = $this->message->setCc($recipientArray);

        $this->assertContainsOnlyInstancesOf(SendinBlue\Client\Model\SendSmtpEmailCc::class, $this->message->getSendinblueModel()->getCc());
        $this->assertEquals($this->message->getCc(), $recipientArray);        
    }      
    
    public function testCanGetSetBcc()
    {
        $this->assertNull($this->message->getBcc()); 
        
        $message = $this->message->setBcc('testBcc@example.com');
        
        $this->assertInstanceOf(SmtpMessage::class, $message); 

        $this->assertContainsOnlyInstancesOf(SendinBlue\Client\Model\SendSmtpEmailBcc::class, $this->message->getSendinblueModel()->getBcc());
        $this->assertContains('testBcc@example.com', $this->message->getBcc());
        
        //test arrays
        $recipientArray = ['testBcc1@example.com', 'testBcc2@example.com'];
        
        $message = $this->message->setBcc($recipientArray);

        $this->assertContainsOnlyInstancesOf(SendinBlue\Client\Model\SendSmtpEmailBcc::class, $this->message->getSendinblueModel()->getBcc());
        $this->assertEquals($this->message->getBcc(), $recipientArray);        
    }  
    
    public function testCanGetSetSubject()
    {
        $this->assertNull($this->message->getSubject()); 
        
        $message = $this->message->setSubject('Test Subject');
        
        $this->assertInstanceOf(SmtpMessage::class, $message); 

        $this->assertEquals('Test Subject', $this->message->getSubject());
        
    }     
    
    public function testCanSetBody()
    {

        $message = $this->message->setTextBody('text-body');
        
        $this->assertInstanceOf(SmtpMessage::class, $message); 

        $message = $this->message->setHtmlBody('<div>text-body</div>');
        
        $this->assertInstanceOf(SmtpMessage::class, $message); 

        $this->assertEquals('text-body', $this->message->getSendinblueModel()->getTextContent());
        $this->assertEquals('<div>text-body</div>', $this->message->getSendinblueModel()->getHtmlContent());
        
    }   
    
    public function testCanAttach()
    {

        $message = $this->message->attachContent('0123456789');
        
        $this->assertInstanceOf(SmtpMessage::class, $message); 
        
        $attachments = $this->message->getSendinblueModel()->getAttachment();

        $this->assertContainsOnlyInstancesOf(SendinBlue\Client\Model\SendSmtpEmailAttachment::class, $attachments);
        $this->assertEquals(base64_encode('0123456789'), $attachments[0]->getContent());
    }

    public function testToString()
    {
         $this->assertInternalType('string', $this->message->toString());
    }

}