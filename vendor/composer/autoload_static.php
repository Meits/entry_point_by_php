<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit91da158e39c9de8cece966df2b7ad951
{
    public static $files = array (
        '8a6bf8f8a9c27df453a2dd7dcb5c3c68' => __DIR__ . '/../..' . '/src/helpers/helpers.php',
    );

    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit91da158e39c9de8cece966df2b7ad951::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit91da158e39c9de8cece966df2b7ad951::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
