<?php

namespace Sli\AuxBundle\Util;

/**
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */ 
class RequiredRequestParametersNotProvidedException extends \RuntimeException
{
    private $parameters;

    /**
     * @param array $parameters
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    static public function clazz()
    {
        return get_called_class();
    }
}