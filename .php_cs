<?php

declare(strict_types=1);

/*
 * This file is part of the FormHandler package.
 *
 * (c) SolidWorx <open-source@solidworx.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$header = <<<'EOF'
This file is part of the FormHandler package.

(c) SolidWorx <open-source@solidworx.co>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
EOF;

$finder = PhpCsFixer\Finder::create()
    ->ignoreDotFiles(true)
    ->in(__DIR__)
    ->exclude([
        'vendor',
    ])
    ->name('.php_cs');

return PhpCsFixer\Config::create()
    ->setFinder($finder)
    ->setRules([
        '@PSR1' => true,
        '@PSR2' => true,
        '@Symfony' => true,
        'concat_space' => false,
        'array_syntax' => ['syntax' => 'short'],
        'phpdoc_no_package' => true,
        'phpdoc_summary' => false,
        'declare_strict_types' => true,
        'strict_param' => true,
        'ordered_imports' => true,
        'header_comment' => [
            'comment_type' => 'PHPDoc',
            'header' => \trim($header),
            'location' => 'after_declare_strict',
            'separate' => 'both',
        ],
    ]);
