<?php

QUI::$Ajax->registerFunction(
    'package_quiqqer_cookieconsent_ajax_isCookieAccepted',
    function ($cookieName) {
        if (!class_exists($cookieName)) {
            return false;
        }

        $ReflectionClass = new ReflectionClass($cookieName);

        if (!$ReflectionClass->implementsInterface(\QUI\CookieConsent\CookieInterface::class)) {
            return false;
        }

        /** @var \QUI\CookieConsent\CookieInterface $Cookie */
        $Cookie = $ReflectionClass->newInstance();


        return \QUI\CookieConsent\CookieManager::isCookieAcceptedInSession($Cookie);
    },
    ['cookieName']
);
