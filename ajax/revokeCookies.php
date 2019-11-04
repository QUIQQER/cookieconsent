<?php

QUI::$Ajax->registerFunction(
    'package_quiqqer_gdpr_ajax_revokeCookies',
    function () {
        \QUI\GDPR\CookieManager::revokeCookies();
    }
);
