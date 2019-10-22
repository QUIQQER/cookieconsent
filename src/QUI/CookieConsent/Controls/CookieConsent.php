<?php

/**
 * @author PCSG (Jan Wennrich)
 */

namespace QUI\CookieConsent\Controls;

use QUI\Control;
use QUI\Projects\Project;

/**
 * Class CookieConsent
 *
 * @package QUI\CookieConsent\Controls
 */
class CookieConsent extends Control
{
    public function __construct($attributes = [])
    {
        $Project = \QUI::getRewrite()->getProject();

        parent::__construct($attributes);

        $this->setJavaScriptControlOption('position', $this::getPosition($Project));
        $this->setJavaScriptControlOption('blocksite', $Project->getConfig('cookieconsent.blocksite'));

        $this->setJavaScriptControl('package/quiqqer/cookieconsent/bin/controls/CookieConsent');

        $this->addCSSFile(dirname(__FILE__) . '/CookieConsent.css');
    }

    public function getBody()
    {
        $TemplateEngine = \QUI::getTemplateManager()->getEngine();

        $TemplateEngine->assign([
            'Control' => $this,
            'Project' => \QUI::getRewrite()->getProject()
        ]);

        return $TemplateEngine->fetch(dirname(__FILE__) . '/CookieConsent.html');
    }


    /**
     * Returns the cookie banner's position for the given project
     *
     * @param Project $Project
     *
     * @return string
     */
    public static function getPosition($Project)
    {
        $position = $Project->getConfig('quiqqer.cookieconsent.position');

        $allowedPositions = ['top', 'bottom', 'topSlide'];

        if (!\in_array($position, $allowedPositions)) {
            $position = 'top';
        }

        return $position;
    }


    /**
     * Returns the selected imprint page's URL for the given project.
     *
     * @param Project $Project
     */
    public static function getImprintUrl($Project)
    {
    }


    /**
     * Returns the selected privacy page's URL for the given project.
     *
     * @param Project $Project
     */
    public static function getPrivacyUrl($Project)
    {
    }
}
