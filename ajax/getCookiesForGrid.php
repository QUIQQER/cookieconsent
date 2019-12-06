<?php
/**
 *
 * @return array
 */
QUI::$Ajax->registerFunction(
    'package_quiqqer_gdpr_ajax_getCookiesForGrid',
    function ($projectName, $page = 1, $perPage = 25, $sortOn = 'name', $sortBy = 'ASC') {
        if (!is_numeric($page) || !is_numeric($perPage)) {
            return;
        }

        if (!in_array($sortOn, ['id', 'name', 'origin', 'purpose', 'lifetime', 'category'])) {
            $sortOn = 'eventid';
        }

        if (!in_array(strtoupper($sortBy), ['ASC, DESC'])) {
            $sortBy = 'ASC';
        }

        $cookieTable = \QUI\GDPR\CookieManager::getManualCookiesTableName(QUI::getProject($projectName));

        $result = QUI::getDataBase()->fetch([
            'from'  => $cookieTable,
            'order' => "$sortOn $sortBy"
        ]);

        return QUI\Utils\Grid::getResult($result, $page, $perPage);

    },
    ['projectName', 'page', 'perPage', 'sortOn', 'sortBy'],
    'Permission::checkAdminUser'
);
