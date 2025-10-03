<?php

namespace App\Support\Helpers;

class UuidHelper
{
    /**
     * Extract the first UUID from a string.
     */
    public static function extractOne(string $text): ?string
    {
        if (preg_match(
            '/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}/i',
            $text,
            $matches
        )) {
            return $matches[0]; // first UUID found
        }

        return null; // no UUID found
    }
}
