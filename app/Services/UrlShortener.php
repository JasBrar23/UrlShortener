<?php

namespace App\Services;

use App\Models\ShortUrl;
use Str;

/**
 * Service for handling URL shortening and retrieval of original URLs.
 *
 * This service provides methods to encode and decode URLs, either storing the urls
 * in a database or temporarily in memory using PHP sessions.
 */
class UrlShortener
{
    /**
     * Encodes a given URL into a shortened URL and stores it in the database.
     *
     * @param string $url The original URL to be shortened.
     * @return string The shortened URL.
     */
    public function encode(string $url): string
    {
        // Check if the short URL already exists in system
        $short_url = ShortUrl::where('original_url', $url)->first();
        if ($short_url)
            return $short_url->short_url;

        $shortUrl = url('/') . '/' . Str::random(6);

        // Make sure that short URL is unique
        while (ShortUrl::where('short_url', $shortUrl)->exists()) {
            $shortUrl = url('/') . '/' . Str::random(6);
        }

        $url = ShortUrl::create(['original_url' => $url, 'short_url' => $shortUrl,]);
        return $url->short_url;
    }

    /**
     * Decodes a shortened URL back to its original URL using the database.
     *
     * @param string $shortUrl The shortened URL.
     * @return string|null The original URL, or null if not found.
     */
    public function decode(string $shortUrl): ?string
    {
        return ShortUrl::where('short_url', $shortUrl)->first()?->original_url;
    }

    /**
     * Encodes a given URL into a shortened URL and stores it temporarily in memory.
     *
     * @param string $url The original URL to be shortened.
     * @return string The shortened URL.
     */
    public function encodeMemory(string $url): string
    {
        // Check if the short URL already exists in memory
        $shortUrl = array_search($url, $_SESSION['url_map']);
        if ($shortUrl !== false) {
            return $shortUrl;
        }

        $shortKey = Str::random(6);
        $shortUrl = url('/') . '/' . $shortKey;

        // Make sure that short URL is unique
        while (isset($_SESSION['url_map'][$shortUrl])) {
            $shortKey = Str::random(6);
            $shortUrl = url('/') . '/' . $shortKey;
        }

        $_SESSION['url_map'][$shortUrl] = $url;

        return $shortUrl;
    }

    /**
     * Decodes a shortened URL back to its original URL using memory storage.
     *
     * @param string $shortUrl The shortened URL.
     * @return string|null The original URL, or null if not found.
     */
    public function decodeMemory(string $shortUrl): ?string
    {
        if (!isset($_SESSION['url_map'][$shortUrl])) return null;
        return $_SESSION['url_map'][$shortUrl];
    }
}
