<?php

namespace Sli\AuxBundle\Tests\Util;

use Sli\AuxBundle\Util\Toolkit as TK;

class MockA
{
    private $a;

    public function fooMethod()
    {

    }
}

class MockB extends MockA
{
    private $b;

    protected function barMethod()
    {

    }
}

class DummyEntity
{
    public $id;

    public function getId()
    {
        return $this->id;
    }
}

class FooX
{
    public $a;
    public $b;
    public $c;

    public function setA($a)
    {
        $this->a = $a;
    }

    public function getA()
    {
        return $this->a;
    }

    public function setB($b)
    {
        $this->b = $b;
    }

    public function getB()
    {
        return $this->b;
    }

    public function setC($c)
    {
        $this->c = $c;
    }

    public function getC()
    {
        return $this->c;
    }
}

class FooZ
{
    private $a;

    public function setA($a)
    {
        $this->a = $a;
    }

    public function getA()
    {
        return $this->a;
    }
}

/**
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */
class ToolkitTest extends \PHPUnit_Framework_TestCase
{
    public function testIsArrayMultiDimensionalTest()
    {
        $md = array(
            'foo' => array(),
            'bar' => array()
        );
        $this->assertTrue(TK::isArrayMultiDimensional($md));

        $nmd = array(
            'foo',
            'bar' => array()
        );
        $this->assertFalse(TK::isArrayMultiDimensional($nmd));
    }

    public function testGetReflectionProperties()
    {
        $this->assertEquals(
            2,
            count(TK::getReflectionProperties('Sli\AuxBundle\Tests\Util\MockB'))
        );
    }

    public function testGerReflectionMethods()
    {
        $this->assertEquals(
            2,
            count(TK::getReflectionMethods('Sli\AuxBundle\Tests\Util\MockB'))
        );
    }

    public function testRemoveValueFromArray()
    {
        $array = array('foo', 'bar');
        $this->assertSame(array('foo'), TK::removeValueFromArray('bar', $array));
    }

    public function testRemoveValuesFromArray()
    {
        $array = array('foo', 'bar', 'baz');
        $this->assertSame(array(1=>'bar'), TK::removeValuesFromArray(array('foo', 'baz'), $array));
    }

    public function testClassHasMethodDeclared()
    {
        $this->assertTrue(TK::classHasMethodDeclared('Sli\AuxBundle\Tests\Util\MockA', 'fooMethod'));
        $this->assertFalse(TK::classHasMethodDeclared('Sli\AuxBundle\Tests\Util\MockB', 'fooMethod'));
        $this->assertFalse(TK::classHasMethodDeclared('Sli\AuxBundle\Tests\Util\MockB', 'blahMethod'));
    }

    public function testCopyProperties()
    {
        $from = new FooX();
        $from->a = 'av';
        $from->b = 'bv';
        $from->c = 'cv';
        $to = new FooX();

        TK::copyProperties($from, $to, array('c'));
        $this->assertEquals('av', $to->a);
        $this->assertEquals('bv', $to->b);
        $this->assertNull($to->c);
    }

    public function testSetPropertyValue()
    {
        $fz = new FooZ();
        TK::setPropertyValue($fz, 'a', 5);
        $this->assertEquals(5, $fz->getA());
    }

    public function testGetPropertyValue()
    {
        $fz = new FooZ();
        $fz->setA(99);
        $this->assertEquals(99, TK::getPropertyValue($fz, 'a'));
    }

    public function testGetIndexedReflectionMethods()
    {
        $result = TK::getIndexedReflectionMethods('Sli\AuxBundle\Tests\Util\MockB');

        $this->assertEquals(2, count($result));
        $this->assertArrayHasKey('fooMethod', $result);
        $this->assertInstanceOf('ReflectionMethod', $result['fooMethod']);
        $this->assertArrayHasKey('barMethod', $result);
        $this->assertInstanceOf('ReflectionMethod', $result['barMethod']);
    }

    public function testGetIndexedReflectionProperties()
    {
        $result = TK::getIndexedReflectionProperties('Sli\AuxBundle\Tests\Util\MockB');

        $this->assertEquals(2, count($result));
        $this->assertArrayHasKey('b', $result);
        $this->assertInstanceOf('ReflectionProperty', $result['b']);
        $this->assertArrayHasKey('a', $result);
        $this->assertInstanceOf('ReflectionProperty', $result['a']);

        $this->assertEquals('b', key($result));
    }

    public function testCreateVariableName()
    {
        $this->assertEquals('variableName', TK::createVariableName('variable name'));
        $this->assertEquals('variablename', TK::createVariableName('variable$name '));
        $this->assertEquals('someOtherFckingVariable', TK::createVariableName('Some other f*cking variable! '));
    }

    public function testAssertEntitiesAreSame()
    {
        $entity1 = new DummyEntity();
        $entity2 = new DummyEntity();

        $this->assertTrue(Tk::assertEntitiesAreSame($entity1, $entity1));

        $entity3 = new DummyEntity();
        $entity3->id = 5;
        $entity4 = new DummyEntity();
        $entity4->id = 5;

        $this->assertTrue(Tk::assertEntitiesAreSame($entity3, $entity4));
    }

    public function testUnderscorizeCamelCasedString()
    {
        $this->assertEquals('camel_case', Tk::underscorizeCamelCasedString('camelCase'));
        $this->assertEquals('foo_bar_baz', Tk::underscorizeCamelCasedString('fooBar_Baz'));
    }
}
