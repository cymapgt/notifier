<?php
require_once("AfricasTalkingGateway.php");

/**
 * This class extends the AfricasTalkingGateway, adding capability to negotiate
 * a proxy server via CURL
 * 
 * @author Cyril Ogana <cogana@gmail.com>
 * 
 */
class AfricasTalkingGatewayWithProxy extends AfricasTalkingGateway
{
  protected $proxySettings = array();   //Container for curl proxy settings
  
  public function __construct($username_, $apiKey_, Array $proxySettings_)
  {
    parent::__construct($username_, $apiKey_);
    
    if(!isset($proxySettings_["PROXYAUTH"])
        && !isset($proxySettings_["PROXYTYPE"])
        && !isset($proxySettings_["PROXYSERVER"])
        && !isset($proxySettings_["PROXYLOGIN"])
    ) {
       throw new Exception("The proxy settings are not complete");
    }
    
    $this->proxySettings = $proxySettings_;
  }
  
  protected function setCurlOpts (&$curlHandle_)
  {
    curl_setopt($curlHandle_, CURLOPT_TIMEOUT, 60);
    curl_setopt($curlHandle_, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curlHandle_, CURLOPT_URL, $this->_requestUrl);
    curl_setopt($curlHandle_, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curlHandle_, CURLOPT_HTTPHEADER, array ('Accept: ' . self::AcceptType,
							 'apikey: ' . $this->_apiKey));
    
    $proxySettings_ = $this->proxySettings;
    curl_setopt($curlHandle_, CURLOPT_PROXYAUTH, $proxySettings_["PROXYAUTH"]); //CURLAUTH_HTTP
    //curl_setopt($ch, CURLOPT_PROXYPORT, 80);
    curl_setopt($curlHandle_, CURLOPT_PROXYTYPE, $proxySettings_["PROXYTYPE"]);
    curl_setopt($curlHandle_, CURLOPT_PROXY, $proxySettings_["PROXYSERVER"]);
    curl_setopt($curlHandle_, CURLOPT_PROXYUSERPWD, $proxySettings_["PROXYLOGIN"]);
  }
}
