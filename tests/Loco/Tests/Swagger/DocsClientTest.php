<?php

namespace Loco\Tests\Swagger;

use Guzzle\Tests\GuzzleTestCase;
use Guzzle\Service\Builder\ServiceBuilder;
use Guzzle\Http\Message\Response;
use Guzzle\Plugin\Mock\MockPlugin;
use Loco\Swagger\DocsClient;

/**
 * Tests DocsClient
 */
class DocsClientTest extends GuzzleTestCase {
    
    /**
     * @var string
     */
    private $resourcesJson;
    
    /**
     * @var string
     */
    private $declarationJson;
    
    /**
     * Set up test with a fake Api consisting of a single /ping method
     */
    public function setUp(){
        // define fake resource listing
        $this->resourcesJson = json_encode( array (
            'apiVersion' => '1.0',
            'apis' => array(
                array (
                    'path' => '/ping',
                ),
            ),
        ) );
        // define fake /test endpoint
        $this->declarationJson = json_encode( array(
            'resourcePath' => '/ping',
            // single api with a single operation
            'apis' => array(
                array (
                    'path' => '/ping',
                    'operations' => array(
                        array(
                            'method' => 'GET',
                            'nickname' => 'ping',
                            'type' => 'Echo',
                        ),
                    ),
                ),
            ),
            // single Echo model that would look like { "pong" : "" }
            'models' => array (
                'Echo' => array (
                    'id' => 'Echo',
                    'properties' => array (
                        'pong' => array (
                            'type' => 'string',
                        ),
                    ),
                ),
            ),
        ) );
    }
    
    
    
    /**
     * @covers Loco\Swagger\DocsClient::factory
     * @returns DocsClient
     */
    public function testFactory(){
        $client = DocsClient::factory();
        $this->assertEquals('https://localise.biz/api/docs', $client->getBaseUrl() );
        return $client;
    }
    
    
    
    /**
     * @group mock
     * @depends testFactory
     * @returns DocsClient
     */
    public function testMockResourceListing( DocsClient $client ){
        $plugin = new MockPlugin();
        $plugin->addResponse( new Response( 200, array(), $this->resourcesJson ) );
        $client->addSubscriber( $plugin );
        $listing = $client->getResources();
        $this->assertInstanceOf('\Loco\Swagger\Response\ResourceListing', $listing );
        $this->assertEquals( '1.0', $listing->getApiVersion() );
        $paths = $listing->getApiPaths();
        $this->assertcount( 1, $paths );
        $this->assertEquals( '/ping', $paths[0] );
    }    



    /**
     * @group mock
     * @depends testFactory
     * @returns DocsClient
     */
    public function testMockApiDeclaration( DocsClient $client ){
        $plugin = new MockPlugin();
        $plugin->addResponse( new Response( 200, array(), $this->declarationJson ) );
        $client->addSubscriber( $plugin );
        $declaration = $client->getDeclaration( array(
            'path' => '/ping',
        ) );
        $this->assertInstanceOf('\Loco\Swagger\Response\ApiDeclaration', $declaration );
        $this->assertEquals( '/ping', $declaration->getResourcePath() );
        // apis
        $apis = $declaration->getApis();
        $this->assertCount( 1, $apis );
        // models
        $models = $declaration->getModels();
        $this->assertCount( 1, $models );
    }    
    
    
        

}
