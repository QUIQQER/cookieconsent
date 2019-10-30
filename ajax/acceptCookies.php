<?php

QUI::$Ajax->registerFunction(
    'package_quiqqer_cookieconsent_ajax_acceptCookies',
    function ($categories) {
        $categories = json_decode($categories);

        if (!$categories) {
            $categories = [];
        }

        $cookies = new \QUI\CookieConsent\CookieCollection();

        foreach ($categories as $category) {
            $categoryCookies = \QUI\CookieConsent\CookieManager::getInstance()->getRegisteredCookiesForCategory($category);
            $cookies->merge($categoryCookies);
        }

        \QUI\CookieConsent\CookieManager::acceptCookiesForSession($cookies);

        return true;
    },
    ['categories']
);
