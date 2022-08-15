<?php

namespace App;

class Developer {
    public static function dump($variable_to_dump): void
    {
        echo "<hr> <pre>";
        var_dump($variable_to_dump);
        echo "</pre><hr>";
    }
}