<?php

QUI::$Ajax->registerFunction(
    'package_quiqqer_gdpr_ajax_acceptCookies',
    function ($categories) {
        $categories = json_decode($categories);

        if (!$categories) {
            $categories = [];
        }

        $cookies = new \QUI\GDPR\CookieCollection();

        foreach ($categories as $category) {
            $categoryCookies = \QUI\GDPR\CookieManager::getInstance()->getRegisteredCookiesForCategory($category);
            $cookies->merge($categoryCookies);
        }

        \QUI\GDPR\CookieManager::acceptCookiesForSession($cookies);

        return true;
    },
    ['categories']
);
