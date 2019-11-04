<?php

/**
 * @author PCSG (Jan Wennrich)
 */

namespace QUI\CookieConsent\Controls;

use QUI\Control;

/**
 * Class CookieConsent
 *
 * @package QUI\CookieConsent\Controls
 */
class RevokeCookiesButton extends Control
{
    public function __construct($attributes = [])
    {
        parent::__construct($attributes);

        $this->setJavaScriptControl('package/quiqqer/cookieconsent/bin/controls/RevokeCookiesButton');
    }
}
