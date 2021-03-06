<?php
namespace cymapgt\core\utility\notifier;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-04-27 at 23:56:49.
 */
class NotifierEmailPhpMailerServiceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var NotifierEmailSwiftMailerService
     */
    protected $object;

    //array of configurations required
    protected $configuration = array(
                                    "EMAIL_CLIENT"    => null,
                                    "USERNAME"        => null,
                                    "PASSWORD"        => null,
                                    "HOST"            => null,
                                    "IS_SMTP"         => null,
                                    "SMTP_DEBUG"      => null,
                                    "SMTP_AUTH"       => null,
                                    "PORT"            => null,
                                    "SMTP_SECURE"     => null,
                                    "LINE_ENDING"     => null,
                                    "CHARSET"         => null,
                                    "EMAIL_FROM"      => null,
                                    "EMAIL_FROM_NAME" => null,
                                    "WORD_WRAP"       => null,
                                    "IS_HTML"         => null
                               );
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() : void
    {
        $this->object = new NotifierEmailPhpMailerService;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() : void
    {
    }

    /**
     * @covers cymapgt\core\utility\notifier\NotifierEmailSwiftMailerService::setNotifierType
     */
    public function testSetNotifierType()
    {
        $notifierType = $this->object->getNotifierType();
        $this->assertInternalType('null', $notifierType);
        $this->object->setNotifierType('EMAIL');
        $notifierTypeUpdated = $this->object->getNotifierType();
        $this->assertInternalType('string', $notifierTypeUpdated);
    }

    /**
     * @covers cymapgt\core\utility\notifier\NotifierEmailSwiftMailerService::getNotifierType
     */
    public function testGetNotifierType()
    {
       $this->assertInternalType(
           'null',
           $this->object->getNotifierType()
       );
    }

    /**
     * @covers cymapgt\core\utility\notifier\NotifierEmailSwiftMailerService::setConfiguration
     */
    public function testSetConfiguration()
    {
        $this->object->setConfiguration($this->configuration);
        $this->assertEquals($this->configuration, $this->object->getConfiguration());
    }

    /**
     * @covers cymapgt\core\utility\notifier\NotifierEmailSwiftMailerService::getConfiguration
     */
    public function testGetConfiguration()
    {
        $configuration = $this->object->getConfiguration();
        $configDiffArr = array_diff_key($configuration, $this->configuration);
        $this->assertEquals(0, count($configDiffArr));
    }

    /**
     * @covers cymapgt\core\utility\notifier\NotifierEmailSwiftMailerService::setTemplatePath
     * @todo   Implement testSetTemplatePath().
     */
    public function testSetTemplatePath()
    {
        $this->object->setTemplatePath('testPath1/testSubPath1/test.tpl', 'testTemplate');
        $this->assertEquals('testPath1/testSubPath1/test.tpl', $this->object->getTemplatePath());
        $this->assertEquals('testTemplate', $this->object->getTemplateName());
    }

    /**
     * @covers cymapgt\core\utility\notifier\NotifierEmailSwiftMailerService::getTemplatePath
     */
    public function testGetTemplatePath()
    {
        $this->assertEquals('',$this->object->getTemplatePath());
    }

    /**
     * @covers cymapgt\core\utility\notifier\NotifierEmailSwiftMailerService::getTemplateName
     */
    public function testGetTemplateName()
    {
        $this->assertEquals('',$this->object->getTemplateName());
    }

    /**
     * @covers cymapgt\core\utility\notifier\NotifierEmailSwiftMailerService::initialize
     * @todo   Implement testInitialize().
     */
    public function testInitialize()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers cymapgt\core\utility\notifier\NotifierEmailSwiftMailerService::setMessage
     */
    public function testSetMessage()
    {
        $this->assertInternalType('array', $this->object->getMessage());
        $this->assertEquals(0, count($this->object->getMessage()));
        $this->object->setMessage(array('this is a test email :)'));
        $this->assertEquals(1, count($this->object->getMessage()));
        $this->object->setMessage(array('this is another test email ;)'));
        $this->assertEquals(2, count($this->object->getMessage()));
    }

    /**
     * @covers cymapgt\core\utility\notifier\NotifierEmailSwiftMailerService::getMessage
     */
    public function testGetMessage()
    {
        $myMessage = array('this is a test email :)');
        $this->object->setMessage($myMessage);
        $messageArr = $this->object->getMessage();
        $firstMessage = $messageArr[0];
        $this->assertEquals($firstMessage, $myMessage);
    }

    /**
     * @covers cymapgt\core\utility\notifier\NotifierEmailSwiftMailerService::getMessageById
     */
    public function testGetMessageById()
    {
        $myMessage = array('this is a test email :)');
        $this->object->setMessage($myMessage);
        $firstMessage = $this->object->getMessageById(0);
        $this->assertEquals($firstMessage, $myMessage);
        $nonExistentMessage = $this->object->getMessageById(1);
        $this->assertEquals(false, $nonExistentMessage);
    }

    /**
     * @covers cymapgt\core\utility\notifier\NotifierEmailSwiftMailerService::resetMessageById
     */
    public function testResetMessageById()
    {
        $myMessage = array('this is a test email :)');
        $this->object->setMessage($myMessage);
        $this->object->resetMessageById(0);
        $nonExistentMessage = $this->object->getMessageById(0);
        $this->assertEquals(false, $nonExistentMessage);
    }

    /**
     * @covers cymapgt\core\utility\notifier\NotifierEmailSwiftMailerService::resetMessagesAll
     * @todo   Implement testResetMessagesAll().
     */
    public function testResetMessagesAll()
    {
        $this->object->setMessage(array('this is a test email :)'));
        $this->object->setMessage(array('this is another test email ;)'));
        $this->assertInternalType('array', $this->object->getMessageById(0));
        $this->assertInternalType('array', $this->object->getMessageById(1));        
        $this->object->resetMessagesAll();
        $this->assertEquals(false, $this->object->getMessageById(0));
        $this->assertEquals(false, $this->object->getMessageById(1));
    }

    /**
     * @covers cymapgt\core\utility\notifier\NotifierEmailSwiftMailerService::sendMessageOne
     * @todo   Implement testSendMessageOne().
     */
    public function testSendMessageOne()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers cymapgt\core\utility\notifier\NotifierEmailSwiftMailerService::sendMessageById
     * @todo   Implement testSendMessageById().
     */
    public function testSendMessageById()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers cymapgt\core\utility\notifier\NotifierEmailSwiftMailerService::sendMessagesAll
     * @todo   Implement testSendMessagesAll().
     */
    public function testSendMessagesAll()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
