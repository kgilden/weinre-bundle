<?php

/*
 * This file is part of the KGWeinreBundle package.
 *
 * (c) Kristen Gilden <kristen.gilden@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KG\WeinreBundle\Tests\EventListener;

use KG\WeinreBundle\EventListener\WeinreListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @author Kristen Gilden <kristen.gilden@gmail.com>
 */
class WeinreListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testSubscribingMethodsActuallyExist()
    {
        $listener = new WeinreListener();

        $methods = WeinreListener::getSubscribedEvents();

        $this->assertGreaterThan(0, $methods);

        foreach ($methods as $method) {
            if (is_array($method)) {
                $method = $method[0];
            }

            $this->assertTrue(method_exists($listener, $method));
        }
    }

    public function testScriptNotAddedForSubRequests()
    {
        $response = new Response($content = '<html><body></body></html>');

        $event = $this->getMockEvent();
        $this->mockGetResponse($event, $response);
        $this->mockGetRequest($event, $this->getMockRequest());
        $this->mockGetRequestType($event, HttpKernelInterface::SUB_REQUEST);

        $listener = new WeinreListener();
        $listener->onKernelResponse($event);

        $this->assertEquals($content, $response->getContent());
    }

    public function testScriptAddedToMasterRequest()
    {
        $response = new Response($content = "<html><body></body></html>");

        $event = $this->getMockEvent();
        $this->mockGetResponse($event, $response);
        $this->mockGetRequest($event, $this->getMockRequest());
        $this->mockGetRequestType($event, HttpKernelInterface::MASTER_REQUEST);

        $listener = new WeinreListener();
        $listener->onKernelResponse($event);

        $expected = <<<EOT
<html>
<body>
<script src="http://192.168.1.64:8080/target/target-script-min.js"></script>
</body>
</html>
EOT;

        $this->assertEquals(
            preg_replace('/\s+/', '', $expected),
            preg_replace('/\s+/', '', $response->getContent())
        );
    }

    public function testScriptNotAddedToXmlHttpRequests()
    {
        $response = new Response($content = '{id: 23}');
        $request = $this->getMockRequest();

        $request
            ->expects($this->once())
            ->method('isXmlHttpRequest')
            ->will($this->returnValue(true))
        ;

        $event = $this->getMockEvent();
        $this->mockGetResponse($event, $response);
        $this->mockGetRequest($event, $request);
        $this->mockGetRequestType($event, HttpKernelInterface::MASTER_REQUEST);

        $listener = new WeinreListener();
        $listener->onKernelResponse($event);

        $this->assertEquals($content, $response->getContent());
    }

    /**
     * @return \Symfony\Component\HttpKernel\Event\FilterResponseEvent|PHPUnit_Framework_MockObject_MockObject
     */
    private function getMockEvent()
    {
        return $this
            ->getMockBuilder('Symfony\Component\HttpKernel\Event\FilterResponseEvent')
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request|PHPUnit_Framework_MockObject_MockObject
     */
    private function getMockRequest()
    {
        return $this->getMock('Symfony\Component\HttpFoundation\Request');
    }

    /**
     * @param \PHPUnit_Framework_MockObject_MockObject   $mock
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    private function mockGetResponse(\PHPUnit_Framework_MockObject_MockObject $mock, $response)
    {
        $mock
            ->expects($this->any())
            ->method('getResponse')
            ->will($this->returnValue($response))
        ;
    }

    /**
     * @param \PHPUnit_Framework_MockObject_MockObject  $mock
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    private function mockGetRequest(\PHPUnit_Framework_MockObject_MockObject $mock, $request)
    {
        $mock
            ->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request))
        ;
    }

    /**
     * @param \PHPUnit_Framework_MockObject_MockObject $mock
     * @param mixed                                    $requestType
     */
    private function mockGetRequestType(\PHPUnit_Framework_MockObject_MockObject $mock, $requestType)
    {
        $mock
            ->expects($this->once())
            ->method('getRequestType')
            ->will($this->returnValue($requestType))
        ;
    }
}
