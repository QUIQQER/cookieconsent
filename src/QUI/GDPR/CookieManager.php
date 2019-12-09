<?php

/**
 * @author PCSG (Jan Wennrich)
 */

namespace QUI\GDPR;

use QUI;

/**
 * Class CookieManager
 *
 * @package QUI\GDPR
 */
class CookieManager extends QUI\Utils\Singleton
{
    const CACHE_KEY_COOKIE_PROVIDERS = 'quiqqer/gdpr/cookieManager/cookieProviders';

    const PROVIDER_KEY = 'cookie';

    const SESSION_KEY_ACCEPTED_COOKIES = 'cookies_accepted_by_user';

    /**
     * @var string Stores if the user accepted the essential cookies.
     */
    const SESSION_KEY_ESSENTIAL_COOKIES_ACCEPTED = 'cookies_essential_accepted_by_user';


    /**
     * @var CookieCollection
     */
    protected $registeredCookies;


    protected function __construct()
    {
        $this->registeredCookies = new CookieCollection();
    }

    /**
     * Returns all cookies that the system's packages might set.
     *
     * @return CookieCollection
     */
    public function getAllRegisteredCookies(): CookieCollection
    {
        if ($this->registeredCookies->isNotEmpty()) {
            return $this->registeredCookies;
        }

        $packages = QUI::getPackageManager()->getInstalled();

        try {
            $cookieProviders = QUI\Cache\Manager::get(self::CACHE_KEY_COOKIE_PROVIDERS);
        } catch (QUI\Cache\Exception $Exception) {
            $cookieProviders = [];

            /* @var QUI\Package\Package $Package */
            foreach ($packages as $packageData) {
                try {
                    $Package = QUI::getPackage($packageData['name']);
                } catch (QUI\Exception $Exception) {
                    QUI\System\Log::writeException($Exception);
                    continue;
                }

                // Get all providers of this package
                $packagesCookieProviders = $Package->getProvider(self::PROVIDER_KEY);

                // Check if the specified classes really exist
                foreach ($packagesCookieProviders as $cookieProvider) {
                    if (!class_exists($cookieProvider)) {
                        continue;
                    }
                }

                // Add the packages dashboard providers to all providers
                $cookieProviders = array_merge($cookieProviders, $packagesCookieProviders);
            }

            try {
                // Cache the providers
                QUI\Cache\Manager::set(self::CACHE_KEY_COOKIE_PROVIDERS, $cookieProviders);
            } catch (\Exception $Exception) {
                QUI\System\Log::writeDebugException($Exception);
            }
        }

        // initialize the instances
        foreach ($cookieProviders as $cookieProvider) {
            try {
                /** @var CookieProviderInterface $Provider */
                $Provider = new $cookieProvider();

                // Check if the given provider is really a DashboardProvider
                if (!($Provider instanceof CookieProviderInterface)) {
                    unset($Provider);
                    continue;
                }

                $this->registeredCookies->merge($Provider->getCookies());
            } catch (\Exception $Exception) {
                QUI\System\Log::writeException($Exception);
            }
        }

        $this->registeredCookies->merge($this->getManualCookies(QUI::getRewrite()->getProject()));

        return $this->registeredCookies;
    }

    public function getAllRegisteredCookiesGroupedByCategory()
    {
        $Cookies = $this->getAllRegisteredCookies();

        /** @var CookieCollection[] $result */
        $result = [];

        foreach ($Cookies as $Cookie) {
            /** @var CookieInterface $Cookie */

            $category = $Cookie->getCategory();
            if (isset($result[$category])) {
                $result[$category]->append($Cookie);
            } else {
                $result[$category] = new CookieCollection([$Cookie]);
            }
        }

        return $result;
    }

    /**
     * Returns the cookies of a given category, that are registered in the system.
     * The passed category should be one of the "COOKIE_CATEGORY_"-prefixed constants of CookieInterface.
     *
     * @param $category
     *
     * @return CookieCollection
     */
    public function getRegisteredCookiesForCategory($category): CookieCollection
    {
        $cookies = $this->getAllRegisteredCookies();

        return $cookies->filter(function ($Cookie) use ($category) {
            /** @var $Cookie CookieInterface */
            return $Cookie->getCategory() == $category;
        });
    }


    /**
     * Returns if a given cookie is accepted in the current session.
     *
     * @param CookieInterface $Cookie
     *
     * @return bool
     */
    public static function isCookieAcceptedInSession(CookieInterface $Cookie): bool
    {
        $AcceptedCookies = static::getAcceptedCookiesForSession();

        // If we check for manual cookies, we have to check for the ID since the class names are anonymous
        if ($Cookie instanceof QUI\GDPR\Cookies\ManualCookie) {
            // Filter all cookies which don't have the given cookie's ID
            $FilteredCookies = $AcceptedCookies->filter(function ($AcceptedCookie) use ($Cookie) {
                // If the accepted cookie isn't a manual cookie, it's filtered
                if (!($AcceptedCookie instanceof QUI\GDPR\Cookies\ManualCookie)) {
                    return false;
                }

                /** @var QUI\GDPR\Cookies\ManualCookie $AcceptedCookie */
                return $AcceptedCookie->getId() === $Cookie->getId();
            });

            return $FilteredCookies->length() > 0;
        }

        return $AcceptedCookies->contains($Cookie);
    }


    /**
     * Sets the given cookies as accepted in the current session.
     *
     * @param CookieCollection $CookieCollection
     *
     * @fires onCookiesAccepted
     */
    public static function acceptCookiesForSession(CookieCollection $CookieCollection): void
    {
        $AcceptedCookies = static::getAcceptedCookiesForSession();

        $AcceptedCookies->merge($CookieCollection);

        QUI::getSession()->set(static::SESSION_KEY_ACCEPTED_COOKIES, $AcceptedCookies);

        try {
            QUI::getEvents()->fireEvent('cookiesAccepted', $AcceptedCookies);
        } catch (QUI\Exception $Exception) {
            QUI\System\Log::writeException($Exception);
        }
    }


    /**
     * Returns a collection of cookies that have been accepted in the current session.
     *
     * @return CookieCollection|null
     */
    public static function getAcceptedCookiesForSession(): CookieCollection
    {
        /** @var $acceptedCookies CookieCollection|null */
        $acceptedCookies = QUI::getSession()->get(static::SESSION_KEY_ACCEPTED_COOKIES);

        if (!$acceptedCookies || !($acceptedCookies instanceof CookieCollection)) {
            $acceptedCookies = new CookieCollection();
        }

        return $acceptedCookies;
    }

    /**
     * Returns if all essential cookies are accepted in the current session.
     * Once the check was successful the result is stored in the user's session.
     * Concurrent calls will use the cached value and therefore always return true.
     *
     * You may set the first parameter to true to force a check again.
     *
     * @param bool $force - Do not use the last cached result (only positive results are cached).
     *
     * @return bool
     */
    public static function areEssentialCookiesAccepted(bool $force = false): bool
    {
        if (QUI::getSession()->get(static::SESSION_KEY_ESSENTIAL_COOKIES_ACCEPTED) && !$force) {
            return true;
        }

        $EssentialCookies = static::getInstance()->getRegisteredCookiesForCategory(CookieInterface::COOKIE_CATEGORY_ESSENTIAL);
        $AcceptedCookies  = static::getAcceptedCookiesForSession();

        foreach ($EssentialCookies as $Cookie) {
            if (!$AcceptedCookies->contains($Cookie)) {
                return false;
            }
        }

        QUI::getSession()->set(static::SESSION_KEY_ESSENTIAL_COOKIES_ACCEPTED, true);

        return true;
    }

    /**
     * Revokes all of the user's cookie settings.
     *
     * @fires onCookiesRevoked
     */
    public static function revokeCookies()
    {
        QUI::getSession()->del(static::SESSION_KEY_ESSENTIAL_COOKIES_ACCEPTED);
        QUI::getSession()->del(static::SESSION_KEY_ACCEPTED_COOKIES);

        try {
            QUI::getEvents()->fireEvent('cookiesRevoked');
        } catch (QUI\Exception $Exception) {
            QUI\System\Log::writeException($Exception);
        }
    }


    /**
     * Returns the name of the table used to store the given project's manually added cookies.
     *
     * @param QUI\Projects\Project $Project
     *
     * @return string
     */
    public static function getManualCookiesTableName(QUI\Projects\Project $Project)
    {
        return QUI::getDBProjectTableName('cookies', $Project, false);
    }


    /**
     * Edits a manually added cookie with the given data.
     * If no or an unused cookie-id is given, a new cookie with the given data is added.
     *
     * @param                      $data
     * @param QUI\Projects\Project $Project
     *
     * @return \PDOStatement
     * @throws QUI\Database\Exception
     */
    public static function editManualCookie($data, QUI\Projects\Project $Project)
    {
        return QUI::getDataBase()->replace(
            self::getManualCookiesTableName($Project),
            $data
        );
    }


    /**
     * Deletes the manually added cookie with the given ID from the given project.
     *
     * @param                      $id
     * @param QUI\Projects\Project $Project
     *
     * @return \PDOStatement
     * @throws QUI\Database\Exception
     */
    public static function deleteManualCookie($id, QUI\Projects\Project $Project)
    {
        return QUI::getDataBase()->delete(
            self::getManualCookiesTableName($Project),
            ['id' => $id]
        );
    }


    /**
     * Returns a collection of all manual cookies in the given project.
     *
     * @param QUI\Projects\Project $Project
     *
     * @return CookieCollection
     *
     * @throws QUI\Database\Exception
     */
    protected static function getManualCookies(QUI\Projects\Project $Project): CookieCollection
    {
        $cookieTable = self::getManualCookiesTableName($Project);

        $result = QUI::getDataBase()->fetch([
            'from' => $cookieTable
        ]);

        $CookieCollection = new CookieCollection();

        foreach ($result as $cookieData) {
            $CookieCollection->append(new QUI\GDPR\Cookies\ManualCookie(
                $cookieData['id'],
                $cookieData['name'],
                $cookieData['origin'],
                $cookieData['purpose'],
                $cookieData['lifetime'],
                $cookieData['category']
            ));
        }

        return $CookieCollection;
    }
}
