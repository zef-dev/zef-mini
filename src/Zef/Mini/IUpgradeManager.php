<?php declare(strict_types=1);

namespace Zef\Mini;

interface IUpgradeManager
{
    public function upgrade( $version);
    
    /**
     * @return bool 
     */
    public function isUpgradeRequired();
    
}