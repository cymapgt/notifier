# notifier
Notifier interface that can be used to encapsulate 3rd party communication
packages using media like email, sms, chat, messengers, voip

The notifier namespace contains libraries that implement the Notifier Interface. 

This interface provides a standard set of common functions for sending communications. This can be email communications or sms communications.

For a detailed documentation on the Notifier Interface, review the Notifier API.

Usage details are provided below

# Usage #

## 1 Sending Email using SwiftMailer ##

### 1.1 Prepare your Autoloader ###
    //configure the SMTP transport (wasteful but use in proto :/

    // Create the Transport
    $swiftMailerTransport = Swift_SmtpTransport::newInstance('smtp.gmail.com', 587, 'tls')
        ->setUsername('smtpuser@smtpserver.com')
        ->setPassword('smtppasswordhere');
    

### 1.2 Edit Your PHP Script ###

### 1.2.1 Configure your SMTP configuration array ###

These settings can be pulled from database or hard coded. User the same smtp user and password that you used to configure the SwiftMailer transport in the previous section

    $configuration = array (
        'EMAIL_CLIENT'          => 'SwiftMailer',
        'USERNAME'              => 'smtpuser@smtpserver.com',
        'PASSWORD'              => 'smtppasswordhere',
        'HOST'                  => 'smtp.gmail.com',
        'IS_SMTP'               => true,
        'SMTP_DEBUG'            => 2,
        'SMTP_AUTH'             => true,
        'PORT'                  => 587,
        'SMTP_SECURE'           => 'tls',
        'LINE_ENDING'           => '\n', 
        'CHARSET'               => 'utf-8',
        'RETURN_PATH'           => 'smptuser@smtpserver.com',
        'EMAIL_FROM'            => 'smtppasswordhere',
        'EMAIL_FROM_NAME'       => 'Cyril Ogana',
        'WORD_WRAP'             => 80,
        'IS_HTML'               => true,
        "USE_ANTIFLOOD"         => false,
        "ANTIFLOOD_FREQUENCY_MAILS"   => null,
        "ANTIFLOOD_FREQUENCY_SECONDS"   => null,
        "USE_THROTTLER"         => false,
        "THROTTLER_MAILS_PER_MINUTE" => null,
        "THROTTLER_BYTES_PER_MINUTE" => null        
    );


### 1.2.2 Build your Notifier Class and Send the messages ###

    //declare namespace usage
    use cymapgt\core\utility\notifier\NotifierEmailSwiftMailerService;

    //create notifier object. The swiftMailerTransport object we pass to it is the one we created in autoloader
    $notifierObj = new NotifierEmailSwiftMailerService($swiftMailerTransport);

    //our message is in the form of an array
    $message = array (
       "TO" => array(
                   array (
                       "name"  => "Cyril Ogana",
                       "email" => "cogana@gmail.com"
                   )
               ),
        "SUBJECT"     =>"MY MAIL",
        "MSG_HTML"    => "Test Notifier 1:)",
        "ATTACHMENTS" => array(
             "imageAttachment" => "c:/User/Image1.jpg"
        )
    );

    //load our message to the notifier
    $notifierObj->setMessage($message);

    //send all messages
    $notifierObj->sendMessagesAll(array()); 

## 2 Sending SMS using AfricasTalking ##

### 2.1 Edit Your PHP Script ###

### 2.2.1 Configure your AfricasTalking configuration array ###

These settings can be pulled from database or hard coded.

    $configuration = array(
                "SMS_CLIENT"      => "AfricasTalking",
                "USERNAME"        => "SMSUSER",
                "PASSWORD"        => "smsApiKeyHere",
                "APIKEY"          => "",
                "IS_API"          => true,
                "IS_BEHIND_PROXY" => false,
                "PROXY_AUTH"      => -1,
                "PROXY_TYPE"      => -1,
                "PROXY_SERVER"    => "",
                "PROXY_LOGIN"     => "",
                "MAX_CHARS"       => 140,
                "SMS_FROM_NAME"   => "SMSUSER"
            );

### 2.2.2 Build your Notifier Class and Send the messages ###

Notice that configuration and message building is what is different. Details
of configuring the authhentication, sending and receiving details of communications
are abstracted away by the Notifier Interface

    //declare namespace usage
    use cymapgt\core\utility\notifier\NotifierSmsAfricasTalkingService;

    /*create notifier object for africas talking. If you are behind a proxy, the
     *second parameter should be true, and your configuration array should contain
     *the proxy server settings
     */
    $notifierObj = new NotifierSmsAfricasTalkingService($configuration, false);

    //our message is in the form of an array
    $message = array (
       "RECIPIENTS" => array(
           "+254723712233",
           "+254720123456",
           "+254721000999"
        ),
        "MESSAGE"     =>"MY TEST SMS :)"
    );

    //load our message to the notifier
    $notifierObj->setMessage($message);

    //send all the SMS
    $notifierObj->sendMessagesAll(array());



# Testing #

PHPUnit Tests are provided with the package

# Contribute

* Email @rhossis or contact via Skype
* You will be added as author for contributions

# License

PROPRIETARY
