<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace _PhpScoper3fe455fa007d\Symfony\Component\VarDumper\Caster;

use _PhpScoper3fe455fa007d\Symfony\Component\HttpFoundation\Request;
use _PhpScoper3fe455fa007d\Symfony\Component\Uid\Ulid;
use _PhpScoper3fe455fa007d\Symfony\Component\Uid\Uuid;
use _PhpScoper3fe455fa007d\Symfony\Component\VarDumper\Cloner\Stub;
/**
 * @final
 */
class SymfonyCaster
{
    private const REQUEST_GETTERS = ['pathInfo' => 'getPathInfo', 'requestUri' => 'getRequestUri', 'baseUrl' => 'getBaseUrl', 'basePath' => 'getBasePath', 'method' => 'getMethod', 'format' => 'getRequestFormat'];
    public static function castRequest(\_PhpScoper3fe455fa007d\Symfony\Component\HttpFoundation\Request $request, array $a, \_PhpScoper3fe455fa007d\Symfony\Component\VarDumper\Cloner\Stub $stub, bool $isNested)
    {
        $clone = null;
        foreach (self::REQUEST_GETTERS as $prop => $getter) {
            $key = \_PhpScoper3fe455fa007d\Symfony\Component\VarDumper\Caster\Caster::PREFIX_PROTECTED . $prop;
            if (\array_key_exists($key, $a) && null === $a[$key]) {
                if (null === $clone) {
                    $clone = clone $request;
                }
                $a[\_PhpScoper3fe455fa007d\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL . $prop] = $clone->{$getter}();
            }
        }
        return $a;
    }
    public static function castHttpClient($client, array $a, \_PhpScoper3fe455fa007d\Symfony\Component\VarDumper\Cloner\Stub $stub, bool $isNested)
    {
        $multiKey = \sprintf("\0%s\0multi", \get_class($client));
        if (isset($a[$multiKey])) {
            $a[$multiKey] = new \_PhpScoper3fe455fa007d\Symfony\Component\VarDumper\Caster\CutStub($a[$multiKey]);
        }
        return $a;
    }
    public static function castHttpClientResponse($response, array $a, \_PhpScoper3fe455fa007d\Symfony\Component\VarDumper\Cloner\Stub $stub, bool $isNested)
    {
        $stub->cut += \count($a);
        $a = [];
        foreach ($response->getInfo() as $k => $v) {
            $a[\_PhpScoper3fe455fa007d\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL . $k] = $v;
        }
        return $a;
    }
    public static function castUuid(\_PhpScoper3fe455fa007d\Symfony\Component\Uid\Uuid $uuid, array $a, \_PhpScoper3fe455fa007d\Symfony\Component\VarDumper\Cloner\Stub $stub, bool $isNested)
    {
        $a[\_PhpScoper3fe455fa007d\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL . 'toBase58'] = $uuid->toBase58();
        $a[\_PhpScoper3fe455fa007d\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL . 'toBase32'] = $uuid->toBase32();
        // symfony/uid >= 5.3
        if (\method_exists($uuid, 'getDateTime')) {
            $a[\_PhpScoper3fe455fa007d\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL . 'time'] = $uuid->getDateTime()->format('_PhpScoper3fe455fa007d\\Y-m-d H:i:s.u \\U\\T\\C');
        }
        return $a;
    }
    public static function castUlid(\_PhpScoper3fe455fa007d\Symfony\Component\Uid\Ulid $ulid, array $a, \_PhpScoper3fe455fa007d\Symfony\Component\VarDumper\Cloner\Stub $stub, bool $isNested)
    {
        $a[\_PhpScoper3fe455fa007d\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL . 'toBase58'] = $ulid->toBase58();
        $a[\_PhpScoper3fe455fa007d\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL . 'toRfc4122'] = $ulid->toRfc4122();
        // symfony/uid >= 5.3
        if (\method_exists($ulid, 'getDateTime')) {
            $a[\_PhpScoper3fe455fa007d\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL . 'time'] = $ulid->getDateTime()->format('_PhpScoper3fe455fa007d\\Y-m-d H:i:s.v \\U\\T\\C');
        }
        return $a;
    }
}
