<?php

/**
 * @author PCSG (Jan Wennrich)
 */

namespace QUI\GDPR;

use QUI\GDPR\Cookies\QuiqqerSessionCookie;

/**
 * Class QuiqqerCookieProvider
 *
 * @package QUI\GDPR
 */
class QuiqqerCookieProvider implements CookieProviderInterface
{
    public static function getCookies(): CookieCollection
    {
        return new CookieCollection([new QuiqqerSessionCookie()]);
    }
}
