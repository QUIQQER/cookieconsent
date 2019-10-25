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
     * @param QUI\Template      $Template
     * @param QUI\Projects\Site $Site
     */
    public static function onTemplateSiteFetch($Template, $Site)
    {
        if (QUI::getSession()->get('cookies-accepted')) {
            return;
        }

        $CookieConstControl = new QUI\CookieConsent\Controls\CookieConsent();
        $Template->extendFooter($CookieConstControl->create());
    }

    /**
     * Listens to project config save
     *
     * @param string $projectName
     * @param array  $config
     * @param array  $params
     *
     * @throws QUI\Exception
     */
    public static function onProjectConfigSave($projectName, array $config, array $params)
    {
        if (!isset($params['cookieconsent.text'])) {
            return;
        }

        try {
            $Project = QUI::getProject($projectName);
        } catch (QUI\Exception $Exception) {
            return;
        }

        $group = 'quiqqer/cookieconsent';

        $localeVariableName    = 'setting.text.project.' . $Project->getName();
        $localeVariableContent = json_decode($params['cookieconsent.text'], true);

        try {
            QUI\Translator::add($group, $localeVariableName, $group);
        } catch (QUI\Exception $Exception) {
            // Throws error if lang var already exists
        }

        try {
            QUI\Translator::update(
                $group,
                $localeVariableName,
                $group,
                $localeVariableContent
            );
        } catch (QUI\Exception $Exception) {
            QUI\System\Log::writeException($Exception);

            return;
        }

        QUI\Translator::publish($group);
    }
}
