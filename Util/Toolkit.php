<?php

namespace Sli\AuxBundle\Util;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpKernel\Kernel;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\Driver;

/**
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */
class Toolkit
{
    static public function isArrayMultiDimensional($input) {
        $arrayKeysAmount = 0;
        foreach ($input as $item) {
            if (is_array($item)) {
                $arrayKeysAmount++;
            }
        }
        return count($input) == $arrayKeysAmount;
    }

    static public function extractIds($col, $method = 'getId')
    {
        if (!$col) {
            return array();
        }

        $ids = array();
        foreach ($col as $item) {
            $ids[] = $item->{$method}();
        }
        return $ids;
    }

    static public function singlifyWord($plural)
    {
        if (strlen($plural) > 4 && substr($plural, -3) == 'ies') {
            return substr($plural, 0, -3);
        } else if (strlen($plural) > 3 && substr($plural, -2) == 'es' && $plural{strlen($plural)-3} == 'e' || $plural{strlen($plural)-2} == 'e') { // employEEs
            return substr($plural, 0, -1);
        } else if (strlen($plural) > 3 && substr($plural, -2) == 'es') {
            return substr($plural, 0, -2);
        }
        return substr($plural, 0, -1); // just 's'
    }

    static public function plurifyWord($input)
    {
        $lastChar = $input{strlen($input)-1};
        if ('y' == $lastChar) {
            return substr($input, 0, strlen($input)-1).'ies';
        } else if (in_array($lastChar, array('x', 's'))) {
            return $input.'es';
        }

        return $input.'s';
    }

    static public function getReflectionSomething($className, $methodName)
    {
        $result = array();

        $reflClass = new \ReflectionClass($className);
        foreach ($reflClass->$methodName() as $reflSomething) {
            $result[$reflSomething->getName()] = $reflSomething;
        }
        if ($reflClass->getParentClass()) {
            $result = array_merge(
                $result,
                self::getReflectionSomething($reflClass->getParentClass()->getName(), $methodName)
            );
        }
        return $result;
    }

    static public function getReflectionProperties($className)
    {
        return self::getReflectionSomething($className, 'getProperties');
    }

    /**
     * @param string $className
     * @param string $propertyName
     * @return \ReflectionProperty
     * @throws \RuntimeException  When provided $propertyName is not found in the provided class nor in its parent classes
     */
    static public function getReflectionProperty($className, $propertyName)
    {
        $properties = self::getReflectionProperties($className);
        if (!isset($properties[$propertyName])) {
            throw new \RuntimeException("Unable to find a property '$propertyName' in '$className'.");
        }

        return $properties[$propertyName];
    }

    static public function getReflectionMethods($className)
    {
        return self::getReflectionSomething($className, 'getMethods');
    }

    /**
     * Returns TRUE if provided class has declared $methodName instead of its parent ( if any ).
     *
     * @param string $fqcn
     * @param string $methodName
     */
    static public function classHasMethodDeclared($fqcn, $methodName)
    {
        $reflClass = new \ReflectionClass($fqcn);
        $reflMethod = $reflClass->getMethod($methodName);

        return $reflMethod->getDeclaringClass()->getName() == $fqcn;
    }

    static public function removeValueFromArray($value, $array)
    {
        $array = array_flip($array);
        if (isset($array[$value])) {
            unset($array[$value]);
        }
        return array_flip($array);
    }

    static public function removeValuesFromArray($values, $array)
    {
        foreach ($values as $value) {
            $array = self::removeValueFromArray($value, $array);
        }
        return $array;
    }

    /**
     * @param \Symfony\Component\Form\Form $form
     * @return array
     */
    static public function getNormalizedFormErrors(Form $form)
    {
        $readableErrors = array();
        foreach ($form->getChildren() as $fieldName => $subForm) {
            /* @var \Symfony\Component\Form\Form $subForm */

            foreach ($subForm->getErrors() as $error) {
                /* @var \Symfony\Component\Form\FormError $error */
                if (!isset($readableErrors[$fieldName])) {
                    $readableErrors[$fieldName] = array();
                }
                $readableErrors[$fieldName][] = $error->getMessageTemplate();
            }
        }
        return $readableErrors;
    }

    static public function getDenormalizedFormErrors(Form $form)
    {
        $readableErrors = array();
        foreach ($form->getErrors() as $error) {
            /* @var \Symfony\Component\Form\FormError $error */
            $readableErrors[] = $error->getMessageTemplate();
        }
        foreach ($form->getChildren() as $subForm) {
            /* @var \Symfony\Component\Form\Form $subForm */
            foreach ($subForm->getErrors() as $error) {
                /* @var \Symfony\Component\Form\FormError $error */
                $readableErrors[] = $error->getMessageTemplate();
            }
        }
        return $readableErrors;
    }

    /**
     * Will try to find a kernel-bundle where the provided class might reside.
     *
     * @param string $class
     * @param \Symfony\Component\HttpKernel\Kernel $kernel
     * @return null|\Symfony\Component\HttpKernel\Bundle\Bundle
     */
    static public function getClassBundle($class, Kernel $kernel)
    {
        $refBundle = null;
        foreach ($kernel->getBundles() as $bundle) {
            /* @var \Symfony\Component\HttpKernel\Bundle\Bundle $bundle */
            if (false !== strpos($class, $bundle->getNamespace())) {
                $refBundle = $bundle;
            }
        }
        return $refBundle;
    }

    static public function copyProperties($from, $to, array $exluded = array())
    {
        $mgm = new JavaBeansObjectFieldsManager();

        // we are interested only in general fields
        foreach (self::getReflectionProperties(get_class($from)) as $reflProp) {
            if (in_array($reflProp->getName(), $exluded)) {
                continue;
            }

            /* @var \ReflectionProperty $reflProp */
            $mgm->set($to, $reflProp->getName(), array($mgm->get($from, $reflProp->getName())));
        }
    }

    static public function setPropertyValue($obj, $propertyName, $propertyValue)
    {
        $reflProp = self::getReflectionProperty(get_class($obj), $propertyName);
        $reflProp->setAccessible(true);
        $reflProp->setValue($obj, $propertyValue);
    }

    static public function getPropertyValue($obj, $propertyName)
    {
        $reflProp = self::getReflectionProperty(get_class($obj), $propertyName);
        $reflProp->setAccessible(true);
        return $reflProp->getValue($obj);
    }

    static public function getIndexedReflectionMethods($className)
    {
        $methods = array();
        foreach (self::getReflectionMethods($className) as $reflMethod) {
            $methods[$reflMethod->getName()] = $reflMethod;
        }
        return $methods;
    }

    static public function getIndexedReflectionProperties($className)
    {
        $properties = array();
        foreach (self::getReflectionProperties($className) as $reflProperty) {
            $properties[$reflProperty->getName()] = $reflProperty;
        }
        return $properties;
    }

    /**
     * @param string $sentence
     * @return array
     */
    static public function createVariableName($sentence)
    {
        $stripped = preg_replace('/[^a-zA-Z0-9_\s]+/', '', $sentence);
        $stripped = preg_replace('/\s{1,}/', ' ', $stripped); // getting rid of duplicate spaces
        $stripped = trim($stripped);

        $exploded = explode(' ', $stripped);
        $compiled = lcfirst($exploded[0]);
        for ($i=1; $i<count($exploded); $i++) {
            $compiled .= ucfirst($exploded[$i]);
        }

        return $compiled;
    }

    static public function addMetadataDriverForEntityManager(EntityManager $em, Driver $driverToInject, $namespace)
    {
        $metadataFactory = $em->getMetadataFactory();
        $reflMetadataFactory = new \ReflectionClass($metadataFactory);

        $reflInitMethod = $reflMetadataFactory->getMethod('initialize');
        $reflInitMethod->setAccessible(true);
        $reflInitMethod->invoke($metadataFactory);

        $reflDriverProp = $reflMetadataFactory->getProperty('driver');
        $reflDriverProp->setAccessible(true);

        /* @var \Doctrine\ORM\Mapping\Driver\DriverChain $driver */
        $driver = $reflDriverProp->getValue($metadataFactory);

        $driver->addDriver($driverToInject, $namespace);
    }
}
