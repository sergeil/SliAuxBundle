<?php

namespace Sli\AuxBundle\Util;

/**
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */
interface ObjectFieldsManagerInterface
{
    /**
     * @param Object $object
     * @param string$key
     * @param array  Values a setter method must be invoked with. Each element of the array will correspond
     *               to argument of the method.
     * @return boolean
     */
    public function set($object, $key, array $values);

    public function get($object, $key);
}
