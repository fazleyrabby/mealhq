<?php

namespace App\Services;

use HTMLPurifier;
use HTMLPurifier_Config;

class HtmlPurifierService
{
    protected static ?HTMLPurifier $purifier = null;

    public static function clean(?string $html): string
    {
        if (empty($html)) {
            return '';
        }

        if (self::$purifier === null) {
            $config = HTMLPurifier_Config::createDefault();
            $config->set('Cache.SerializerPath', storage_path('app/htmlpurifier'));
            $config->set('Cache.SerializerPermissions', 0775);
            $config->set('HTML.Allowed', 'p,br,b,strong,i,em,u,a[href|title|target|rel],ul,ol,li,blockquote,h1,h2,h3,h4,span,div,img[src|alt|width|height]');
            $config->set('Attr.AllowedRel', ['noopener', 'nofollow']);
            $config->set('HTML.TargetBlank', true);
            $config->set('AutoFormat.Linkify', true);
            self::$purifier = new HTMLPurifier($config);
        }

        return self::$purifier->purify($html);
    }
}
