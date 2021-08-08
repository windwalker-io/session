<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2021 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Session\Cookie;

use DateTimeImmutable;
use DateTimeInterface;

/**
 * The AbstractCookies class.
 */
abstract class AbstractCookies implements CookiesInterface
{
    protected int $expires = 0;

    protected string $path = '';

    protected string $domain = '';

    protected bool $secure = false;

    protected bool $httpOnly = false;

    protected string $sameSite = self::SAMESITE_LAX;

    /**
     * AbstractCookies constructor.
     *
     * @param  array  $options
     */
    public function __construct(array $options = [])
    {
        $this->setOptions($options);
    }

    public function getOptions(): array
    {
        return array_change_key_case(get_object_vars($this), CASE_LOWER);
    }

    public function setOptions(array $options): static
    {
        $this->path = $options['path'] ?? $this->path;
        $this->domain = $options['domain'] ?? $this->domain;
        $this->secure = $options['secure'] ?? $this->secure;
        $this->httpOnly = $options['httponly'] ?? $this->httpOnly;
        $this->sameSite = $options['samesite'] ?? $this->sameSite;

        $this->expires($options['expires'] ?? $this->expires);

        return $this;
    }

    /**
     * @return int
     */
    public function getExpires(): int
    {
        return $this->expires;
    }

    /**
     * @param  DateTimeInterface|int|string  $expires  Timestamp, DateTime or time modify string.
     *
     * @return static Return self to support chaining.
     */
    public function expires(int|string|DateTimeInterface $expires): static
    {
        if (is_string($expires) && !is_numeric($expires)) {
            $date = new DateTimeImmutable('now');
            $expires = $date->modify($expires)->getTimestamp();
        }

        if ($expires instanceof DateTimeInterface) {
            $expires = $expires->getTimestamp();
        }

        $this->expires = (int) $expires;

        return $this;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param  string  $path
     *
     * @return  static  Return self to support chaining.
     */
    public function path(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * @param  string  $domain
     *
     * @return  static  Return self to support chaining.
     */
    public function domain(string $domain): static
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSecure(): bool
    {
        return $this->secure;
    }

    /**
     * @param  bool  $secure
     *
     * @return  static  Return self to support chaining.
     */
    public function secure(bool $secure): static
    {
        $this->secure = $secure;

        return $this;
    }

    /**
     * @return bool
     */
    public function isHttpOnly(): bool
    {
        return $this->httpOnly;
    }

    /**
     * @param  bool  $httpOnly
     *
     * @return  static  Return self to support chaining.
     */
    public function httpOnly(bool $httpOnly): static
    {
        $this->httpOnly = $httpOnly;

        return $this;
    }

    /**
     * @return string
     */
    public function isSameSite(): string
    {
        return $this->sameSite;
    }

    /**
     * @param  string  $sameSite
     *
     * @return  static  Return self to support chaining.
     */
    public function sameSite(string $sameSite): static
    {
        $this->sameSite = $sameSite;

        return $this;
    }

    public function getCookieHeaders(): array
    {
        $headers = [];

        foreach ($this->getStorage() as $k => $item) {
            $header = $k . '=' . $item;

            if ($settings = $this->buildHeaderSettings()) {
                $header .= '; ' . $settings;
            }

            $headers[] = $header;
        }

        return $headers;
    }

    protected function buildHeaderSettings(): string
    {
        $settings = [];

        if ($this->domain) {
            $settings[] = 'domain=' . $this->domain;
        }

        if ($this->path) {
            $settings[] = 'path=' . $this->path;
        }

        if ($this->expires) {
            $settings[] = 'expires=' . $this->expires;
        }

        if ($this->secure) {
            $settings[] = 'secure';
        }

        if ($this->sameSite) {
            $settings[] = 'SameSite=' . $this->sameSite;
        }

        if ($this->httpOnly) {
            $settings[] = 'HttpOnly';
        }

        return implode('; ', $settings);
    }
}
