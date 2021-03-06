<?php

namespace Loco\Console\Command\Generated;

use Loco\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Auto-generated Loco API console command.
 */
class PingCommand extends Command {
    
    /**
     * Configure loco:ping command
     * @internal
     */
    protected function configure(){
        $this
            ->setName( 'loco:ping' )
            ->setMethod( 'ping' )
            ->setDescription( 'Check the API is up' )
            /* %options% */
        ;
    }
    
}
