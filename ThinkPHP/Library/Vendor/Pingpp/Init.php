<?php

if (!function_exists('curl_init')) {
  throw new Exception('Pingpp needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
  throw new Exception('Pingpp needs the JSON PHP extension.');
}
if (!function_exists('mb_detect_encoding')) {
  throw new Exception('Pingpp needs the Multibyte String PHP extension.');
}

// Pingpp singleton
require('Pingpp.php');

// Utilities
require('Util/Util.php');
require('Util/Set.php');
require('Util/RequestOptions.php');

// Errors
require('Error/Base.php');
require('Error/Api.php');
require('Error/ApiConnection.php');
require('Error/Authentication.php');
require('Error/InvalidRequest.php');
require('Error/RateLimit.php');

// Plumbing
require('Object.php');
require('ApiRequestor.php');
require('ApiResource.php');
require('SingletonApiResource.php');
require('AttachedObject.php');
require('Collection.php');

// Pingpp API Resources
require('Charge.php');
require('Refund.php');
require('RedEnvelope.php');
require('Event.php');


// wx_pub OAuth 2.0 method
require('WxpubOAuth.php');
