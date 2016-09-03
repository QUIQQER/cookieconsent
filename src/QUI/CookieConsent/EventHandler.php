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

        if ($Project->getConfig('quiqqer.cookieconsent.position') == 'bottom') {
            $position = 'bottom';
        }

        $Template->extendFooter(
            '<script>' .
            'var QUIQQER_CC_POS = "' . $position . '";' .
            'require(["package/quiqqer/cookieconsent/bin/CookieConsent"]);' .
            '</script>'
        );


        // PrivacyPolicy Site
        $list = $Project->getSites(array(
            'where' => array(
                'type' => 'quiqqer/sitetypes:types/privacypolicy'
            ),
            'limit' => 1
        ));

        if (isset($list[0])) {
            try {
                /* @var $PrivacyPolicy QUI\Projects\Site */
                $PrivacyPolicy = $list[0];

                $Template->extendFooter(
                    '<script>var QUIQQER_CC_LINK = "' . $PrivacyPolicy->getUrlRewritten() . '";</script>'
                );
            } catch (QUI\Exception $Exception) {
            }

        }
    }
}
