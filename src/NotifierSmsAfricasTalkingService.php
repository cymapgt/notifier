<?php
namespace cymapgt\core\utility\notifier;

use cymapgt\Exception\NotifierEmailException;
use cymapgt\core\utility\notifier\abstractclass\NotifierInterface;

/**
 * class NotifierSmsService
 * 
 * This interface implements the Notifier interface to provide a standard
 * method of configuring and using SMS Notification library of AfricasTalking
 *
 * PHP versions 5.4+
 *
 * @category    utility
 * @package     cymapgt.core.utility.notifier
 * @author      Cyril Ogana <cogana@gmail.com>
 * @copyright   2014 CYMAPK
 */

class NotifierSmsAfricasTalkingService implements NotifierInterface
{
    //the notifier type e.g. Email, SMS
    protected $notifierType  = null;
    
    //array of configurations required
    protected $configuration = array (
                                    "SMS_CLIENT"      => "AfricasTalking",
                                    "USERNAME"        => "",
                                    "PASSWORD"        => "",
                                    "APIKEY"          => "",
                                    "IS_API"          => true,
                                    "IS_BEHIND_PROXY" => false,
                                    "PROXY_AUTH"      => -1,
                                    "PROXY_TYPE"      => -1,
                                    "PROXY_SERVER"    => "",
                                    "PROXY_LOGIN"     => "",
                                    "MAX_CHARS"       => 140,
                                    "SMS_FROM_NAME"   => ""
                               );
    
    //template settings e.g. if using php, smarty or twig
    protected $templatePath = "";
    protected $templateName = "";
    
    //init array, for additional data needed to do the job, after configuration
    protected $initArr = array();
    
    //messages array, where message payloads are stored before sending
    protected $messages = array();
    
    protected $mailHandler = null;
    
    
    /**
     * function __construct() - Constructor method
     */
    public function __construct($configurationArr, $isBehindProxy = false){        
        //bootstrap the handler immediately
        $this->_bootstrapSmsHandler($configurationArr, $isBehindProxy);
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
    public function setConfiguration($configuration){
        $emailRequiredConfigurations = $this->configuration;
        $configDiff = array_diff($emailRequiredConfigurations, $configuration);
        
        //validate that all required email settings are present
        if(is_array($configDiff
           && count($configDiff))
        ){
            throw new NotifierEmailException("The configuration array does not contain all required Email configurations");
        }
        
        //set the configuration provided
        $this->configuration = $configuration;
    }

    
    /**
     * function getConfiguration() - Return the set configuration option array
     * 
     * @return  array
     */     
    public function getConfiguration(){
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
    public function setTemplatePath($path, $name){
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
    public function getTemplateName(){
        return $this->templateName;
    } 
    
     /**
     * function initialize() - Initialize the object to workable state, should be called after
     *                         setting the configurations
     * @param   array $initArr     - Any additional initialization parameters
     *
     * @return  void
     */    
    public function initialize($initArr){
        $this->initArr = $initArr;
    }
    
    
    /**
     * function setMessage() - Input the actual message payload
     * @param   array $message - Array containing message payload e.g head,body,subject
     *
     * @return  void
     */     
    public function setMessage($message){
        $this->messages[] = $message;
    }

    
     /**
     * function getMessage() - Return all message payloads
     * 
     * @return  array
     */    
    public function getMessage(){
        return $this->messages;
    }
     
    /**
     * function getMessageById() - Return a message by its id
     * 
     * @param mixed  $messageId
     * 
     * @return array
     */
    public function getMessageById($messageId){
        if(isset($this->messages[$messageId])){
            return $this->messages[$messageId];
        }else{
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
    public function resetMessageById($messageId){
        if(isset($this->messages[$messageId])){
            unset($this->messages[$messageId]);
            return true;
        }else{
            return false;
        }        
    }
    
    
    /**
     * function resetMessagesAll() - Unset all messages in $messages array
     *
     * @return  boolean
     */      
    public function resetMessagesAll(){
        foreach($this->messages as $key => $message){
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
    public function sendMessageOne($sendSettings){
        //get the key of the first message
        reset($this->messages);
        $key = key($this->messages);
        $recipients = $this->messages[$key]["RECIPIENTS"];
        $message    = $this->messages[$key]["MESSAGE"];
        $senderName = $this->configuration["SMS_FROM_NAME"];
        
        //phpmailer exception used
        try {
            $this->mailHandler->sendMessage($recipients, $message, $senderName);
        } catch (AfricasTalkingGatewayException $ex) {
            die($ex->errorMessage());
        }
        
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
    public function sendMessageById($sendSettings, $messageId) {
        $messageId = (int) $messageId;
        
        if (!isset($this->messages[$messageId])) {
            throw new NotifierEmailException('Trying to send Message by Id, for an Id that is not set');
        }
        
        $recipients = $this->messages[$messageId]['RECIPIENTS'];
        $message    = $this->messages[$messageId]['MESSAGE'];
        $senderName = $this->configuration['SMS_FROM_NAME'];
        
        //AfricasTalkingGatewayException used
        try {
            $this->mailHandler->sendMessage($recipients, $message, $senderName);
        } catch (Exception $ex) {
            die($ex->errorMessage());
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
        while(count($this->messages) > 0) {
            $this->sendMessageOne($sendSettings);
        }
    }
    
    /**
     * function _bootstrapSmsHandler() - Prepare the mailHandler by setting the configurations
     * 
     * @return void
     * 
     * @access private
     */
    private function _bootstrapSmsHandler($configurationArr, $isBehindProxy = false){
        //validate that username and APIKEY exist in the configuration
        if (
            !isset($configurationArr["USERNAME"])
            && !isset($configurationArr["APIKEY"])
        ) {
            throw new NotifierEmailException("The username or APIKEY is not configured");
        }
        
        $userName = $configurationArr["USERNAME"];
        $apiKey   = $configurationArr["APIKEY"];
        
        //if behind proxy, we also need proxy settings
        if($isBehindProxy){
            if (
                !isset($configurationArr["PROXY_AUTH"])
                && !isset($configurationArr["PROXY_TYPE"])
                && !isset($configurationArr["PROXY_SERVER"])
                && !isset($configurationArr["PROXY_LOGIN"])
            ){
                throw new NotifierEmailException("The proxy settings are not configured properly");
            }
            
            $proxySettings = array (
                "PROXYAUTH"   => $configurationArr["PROXY_AUTH"],
                "PROXYTYPE"   => $configurationArr["PROXY_TYPE"],
                "PROXYSERVER" => $configurationArr["PROXY_SERVER"],
                "PROXYLOGIN"  => $configurationArr["PROXY_LOGIN"]
            );
            
            //initiate the handler with credentials
            $this->mailHandler = new \AfricasTalkingGatewayWithProxy(
                $userName,
                $apiKey,
                $proxySettings
            );
        } else {
            $this->mailHandler = new \AfricasTalkingGateway(
                $userName,
                $apiKey
            );
        }
        
        //Set the configuration array
        $this->setConfiguration($configurationArr);
    } 
}
