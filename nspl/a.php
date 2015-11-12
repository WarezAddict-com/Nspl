<?php

namespace nspl\a;

use nspl\f;
use nspl\ds;
use nspl\op;

/**
 * Adds $list2 items to the end of $list1
 *
 * @param array $list1
 * @param array $list2
 * @return array
 */
function extend(array $list1, array $list2)
{
    return array_merge($list1, $list2);
}

/**
 * Zips two or more lists
 *
 * @param array $list1
 * @param array $list2
 * @return array
 */
function zip(array $list1, array $list2)
{
    $lists = func_get_args();
    $count = func_num_args();

    $i = 0;
    $result = array();
    do {
        $zipped = array();
        for ($j = 0; $j < $count; ++$j) {
            if (!isset($lists[$j][$i]) && !array_key_exists($i, $lists[$j])) {
                break 2;
            }
            $zipped[] = $lists[$j][$i];
        }
        $result[] = $zipped;
        ++$i;
    } while (true);

    return $result;
}

/**
 * Flattens multidimensional list
 *
 * @param array $list
 * @return array
 */
function flatten(array $list)
{
    $result = array();
    $lenght = count($list);
    for ($i = 0; $i < $lenght; ++$i) {
        if (is_array($list[$i])) {
            $flattened = flatten($list[$i]);
            $subLenght = count($flattened);
            for ($j = 0; $j < $subLenght; ++$j) {
                $result[] = $flattened[$j];
            }
        }
        else {
            $result[] = $list[$i];
        }
    }

    return $result;
}

/**
 * Returns sorted copy of passed array
 *
 * @param array $array
 * @param bool $reversed
 * @param callable $key Function of one argument that is used to extract a comparison key from each element
 * @param callable $cmp Function of two arguments which returns a negative number, zero or positive number depending on
 *                      whether the first argument is smaller than, equal to, or larger than the second argument
 * @return array
 */
function sorted(array $array, $reversed = false, $key = null, $cmp = null)
{
    if (!$cmp) {
        $cmp = function ($a, $b) { return $a > $b ? 1 : -1; };
    }

    if ($key) {
        $cmp = function($a, $b) use ($key, $cmp) {
            return call_user_func_array($cmp, array($key($a), $key($b)));
        };
    }

    if ($reversed) {
        $cmp = f\compose(op::$neg, $cmp);
    }

    $isList = ds\isList($array);
    uasort($array, $cmp);

    return $isList ? array_values($array) : $array;
}

/**
 * Returns first N list items
 *
 * @param array $list
 * @param int $N
 * @param int $step
 * @return array
 */
function take(array $list, $N, $step = 1)
{
    if (1 === $step) {
        return array_values(array_slice($list, 0, $N));
    }

    $result = array();
    $lenght = min(count($list), $N * $step);
    for ($i = 0; $i < $lenght; $i += $step) {
        $result[] = $list[$i];
    }

    return $result;
}

/**
 * Returns the first list item
 *
 * @param array $list
 * @return array
 */
function first(array $list)
{
    if (!$list) {
        throw new \InvalidArgumentException('Can not return the first item of an empty list');
    }

    return current(take($list, 1));
}

/**
 * Drops first N list items
 *
 * @param array $list
 * @param int $N
 * @return array
 */
function drop(array $list, $N)
{
    return array_slice($list, $N);
}

/**
 * Returns the last list item
 *
 * @param array $list
 * @return array
 */
function last(array $list)
{
    if (!$list) {
        throw new \InvalidArgumentException('Can not return the last item of an empty list');
    }

    return $list[count($list) - 1];
}

// @todo Add pop key

namespace nspl;

class a
{
    static public $extend;
    static public $zip;
    static public $flatten;
    static public $sorted;
    static public $take;
    static public $first;
    static public $drop;
    static public $last;

}

a::$extend = function(array $list1, array $list2) { return \nspl\a\extend($list1, $list2); };
a::$zip = function(array $list1, array $list2) { return call_user_func_array('\nspl\a\zip', func_get_args()); };
a::$flatten = function(array $list) { return \nspl\a\flatten($list); };
a::$sorted = function(array $array, $reversed = false, $key = null, $cmp = null) { return \nspl\a\sorted($array, $reversed, $key, $cmp); };
a::$take = function(array $list, $N, $step = 1) { return \nspl\a\take($list, $N, $step); };
a::$first = function(array $list) { return \nspl\a\first($list); };
a::$drop = function(array $list, $N) { return \nspl\a\drop($list, $N); };
a::$last = function(array $list) { return \nspl\a\last($list); };
