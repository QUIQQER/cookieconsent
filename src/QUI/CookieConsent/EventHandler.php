<?php

namespace QUI\CookieConsent;

use QUI;

/**
 * Class EventHandler
 *
 * @package QUI\CookieConsent
 */
class EventHandler
{
    /**
     * @param QUI\Template $Template
     * @param QUI\Projects\Site $Site
     */
    public static function onTemplateSiteFetch($Template, $Site)
    {
        $Project = $Site->getProject();

        // CookieConsent Data
        $position = 'top';

        switch ($Project->getConfig('quiqqer.cookieconsent.position')) {
            case 'top':
            case 'bottom':
            case 'topSlide':
                $position = $Project->getConfig('quiqqer.cookieconsent.position');
                break;
        }

        $Template->extendFooter(
            '<script>'.
            'var QUIQQER_CC_POS = "'.$position.'";'.
            'require(["package/quiqqer/cookieconsent/bin/CookieConsent"]);'.
            '</script>'
        );


        // PrivacyPolicy Site
        $list = $Project->getSites([
            'where' => [
                'type' => 'quiqqer/sitetypes:types/privacypolicy'
            ],
            'limit' => 1
        ]);

        if (isset($list[0])) {
            try {
                /* @var $PrivacyPolicy QUI\Projects\Site */
                $PrivacyPolicy = $list[0];

                $Template->extendFooter(
                    '<script>var QUIQQER_CC_LINK = "'.$PrivacyPolicy->getUrlRewritten().'";</script>'
                );
            } catch (QUI\Exception $Exception) {
            }
        }
    }

    /**
     * @param QUI\Template $Template
     */
    public static function onTemplateGetHeader($Template)
    {
        try {
            $lastUpdate = QUI::getPackageManager()->getLastUpdateDate();
            $lastUpdate = \md5($lastUpdate);
        } catch (QUI\Exception $Exception) {
            return;
        }
        
        $Template->extendHeader(
            '<link rel="preload" as="style" href="'.URL_OPT_DIR.'quiqqer/cookieconsent/bin/CookieConsent.css?update='.$lastUpdate.'">'
        );
    }
}
