<?php
namespace cymapgt\core\utility\notifier\abstractclass;

/**
 * interface NotifierInterface
 * 
 * This interface defines the implementations items of Notifier must implement
 *
 * PHP versions 5.4+
 *
 * @category    utility
 * @package     cymapgt.core.utility.notifier
 * @author      <cogana@gmail.com>
 * @copyright   2014 CYMAPGT
 */

interface NotifierInterface
{
    /**
     * function setNotifierType() - Set the notifier type as per NotifierType data store
     * @param   string $type  - The notification type
     *
     * @return  void
     */
    public function setNotifierType($type);
    

    /**
     * function getNotifierType() - Get the notifier type as per NotifierType data store
     * 
     * @return  string
     */
    public function getNotifierType();
    
    
    /**
     * function setConfiguration() - Set all required configurations, passed in as an array
     * @param   array $configuration  - Array of various required configuration options
     *
     * @return  void
     */
    public function setConfiguration($configuration);

    
    /**
     * function getConfiguration() - Return the set configuration option array
     * 
     * @return  array
     */
    public function getConfiguration();
    
    
    /**
     * function setTemplatePath() - Set the path to template files to use for the notifier service
     * 
     * @param   string $path     - Path to the notification template file
     * @param   string $name     - Name of the notification template file
     *
     * @return  void
     */
    public function setTemplatePath($path, $name);

    
    /**
     * function getTemplatePath() - Return the set template path
     * 
     * @return  string
     */
    public function getTemplatePath();

    
    /**
     * function getTemplateName() - Return the set template name
     * 
     * @return  string
     */
    public function getTemplateName();
    
    
     /**
     * function initialize() - Initialize the object to workable state, should be called after
     *                         setting the configurations
     * @param   array $initArr     - Any additional initialization parameters
     *
     * @return  void
     */
    public function initialize($initArr);
    
    
    /**
     * function setMessage() - Input the actual message payload
     * @param   array $message - Array containing message payload e.g head,body,subject
     *
     * @return  void
     */
    public function setMessage($message);

    
     /**
     * function getMessage() - Return all message payloads
     * 
     * @return  array
     */
    public function getMessage();
     
    /**
     * function getMessageById() - Return a message by its id
     * 
     * @param mixed  $messageId
     * 
     * @return array
     */
    public function getMessageById($messageId);
    
    /**
     * function resetMessageById() - Unset the message from $messages array
     *                               based on its ID
     * @param   mixed $messageId
     *
     * @return  boolean
     */
    public function resetMessageById($messageId);
    
    
    /**
     * function resetMessagesAll() - Unset all messages in $messages array
     *
     * @return  boolean
     */
    public function resetMessagesAll();
  

    /**
     * function resetMessageById() - Send message where the payload has only
     *                               one message in its listing
     * @param   mixed $messageId
     *
     * @return  boolean
     */
    public function sendMessageOne($sendSettings);
    
    /**
     * function sendMessageById() - Set message using its id
     * 
     * @param   array $sendSettings - Additional send settings
     * @param   int   $messageId    - The message Id
     *
     * @return  boolean
     */
    public function sendMessageById($sendSettings, $messageId);
    
    /**
     * function sendMessagesAll() - Send all messages in the messages
     *                              array
     * 
     * @param   array $sendSettings - Additional send settings
     *
     * @return  boolean
     */
    public function sendMessagesAll($sendSettings);
}
