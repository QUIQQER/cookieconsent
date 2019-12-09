<?php
/**
 *
 * @return array
 */
QUI::$Ajax->registerFunction(
    'package_quiqqer_gdpr_ajax_editCookie',
    function ($data, $projectName) {
        $Project = QUI::getProject($projectName);

        $data = json_decode($data, true);

        if (isset($data['id']) && empty($data['id'])) {
            unset($data['id']);
        }

        \QUI\GDPR\CookieManager::editManualCookie($data, $Project);
    },
    ['data', 'projectName'],
    'Permission::checkAdminUser'
);
