<?php

/**
 * @deprecated deprecated since version 1.0.0
 */
class Security{

    /**
     * @deprecated deprecated since version 1.0.0
     */
    public static function vulnerableExtensions(): string {
        return '/^.*\.('.implode('|',["php","php5","php7","phar","phtml","htaccess"]).')$/i';
    }

    /**
     * @deprecated deprecated since version 1.0.0
     */
    public static function isExecutable(string $file): int|false {
        return preg_match(self::vulnerableExtensions(), strtolower($file));
    }


}