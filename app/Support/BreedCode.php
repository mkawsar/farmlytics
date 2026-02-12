<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Maps breed names to short codes for animal ID (e.g. Holstein → HF).
 * Format: CODE-YYYYMM-N (e.g. HF-202602-1).
 */
final class BreedCode
{
    /** @var array<string, string> breed name (lowercase) => code */
    private static array $map = [
        'holstein' => 'HF',
        'holstein friesian' => 'HF',
        'jersey' => 'JY',
        'sahiwal' => 'SW',
        'sahiwal red' => 'SW',
        'red sindhi' => 'RS',
        'sindhi' => 'RS',
        'gir' => 'GR',
        'tharparkar' => 'TP',
        'crossbred' => 'CB',
        'cross breed' => 'CB',
        'local' => 'LC',
        'indigenous' => 'LC',
    ];

    /**
     * Get the 2-letter (or short) code for a breed name.
     * Unknown breeds: first 2 characters of trimmed breed, uppercased (e.g. "Angus" → "AN").
     */
    public static function codeFor(string $breed): string
    {
        $key = strtolower(trim($breed));
        if ($key === '') {
            return 'XX';
        }
        if (isset(self::$map[$key])) {
            return self::$map[$key];
        }
        // Fallback: first 2 alpha chars of trimmed breed, uppercase (e.g. "Angus" → "AN")
        $letters = preg_replace('/[^a-zA-Z]/', '', trim($breed)) ?: 'XX';

        return strtoupper(mb_substr($letters, 0, 2));
    }

    /** All known breed names (lowercase, for internal use). */
    public static function knownBreeds(): array
    {
        return array_keys(self::$map);
    }

    /**
     * Options for breed select/datalist: [ ['value' => 'Holstein', 'code' => 'HF'], ... ].
     * Value is title-cased so form submission matches map (case-insensitive).
     *
     * @return array<int, array{value: string, code: string}>
     */
    public static function optionsForSelect(): array
    {
        $out = [];
        foreach (array_keys(self::$map) as $key) {
            $out[] = [
                'value' => ucwords($key),
                'code' => self::$map[$key],
            ];
        }
        usort($out, fn ($a, $b) => strcasecmp($a['value'], $b['value']));

        return $out;
    }
}
