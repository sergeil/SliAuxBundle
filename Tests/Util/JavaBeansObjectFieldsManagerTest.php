<?php

namespace Sli\AuxBundle\Tests\Util;

use Sli\AuxBundle\Util\JavaBeansObjectFieldsManager;

class MockTargetClz
{
    private $foo;

    private $isBar;

    private $isBaz;

    private $is1;

    public function setFoo($foo)
    {
        $this->foo = $foo;
    }

    public function getFoo()
    {
        return $this->foo;
    }

    public function setBar($isBar)
    {
        $this->isBar = $isBar;
    }

    public function isBar()
    {
        return $this->isBar;
    }

    private function setBaz($isBaz)
    {
        $this->isBaz = $isBaz;
    }

    public function isBaz()
    {
        return $this->isBaz;
    }

    public function set1($is1)
    {
        $this->is1 = $is1;
    }

    public function is1()
    {
        return $this->is1;
    }
}

/**
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */
class JavaBeansObjectFieldsManagerTest extends \PHPUnit\Framework\TestCase
{
    /* @var \Transportir\CoreBundle\Util\JavaBeansObjectFieldsManager*/
    private $mgm;

    private $refl;

    public function setUp()
    {
        $this->mgm = new JavaBeansObjectFieldsManager();
        $this->refl = new \ReflectionClass('Sli\AuxBundle\Tests\Util\MockTargetClz');
    }

    public function testSet()
    {
        $obj = new MockTargetClz();
        $this->mgm->set($obj, 'foo', array('fooVal'));
        $this->mgm->set($obj, 'isBar', array('isBarVal'));
        $this->mgm->set($obj, 'isBaz', array('izBazVal'));
        $this->mgm->set($obj, 'is1', array('is1val'));

        $this->assertEquals('fooVal', $obj->getFoo());
        $this->assertEquals('isBarVal', $obj->isBar());
        $this->assertNull($obj->isBaz());
        $this->assertEquals('is1val', $obj->is1());
    }

    public function testGet()
    {
        $obj = new MockTargetClz();
        $obj->setFoo('fooVal');
        $obj->setBar('isBarVal');
        $obj->set1('is1Val');

        $this->assertEquals('fooVal', $this->mgm->get($obj, 'foo'));
        $this->assertEquals('isBarVal', $this->mgm->get($obj, 'isBar'));
        $this->assertEquals('is1Val', $this->mgm->get($obj, 'is1'));
    }
}
