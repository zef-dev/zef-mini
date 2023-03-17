<?php declare(strict_types=1);

namespace Zef\Mini;

class MarshallerService
{
    /**
     * @var IMarshaller[]
     */
    private $_marshallers = [];
    
    /**
     * @param string $name
     * @param IMarshaller $marshaller
     */
    public function registerMarshaller( $name, $marshaller) 
    {
        $this->_marshallers[$name] = $marshaller;
    }
    
    /**
     * @param string $name
     * @return IMarshaller
     * @throws NotFoundException
     */
    public function getMarshaller( $name) 
    {
        if ( !isset( $this->_marshallers[$name])) {
            throw new NotFoundException( 'Marshaller ['.$name.'] not found');
        }
        return $this->_marshallers[$name];
    }
    
}