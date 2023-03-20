<?php declare(strict_types=1);

namespace Zef\Mini;

use Psr\Http\Message\ServerRequestInterface;

class SimpleAuthHeaderService implements IAuthService
{
    /**
     * @var string
     */
    private $_headerName;
    
    /**
     * @var string[]
     */
    private $_allowed = [];
    
    public function __construct( $headerName, $allowed) {
        $this->_headerName = $headerName;
        $this->_allowed = $allowed;
    }
    
    
    public function isAuthenticated( ServerRequestInterface $request)
    {
        $auth_token =   $request->getHeader( $this->_headerName);
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