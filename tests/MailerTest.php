<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use yii\sendinblue\transactional\Mailer;
use yii\sendinblue\transactional\SmtpMessage;
use yii\sendinblue\transactional\TemplateMessage;
use yii\base\InvalidConfigException;

use yii\mail\BaseMessage;

final class MailerTest extends TestCase
{
    
    protected $mailer;

    protected function setUp()
    {
        $this->mailer = new Mailer(['apikey' => SENDINBLUE_API_KEY ]);
    }    
    
    public function testCanSetApiKey()
    {
        $this->assertEquals($this->mailer->config->getApiKey('api-key'), SENDINBLUE_API_KEY);
        
        $this->mailer->setApiKey('baz');
        $this->assertEquals($this->mailer->config->getApiKey('api-key'), 'baz');
        
        $this->expectException(InvalidConfigException::class);
        $this->mailer->setApiKey(000);
        
        $this->mailer->setApiKey(SENDINBLUE_API_KEY);
    }
    
    public function testCompose()
    {
        $this->assertInstanceOf(SmtpMessage::class, $this->mailer->compose());
        $this->assertInstanceOf(TemplateMessage::class, $this->mailer->compose(1));
    }  
    
    public function testSendText()
    {
        $message = $this->mailer->compose();
        
        $message->setHtmlBody('<p>this is a html test</p>');
        
        $message->setFrom(SENDINBLUE_FROM);
        
        $message->setTo(SENDINBLUE_TO);
        $message->setReplyTo(SENDINBLUE_REPLYTO);
        $message->setCc(SENDINBLUE_CC);
        $message->setBcc(SENDINBLUE_BCC);
        
        $message->setSubject('Yii2 Sendinblue html test');
        $result = $message->send();
        
        $message->setTextBody('this is a text test');
        $message->setSubject('Yii2 Sendinblue text test');
        
        $result = $message->send();
        
        $this->assertTrue($result);
    }     
    
    public function testSendTemplate()
    {
        $message = $this->mailer->compose(SENDINBLUE_TEMPLATE);
        
        $message->setTo(SENDINBLUE_TO);
        $message->setSubject('Yii2 Sendinblue test template');
        
        $result = $message->send();
        
        $this->assertTrue($result);
        
        $message = $this->mailer->compose(
            SENDINBLUE_TEMPLATE, [
            'TEST_ATTRIBUTE' => 'test value',
            ]
        );
        
        $message->setTo(SENDINBLUE_TO);
        $result = $message->send();
        
        $this->assertTrue($result);
    }         
    
    public function testMissingApiKey()
    {
        $this->expectException(InvalidConfigException::class);
        
        $mailer  = new Mailer(
            array( 
            'config' => new SendinBlue\Client\Configuration
            )
        );
    }    

}