<?php declare(strict_types=1);

namespace Zef\Mini;


use Psr\Http\Message\ServerRequestInterface;

interface IUpgradeSafeHandler
{	
	public function isUpgradeSafeRequest( ServerRequestInterface $request);


}