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
namespace _PhpScoper3fe455fa007d\PhpCsFixer\Console\SelfUpdate;

use _PhpScoper3fe455fa007d\Composer\Semver\Comparator;
use _PhpScoper3fe455fa007d\Composer\Semver\Semver;
use _PhpScoper3fe455fa007d\Composer\Semver\VersionParser;
/**
 * @internal
 */
final class NewVersionChecker implements \_PhpScoper3fe455fa007d\PhpCsFixer\Console\SelfUpdate\NewVersionCheckerInterface
{
    /**
     * @var GithubClientInterface
     */
    private $githubClient;
    /**
     * @var VersionParser
     */
    private $versionParser;
    /**
     * @var null|string[]
     */
    private $availableVersions;
    public function __construct(\_PhpScoper3fe455fa007d\PhpCsFixer\Console\SelfUpdate\GithubClientInterface $githubClient)
    {
        $this->githubClient = $githubClient;
        $this->versionParser = new \_PhpScoper3fe455fa007d\Composer\Semver\VersionParser();
    }
    /**
     * {@inheritdoc}
     */
    public function getLatestVersion()
    {
        $this->retrieveAvailableVersions();
        return $this->availableVersions[0];
    }
    /**
     * {@inheritdoc}
     */
    public function getLatestVersionOfMajor($majorVersion)
    {
        $this->retrieveAvailableVersions();
        $semverConstraint = '^' . $majorVersion;
        foreach ($this->availableVersions as $availableVersion) {
            if (\_PhpScoper3fe455fa007d\Composer\Semver\Semver::satisfies($availableVersion, $semverConstraint)) {
                return $availableVersion;
            }
        }
        return null;
    }
    /**
     * {@inheritdoc}
     */
    public function compareVersions($versionA, $versionB)
    {
        $versionA = $this->versionParser->normalize($versionA);
        $versionB = $this->versionParser->normalize($versionB);
        if (\_PhpScoper3fe455fa007d\Composer\Semver\Comparator::lessThan($versionA, $versionB)) {
            return -1;
        }
        if (\_PhpScoper3fe455fa007d\Composer\Semver\Comparator::greaterThan($versionA, $versionB)) {
            return 1;
        }
        return 0;
    }
    private function retrieveAvailableVersions()
    {
        if (null !== $this->availableVersions) {
            return;
        }
        foreach ($this->githubClient->getTags() as $tag) {
            $version = $tag['name'];
            try {
                $this->versionParser->normalize($version);
                if ('stable' === \_PhpScoper3fe455fa007d\Composer\Semver\VersionParser::parseStability($version)) {
                    $this->availableVersions[] = $version;
                }
            } catch (\UnexpectedValueException $exception) {
                // not a valid version tag
            }
        }
        $this->availableVersions = \_PhpScoper3fe455fa007d\Composer\Semver\Semver::rsort($this->availableVersions);
    }
}