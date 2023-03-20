<?php declare(strict_types=1);

namespace Zef\Mini;


use Psr\Http\Message\ServerRequestInterface;

interface IAuthService
{	

    /**
     * @param ServerRequestInterface $request
     * @return bool
     */
    public function isAuthenticated( ServerRequestInterface $request);
}