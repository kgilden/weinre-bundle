<?php

/*
 * This file is part of the KGWeinreBundle package.
 *
 * (c) Kristen Gilden <kristen.gilden@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KG\WeinreBundle\Tests\DependencyInjection;

use KG\WeinreBundle\DependencyInjection\KGWeinreExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Parser;

/**
 * @author Kristen Gilden <kristen.gilden@gmail.com>
 */
class KGWeinreExtensionTest extends \PHPUnit_Framework_TestCase
{
    private $configuration;

    public function testLoadsScriptInjector()
    {
        $this->createEmptyConfiguration();

        $this->assertHasDefinition('kg_weinre.script_injector');
        $this->assertDefinitionHasTag('kg_weinre.script_injector', 'kernel.event_subscriber');
    }

    public function testEmptyConfigLoadSetsHost()
    {
        $this->createEmptyConfiguration();

        $this->assertParameter(null, 'kg_weinre.target_script_url');
    }

    public function testFullConfigSetsTargetScriptUrl()
    {
        $this->createFullConfiguration();

        $this->assertParameter('http://example.com/script.js', 'kg_weinre.target_script_url');
    }

    protected function tearDown()
    {
        unset($this->configuration);
    }

    private function createEmptyConfiguration()
    {
        $this->configuration = new ContainerBuilder();
        $loader = new KGWeinreExtension();
        $config = $this->getEmptyConfig();
        $loader->load(array($config), $this->configuration);
        $this->assertTrue($this->configuration instanceof ContainerBuilder);
    }

    private function createFullConfiguration()
    {
        $this->configuration = new ContainerBuilder();
        $loader = new KGWeinreExtension();
        $config = $this->getFullConfig();
        $loader->load(array($config), $this->configuration);
        $this->assertTrue($this->configuration instanceof ContainerBuilder);
    }

    private function getEmptyConfig()
    {
        return array();
    }

    private function getFullConfig()
    {
        $yaml = <<<EOF
target_script_url: http://example.com/script.js
EOF;

        $parser = new Parser();

        return $parser->parse($yaml);
    }

    /**
     * @param mixed $value
     * @param string $key
     */
    private function assertParameter($value, $key)
    {
        $this->assertEquals($value, $this->configuration->getParameter($key), sprintf('%s parameter is correct', $key));
    }

    /**
     * @param string $id
     */
    private function assertHasDefinition($id)
    {
        $this->assertTrue(($this->configuration->hasDefinition($id) ?: $this->configuration->hasAlias($id)));
    }

    /**
     * @param string $id
     * @param string $tag
     */
    private function assertDefinitionHasTag($id, $tag)
    {
        $this->assertArrayHasKey($id, $this->configuration->findTaggedServiceIds($tag));
    }
}
