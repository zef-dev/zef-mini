<?php declare(strict_types=1);

namespace Zef\Mini;

interface IMarshaller
{
    public function marshal( $row);
    
    public function marshalAll( $data);
    
}