<?php

namespace Palmtree\CanonicalUrlBundle\Tests\DependencyInjection;

use Palmtree\CanonicalUrlBundle\DependencyInjection\Configuration;
use Palmtree\CanonicalUrlBundle\Tests\AbstractTest;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends AbstractTest
{
    /**
     * Some basic tests to make sure the configuration is correctly processed in
     * the standard case.
     */
    public function testProcessSimpleCase()
    {
        $configs = array(
            array(
                'site_url' => 'https://example.org',
                'redirect' => true,
                'redirect_code' => 302,
                'trailing_slash' => true,
            ),
            array(
                'trailing_slash' => false
            )
        );

        $config = $this->process($configs);

        $this->assertArrayHasKey('site_url', $config);
        $this->assertTrue($config['redirect']);
        $this->assertFalse($config['trailing_slash']);
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testInvalidSiteUrl()
    {
        $configs = array(
            array(
                'site_url' => false,
            )
        );

        $config = $this->process($configs);
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testInvalidRedirectCode()
    {
        $configs = array(
            array(
                'redirect_code' => 404,
            )
        );

        $config = $this->process($configs);
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testInvalidRedirect()
    {
        $configs = array(
            array(
                'redirect' => 9,
            )
        );

        $config = $this->process($configs);
    }

    /**
     * Processes an array of configurations and returns a compiled version.
     *
     * @param array $configs An array of raw configurations
     *
     * @return array A normalized array
     */
    protected function process($configs)
    {
        $processor = new Processor();

        return $processor->processConfiguration(new Configuration(), $configs);
    }
}
