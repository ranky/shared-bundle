<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Common;

class TextHelper
{
    public static function truncate(string $value, int $length = 30, string $separator = '...'): string
    {
        $value = \strip_tags(html_entity_decode($value));
        // Remove shortcode, format [shortcode] or [shortcode]shortcodes[/shortcode]
        $value = (string)\preg_replace('/\[(.*?)\](.*?(\[\/\1\]))?/', '', $value);
        // remove whitespace
        $value = (string)preg_replace('/\s+/', ' ', $value);

        return \mb_strimwidth($value, 0, $length, $separator, 'utf-8');
    }

    /**
     * @param string $value
     * @param int $length
     * @param string $separator
     * @param bool $exact
     * @return string
     * https://gist.github.com/getmanzooronline/61e341cb8de3d98ec12b
     */
    public static function truncateWithHTML(
        string $value,
        int $length = 30,
        string $separator = '...',
        bool $exact = true
    ): string {
        // if the plain text is shorter than the maximum length, return the whole text
        if (\strlen(\preg_replace('/<.*?>/', '', $value) ?? '') <= $length) {
            return $value;
        }

        // splits all html-tags to scanable lines
        \preg_match_all('/(<.+?>)?([^<>]*)/s', $value, $lines, PREG_SET_ORDER);

        $total_length = \strlen($separator);
        $open_tags    = [];
        $truncate     = '';

        foreach ($lines as $lineMatching) {
            // if there is any html-tag in this line, handle it and add it (uncounted) to the output
            if (!empty($lineMatching[1])) {
                // if it’s an “empty element” with or without xhtml-conform closing slash (f.e.)
                if (\preg_match(
                    '/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is',
                    $lineMatching[1]
                )) {
                    // do nothing
                    // if tag is a closing tag (f.e.)
                } elseif (\preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $lineMatching[1], $tagMatching)) {
                    // delete tag from $open_tags list
                    $pos = \array_search($tagMatching[1], $open_tags, true);
                    if ($pos !== false) {
                        unset($open_tags[$pos]);
                    }
                    // if tag is an opening tag (f.e. )
                } elseif (\preg_match('/^<\s*([^\s>!]+).*?>$/s', $lineMatching[1], $tagMatching)) {
                    // add tag to the beginning of $open_tags list
                    \array_unshift($open_tags, \strtolower($tagMatching[1]));
                }
                // add html-tag to $truncate’d text
                $truncate .= $lineMatching[1];
            }

            // calculate the length of the plain text part of the line; handle entities as one character
            $content_length = \strlen(
                \preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $lineMatching[2]) ?? ''
            );
            if ($total_length + $content_length > $length) {
                // the number of characters which are left
                $left            = $length - $total_length;
                $entities_length = 0;
                // search for html entities
                if (\preg_match_all(
                    '/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i',
                    $lineMatching[2],
                    $entities,
                    PREG_OFFSET_CAPTURE
                )) {
                    // calculate the real length of all entities in the legal range
                    foreach ($entities[0] as $entity) {
                        if ($entity[1] + 1 - $entities_length <= $left) {
                            $left--;
                            $entities_length += \strlen($entity[0]);
                        } else {
                            // no more characters left
                            break;
                        }
                    }
                }
                $truncate .= \substr($lineMatching[2], 0, $left + $entities_length);
                // maximum lenght is reached, so get off the loop
                break;
            }
                $truncate     .= $lineMatching[2];
                $total_length += $content_length;


            // if the maximum length is reached, get off the loop
            if ($total_length >= $length) {
                break;
            }
        }
        // if the words shouldn't be cut in the middle...
        if (!$exact) {
            // ...search the last occurance of a space...
            $spacePosition = \strrpos($truncate, ' ');
            if ($spacePosition) {
                // ...and cut the text in this position
                $truncate = \substr($truncate, 0, $spacePosition);
            }
        }

        // add the defined ending to the text
        $truncate .= $separator;

        foreach ($open_tags as $ignored) {
            $truncate .= '';
        }

        return $truncate;
    }

    /**
     * Wrap string to array
     * @param string|null $text
     * @param int|null $width
     * @return array<int, string>|null
     */
    public static function wordWrap(?string $text, ?int $width): ?array
    {
        if (!$text) {
            return null;
        }

        return \explode("\n", \wordwrap($text, $width ?? 75));
    }

    public static function snakeCase(string $text): string
    {
        return \ctype_lower($text) ? $text : \mb_strtolower(
            (string)\preg_replace('/([^A-Z\s])([A-Z])/', '$1_$2', $text)
        );
    }

    public static function human(string $text): string
    {

        return \ucfirst(
            \mb_strtolower(
                \trim(
                    (string)\preg_replace(
                        [
                            '/([A-Z])/',
                            \sprintf('/[%s\s]+/', '-|_|.'),
                        ],
                        ['$1', ' '],
                        $text
                    )
                )
            )
        );
    }
}
