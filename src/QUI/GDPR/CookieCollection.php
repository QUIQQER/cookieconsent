<?php

/**
 * @author PCSG (Jan Wennrich)
 */

namespace QUI\GDPR;

use QUI\Collection;

/**
 * Class CookieCollection
 *
 * @package QUI\GDPR
 */
class CookieCollection extends Collection
{
    protected $allowed = [CookieInterface::class];
}
