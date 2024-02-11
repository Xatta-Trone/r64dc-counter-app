<?php

namespace App\Traits;


trait ExcelHelperTrait
{
    private function getCharacterAt(int $index): string
    {
        $letters = range('A', 'Z');

        if ($index > 26) {
            $reminder = $index % 26;
            $quotient = (int)floor($index / 26);

            return $letters[($quotient - 1) < 0 ? 0 : ($quotient - 1)] . $letters[($reminder - 1) < 0 ? 0 : ($reminder - 1)];
        } else {
            return $letters[$index - 1];
        }
    }
}
