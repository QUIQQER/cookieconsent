<?php

/**
 * @author PCSG (Jan Wennrich)
 */

namespace QUI\GDPR\Cookies;

use QUI;
use QUI\GDPR\CookieInterface;

/**
 * Class QuiqqerSessionCookie
 *
 * @package QUI\GDPR\Cookies
 */
class QuiqqerSessionCookie implements CookieInterface
{
    /**
     * @inheritDoc
     */
    public static function getName(): string
    {
        return QUI::conf('session', 'name');
    }

    /**
     * @inheritDoc
     */
    public static function getOrigin(): string
    {
        return QUI::getRequest()->getHost();
    }

    /**
     * @inheritDoc
     */
    public static function getPurpose(): string
    {
        return QUI::getLocale()->get('quiqqer/gdpr', 'cookie.quiqqer.purpose');
    }

    /**
     * @inheritDoc
     */
    public static function getLifetime(): string
    {
        return \sprintf(
            '%d %s',
            QUI::conf('session', 'max_life_time'),
            QUI::getLocale()->get('quiqqer/quiqqer', 'seconds')
        );
    }

    /**
     * @inheritDoc
     */
    public static function getCategory(): string
    {
        return static::COOKIE_CATEGORY_ESSENTIAL;
    }
}
