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
namespace _PhpScoper3fe455fa007d\PhpCsFixer\Report;

use _PhpScoper3fe455fa007d\Symfony\Component\Console\Formatter\OutputFormatter;
/**
 * Generates a report according to gitlabs subset of codeclimate json files.
 *
 * @see https://github.com/codeclimate/spec/blob/master/SPEC.md#data-types
 *
 * @author Hans-Christian Otto <c.otto@suora.com>
 *
 * @internal
 */
final class GitlabReporter implements \_PhpScoper3fe455fa007d\PhpCsFixer\Report\ReporterInterface
{
    public function getFormat()
    {
        return 'gitlab';
    }
    /**
     * Process changed files array. Returns generated report.
     *
     * @return string
     */
    public function generate(\_PhpScoper3fe455fa007d\PhpCsFixer\Report\ReportSummary $reportSummary)
    {
        $report = [];
        foreach ($reportSummary->getChanged() as $fileName => $change) {
            foreach ($change['appliedFixers'] as $fixerName) {
                $report[] = ['description' => $fixerName, 'fingerprint' => \md5($fileName . $fixerName), 'location' => ['path' => $fileName, 'lines' => ['begin' => 0]]];
            }
        }
        $jsonString = \json_encode($report);
        return $reportSummary->isDecoratedOutput() ? \_PhpScoper3fe455fa007d\Symfony\Component\Console\Formatter\OutputFormatter::escape($jsonString) : $jsonString;
    }
}
