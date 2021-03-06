<?php
/**
 * Ping the API, just to check everything is working.
 */
require __DIR__.'/../vendor/autoload.php';

$client = Loco\Http\ApiClient::factory();

/* @var $result \Guzzle\Service\Resource\Model */
$result = $client->ping();

if( $version = $result->get('version') ){
    if( $client->getApiVersion() === $version ){
        printf("Ping OK, API version is %s\n", $version );
    }
    else {
        printf("Ping OK, but API version is %s and SDK is built against %s\n", $version, $client->getApiVersion() );
    }
}
else {
    printf("Ping failed to ping API\n");
}