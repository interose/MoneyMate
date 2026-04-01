<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CategoryColorExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('cat_styles', [$this, 'catStyles']),
            new TwigFunction('cat_styles_ungrouped', [$this, 'catStylesUngrouped']),
            new TwigFunction('cat_bg', [$this, 'catBg']),
            new TwigFunction('cat_text', [$this, 'catText']),
            new TwigFunction('cat_border', [$this, 'catBorder']),
        ];
    }

    /**
     * Returns a ready-to-use inline style string for a category pill.
     *
     * Usage in Twig:
     *   <span style="{{ cat_styles(category.color) }}" class="px-2 py-0.5 rounded-full text-xs font-medium border">
     *     {{ category.name }}
     *   </span>
     *
     * @param string|null $hex Hex color value, e.g. "#6366f1"
     * @return string
     */
    public function catStyles(?string $hex): string
    {
        $hex = $this->normalizeHex($hex);

        return sprintf(
            'background-color: %s; color: %s;',
            $this->hexWithAlpha($hex, 0.10),  // 10% bg
            $hex                              // full color text
        );
    }

    /**
     * Returns only the background-color style (10% opacity).
     *
     * Usage: style="{{ cat_bg(category.color) }}"
     */
    public function catBg(string $hex, float $opacity = 0.10): string
    {
        return sprintf('background-color: %s;', $this->hexWithAlpha($this->normalizeHex($hex), $opacity));
    }

    /**
     * Returns only the color style (full opacity).
     *
     * Usage: style="{{ cat_text(category.color) }}"
     */
    public function catText(string $hex): string
    {
        return sprintf('color: %s;', $this->normalizeHex($hex));
    }

    /**
     * Returns only the border-color style (30% opacity).
     *
     * Usage: style="{{ cat_border(category.color) }}"
     */
    public function catBorder(string $hex, float $opacity = 0.30): string
    {
        return sprintf('border-color: %s;', $this->hexWithAlpha($this->normalizeHex($hex), $opacity));
    }

    // ── Fallback ─────────────────────────────────────────────────

    /**
     * Neutral slate color used for categories without a group.
     * Change this value to adjust the fallback appearance app-wide.
     */
    private const UNGROUPED_COLOR = '#64748b'; // slate-500

    /**
     * Returns the inline style string for a category that has no group.
     *
     * Usage in Twig:
     *   <span style="{{ cat_styles_ungrouped() }}" class="px-2 py-0.5 rounded-full text-xs font-medium border">
     *     {{ category.name }}
     *   </span>
     */
    public function catStylesUngrouped(): string
    {
        return $this->catStyles(self::UNGROUPED_COLOR);
    }

    // ── Internals ────────────────────────────────────────────────

    /**
     * Ensures the hex value starts with # and is 6 characters long.
     * Expands shorthand (#abc → #aabbcc).
     * Falls back to UNGROUPED_COLOR for null or invalid values.
     */
    private function normalizeHex(?string $hex): string
    {
        if (null === $hex) {
            return self::UNGROUPED_COLOR;
        }

        $hex = ltrim(trim($hex), '#');

        if (3 === strlen($hex)) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        if (6 !== strlen($hex) || !ctype_xdigit($hex)) {
            return self::UNGROUPED_COLOR;
        }

        return '#'.strtolower($hex);
    }

    /**
     * Converts a hex color to rgba() with the given opacity.
     */
    private function hexWithAlpha(string $hex, float $opacity): string
    {
        $hex = ltrim($hex, '#');

        [$r, $g, $b] = [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];

        return sprintf('rgba(%d, %d, %d, %.2f)', $r, $g, $b, $opacity);
    }
}
