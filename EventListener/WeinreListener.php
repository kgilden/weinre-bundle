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

        if ($event->getRequest()->isXmlHttpRequest()) {
            return;
        }

        $this->injectScript($event->getResponse());
    }

    private function injectScript(Response $response)
    {
        $posrFunction = function_exists('mb_strripos') ? 'mb_strripos' : 'strripos';
        $substrFunction = function_exists('mb_substr') ? 'mb_substr' : 'substr';

        $content = $response->getContent();
        $pos = $posrFunction($content, '</body>');

        if (false !== $pos) {
            $script = <<<EOT
<script src="http://192.168.1.64:8080/target/target-script-min.js"></script>

EOT;

            $content = $substrFunction($content, 0, $pos).$script.$substrFunction($content, $pos);
            $response->setContent($content);
        }
    }
}
