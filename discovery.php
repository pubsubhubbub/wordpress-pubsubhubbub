<?php
/**
 * class to do dicover pubsubhubbub settings
 * 
 * @author Matthias Pfefferle
 */
class PshbDiscovery {
  var $self = null;
  var $hub = array();
  
  /**
   * constructor
   */
  function __construct($url) {
    $xml = $this->get($url);
    $this->discover($xml);
    
    if (!$this->self) {
      $this->self = $url;
    }
  }
  
  /**
   *
   * 
   */
  function get($url) {
    // add any additional curl options here
    $options = array(CURLOPT_URL => $url,
      CURLOPT_USERAGENT => "PubSubHubbub-Discovery-PHP/1.0",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_FOLLOWLOCATION => true);
    
    $ch = curl_init();
    curl_setopt_array($ch, $options);
    
    $response = curl_exec($ch);
    $info = curl_getinfo($ch);
    
    return $response;
  }
  
  /**
   *
   * 
   */
  function discover($xml) {
    $xml_parser = xml_parser_create('');
    $xml_values = array();
    $xml_tags = array();
    if(!$xml_parser)
      return false;
    xml_parser_set_option($xml_parser, XML_OPTION_TARGET_ENCODING, 'UTF-8');
    xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, 0);
    xml_parser_set_option($xml_parser, XML_OPTION_SKIP_WHITE, 1);
    xml_parse_into_struct($xml_parser, trim($xml), $xml_values);
    xml_parser_free($xml_parser);
    
    foreach ($xml_values as $value) {
      // get hubs
      if ($value['attributes']['rel'] == 'hub') {
        $this->hub[] = $value['attributes']['href'];
      }
      // get self url
      if ($value['attributes']['rel'] == 'self') {
        $this->self = $value['attributes']['href'];
      }
    }
  }
}
?>