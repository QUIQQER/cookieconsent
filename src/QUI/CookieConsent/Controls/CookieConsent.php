<?php

/**
 * @author PCSG (Jan Wennrich)
 */

namespace QUI\CookieConsent\Controls;

use QUI\Control;
use QUI\CookieConsent\CookieInterface;
use QUI\CookieConsent\CookieManager;
use QUI\Projects\Project;
use QUI\Projects\Site;

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
        $this->setJavaScriptControlOption('privacy-url', $this);
        $this->setJavaScriptControlOption('imprint-url', $this::getImprintUrl($Project));
        $this->setJavaScriptControlOption('blocksite', $Project->getConfig('cookieconsent.blocksite'));

        $this->setJavaScriptControl('package/quiqqer/cookieconsent/bin/controls/CookieConsent');

        $this->addCSSFile(dirname(__FILE__) . '/CookieConsent.css');
    }

    public function getBody()
    {
        $TemplateEngine = \QUI::getTemplateManager()->getEngine();

        $TemplateEngine->assign([
            'Control'                => $this,
            'Project'                => \QUI::getRewrite()->getProject(),
            'cookieCategories'       => [
                CookieInterface::COOKIE_CATEGORY_ESSENTIAL,
                CookieInterface::COOKIE_CATEGORY_PREFERENCES,
                CookieInterface::COOKIE_CATEGORY_STATISTICS,
                CookieInterface::COOKIE_CATEGORY_MARKETING
            ],
            'Cookies' => CookieManager::getInstance()->getAllRegisteredCookies(),
            'requiredCookieCategory' => CookieInterface::COOKIE_CATEGORY_ESSENTIAL
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
     * Returns the URL of the first site with the legalnotes sitetype for a given project.
     *
     * @param Project $Project
     *
     * @return false|string
     */
    public static function getImprintUrl($Project)
    {
        return self::getSiteUrlBySitetype($Project, 'quiqqer/sitetypes:types/legalnotes');
    }


    /**
     * Returns the URL of the first site with the privacypolicy sitetype for a given project.
     *
     * @param Project $Project
     *
     * @return false|string
     */
    public static function getPrivacyUrl($Project)
    {
        return self::getSiteUrlBySitetype($Project, 'quiqqer/sitetypes:types/privacypolicy');
    }

    /**
     * Returns the URL of the first site of a given sitetype for a given project.
     *
     * @param Project $Project
     *
     * @return false|string
     */
    protected static function getSiteUrlBySitetype($Project, $type)
    {
        /* @var $result Site[] */
        $result = $Project->getSites([
            'where' => [
                'type' => $type
            ],
            'limit' => 1
        ]);

        if (!isset($result[0])) {
            return false;
        }

        try {
            return $result[0]->getUrlRewritten();
        } catch (\QUI\Exception $Exception) {
            return false;
        }
    }

    /**
     * Returns the cookie-banner text for the given project.
     *
     * @param $Project
     *
     * @return string
     */
    public static function getText($Project)
    {
        $lg     = 'quiqqer/cookieconsent';
        $locale = \QUI::getLocale();

        $text = $locale->get($lg, "setting.text.project.{$Project->getName()}");

        if ($locale->isLocaleString($text) || empty($text)) {
            $text = $locale->get($lg, 'setting.text.default');
        }

        return $text;
    }

    /**
     * Returns the cookie-banner text for the given project.
     *
     * @param $Project
     *
     * @return string
     */
    public static function getButtonText($Project)
    {
        $lg     = 'quiqqer/cookieconsent';
        $locale = \QUI::getLocale();

        $text = $locale->get($lg, "setting.buttontext.project.{$Project->getName()}");

        if ($locale->isLocaleString($text) || empty($text)) {
            $text = $locale->get($lg, 'setting.buttontext.default');
        }

        return $text;
    }
}
