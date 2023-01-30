<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Common;

class UrlHelper
{

    /**
     * @param string $string $string
     * @param string $separator
     * @return string
     */
    public static function slug(string $string = '', string $separator = '-'): string
    {
        /*
         * Is home or is empty
         */
        if ('/' === $string || '' === $string) {
            return '';
        }

        $string        = \strtolower(\trim($string));
        $special_cases = ['&' => 'and', "'" => '', '/' => '', '\\' => ''];
        $string        = \str_replace(\array_keys($special_cases), \array_values($special_cases), $string);
        $accents_regex = '~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i';
        $string        = (string)\preg_replace(
            $accents_regex,
            '$1',
            \htmlentities(
                $string,
                \ENT_QUOTES,
                'UTF-8'
            )
        );
        $string        = (string)\preg_replace('/[^a-z0-9]/u', $separator, $string);

        return \trim((string)\preg_replace('/['.$separator.']+/u', $separator, $string), $separator);
    }
}
