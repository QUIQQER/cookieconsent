<?php

QUI::$Ajax->registerFunction(
    'package_quiqqer_cookieconsent_ajax_acceptCookies',
    function () {
        QUI::getSession()->set('cookies-accepted', true);

        return true;
    }
);
