<?php

namespace Loco\Tests\Http;

use Loco\Http\ApiClient;
use Guzzle\Service\Resource\Model;

/**
 * Test the live /locales API.
 * @group live
 * @group locales
 */
class ApiClientLocalesTest  extends ApiClientTest {
    
    
    /**
     * getLocales
     * @group readonly
     */
    public function testLocalesList(){
        $client = $this->getClient();
        // top level is array
        $locales = $client->getLocales();
        $this->assertInternalType('array', $locales );
        // items are instances of Locale model, but Guzzle won't validate due to primitive being top level.
        $locale = $locales[0]; 
        $this->assertInternalType('array', $locale );
        $this->assertArrayHasKey('code', $locale );
        
        return $locale;
    }
    
    
    
    /**
     * getLocale
     * @depends testLocalesList
     * @group readonly
     */
    public function testLocaleGet( array $locale ){
        $client = $this->getClient();
        $model = $client->getLocale( array( 'locale' => $locale['code'] ) );
        $this->assertInstanceOf( '\Guzzle\Service\Resource\Model', $model );
        $code = $model['code'];
        $this->assertEquals( $locale['code'], $code );
        return $code;
    }    



    /**
     * createLocale
     */
    public function testLocaleCreate(){
        $rand = substr( md5( microtime() ), 0, 5 );
        $code = 'en-GB-x-'.$rand;
        $client = $this->getClient();
        $model = $client->createLocale( array( 'code' => $code ) );
        $this->assertInstanceOf( '\Guzzle\Service\Resource\Model', $model );
        $this->assertEquals( $code, $model['code'] );
        $this->assertStringStartsWith( 'English ', $model['name'] );
        return $code;
    }

    
    /**
     * patchLocale
     * @depends testLocaleCreate
     */
    public function testLocalePatch( $code ){
        $client = $this->getClient();
        $update = array (
            'name' => 'Renamed OK',
            'locale' => $code,
        );
        $model = $client->patchLocale( $update );
        $this->assertInstanceOf( '\Guzzle\Service\Resource\Model', $model );
        $this->assertEquals( 'Renamed OK', $model['name'] );
        return $code;
    }
    
    
    
    /**
     * patchLocale with failure trying to set a non-existant property
     * @depends testLocaleCreate
     * @expectedException \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function testLocalePatchRejectsUnpatchable( $code ){
        $client = $this->getClient();
        $update = array (
            'locale' => $code,
            'rubbish' => 1,
        );
        $client->patchLocale( $update );
    }
    
    
     
    /**
     * deleteLocale
     * @depends testLocalePatch
     */
    public function testLocaleDelete( $code ){
        $client = $this->getClient();
        $model = $client->deleteLocale( array( 'locale' => $code ) );
        $this->assertInstanceOf( '\Guzzle\Service\Resource\Model', $model );
        $this->assertEquals( 200, $model['status'] );
    }   


     
    
}

