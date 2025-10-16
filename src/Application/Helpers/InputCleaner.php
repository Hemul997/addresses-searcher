<?php

namespace App\Application\Helpers;

class InputCleaner
{
    public static function clean(string $searchTerm): string
    {
        $data = trim($searchTerm);
        $data = stripslashes($data);
        return htmlspecialchars($data);
    }
}
