<?php
/**
 * @author PCSG (Jan Wennrich)
 */

namespace QUI\GDPR;

/**
 * This interface describes the structure of a cookie.
 * The defined methods should be overwritten by a class implementing this interface.
 *
 * @package QUI\GDPR
 */
interface CookieInterface
{
    /**
     * Returns the name of the cookie.
     *
     * @return string
     *
     * @example "session_cookie"
     */
    public function getName(): string;


    /**
     * Returns the origin of the cookie.
     *
     * @return string
     *
     * @example google.com
     */
    public function getOrigin(): string;


    /**
     * Returns the purpose of this cookie.
     * Should be returned in the user's language. Therefore locale variables should be used.
     *
     * @return string
     *
     * @example "Identifies the user and allows authentication to the server"
     */
    public function getPurpose(): string;


    /**
     * Returns the cookie's lifetime.
     * Should be returned in the user's language. Therefore locale variables should be used.
     * This package provides some basic locale variables.
     *
     * @return string
     *
     * @example "1 day"
     * @example "Session"
     */
    public function getLifetime(): string;


    /**
     * Returns the cookie's category.
     * Should be one of the COOKIE_CATEGORY_ prefixed constants.
     *
     * @return string
     *
     * @example 'essential'
     */
    public function getCategory(): string;


    /**
     * These cookies are essential for users to browse the website and use its features
     * Cookies in a web-shop, that store the items in the cart, are an example of strictly necessary cookies.
     * These cookies will generally be first-party session cookies.
     *
     * @type string
     */
    const COOKIE_CATEGORY_ESSENTIAL = 'essential';

    /**
     * These cookies allow a website to remember choices a user has made in the past.
     * Like what language they prefer or what region they would like weather reports for.
     *
     * @type string
     */
    const COOKIE_CATEGORY_PREFERENCES = 'preferences';

    /**
     * These cookies collect information about how users use a website, like which pages they visited.
     * None of this information should be used to identify the user.
     * Their sole purpose is to improve website functions.
     * This includes cookies from third-parties as long as the cookies are for the exclusive use of the website's owner.
     *
     * @type string
     */
    const COOKIE_CATEGORY_STATISTICS = 'statistics';

    /**
     * These cookies track a user's online activity.
     * They help advertisers to deliver more relevant advertising or to limit how many times users see an ad.
     * These cookies can share that information with other organizations or advertisers.
     * These are persistent cookies and almost always of third-party provenance.
     *
     * @type string
     */
    const COOKIE_CATEGORY_MARKETING = 'marketing';
}
