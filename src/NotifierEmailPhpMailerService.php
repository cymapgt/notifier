<?php
namespace cymapgt\core\utility\notifier;

use cymapgt\Exception\NotifierEmailException;
use cymapgt\core\utility\notifier\abstractclass\NotifierInterface;

/**
 * class NotifierEmailPhpMailerService
 * 
 * This interface implements the Notifier interface to provide a standard
 * method of configuring and using PHPMailer library from Wrox Labs
 * 
 * PHP versions 7.0+
 *
 * @category    utility
 * @package     notifier
 * @author      <cogana@gmail.com>
 * @copyright   2017 CYMAPGT
 */

class NotifierEmailPhpMailerService implements NotifierInterface
{
    //the notifier type e.g. Email, SMS
    protected $notifierType  = null;
    
    //array of configurations required
    protected $configuration = array(
                                    'EMAIL_CLIENT'    => 'PHPMailer',
                                    'USERNAME'        => '',
                                    'PASSWORD'        => '',
                                    'HOST'            => '',
                                    'IS_SMTP'         => true,
                                    'SMTP_DEBUG'      => 2,
                                    'SMTP_AUTH'       => true,
                                    'PORT'            => 587,
                                    'SMTP_SECURE'     => 'tls',
                                    'LINE_ENDING'     => '\n',
                                    'CHARSET'         => 'utf-8',
                                    'EMAIL_FROM'      => '',
                                    'EMAIL_FROM_NAME' => '',
                                    'WORD_WRAP'       => 80,
                                    'IS_HTML'         => true
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
    public function __construct(){
        //we embed an instance of PHPMailer to do the hard work
        $this->mailHandler = new \PHPMailer(true); //exceptions enabled
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
        
        //phpmailer exception used
        try {
            $this->mailHandler->Send();            
        } catch (phpmailerException $ex) {
            echo $ex->errorMessage();
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
          && !($this->mailHandler instanceof \PHPMailer)
        ){
            throw new NotifierEmailException("The mail handler is not an instance of PHPMailer");
        }
        
        //bootstrap some common settings
        if((bool)$this->configuration["IS_SMTP"] === true){
            $this->mailHandler->IsSMTP();
        }
        
        if((bool) $this->configuration["IS_HTML"] === true){
            $this->mailHandler->IsHTML(true);
        }
        
        $this->mailHandler->SMTPDebug  = (int)   $this->configuration["SMTP_DEBUG"];
        $this->mailHandler->SMTPAuth   = (bool)  $this->configuration["SMTP_AUTH"];
        $this->mailHandler->Port       = (int)   $this->configuration["PORT"];
        $this->mailHandler->SMTPSecure = (string)$this->configuration["SMTP_SECURE"];
        $this->mailHandler->Host       = (string)$this->configuration["HOST"];
        $this->mailHandler->Username   = (string)$this->configuration["USERNAME"];
        $this->mailHandler->Password   = (string)$this->configuration["PASSWORD"];
        $this->mailHandler->LE         = (string)$this->configuration["LINE_ENDING"];
        $this->mailHandler->CharSet    = (string)$this->configuration["CHARSET"];
        $this->mailHandler->From       = (string)$this->configuration["EMAIL_FROM"];
        $this->mailHandler->FromName   = (string)$this->configuration["EMAIL_FROM_NAME"];
        $this->mailHandler->WordWrap   = (int)   $this->configuration["WORD_WRAP"];
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
            throw new NotifierEmailException("Illegal attempt to bootstrap a message that doesnt exist");
        }
        
        //clear the mailHandler
        $this->mailHandler->ClearAddresses();
        $this->mailHandler->ClearAllRecipients();
        $this->mailHandler->ClearAttachments();
        $this->mailHandler->ClearBCCs();
        $this->mailHandler->ClearCCs();
        $this->mailHandler->ClearCustomHeaders();
        $this->mailHandler->ClearReplyTos();
        $this->mailHandler->AltBody = "";
        $this->mailHandler->Timeout = 120;
        
        //begin bootstrapping the message
        
        //add receipients on main address
        foreach($this->messages[($messageId)]["TO"] as $emailUser){
            $this->mailHandler->AddAddress($emailUser["email"], $emailUser["name"]);
        }
        
        //add recepients on CC
        foreach($this->messages[($messageId)]["CC"] as $emailUser){
            $this->mailHandler->AddCC($emailUser["mail"], $emailUser["name"]);
        }
        
        //add the subject
        $this->mailHandler->Subject = $this->messages[($messageId)]["SUBJECT"];
        
        //add the email altbody
        $this->mailHandler->AltBody = $this->messages[($messageId)]["ALT_BODY"];

        //set the message body (as HTML)
        $this->mailHandler->MsgHTML($this->messages[($messageId)]["MSG_HTML"]);
                
        //set the bootstrapped property to true
        $this->messages[($messageId)]["IS_BOOSTRAPPED"] = true;
    }
}
 