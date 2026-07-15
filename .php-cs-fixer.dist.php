<?php
/**
 * PHP CS Fixer 3.x configuration (migrated from the legacy .php_cs.dist format).
 * Same rule intent as before; only the rule names/config syntax were updated.
 */

$aRules = [
    '@PSR2' => true,
    '@Symfony' => true,
    'concat_space' => ['spacing' => 'one'],
    'array_syntax' => ['syntax' => 'short'],
    'no_trailing_comma_in_singleline' => true,
    'trailing_comma_in_multiline' => false,
    'cast_spaces' => false,
    'combine_consecutive_unsets' => true,
    'general_phpdoc_annotation_remove' => [
        'annotations' => ['expectedException', 'expectedExceptionMessage', 'expectedExceptionMessageRegExp']
    ],
    'heredoc_to_nowdoc' => true,
    'no_extra_blank_lines' => [
        'tokens' => ['break', 'continue', 'extra', 'return', 'throw', 'use', 'parenthesis_brace_block', 'square_brace_block', 'curly_brace_block']
    ],
    'echo_tag_syntax' => ['format' => 'long'],
    'no_php4_constructor' => true,
    'no_unreachable_default_argument_value' => true,
    'no_useless_else' => true,
    'no_useless_return' => true,
    'ordered_class_elements' => true,
    'ordered_imports' => true,
    'phpdoc_add_missing_param_annotation' => true,
    'phpdoc_order' => true,
    'semicolon_after_instruction' => true,
    'psr_autoloading' => true,
    'strict_comparison' => true,
    'yoda_style' => false,
    'strict_param' => true,
    'standardize_not_equals' => true
];

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules($aRules)
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->exclude('vendor')
            ->exclude('library')
            ->in('_protected')
    );
