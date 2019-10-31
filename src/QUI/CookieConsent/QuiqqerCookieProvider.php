<?php

/**
 * @author PCSG (Jan Wennrich)
 */

namespace QUI\CookieConsent;

use QUI\CookieConsent\Cookies\QuiqqerSessionCookie;

/**
 * Class QuiqqerCookieProvider
 *
 * @package QUI\CookieConsent
 */
class QuiqqerCookieProvider implements CookieProviderInterface
{
    public static function getCookies(): CookieCollection
    {
        return new CookieCollection([new QuiqqerSessionCookie()]);
    }
}
