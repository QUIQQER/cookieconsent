<?php
/**
 *
 * @return array
 */
QUI::$Ajax->registerFunction(
    'package_quiqqer_gdpr_ajax_deleteCookie',
    function ($id, $projectName) {
        $Project = QUI::getProject($projectName);

        \QUI\GDPR\CookieManager::deleteManualCookie($id, $Project);
    },
    ['id', 'projectName'],
    'Permission::checkAdminUser'
);
