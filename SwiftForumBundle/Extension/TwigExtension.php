<?php
/*
* This file is part of the Swift Forum package.
*
* (c) SwiftForum <https://github.com/swiftforum>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Talis\SwiftForumBundle\Extension;

use Twig_Extension;

/**
 * Extends Twig functionality.
 *
 * @author Felix Kastner <felix@chapterfain.com>
 */
class TwigExtension extends Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('cleanlink', array($this, 'cleanLinkFilter')),
            new \Twig_SimpleFilter('cleanmarkup', array($this, 'cleanMarkupFilter')),
        );
    }

    public function cleanMarkupFilter($string)
    {
        $string = str_replace('<', '&lt;', $string);

        return $string;
    }

    /**
     * Cleans a string for use in forum links.
     *
     * @todo Figure out if it would be better to make a extra DB field with the cleaned link, rather than memory caching
     * @param string $string The String to clean up
     * @param int $forceLower Force Lowercase ?
     * @param int $maxLength Max Length of returned string
     * @param string $replaceChar Character to replace non-alphanumeric characters with
     * @return string The cleaned up string
     */
    public function cleanLinkFilter($string, $forceLower = 1, $maxLength = 50, $replaceChar = "-")
    {
        // Key to search the cache with.
        $cacheIdentifier = 'CleanLink[' . md5($string) . ']';

        // Attempt to fetch from memory.
        if($preparedString = apc_fetch($cacheIdentifier)) {
            return $preparedString;
        }

        // Shorten to $maxLength.
        if($maxLength) {
            $string = substr($string, 0, $maxLength);
        }

        if($forceLower) {
            $string = strtolower($string);
        }

        // Turns any non-alphanumerical character into replaceChar, consolidates multiple occurrences into a single replaceChar.
        $string = preg_replace("/[^[:alnum:]]+/u", $replaceChar, $string);

        // Remove leading and trailing replaceChar.
        $string = trim($string, $replaceChar);

        // Store in memory
        apc_store($cacheIdentifier, $string, 7200);

        return $string;
    }

    public function getName()
    {
        return 'twig_extension';
    }
} 