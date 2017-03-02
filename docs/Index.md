#SamsonFramework string condition tree generator official documentation

This module generates optimized conditions tree for searching in given strings array.

Example
```php
$input = [
    'test-string_with_me',
    'test-string_with_you',
    'test-string-with_me',
    'test-string-with-me',
    'test-string-with-you',
    'test-string-{parameter1}-with-you',
    'test-string-{parameter1}-with-me',
];

$sct = new StringConditionTree();
$sct->process($input);
/**
[
    'test-string' => [
        '_with_' => [
            'me',
            'you'
        ],
        '-with-' => [
            'me',
            'you'
        ],
        '{parameter1}' => [
            '-with-you',
            '-with-me'
        ]
    ]
]
*/
```