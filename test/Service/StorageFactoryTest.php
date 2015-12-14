<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Session\Service;

use Zend\ServiceManager\ServiceManager;

/**
 * @group      Zend_Session
 * @covers Zend\Session\Service\StorageFactory
 */
class StorageFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $config = [
            'factories' => [
                'Zend\Session\Storage\StorageInterface' => 'Zend\Session\Service\StorageFactory',
            ],
        ];
        $this->services = new ServiceManager($config);
    }

    public function sessionStorageConfig()
    {
        return [
            'array-storage-short' => [[
                'session_storage' => [
                    'type' => 'ArrayStorage',
                    'options' => [
                        'input' => [
                            'foo' => 'bar',
                        ],
                    ],
                ],
            ], 'Zend\Session\Storage\ArrayStorage'],
            'array-storage-fqcn' => [[
                'session_storage' => [
                    'type' => 'Zend\Session\Storage\ArrayStorage',
                    'options' => [
                        'input' => [
                            'foo' => 'bar',
                        ],
                    ],
                ],
            ], 'Zend\Session\Storage\ArrayStorage'],
            'session-array-storage-short' => [[
                'session_storage' => [
                    'type' => 'SessionArrayStorage',
                    'options' => [
                        'input' => [
                            'foo' => 'bar',
                        ],
                    ],
                ],
            ], 'Zend\Session\Storage\SessionArrayStorage'],
            'session-array-storage-fqcn' => [[
                'session_storage' => [
                    'type' => 'Zend\Session\Storage\SessionArrayStorage',
                    'options' => [
                        'input' => [
                            'foo' => 'bar',
                        ],
                    ],
                ],
            ], 'Zend\Session\Storage\SessionArrayStorage'],
        ];
    }

    /**
     * @dataProvider sessionStorageConfig
     */
    public function testUsesConfigurationToCreateStorage($config, $class)
    {
        $services = $this->services->withConfig([
            'services' => [
                'config' => $config
            ]
        ]);
        $storage = $services->get('Zend\Session\Storage\StorageInterface');
        $this->assertInstanceOf($class, $storage);
        $test = $storage->toArray();
        $this->assertEquals($config['session_storage']['options']['input'], $test);
    }

    public function invalidSessionStorageConfig()
    {
        return [
            'unknown-class-short' => [[
                'session_storage' => [
                    'type' => 'FooStorage',
                    'options' => [],
                ],
            ]],
            'unknown-class-fqcn' => [[
                'session_storage' => [
                    'type' => 'Foo\Bar\Baz\Bat',
                    'options' => [],
                ],
            ]],
            'bad-class' => [[
                'session_storage' => [
                    'type' => 'Zend\Session\Config\StandardConfig',
                    'options' => [],
                ],
            ]],
            'good-class-invalid-options' => [[
                'session_storage' => [
                    'type' => 'ArrayStorage',
                    'options' => [
                        'input' => 'this is invalid',
                    ],
                ],
            ]],
        ];
    }

    /**
     * @dataProvider invalidSessionStorageConfig
     */
    public function testInvalidConfigurationRaisesServiceNotCreatedException($config)
    {
        $services = $this->services->withConfig([
            'services' => [
                'config' => $config
            ]
        ]);
        $this->setExpectedException('Zend\ServiceManager\Exception\ServiceNotCreatedException');
        $storage = $services->get('Zend\Session\Storage\StorageInterface');
    }
}
