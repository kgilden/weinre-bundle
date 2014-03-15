<?php

/*
 * This file is part of the KGWeinreBundle package.
 *
 * (c) Kristen Gilden <kristen.gilden@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KG\WeinreBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * WeinreListener injects the target script to make the application act as a
 * debug target.
 *
 * The script is only injected on well-formed HTML (with proper </body> tag).
 *
 * @author Kristen Gilden <kristen.gilden@gmail.com>
 */
class WeinreListener implements EventSubscriberInterface
{
    /**
     * @var string
     */
    private $host;

    /**
     * @var string
     */
    private $port;

    /**
     * @param string $host
     * @param string $port
     */
    public function __construct($host = null, $port = null)
    {
        $this->host = $host;
        $this->port = $port;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::RESPONSE => array('onKernelResponse', -128),
        );
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $request = $event->getRequest();

        if ($request->isXmlHttpRequest()) {
            return;
        }

        $this->injectScript($event->getResponse(), $this->guessSchemeAndHost($request));
    }

    /**
     * Injects the script tag at the end of response content body.
     *
     * @param Response $response
     * @param string   $schemeAndHost
     */
    private function injectScript(Response $response, $schemeAndHost)
    {
        $posrFunction = function_exists('mb_strripos') ? 'mb_strripos' : 'strripos';
        $substrFunction = function_exists('mb_substr') ? 'mb_substr' : 'substr';

        $content = $response->getContent();
        $pos = $posrFunction($content, '</body>');

        if (false !== $pos) {
            $script = <<<EOT
<script src="$schemeAndHost/target/target-script-min.js"></script>
EOT;

            $content = $substrFunction($content, 0, $pos).$script.$substrFunction($content, $pos);
            $response->setContent($content);
        }
    }

    /**
     * Guesses the weinre server scheme and host. It either uses the passed
     * host & port values or expects the weinre server to be on the same
     * machine with the port 8080.
     *
     * @param Request $request
     *
     * @return [type]
     */
    private function guessSchemeAndHost(Request $request)
    {
        $schemeAndHost = $this->host ?: $request->server->get('SERVER_ADDR');
        $port = $this->port ?: '8080';

        if ($this->port) {
            $schemeAndHost .= ':'.$this->port;
        }

        return $schemeAndHost;
    }
}
