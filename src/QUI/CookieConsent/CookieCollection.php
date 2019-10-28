<?php

/**
 * @author PCSG (Jan Wennrich)
 */

namespace QUI\CookieConsent;

use QUI\Collection;

/**
 * Class CookieCollection
 *
 * @package QUI\CookieConsent
 */
class CookieCollection extends Collection
{
    protected $allowed = [CookieInterface::class];
}
