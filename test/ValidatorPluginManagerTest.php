<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Validator;

use Zend\ServiceManager\Test\CommonPluginManagerTrait;
use Zend\Validator\Barcode;
use Zend\Validator\Exception\RuntimeException;
use Zend\Validator\ValidatorInterface;
use Zend\Validator\ValidatorPluginManager;
use Zend\ServiceManager\ServiceManager;

/**
 * @group      Zend_Validator
 */
class ValidatorPluginManagerTest extends \PHPUnit_Framework_TestCase
{
    use CommonPluginManagerTrait {
        aliasProvider as commonAliasProvider;
    }

    public function setUp()
    {
        $this->validators = new ValidatorPluginManager(new ServiceManager);
    }

    public function testAllowsInjectingTranslator()
    {
        $translator = $this->getMock('ZendTest\Validator\TestAsset\Translator');

        $serviceLocator = new ServiceManager();
        $serviceLocator->setService('MvcTranslator', $translator);

        $validators = new ValidatorPluginManager($serviceLocator);

        $validator = $validators->get('notempty');
        $this->assertEquals($translator, $validator->getTranslator());
    }

    public function testNoTranslatorInjectedWhenTranslatorIsNotPresent()
    {
        $validators = new ValidatorPluginManager(new ServiceManager());

        $validator = $validators->get('notempty');
        $this->assertNull($validator->getTranslator());
    }

    public function testRegisteringInvalidValidatorRaisesException()
    {
        $this->setExpectedException($this->getServiceNotFoundException());
        $this->validators->setService('test', $this);
        $this->validators->get('test');
    }

    public function testLoadingInvalidValidatorRaisesException()
    {
        $this->validators->setInvokableClass('test', get_class($this));
        $this->setExpectedException($this->getServiceNotFoundException());
        $this->validators->get('test');
    }

    public function testInjectedValidatorPluginManager()
    {
        $validator = $this->validators->get('explode');
        $this->assertSame($this->validators, $validator->getValidatorPluginManager());
    }

    /**
     * @dataProvider aliasProvider
     */
    public function testPluginAliasesResolve($alias, $expected, $options)
    {
        // this is weird - these aliases were invokables prior to v2-v3-compat, but aren't valid instances...
        $lcAlias = strtolower($alias);
        if (strstr($lcAlias, 'barcode') && $lcAlias != 'barcode') {
            $this->setExpectedException($this->getServiceNotFoundException());
        }

        $this->assertInstanceOf(
            $expected,
            $this->getPluginManager()->get($alias, $options),
            "Alias '$alias' does not resolve'"
        );
    }

    protected function getPluginManager()
    {
        return new ValidatorPluginManager(new ServiceManager);
    }

    protected function getV2InvalidPluginException()
    {
        return RuntimeException::class;
    }

    protected function getInstanceOf()
    {
        return ValidatorInterface::class;
    }

    public function aliasProvider()
    {
        // add dummy options to all aliases
        $dummyOpts = [
            'min' => 0,
            'max' => 1,
            'className' => self::class,
            'pattern' => '/.*/',
            'table' => 'a',
            'field' => 'b'
        ];
        $aliases = array_map(function ($alias) use ($dummyOpts) {
            $alias[] = $dummyOpts;
            return $alias;
        }, $this->commonAliasProvider());

        $barcodes = [
            'Code25interleaved',
            'Code25',
            'Code39ext',
            'Code39',
            'Code93ext',
            'Code93',
            'Ean12',
            'Ean13',
            'Ean14',
            'Ean18',
            'Ean2',
            'Ean5',
            'Ean8',
            'Gtin12',
            'Gtin13',
            'Gtin14',
            'Identcode',
            'Intelligentmail',
            'Issn',
            'Itf14',
            'Leitcode',
            'Planet',
            'Postnet',
            'Royalmail',
            'Sscc',
            'Upca',
            'Upce',
        ];
        foreach ($barcodes as $barcode) {
            $aliases[] = ['barcode', Barcode::class, ['adapter' => $barcode]];
        }

        return $aliases;
    }
}
