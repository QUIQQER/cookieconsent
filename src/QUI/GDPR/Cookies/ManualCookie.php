<?php

/**
 * @author PCSG (Jan Wennrich)
 */

namespace QUI\GDPR\Cookies;

use QUI;
use QUI\GDPR\CookieInterface;

/**
 * Class ManualCookie.
 *
 * Represents Cookies manually added by an administrator.
 *
 * @package QUI\GDPR\Cookies
 */
class ManualCookie implements CookieInterface
{
    protected $id;

    protected $name;

    protected $origin;

    protected $purpose;

    protected $lifetime;

    protected $category;

    /**
     * ManualCookie constructor.
     *
     * @param $id
     * @param $name
     * @param $origin
     * @param $purpose
     * @param $lifetime
     * @param $category
     */
    public function __construct($id, $name, $origin, $purpose, $lifetime, $category)
    {
        $this->id       = $id;
        $this->name     = $name;
        $this->origin   = $origin;
        $this->purpose  = $purpose;
        $this->lifetime = $lifetime;
        $this->category = $category;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getOrigin(): string
    {
        return $this->origin;
    }

    /**
     * @inheritDoc
     */
    public function getPurpose(): string
    {
        return $this->purpose;
    }

    /**
     * @inheritDoc
     */
    public function getLifetime(): string
    {
        return $this->lifetime;
    }

    /**
     * @inheritDoc
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * Returns the manual cookie's ID
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
}
