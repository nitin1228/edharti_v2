<?php

namespace App\Services;

use Mews\Captcha\Captcha;

class CustomCaptcha extends Captcha
{
    protected function generateCharacters($length)
    {
        $numbers = ['2','3','4','6','7','8','9'];
        $letters = ['a','b','c','d','e','f','g','h','j','m','n','p','q','r','t','u','x','y','z',
                    'A','B','C','D','E','F','G','H','J','M','N','P','Q','R','T','U','X','Y','Z'];

        $characters = [];

        // Add at least one letter
        $characters[] = $letters[array_rand($letters)];

        // Add at least one number
        $characters[] = $numbers[array_rand($numbers)];

        // Fill the remaining characters randomly
        $pool = array_merge($letters, $numbers);
        while (count($characters) < $length) {
            $characters[] = $pool[array_rand($pool)];
        }

        // Shuffle to avoid fixed positions
        shuffle($characters);

        return implode('', $characters);
    }
}
