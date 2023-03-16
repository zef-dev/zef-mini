<?php declare(strict_types=1);

namespace Zef\Mini;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Log\LoggerInterface;

class RestApp implements RequestHandlerInterface, IUpgradeSafeHandler
{	
	/**
	 * @var LoggerInterface
	 */
	private $_logger;
	
	/**
	 * @var MiddlewareInterface[]
	 */
	private $_middlewares  =   [];

	/**
	 * @var RequestHandlerInterface
	 */
	private $_defaultHandler;
	
	public function __construct( LoggerInterface $logger, $defaultHandler, $middlewares)
	{
        $this->_logger          =   $logger;
        $this->_defaultHandler  =   $defaultHandler;
        $this->_middlewares     =   $middlewares;
	}
	
	public function isUpgradeSafeRequest( ServerRequestInterface $request)
	{
		
		if ( $this->_defaultHandler instanceof IUpgradeSafeHandler) {
		    $this->_logger->info( 'Handler is util api');
		    /* @var IUpgradeSafeHandler $handler */
		    return $this->_defaultHandler->isUpgradeSafeRequest( $request);
		}
	    
	    return false;
	}
	
	/**
	 * Helper method to dump response.
	 * @param ResponseInterface $response
	 */
	public function writeResponse( ResponseInterface $response) {
	    http_response_code( $response->getStatusCode());
	    foreach ( $response->getHeaders() as $name=>$values) {
	        foreach ( $values as $value) {
	            header( sprintf( '%s: %s', $name, $value), false);
	        }
	    }
	    echo $response->getBody()->getContents();
	}
	
	public function handle( ServerRequestInterface $request): ResponseInterface
	{
	    if ( !empty( $this->_middlewares)) {
	        $next  =   array_shift( $this->_middlewares); 
	        return $next->process( $request, $this);
	    }
	    
	    $this->_logger->info( 'Running actual handler ['.get_class( $this->_defaultHandler).']');
	    
	    return $this->_defaultHandler->handle( $request);
	}
	
	
	// UTIL
	public function __toString()
	{
		return get_class( $this).'[]';
	}


}