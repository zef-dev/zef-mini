<?php declare(strict_types=1);

namespace Zef\Mini;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class SimpleAuthHeaderService implements IAuthService
{
    
    /**
     * @var LoggerInterface
     */
    private $_logger;
    
    /**
     * @var string
     */
    private $_headerName;
    
    /**
     * @var string[]
     */
    private $_allowed = [];
    
    public function __construct( $logger, $headerName, $allowed) {
        $this->_logger = $logger;
        $this->_headerName = $headerName;
        $this->_allowed = $allowed;
    }
    
    
    public function isAuthenticated( ServerRequestInterface $request)
    {
        $auth_token =   $request->getHeader( $this->_headerName);
        
        if ( !empty( $auth_token)) {
            $auth_token =   array_shift( $auth_token);
        }
        
        if ( $auth_token && in_array( $auth_token, $this->_allowed)) {
            return true;
        }
        return false;
    }

    // UTIL
    public function __toString()
    {
        return get_class( $this).'['.$this->_headerName.']';
    }
}