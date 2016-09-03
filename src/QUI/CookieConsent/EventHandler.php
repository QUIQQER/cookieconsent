<?php

namespace QUI\CookieConsent;

use QUI;

/**
 * Class EventHandler
 *
 * @package QUI\CookieConsent
 */
class EventHandler
{
    /**
     * @param QUI\Template $Template
     * @param QUI\Projects\Site $Site
     */
    public static function onTemplateSiteFetch($Template, $Site)
    {
        $Template->extendFooter(
            '<script>require(["package/quiqqer/cookieconsent/bin/CookieConsent"])</script>'
        );
    }
}
