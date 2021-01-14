<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace _PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer;

use _PhpScoper3fe455fa007d\PhpCsFixer\Utils;
/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
abstract class AbstractTransformer implements \_PhpScoper3fe455fa007d\PhpCsFixer\Tokenizer\TransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        $nameParts = \explode('\\', static::class);
        $name = \substr(\end($nameParts), 0, -\strlen('Transformer'));
        return \_PhpScoper3fe455fa007d\PhpCsFixer\Utils::camelCaseToUnderscore($name);
    }
    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 0;
    }
    /**
     * {@inheritdoc}
     */
    public function getCustomTokens()
    {
        @\trigger_error(\sprintf('%s is deprecated and will be removed in 3.0.', __METHOD__), \E_USER_DEPRECATED);
        return $this->getDeprecatedCustomTokens();
    }
    /**
     * @return int[]
     */
    protected abstract function getDeprecatedCustomTokens();
}
