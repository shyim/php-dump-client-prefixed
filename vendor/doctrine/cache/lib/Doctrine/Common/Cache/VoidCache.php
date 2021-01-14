<?php

namespace _PhpScoper3fe455fa007d\Doctrine\Common\Cache;

/**
 * Void cache driver. The cache could be of use in tests where you don`t need to cache anything.
 *
 * @link   www.doctrine-project.org
 */
class VoidCache extends \_PhpScoper3fe455fa007d\Doctrine\Common\Cache\CacheProvider
{
    /**
     * {@inheritDoc}
     */
    protected function doFetch($id)
    {
        return \false;
    }
    /**
     * {@inheritDoc}
     */
    protected function doContains($id)
    {
        return \false;
    }
    /**
     * {@inheritDoc}
     */
    protected function doSave($id, $data, $lifeTime = 0)
    {
        return \true;
    }
    /**
     * {@inheritDoc}
     */
    protected function doDelete($id)
    {
        return \true;
    }
    /**
     * {@inheritDoc}
     */
    protected function doFlush()
    {
        return \true;
    }
    /**
     * {@inheritDoc}
     */
    protected function doGetStats()
    {
        return;
    }
}
