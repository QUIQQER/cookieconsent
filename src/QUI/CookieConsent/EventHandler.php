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
     * @param QUI\Template      $Template
     * @param QUI\Projects\Site $Site
     */
    public static function onTemplateSiteFetch($Template, $Site)
    {
        if (QUI::getSession()->get('cookies-accepted')) {
            return;
        }

        $CookieConstControl = new QUI\CookieConsent\Controls\CookieConsent();
        $Template->extendFooter($CookieConstControl->create());
    }
}
