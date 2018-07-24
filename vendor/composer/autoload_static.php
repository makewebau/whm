<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitf7be1f69aa4acad27d3b78801b00aefe
{
    public static $prefixLengthsPsr4 = array (
        '\\' => 
        array (
            '\\MakeWeb\\WHM\\' => 13,
        ),
    );

    public static $prefixDirsPsr4 = array (
        '\\MakeWeb\\WHM\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitf7be1f69aa4acad27d3b78801b00aefe::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitf7be1f69aa4acad27d3b78801b00aefe::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
