<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Event;

use _PhpScoper3fe455fa007d\Doctrine\Common\EventArgs;
/**
 * Base class for schema related events.
 */
class SchemaEventArgs extends \_PhpScoper3fe455fa007d\Doctrine\Common\EventArgs
{
    /** @var bool */
    private $preventDefault = \false;
    /**
     * @return SchemaEventArgs
     */
    public function preventDefault()
    {
        $this->preventDefault = \true;
        return $this;
    }
    /**
     * @return bool
     */
    public function isDefaultPrevented()
    {
        return $this->preventDefault;
    }
}
