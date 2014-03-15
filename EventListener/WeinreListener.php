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
    private $scheme;

    /**
     * @var string|null
     */
    private $host;

    /**
     * @var string
     */
    private $port;

    /**
     * @var string
     */
    private $path;

    /**
     * @param string      $scheme
     * @param string|null $host
     * @param string      $port
     * @param string      $path
     */
    public function __construct($scheme, $host = null, $port, $path)
    {
        $this->scheme = $scheme;
        $this->host = $host;
        $this->port = $port;
        $this->path = $path;
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

        $this->injectScript($event->getResponse(), $this->getTargetScriptUrl($request));
    }

    /**
     * Injects the script tag at the end of response content body.
     *
     * @param Response $response
     * @param string   $targetScriptUrl
     */
    private function injectScript(Response $response, $targetScriptUrl)
    {
        $posrFunction = function_exists('mb_strripos') ? 'mb_strripos' : 'strripos';
        $substrFunction = function_exists('mb_substr') ? 'mb_substr' : 'substr';

        $content = $response->getContent();
        $pos = $posrFunction($content, '</body>');

        if (false !== $pos) {
            $script = "<script src=\"$targetScriptUrl\"></script>";

            $content = $substrFunction($content, 0, $pos).$script.$substrFunction($content, $pos);
            $response->setContent($content);
        }
    }

    /**
     * Returns the target script url. Uses the server ip, if the host is unset.
     *
     * @param Request $request
     *
     * @return string
     */
    private function getTargetScriptUrl(Request $request)
    {
        return sprintf(
            '%s://%s:%s/%s',
            $this->scheme,
            $this->host ?: $request->server->get('SERVER_ADDR'),
            $this->port,
            ltrim($this->path, '/')
        );
    }
}
