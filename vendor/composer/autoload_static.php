<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInita341fc4f9bb7014fd82bc77aa9ca3506
{
    public static $prefixesPsr0 = array (
        'D' => 
        array (
            'Detection' => 
            array (
                0 => __DIR__ . '/..' . '/mobiledetect/mobiledetectlib/namespaced',
            ),
        ),
    );

    public static $classMap = array (
        'Mobile_Detect' => __DIR__ . '/..' . '/mobiledetect/mobiledetectlib/Mobile_Detect.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixesPsr0 = ComposerStaticInita341fc4f9bb7014fd82bc77aa9ca3506::$prefixesPsr0;
            $loader->classMap = ComposerStaticInita341fc4f9bb7014fd82bc77aa9ca3506::$classMap;

        }, null, ClassLoader::class);
    }
}
