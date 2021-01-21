<?php

// scoper-autoload.php @generated by PhpScoper

$loader = require_once __DIR__.'/autoload.php';

// Aliases for the whitelisted classes. For more information see:
// https://github.com/humbug/php-scoper/blob/master/README.md#class-whitelisting
if (!class_exists('ComposerAutoloaderInit69b13108c6f704d877adeccec3899d83', false) && !interface_exists('ComposerAutoloaderInit69b13108c6f704d877adeccec3899d83', false) && !trait_exists('ComposerAutoloaderInit69b13108c6f704d877adeccec3899d83', false)) {
    spl_autoload_call('_PhpScoper3fe455fa007d\ComposerAutoloaderInit69b13108c6f704d877adeccec3899d83');
}
if (!class_exists('ValueError', false) && !interface_exists('ValueError', false) && !trait_exists('ValueError', false)) {
    spl_autoload_call('_PhpScoper3fe455fa007d\ValueError');
}
if (!class_exists('Attribute', false) && !interface_exists('Attribute', false) && !trait_exists('Attribute', false)) {
    spl_autoload_call('_PhpScoper3fe455fa007d\Attribute');
}
if (!class_exists('UnhandledMatchError', false) && !interface_exists('UnhandledMatchError', false) && !trait_exists('UnhandledMatchError', false)) {
    spl_autoload_call('_PhpScoper3fe455fa007d\UnhandledMatchError');
}
if (!class_exists('Stringable', false) && !interface_exists('Stringable', false) && !trait_exists('Stringable', false)) {
    spl_autoload_call('_PhpScoper3fe455fa007d\Stringable');
}

// Functions whitelisting. For more information see:
// https://github.com/humbug/php-scoper/blob/master/README.md#functions-whitelisting
if (!function_exists('composerRequire69b13108c6f704d877adeccec3899d83')) {
    function composerRequire69b13108c6f704d877adeccec3899d83() {
        return \_PhpScoper3fe455fa007d\composerRequire69b13108c6f704d877adeccec3899d83(...func_get_args());
    }
}
if (!function_exists('dump')) {
    function dump() {
        return \_PhpScoper3fe455fa007d\dump(...func_get_args());
    }
}
if (!function_exists('dd')) {
    function dd() {
        return \_PhpScoper3fe455fa007d\dd(...func_get_args());
    }
}
if (!function_exists('includeIfExists')) {
    function includeIfExists() {
        return \_PhpScoper3fe455fa007d\includeIfExists(...func_get_args());
    }
}
if (!function_exists('pd')) {
    function pd() {
        return \_PhpScoper3fe455fa007d\pd(...func_get_args());
    }
}

return $loader;
