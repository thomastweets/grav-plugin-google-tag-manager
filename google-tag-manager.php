<?php
namespace Grav\Plugin;

use Grav\Common\Plugin;
use RocketTheme\Toolbox\Event\Event;

/**
 * Class GoogleTagManagerPlugin
 * @package Grav\Plugin
 */
class GoogleTagManagerPlugin extends Plugin
{
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
    public static function getSubscribedEvents()
    {
        return [
            'onAssetsInitialized' => ['onAssetsInitialized', 0]
        ];
    }
    /**
     * Add GTM tracking JS when the assets are initialized
     */
    public function onAssetsInitialized() {
        // Don't proceed if we are in the admin plugin
        if ($this->isAdmin()) return;

        // Don't proceed if there is no GTM Tracking ID
        $trackingId = trim($this->grav['config']->get('plugins.google-tag-manager.trackingId', ''));
        if (empty($trackingId)) return;

        if (!$this->isTheRightDomain()) return;

        // Embed Google Tag Manager script
        $this->grav['assets']->addInlineJs($this->generateTrackingCode($trackingId), null);
    }
    /**
     * Generate GTM Code for embeded.
     * @param string $trackingId Google Tag Manager Tracking ID
     */  
    private function generateTrackingCode($trackingId) {
        $code = <<<EOF
<!-- Google Tag Manager -->
(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','{$trackingId}');
<!-- End Google Tag Manager -->
EOF;
        return $code;
    }
    /**
     * Check if it is the right domain to apply.
     */      
    private function isTheRightDomain() {

        $domains = $this->grav['config']->get('plugins.google-tag-manager.domains', []);

        // if not set, apply all domain.
        if (empty($domains)) return True;

        // if set, only match domain will be applied.
        if (in_array($_SERVER['SERVER_NAME'], $domains)) return True;

        return False;     
    }
}
