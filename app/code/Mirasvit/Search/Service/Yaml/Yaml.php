<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-search
 * @version   1.0.151
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Search\Service\Yaml;

class Yaml extends \Zend_Config_Yaml
{
    /**
     * @var int
     */
    static $n = -1;

    /**
     * {@inheritdoc}
     */
    public static function decode($yaml)
    {
        $lines = explode("\n", $yaml);
        reset($lines);

        return self::_decodeYaml(0, $lines);
    }



    /**
     * {@inheritdoc}
     */
    /**
     * Service function to decode YAML
     *
     * @param  int   $currentIndent Current indent level
     * @param  array $lines YAML lines
     *
     * @return array|string
     */
    protected static function _decodeYaml($currentIndent, &$lines)
    {
        $config   = [];
        $inIndent = false;
        foreach ($lines as $n => $line) {
            if (self::$n >= $n) {
                continue;
            }

            self::$n = $n;

            $lineno = $n + 1;

            $line = rtrim(preg_replace("/#.*$/", "", $line));
            if (strlen($line) == 0) {
                continue;
            }

            $indent = strspn($line, " ");

            // line without the spaces
            $line = trim($line);
            if (strlen($line) == 0) {
                continue;
            }

            if ($indent < $currentIndent) {
                // this level is done
                self::$n--;

                return $config;
            }

            if (!$inIndent) {
                $currentIndent = $indent;
                $inIndent      = true;
            }

            if (preg_match("/(?!-)([\w\-]+):\s*(.*)/", $line, $m)) {
                // key: value
                if (strlen($m[2])) {
                    // simple key: value
                    $value = preg_replace("/#.*$/", "", $m[2]);
                    $value = self::_parseValue($value);
                } else {
                    // key: and then values on new lines
                    $value = self::_decodeYaml($currentIndent + 1, $lines);
                    if (is_array($value) && !count($value)) {
                        $value = "";
                    }
                }
                $config[$m[1]] = $value;
            } elseif ($line[0] == "-") {
                // item in the list:
                // - FOO
                if (strlen($line) > 2) {
                    $value = substr($line, 2);

                    $config[] = self::_parseValue($value);
                } else {
                    $config[] = self::_decodeYaml($currentIndent + 1, $lines);
                }
            } else {
                #require_once 'Zend/Config/Exception.php';
                throw new \Zend_Config_Exception(sprintf(
                    'Error parsing YAML at line %d - unsupported syntax: "%s"',
                    $lineno,
                    $line
                ));
            }
        }

        return $config;
    }
}
