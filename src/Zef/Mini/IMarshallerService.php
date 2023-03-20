<?php declare(strict_types=1);

namespace Zef\Mini;

interface IMarshallerService
{
    
    /**
     * @param string $name
     * @param IMarshaller $marshaller
     */
    public function registerMarshaller( $name, $marshaller);
    
    /**
     * @param string $name
     * @return IMarshaller
     * @throws NotFoundException
     */
    public function getMarshaller( $name);
    
}