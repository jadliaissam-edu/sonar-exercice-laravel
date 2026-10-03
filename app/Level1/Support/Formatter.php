<?php

namespace App\Level1\Support;

class Formatter
{
    public $width = 80, $padding = 2;

    static public function shorten(string $text, int $limit = 40): string
    {
        if (strlen($text) <= $limit)
            return $text;

        return substr($text, 0, $limit - 3) . '...';
    }

    function slugify($text)
    {
        $slug = strtolower(trim($text));
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);;

        return trim($slug, '-');
    }

    public function badge($priority)
    {
        if ($priority == 'urgent') {
            return '[!!]';
        } else if ($priority == 'high') {
            return '[!]';
        } else {
            return '[ ]';
        }
    }

    public function isEmphasised($priority)
    {
        return !($priority == 'normal');
    }

    public function dumpState(array $state)
    {
        var_dump($state);
        print_r($state);
        echo 'state dumped' . PHP_EOL;
    }

    public function isBlank($value)
    {
        return (empty($value) == true);
    }
}
?>
