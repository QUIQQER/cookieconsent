<?php

/**
 * @author PCSG (Jan Wennrich)
 */

namespace QUI\GDPR\Controls;

use QUI\Control;

/**
 * Class CookieConsent
 *
 * @package QUI\GDPR\Controls
 */
class RevokeCookiesButton extends Control
{
    public function __construct($attributes = [])
    {
        parent::__construct($attributes);

        $this->setJavaScriptControl('package/quiqqer/gdpr/bin/controls/RevokeCookiesButton');
    }
}
