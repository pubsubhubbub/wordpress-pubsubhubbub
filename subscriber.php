<?php
// a PHP client library for pubsubhubbub
// as defined at http://code.google.com/p/pubsubhubbub/
// written by Josh Fraser | joshfraser.com | josh@eventvue.com
// modified by Matthias Pfefferle | notizblog.org | matthias@pfefferle.org
// Released under Apache License 2.0

/**
 * a pubsubhubbub subscriber
 *
 * @author Josh Fraser
 * @author Matthias Pfefferle
 */
class PshbSubscriber {
  // put your google key here
  // required if you want to use the google feed API to lookup RSS feeds
  protected $google_key = "";

  protected $hub_url;
  protected $topic_url;
  protected $callback_url;
  protected $credentials;
  // accepted values are "async" and "sync"
  protected $verify = "async";
  protected $verify_token;
  protected $lease_seconds;

  // create a new Subscriber (credentials added for SuperFeedr support)
  public function __construct($callback_url, $hub_url = null, $credentials = false) {
    if ($hub_url && !preg_match("|^https?://|i",$hub_url))
      throw new Exception('The specified hub url does not appear to be valid: '.$hub_url);

    if (!isset($callback_url))
      throw new Exception('Please specify a callback');

    $this->hub_url = $hub_url;
    $this->callback_url = $callback_url;
    $this->credentials = $credentials;
  }

  // $use_regexp lets you choose whether to use google AJAX feed api (faster, but cached) or a regexp to read from site
  public function find_feed($url, $http_function = false) {
    // using google feed API
    $url = "http://ajax.googleapis.com/ajax/services/feed/lookup?key={$this->google_key}&v=1.0&q=".urlencode($url);
    // fetch the content
    if ($http_function)
      $response = $http_function($url);
    else
      $response = $this->http($url);

    $result = json_decode($response, true);
    $rss_url = $result['responseData']['url'];
    return $rss_url;
  }

  public function subscribe($topic_url, $http_function = false) {
    if (!$this->hub_url) {
      $this->find_hub($topic_url);
    }

    return $this->change_subscription("subscribe", $topic_url, $http_function = false);
  }

  public function unsubscribe($topic_url, $http_function = false) {
    return $this->change_subscription("unsubscribe", $topic_url, $http_function = false);
  }

  // helper function since sub/unsub are handled the same way
  private function change_subscription($mode, $topic_url, $http_function = false) {
    if (!isset($topic_url))
      throw new Exception('Please specify a topic url');

    // lightweight check that we're actually working w/ a valid url
    if (!preg_match("|^https?://|i",$topic_url))
      throw new Exception('The specified topic url does not appear to be valid: '.$topic_url);

    // set the mode subscribe/unsubscribe
    $post_string = "hub.mode=".$mode;
    $post_string .= "&hub.callback=".urlencode($this->callback_url);
    $post_string .= "&hub.verify=".$this->verify;
    $post_string .= "&hub.verify_token=".$this->verify_token;
    $post_string .= "&hub.lease_seconds=".$this->lease_seconds;

    // append the topic url parameters
    $post_string .= "&hub.topic=".urlencode($topic_url);

    // make the http post request and return true/false
    // easy to over-write to use your own http function
    if ($http_function)
      return $http_function($this->hub_url,$post_string);
    else
      return $this->http($this->hub_url,$post_string);
  }

  // default http function that uses curl to post to the hub endpoint
  private function http($url, $post_string = null) {

    // add any additional curl options here
    $options = array(CURLOPT_URL => $url,
                     CURLOPT_USERAGENT => "PubSubHubbub-Subscriber-PHP/1.0",
                     CURLOPT_RETURNTRANSFER => true,
                     CURLOPT_FOLLOWLOCATION => true);

    if ($post_string) {
      $options[CURLOPT_POST] = true;
      $options[CURLOPT_POSTFIELDS] = $post_string;
    }

    if ($this->credentials)
      $options[CURLOPT_USERPWD] = $this->credentials;

    $ch = curl_init();
    curl_setopt_array($ch, $options);

    $response = curl_exec($ch);
    $info = curl_getinfo($ch);

    // all good -- anything in the 200 range
    if (substr($info['http_code'],0,1) == "2") {
      return $response;
    }

    return false;
  }

  //
  public function find_hub($topic_url) {
    $xml = $this->http($topic_url);
    if (!$xml)
      throw new Exception('Please enter a valid URL');

    $xml_parser = xml_parser_create('');
    $xml_values = array();
    $xml_tags = array();

    if(!$xml_parser)
      throw new Exception('Your webserver doesn\'t support xml-parsing');

    xml_parser_set_option($xml_parser, XML_OPTION_TARGET_ENCODING, 'UTF-8');
    xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, 0);
    xml_parser_set_option($xml_parser, XML_OPTION_SKIP_WHITE, 1);
    xml_parse_into_struct($xml_parser, trim($xml), $xml_values);
    xml_parser_free($xml_parser);

    $hubs = array();

    foreach ($xml_values as $value) {
      // get hubs
      if ($value['attributes']['rel'] == 'hub') {
        $hubs[] = $value['attributes']['href'];
      }
      // get self url
      if ($value['attributes']['rel'] == 'self') {
        $self = $value['attributes']['href'];
      }
    }

    if (count($hubs) >= 1)
      $this->hub_url = $hubs[0];
    else
      throw new Exception('This feed doesn\'t reference a hub url');

    $this->topic_url = $self;
  }
}
?>