<?php declare(strict_types=1);

namespace Zef\Mini;


use Psr\Http\Message\ServerRequestInterface;

interface IAuthAwareHandler
{	

    public function allowNotAuthenticated( ServerRequestInterface $request);
}