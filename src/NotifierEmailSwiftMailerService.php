<?php
namespace cymapgt\core\utility\notifier;

use cymapgt\Exception\NotifierEmailException;
use cymapgt\core\utility\notifier\abstractclass\NotifierInterface;

/**
 * class NotifierEmailSwiftMailerService
 * 
 * This interface implements the Notifier interface to provide a standard
 * method of configuring and using SwiftMailer library from SensioLabs
 * 
 * PHP versions 7.0+
 *
 * @category    utility
 * @package     notifier
 * @author      <cogana@gmail.com>
 * @copyright   2017 CYMAPGT
 */

class NotifierEmailSwiftMailerService implements NotifierInterface
{
    //the notifier type e.g. Email, SMS
    protected $notifierType  = null;
    
    //array of configurations required  
    protected $configuration = array(
                                    'EMAIL_CLIENT'          => 'SwiftMailer',
                                    'USERNAME'              => '',
                                    'PASSWORD'              => '',
                                    'HOST'                  => '',
                                    'IS_SMTP'               => true,
                                    'SMTP_DEBUG'            => 2,
                                    'SMTP_AUTH'             => true,
                                    'PORT'                  => 587,
                                    'SMTP_SECURE'           => 'tls',
                                    'LINE_ENDING'           => '\n',
                                    'CHARSET'               => 'utf-8',
                                    'RETURN_PATH'           => '',
                                    'EMAIL_FROM'            => '',
                                    'EMAIL_FROM_NAME'       => '',
                                    'WORD_WRAP'             => 80,
                                    'IS_HTML'               => true,
                                    "USE_ANTIFLOOD"         => false,
                                    "ANTIFLOOD_FREQUENCY_MAILS"   => null,
                                    "ANTIFLOOD_FREQUENCY_SECONDS"   => null,
                                    "USE_THROTTLER"         => false,
                                    "THROTTLER_MAILS_PER_MINUTE" => null,
                                    "THROTTLER_BYTES_PER_MINUTE" => null        
                               ); 
    
    //template settings e.g. if using php, smarty or twig
    protected $templatePath = '';
    protected $templateName = '';
    
    //init array, for additional data needed to do the job, after configuration
    protected $initArr = array();
    
    //messages array, where message payloads are stored before sending
    protected $messages = array();
    
    protected $mailHandler = null;
    
    
    /**
     * function __construct() - Constructor method
     */
    public function __construct(\Swift_SmtpTransport $swiftMailerTransport){
        $this->mailHandler = new \Swift_Mailer($swiftMailerTransport);
    }

    
    /**
     * function setNotifierType() - Set the notifier type as per NotifierType data store
     * @param   string $type  - The notification type
     *
     * @return  void
     */
    public function setNotifierType($type){
        $this->notifierType = $type;
    }
    

    /**
     * function getNotifierType() - Get the notifier type as per NotifierType data store
     * 
     * @return  string
     */     
    public function getNotifierType(){
        return $this->notifierType;
    }
    
    
    /**
     * function setConfiguration() - Set all required configurations, passed in as an array
     * @param   array $configuration  - Array of various required configuration options
     *
     * @return  void
     */      
    public function setConfiguration($configuration) {
        $emailRequiredConfigurations = $this->configuration;
        $configDiff = array_diff($emailRequiredConfigurations, $configuration);
        
        //validate that all required email settings are present
        if (
            is_array($configDiff
            && count($configDiff))
        ) {
            throw new NotifierEmailException('The configuration array does not contain all required Email configurations');
        }
        
        //set the configuration provided
        $this->configuration = $configuration;
    }

    
    /**
     * function getConfiguration() - Return the set configuration option array
     * 
     * @return  array
     */     
    public function getConfiguration() {
        return $this->configuration;
    }
    
    
    /**
     * function setTemplatePath() - Set the path to template files to use for the notifier service
     * 
     * @param   string $path     - Path to the notification template file
     * @param   string $name     - Name of the notification template file
     *
     * @return  void
     */      
    public function setTemplatePath($path, $name) {
        $this->templateName = $name;
        $this->templatePath = $path;
    }

    
    /**
     * function getTemplatePath() - Return the set template path
     * 
     * @return  string
     */    
    public function getTemplatePath(){
        return $this->templatePath;
    }

    /**
     * function getTemplateName() - Return the set template name
     * 
     * @return  string
     */    
    public function getTemplateName() {
        return $this->templateName;
    }
    
     /**
     * function initialize() - Initialize the object to workable state, should be called after
     *                         setting the configurations
     * @param   array $initArr     - Any additional initialization parameters
     *
     * @return  void
     */    
    public function initialize($initArr) {
        $this->initArr = $initArr;
    }
    
    
    /**
     * function setMessage() - Input the actual message payload
     * @param   array $message - Array containing message payload e.g head,body,subject
     *
     * @return  void
     */     
    public function setMessage($message) {
        $this->messages[] = $message;
    }

    
     /**
     * function getMessage() - Return all message payloads
     * 
     * @return  array
     */    
    public function getMessage() {
        return $this->messages;
    }
     
    /**
     * function getMessageById() - Return a message by its id
     * 
     * @param mixed  $messageId
     * 
     * @return array
     */
    public function getMessageById($messageId) {
        if (isset($this->messages[$messageId])) {
            return $this->messages[$messageId];
        } else {
            return false;
        }
    }
    
    /**
     * function resetMessageById() - Unset the message from $messages array
     *                               based on its ID
     * @param   mixed $messageId
     *
     * @return  boolean
     */     
    public function resetMessageById($messageId) {
        if (isset($this->messages[$messageId])) {
            unset($this->messages[$messageId]);
            return true;
        } else {
            return false;
        }        
    }
    
    
    /**
     * function resetMessagesAll() - Unset all messages in $messages array
     *
     * @return  boolean
     */      
    public function resetMessagesAll() {
        foreach ($this->messages as $key => $message) {
            unset($this->messages[$key]);
        }
    }
  

    /**
     * function resetMessageById() - Send message where the payload has only
     *                               one message in its listing
     * @param   mixed $messageId
     *
     * @return  boolean
     */     
    public function sendMessageOne($sendSettings) {
        $this->_bootstrapMailHandler();
        
        //get the key of the first message
        reset($this->messages);
        $key = key($this->messages);
        $this->_bootstrapMailMessage($key);
        
        //TODO: Chain exceptions here
        //try {
        $messageObj = $this->messages[$key]['messageObj'];
        $this->mailHandler->send($messageObj);
        //} catch (phpmailerException $ex) {
            //echo $ex->errorMessage();
        //}
        
        //pop the message stack
        $msgStack = $this->messages;
        array_shift($msgStack);
        $this->messages = $msgStack;
    }
    
    /**
     * function sendMessageById() - Set message using its id
     * 
     * @param   array $sendSettings - Additional send settings
     * @param   int   $messageId    - The message Id
     *
     * @return  boolean
     */    
    public function sendMessageById($sendSettings, $messageId){
        $messageId = (int) $messageId;
        
        if (!(isset($this->messages[$messageId]))) {
            throw new NotifierEmailException('Trying to send Message by Id, for an Id that is not set');
        }

        $this->_bootstrapMailHandler();        
        $this->_bootstrapMailMessage($messageId);
        
        //phpmailer exception used
        try {
            $this->mailHandler->Send();            
        } catch (phpmailerException $ex) {
            echo $ex->errorMessage();
        }        
    }
    
    /**
     * function sendMessagesAll() - Send all messages in the messages
     *                              array
     * 
     * @param   array $sendSettings - Additional send settings
     *
     * @return  boolean
     */    
    public function sendMessagesAll($sendSettings){
        while (count($this->messages) > 0) {
            $this->sendMessageOne($sendSettings);
        }
    }
    
    /**
     * function _bootstrapMailHandler() - Prepare the mailHandler by setting the configurations
     * 
     * @return void
     * 
     * @access private
     */
    private function _bootstrapMailHandler(){
        //confirm that mailHandler is an instance of PHPMailer
        if(!isset($this->mailHandler)
          && !($this->mailHandler instanceof \Swift_SmtpTransport)
        ) {
            throw new NotifierEmailException("The mail handler is not an instance of Swift_SmtpTransport");
        }
    }
    
    /**
     * function _bootstrapMailMessage() - Bootstrap a message for sending, clearing previous settings
     * 
     * @param   $messageId  - The message Id
     * 
     * @return void
     */
    private function _bootstrapMailMessage($messageId){
       //validate that the message exists
        if(!isset($this->messages[($messageId)])){
            throw new NotifierEmailException('Illegal attempt to bootstrap a message that doesnt exist');
        }
        
        $swiftMessage = new \Swift_Message();
        
        $swiftMessage->setFrom($this->configuration['EMAIL_FROM']);
        
        $arrMsgTo = array();
        
        //add receipients on main address
        foreach($this->messages[($messageId)]['TO'] as $emailUser) {
            if (isset($emailUser['name'])) {
                $arrMsgTo[($emailUser['email'])] = $emailUser['name'];
            } else {
                $arrMsgTo[] = $emailUser['email'];
            }
        }
        
        //embed the recipients to the Swift Message
        $swiftMessage->setTo($arrMsgTo);
        $arrMsgCC = array();
        
        if (array_key_exists('CC', $this->messages[($messageId)])) {
            //add recepients on CC
            foreach($this->messages[($messageId)]["CC"] as $emailUser){
                if (isset($emailUser['name'])) {
                    $arrMsgCC[($emailUser['email'])] = $emailUser['name'];
                } else {
                    $arrMsgCC[] = $emailUser['email'];
                }            
            }

            if (count($arrMsgCC)) {
                $swiftMessage->setCc($arrMsgCC);
            }            
        }
        
        //add recepients on bCC
        if (array_key_exists('BCC', $this->messages[($messageId)])) {
            //add recepients on BCC
            foreach($this->messages[($messageId)]["BCC"] as $emailUser){
                if (isset($emailUser['name'])) {
                    $arrMsgBCC[($emailUser['email'])] = $emailUser['name'];
                } else {
                    $arrMsgBCC[] = $emailUser['email'];
                }            
            }

            if (count($arrMsgBCC)) {
                $swiftMessage->setBcc($arrMsgBCC);
            }            
        }       

        //set ReturnPath
        $returnPath = $this->configuration['RETURN_PATH'];
        $swiftMessage->setReturnPath($returnPath);
        
        //Handle Attachments
        foreach($this->messages[($messageId)]['ATTACHMENTS'] as $attachmentFilename => $attachmentFilepath) {           
            if (!(is_int($attachmentFilename))) {
                $swiftMessage->attach(\Swift_Attachment::fromPath($attachmentFilepath)->setFilename($attachmentFilename));
            } else {
                $swiftMessage->attach(\Swift_Attachment::fromPath($attachmentFilepath));
            }

        }
        
        //add the subject
        $swiftMessage->setSubject($this->messages[($messageId)]["SUBJECT"]);
        
        //add the email altbody
        $swiftMessage->addPart($this->messages[($messageId)]["ALT_BODY"]);

        //set the message body (as HTML)
        $swiftMessage->setBody($this->messages[($messageId)]["MSG_HTML"], 'text/html');
                
        //Check for Anti-Flood Setting
        if (array_key_exists('USE_ANTIFLOOD', $this->configuration)) {
            $antiFloodConfig = $this->configuration['USE_ANTIFLOOD'];            
        } else {
            $antiFloodConfig = false;
        }
        
        //Set AntiFlood Settings
        if ($antiFloodConfig) {
            $antiFloodFrequencyMails   = $this->configuration['ANTIFLOOD_FREQUENCY_MAILS'];
            $antiFloodFrequencySeconds = $this->configuration['ANTIFLOOD_FREQUENCY_SECONDS'];
            $this->mailHandler->registerPlugin(
                new \Swift_Plugins_AntiFloodPlugin($antiFloodFrequencyMails, $antiFloodFrequencySeconds)
            );
        }
       
        //Check for Throttler Setting
        if (array_key_exists('USE_THROTTLER', $this->configuration)) {
            $throttlerConfig = $this->configuration['USE_THROTTLER'];            
        } else {
            $throttlerConfig = false;
        }
        
        //Set AntiFlood Settings
        if ($throttlerConfig) {
            $throttlerMailsPerMinute = $this->configuration['THROTTLER_MAILS_PER_MINUTE'];
            $throttlerBytesPerMinute = $this->configuration['THROTTLER_BYTES_PER_MINUTE'];
            $this->mailHandler->registerPlugin(
                new \Swift_Plugins_ThrottlerPlugin(
                    $throttlerMailsPerMinute,
                    \Swift_Plugins_ThrottlerPlugin::MESSAGES_PER_MINUTE
                )
            );
            $this->mailHandler->registerPlugin(
                new \Swift_Plugins_ThrottlerPlugin(
                    $throttlerBytesPerMinute,
                    \Swift_Plugins_ThrottlerPlugin::BYTES_PER_MINUTE
                )
            );            
        }
        
        $this->messages[($messageId)]['messageObj'] = $swiftMessage;
        
        //set the bootstrapped property to true
        $this->messages[($messageId)]["IS_BOOSTRAPPED"] = true;
    }
}
 