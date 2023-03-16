<?php declare(strict_types=1);

namespace Zef\Mini;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

class CheckForUpgradeMiddleware implements \Psr\Http\Server\MiddlewareInterface
{	

    /**
     * @var IUpgradeManager
     */
    private $_upgradeManager;
    
    
    /**
     * @var LoggerInterface
     */
    private $_logger;
    
    /**
     * @var IHttpFactory
     */
    private $_httpFactory;
    
    public function __construct( $logger, $httpFactory, $upgradeManager)
    {
        $this->_logger          =   $logger;
        $this->_httpFactory     =   $httpFactory;
        $this->_upgradeManager  =   $upgradeManager;
	}
    
    public function process( ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        if ( !$this->_upgradeManager->isUpgradeRequired()) {
            return $handler->handle( $request); 
        }
        
        if ( $handler instanceof IUpgradeSafeHandler) {
            /* @var IUpgradeSafeHandler $handler */
            if ( $handler->isUpgradeSafeRequest( $request)) {
                return $handler->handle( $request);
            }
        }
        
        $this->_logger->info( 'Blocking request');
        
        return $this->_httpFactory->buildResponse( ['message' => 'Upgrade required'], 503);
    }
	
	// UTIL
	public function __toString()
	{
		return get_class( $this).'[]';
	}
}