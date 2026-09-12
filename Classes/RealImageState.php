<?php

declare(strict_types=1);

namespace Woit\WitFilereplace;

/**
 * Tracks whether the content element currently being rendered is on the
 * excluded-CType allowlist, so real images bypass both the FAL placeholder
 * swap and the raw-HTML <img> replacement while it renders.
 */
final class RealImageState
{
    private static int $depth = 0;

    public static function push(): void
    {
        self::$depth++;
    }

    public static function pop(): void
    {
        if (self::$depth > 0) {
            self::$depth--;
        }
    }

    public static function isActive(): bool
    {
        return self::$depth > 0;
    }
}
