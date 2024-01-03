<?php

declare(strict_types=1);

function mb_wordwrap(
    string $string,
    int $width = 75,
    string $break = "\n",
    bool $cut_long_words = false
): string {
    $lines = explode($break, $string);
    $result = '';

    foreach ($lines as $originalLine) {
        if (mb_strwidth($originalLine) < $width) {
            $result .= $originalLine . $break;
            continue;
        }

        $words = explode(' ', $originalLine);
        $line = null;
        $lineWidth = 0;

        if ($cut_long_words) {
            foreach ($words as $index => $word) {
                $characters = mb_str_split($word);
                $strings = [];
                $string = '';

                foreach ($characters as $character) {
                    $tmp = $string . $character;

                    if (mb_strwidth($tmp) > $width) {
                        $strings[] = $string;
                        $string = $character;
                    } else {
                        $string = $tmp;
                    }
                }

                if ($string !== '') {
                    $strings[] = $string;
                }

                $words[$index] = implode(' ', $strings);
            }

            $words = explode(' ', implode(' ', $words));
        }

        foreach ($words as $word) {
            $tmp = ($line === null) ? $word : $line . ' ' . $word;

            // Look for zero-width joiner characters (combined emojis)
            preg_match('/\p{Cf}/u', $word, $joinerMatches);

            $wordWidth = count($joinerMatches) > 0 ? 2 : mb_strwidth($word);

            $lineWidth += $wordWidth;

            if ($line !== null) {
                // Space between words
                $lineWidth += 1;
            }

            if ($lineWidth <= $width) {
                $line = $tmp;
            } else {
                if ($line !== '') {
                    $result .= $line . $break;
                }

                $line = $word;
                $lineWidth = $wordWidth;
            }
        }

        if ($line !== '') {
            $result .= $line;
        }

        $line = null;
    }

    return $result;
}
