<?php

/**
 * @author PCSG (Jan Wennrich)
 */

namespace QUI\CookieConsent\Cookies;

use QUI;
use QUI\CookieConsent\CookieInterface;

/**
 * Class QuiqqerSessionCookie
 *
 * @package QUI\CookieConsent\Cookies
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
        return QUI::getLocale()->get('quiqqer/cookieconsent', 'cookie.quiqqer.purpose');
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
