<?php

/**
 * @author PCSG (Jan Wennrich)
 */

namespace QUI\CookieConsent;

use QUI;

/**
 * Class CookieManager
 *
 * @package QUI\CookieConsent
 */
class CookieManager extends QUI\Utils\Singleton
{
    const CACHE_KEY_COOKIE_PROVIDERS = 'quiqqer/cookieconsent/cookieManager/cookieProviders';

    const PROVIDER_KEY = 'cookie';


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

        return $this->registeredCookies;
    }
}
