<?php
namespace Loco\Http\Response;

use Guzzle\Service\Command\ResponseClassInterface;
use Guzzle\Service\Command\OperationCommand;
use Guzzle\Http\Message\Response;

/**
 * Response class for endpoints that return binary zip files.
 */
class ZipResponse extends RawResponse implements ResponseClassInterface {
    
    /**
     * @var \ZipArchive
     */
    private $zip;
    

    /**
     * Create a response model object from a completed command
     * @param OperationCommand Command that serialized the request
     * @return ZipResponse
     */
    public static function fromCommand( OperationCommand $command ) {
        $response = $command->getResponse();
        $me = new self;
        return $me->init( $response );
    }
    
    
    /**
     * Get zip archive instance.
     * @throws \Exception if zip file is invalid
     * @return \ZipArchive
     */
    public function getZip(){
        if( ! $this->zip ){
            $bin = $this->__toString();
            // temporary file required for opening zip
            $tmp = tempnam( sys_get_temp_dir(), 'loco_zip_' );
            register_shutdown_function( 'unlink', $tmp );
            file_put_contents( $tmp, $bin );
            $this->zip = new \ZipArchive;
            $valid = $this->zip->open( $tmp, \ZipArchive::CHECKCONS );
            // fatal server error might still respond 200 (e.g. memory exhaustion) so need to ensure Zip was valid
            if( true !== $valid ){
                $sniff = substr( $bin, 0, 100 );
                trigger_error('Invalid zip data begins: '.$bin, E_USER_WARNING );
                throw new \Exception('Response data was invalid zip archive');
            }
        }
        return $this->zip;
    }   
     

}