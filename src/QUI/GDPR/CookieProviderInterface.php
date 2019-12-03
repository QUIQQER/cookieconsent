<?php
/**
 * @author PCSG (Jan Wennrich)
 */

namespace QUI\GDPR;

interface CookieProviderInterface
{
    /**
     * Returns a collection of cookies the package provides.
     * The CookieCollection should contain instances of CookieInterface.
     *
     * @return CookieCollection
     */
    public static function getCookies(): CookieCollection;
}
