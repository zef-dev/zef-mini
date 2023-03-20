<?php declare(strict_types=1);

namespace Zef\Mini;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

class CheckAuthMiddleware implements \Psr\Http\Server\MiddlewareInterface
{	

    /**
     * @var LoggerInterface
     */
    private $_logger;
    
    /**
     * @var IHttpFactory
     */
    private $_httpFactory;
    
    /**
     * @var IAuthService
     */
    private $_authService;
    
    public function __construct( $logger, $httpFactory, $authService)
    {
        $this->_logger          =   $logger;
        $this->_httpFactory     =   $httpFactory;
        $this->_authService     =   $authService;
	}
    
    public function process( ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $allowed        =   $this->_authService->isAuthenticated( $request);
        
        if ( !$allowed) {
            if ( $handler instanceof IAuthAwareHandler) {
                /* @var IAuthAwareHandler $handler */
                if ( $handler->allowNotAuthenticated( $request)) {
                    $this->_logger->info( 'Not authenticated, but handler allows anonymous access.');
                    $allowed  =   true;
                }
            }
        }
        
        if ( $allowed) {
            return $handler->handle( $request); 
        }
        
        $this->_logger->info( 'Blocking request');
        
        return $this->_httpFactory->buildResponse( ['message' => 'Unauthorized'], 401);
    }
	
	// UTIL
	public function __toString()
	{
		return get_class( $this).'[]';
	}
}