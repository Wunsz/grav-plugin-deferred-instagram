<?php

namespace Grav\Plugin;

use Grav\Common\Plugin;
use RocketTheme\Toolbox\Event\Event;

/**
 * Class DeferredInstagramPlugin
 * @package Grav\Plugin
 */
class DeferredInstagramPlugin extends Plugin {

    private $template_html = 'partials/deferred-instagram.html.twig';


    /**
     * @return array
     *
     * The getSubscribedEvents() gives the core a list of events
     *     that the plugin wants to listen to. The key of each
     *     array section is the event that the plugin listens to
     *     and the value (in the form of an array) contains the
     *     callable (or function) as well as the priority. The
     *     higher the number the higher the priority.
     */
    public static function getSubscribedEvents() {
        return [
            'onPagesInitialized' => ['onPagesInitialized', 0],
            'onPluginsInitialized' => ['onPluginsInitialized', 0],
        ];
    }


    /**
     * Initialize configuration.
     */
    public function onPluginsInitialized()
    {
        $this->enable([
            'onTwigTemplatePaths' => ['onTwigTemplatePaths', 0],
            'onTwigInitialized' => ['onTwigInitialized', 0]
        ]);
    }

    /**
     * Add Twig Extensions.
     */
    public function onTwigInitialized()
    {
        $this->grav['twig']->twig->addFunction(new \Twig_SimpleFunction('deferred_instagram_feed', [$this, 'getFeedPlaceholder']));
    }

    /**
     * Add current directory to twig lookup paths.
     */
    public function onTwigTemplatePaths() {
        $this->grav['twig']->twig_paths[] = __DIR__ . '/templates';
    }

    public function onPagesInitialized() {
        $uri = $this->grav['uri'];

        if (strpos($uri->path(), '/deferred-instagram/fetch') === false) {
            return;
        }

        // Extract get feed from instagram plugin twig extension and call it.
        [$instagram, $getFeed] = $this->grav['twig']->twig->getFunction('instagram_feed')->getCallable();

        echo $instagram->$getFeed();
        exit();
    }

    public function getFeedPlaceholder() {
        $output = $this->grav['twig']->twig()->render($this->template_html, []);

        return $output;
    }
}
