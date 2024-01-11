<?php

/* testEmptyArray */
$var = array();

/* testArrayWithSpace */
$var = array    (1 => 10);

/* testArrayWithComment */
$var = array /*comment*/ (1 => 10);

/* testNestingArray */
$var = array(
    /* testNestedArray */
    array(
        'key' => 'value',

        /* testClosureReturnType */
        'closure' => function ($a) use ($global): array {
        },
    ),
);

/* testFunctionDeclarationParamType */
function foo(array $a)
{
}

/* testFunctionDeclarationReturnType */
function foo($a): int|array|null
{
}

class Bar
{
    /* testClassConst */
    const ARRAY = [];

    /* testClassMethod */
    public function array()
    {
    }
}
