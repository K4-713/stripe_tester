<?php

require_once 'config.php';

//these are the keys that we expect to be useful to the backend.
$relevant_keys = array(
    'amount',
    'currency',
    'description',
    'source',
    'receipt_email',
);

//debug: - what got posted to us. 
echo "<pre>";
foreach ($_REQUEST as $thing => $stuff) {

    echo "$thing = $stuff\n";
}
echo "</pre>";

//now, let's get real and scrub everything we don't want.
$data = array();
foreach ($relevant_keys as $key) {
    $data[$key] = @$_REQUEST[$key];
}

$response = curl_transaction('https://api.stripe.com/v1/charges', $data);

echolog("Response Type = '" . gettype($response) . "'");

function echolog($string) {
    echo "\n\t$string";
    //TODO: Also log if you were going to really use this, for real.
}

/**
 * uses cURL to contact the server. If present, turns the 
 * associative array in $data into the querystring and gets from the URL you 
 * supply. Returns a response or false if it failed.
 * 
 * Largely copied from myself via DonationInterface, and simplified. 
 * 
 * @param string $url The URL to contact
 * @param array|false $data Associative array of the elements to send in the
 *  querystring, or false if you don't need it.
 * @return string|false
 */
function curl_transaction($url, $data = false) {
    // Initialize cURL
    $ch = curl_init();

    // assign header data necessary for the curl_setopt() function
    $content_type = 'application/x-www-form-urlencoded';
    $headers = array(
	'Content-Type: ' . $content_type . '; charset=utf-8',
	'X-VPS-Client-Timeout: 45',
    );

    //We're not posting, so turn that $data array into a querystring...
    if ($data) {
	$querystring = http_build_query($data);
	$url .= '?' . $querystring;
    }


    $curl_opts = array(
	CURLOPT_URL => $url,
	CURLOPT_USERNAME => $stripe_secret_key,
	//CURLOPT_HEADER => 0,
	CURLOPT_POST => true,
	CURLOPT_RETURNTRANSFER => 1,
	CURLOPT_TIMEOUT => 30,
	CURLOPT_FOLLOWLOCATION => 0,
	//TODO: The next two, if you were going to really use this for real.
	//CURLOPT_SSL_VERIFYPEER => 1,
	//CURLOPT_SSL_VERIFYHOST => 2,
	CURLOPT_FORBID_REUSE => true,
	CURLOPT_VERBOSE => false
    );

    $curl_opts[CURLOPT_HTTPHEADER] = $headers;
    curl_setopt_array($ch, $curl_opts);

    // Execute the cURL operation
    $curl_response = curl_exec($ch);


    if ($curl_response !== false) {

	echolog("Curl Response: $curl_response");

	$headers = curl_getinfo($ch);
	$httpCode = $headers['http_code'];

	//Nice to have: More log messaging here.
	switch ($httpCode) {
	    case 200:   // Everything is AWESOME
		break;

	    case 400:   // Oh noes! Bad request.. BAD CODE, BAD BAD CODE!
	    default:    // No clue what happened... break out and log it
		echolog("Something strange happened with your cURL request. HTTP code $httpCode");

		break;
	}
    } else {
	// Well the cURL transaction failed for some reason or another.
	$errno = $this->curl_errno($ch);
	$err = curl_error($ch);
	echolog("cURL erorred out thusly: $errno - $err");
    }

    // Clean up and return
    curl_close($ch);
    return $curl_response;
}


