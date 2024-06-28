<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude(['.git', 'var', 'docs', 'build'])
    ->notPath('config/preload.php')
    ->notPath('config/bundles.php');

$config = new PhpCsFixer\Config();

return $config->setFinder($finder)
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        'array_syntax' => ['syntax' => 'short'],
        'native_function_invocation' => true,
        'blank_line_after_opening_tag' => true,
        'types_spaces' => ['space_multiple_catch' => 'single'],
        'control_structure_braces' => true,
        'compact_nullable_type_declaration' => true,
        'concat_space' => ['spacing' => 'one'],
        'declare_equal_normalize' => ['space' => 'none'],
        'declare_strict_types' => true,
        'list_syntax' => ['syntax' => 'short'],
        'new_with_parentheses' => true,
        'method_argument_space' => ['on_multiline' => 'ensure_fully_multiline'],
        'no_empty_phpdoc' => true,
        'no_empty_statement' => true,
        'no_leading_import_slash' => true,
        'no_leading_namespace_whitespace' => true,
        'no_trailing_comma_in_singleline' => true,
        'no_whitespace_before_comma_in_array' => true,
        'no_unused_imports' => true,
        'no_whitespace_in_blank_line' => true,
        'normalize_index_brace' => true,
        'ordered_imports' => true,
        'return_type_declaration' => ['space_before' => 'none'],
        'single_trait_insert_per_statement' => true,
        'single_quote' => true,
        'trailing_comma_in_multiline' => true,
        'trim_array_spaces' => true,
        'cast_spaces' => ['space' => 'none'],
        'type_declaration_spaces' => true,
        'blank_line_before_statement' => [
            'statements' => [
                'return',
                'break',
                'continue',
            ],
        ],
    ]);
