<?php

namespace Canvas\Helpers;

use Illuminate\Support\Str;

class URL
{
    /**
     * Check if a given URL is valid.
     *
     * @param string|null $url
     * @return bool
     */
    public static function isValid(?string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) ? true : false;
    }

    /**
     * Trim a given URL.
     *
     * @param string $url
     * @return mixed
     */
    public static function trim(string $url)
    {
        return parse_url($url)['host'];
    }

    /**
     * Generate a Gravatar for a given email.
     *
     * @param string $email
     * @param int $size
     * @param string $default
     * @param string $rating
     * @return string
     */
    public static function gravatar(string $email, int $size = 200, string $default = 'retro', string $rating = 'g'): string
    {
        return sprintf('https://secure.gravatar.com/avatar/%s?s=%s&d=%s&r=%s',
            md5(trim(Str::lower($email))),
            $size,
            $default,
            $rating
        );
    }
}
