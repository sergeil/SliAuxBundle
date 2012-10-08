<?php

namespace Sli\AuxBundle\Util;

/**
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */
class JavaBeansObjectFieldsManager implements ObjectFieldsManagerInterface
{
    private $classRefls = array();

    private $isRegex = '/^is([A-Z0-9]+.*)$/';

    public function formatGetterName($key)
    {
        if (preg_match($this->isRegex, $key)) { // isBlah, isFoo, is111
            return $key;
        } else { // foo, bar, isotope
            return 'get'.ucfirst($key);
        }
    }

    public function formatSetterName($key)
    {
        if (preg_match($this->isRegex, $key, $matches)) {
            return 'set'.$matches[1];
        } else {
            return 'set'.ucfirst($key);
        }
    }

    /**
     * @param $object
     * @return \ReflectionClass
     */
    private function getReflClass($object)
    {
        $index = get_class($object);
        if (!isset($this->classRefls[$index])) {
            $this->classRefls[$index] = new \ReflectionClass($object);
        }
        return $this->classRefls[$index];
    }

    public function set($object, $key, array $values)
    {
        $methodName = $this->formatSetterName($key);
        $refl = $this->getReflClass($object);

        if ($refl->hasMethod($methodName) && $refl->getMethod($methodName)->isPublic()) {
            $refl->getMethod($methodName)->invokeArgs($object, $values);
            return true;
        }
        return false;
    }

    public function get($object, $key)
    {
        $methodName = $this->formatGetterName($key);
        $refl = $this->getReflClass($object);

        if ($refl->hasMethod($methodName) && $refl->getMethod($methodName)->isPublic()) {
            return $object->{$methodName}();
        }
    }
}
