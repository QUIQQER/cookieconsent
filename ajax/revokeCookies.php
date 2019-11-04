<?php

QUI::$Ajax->registerFunction(
    'package_quiqqer_cookieconsent_ajax_revokeCookies',
    function () {
        \QUI\CookieConsent\CookieManager::revokeCookies();
    }
);
