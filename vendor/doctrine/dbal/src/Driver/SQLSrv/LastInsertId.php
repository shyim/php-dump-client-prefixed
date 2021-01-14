<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\SQLSrv;

/**
 * Last Id Data Container.
 *
 * @internal
 */
final class LastInsertId
{
    /** @var int */
    private $id;
    /**
     * @param int $id
     *
     * @return void
     */
    public function setId($id)
    {
        $this->id = $id;
    }
    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}
