<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Visitor;

/**
 * Visitor that can visit schema namespaces.
 */
interface NamespaceVisitor
{
    /**
     * Accepts a schema namespace name.
     *
     * @param string $namespaceName The schema namespace name to accept.
     *
     * @return void
     */
    public function acceptNamespace($namespaceName);
}
