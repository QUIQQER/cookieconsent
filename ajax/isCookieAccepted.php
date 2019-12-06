<?php

QUI::$Ajax->registerFunction(
    'package_quiqqer_gdpr_ajax_isCookieAccepted',
    function ($cookieName) {
        if (!class_exists($cookieName)) {
            return false;
        }

        $ReflectionClass = new ReflectionClass($cookieName);

        if (!$ReflectionClass->implementsInterface(\QUI\GDPR\CookieInterface::class)) {
            return false;
        }

        /** @var \QUI\GDPR\CookieInterface $Cookie */
        $Cookie = $ReflectionClass->newInstance();


        return \QUI\GDPR\CookieManager::isCookieAcceptedInSession($Cookie);
    },
    ['cookieName']
);