<?php declare(strict_types=1);

namespace Zef\Mini;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

class ExceptionHandlerMiddleware implements \Psr\Http\Server\MiddlewareInterface
{	
	
	/**
	 * @var LoggerInterface
	 */
	private $_logger;
	
	/**
	 * @var IHttpFactory
	 */
	private $_httpFactory;
	
	public function __construct( LoggerInterface $logger, IHttpFactory $httpFactory)
	{
		$this->_logger		=	$logger;
		$this->_httpFactory	=	$httpFactory;
	}
	
	public function process( ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		try {
			return $handler->handle( $request);
		} catch ( NotFoundException $e) {
		    $this->_logger->warning( $e);
		    header("HTTP/1.0 404 Not Found");
		    return $this->_httpFactory->buildResponse( [ 'message' => $e->getMessage()], 404, ['Content-Type'=>'application/json']);
		} catch ( BadRequestException $e) {
		    $this->_logger->warning( $e);
		    header("HTTP/1.0 400 Bad request");
		    return $this->_httpFactory->buildResponse( [ 'message' => $e->getMessage()], 400, ['Content-Type'=>'application/json']);
		} catch ( \Exception $e) {
		    $this->_logger->critical( $e);
		    header("HTTP/1.0 500 Error");
		    return $this->_httpFactory->buildResponse( [ 'message' => $e->getMessage()], 500, ['Content-Type'=>'application/json']);
		}
	}
	
	// UTIL
	public function __toString()
	{
		return get_class( $this).'[]';
	}
}