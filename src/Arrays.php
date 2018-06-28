<?php
/**
 * Arrays related library.
 *
 * @copyright <a href="http://donbidon.rf.gd/" target="_blank">donbidon</a>
 * @license   https://opensource.org/licenses/mit-license.php
 */

namespace donbidon\Lib;

/**
 * Arrays related library.
 *
 * <!-- move: index.html -->
 * <a href="classes/donbidon.Lib.Arrays.html">\donbidon\Lib\Arrays</a> -
 * arrays related library.
 * <!-- /move -->
 *
 * @static
 */
class Arrays
{
    /**
     * Key
     *
     * @var string|int
     *
     * @see self::searchByCol()
     * @see self::filterBySubkey()
     *
     * @internal
     */
    protected static $key;

    /**
     * Value
     *
     * @var mixed
     *
     * @see self::searchByCol()
     * @see self::filterBySubkey()
     *
     * @internal
     */
    protected static $value;

    /**
     * Flag specifying to compare values strict way
     *
     * @var bool
     *
     * @see self::searchByCol()
     * @see self::filterBySubkey()
     *
     * @internal
     */
    protected static $strict;

    /**
     * Merges arrays recursively and distinctly.
     *
     * @param array $first   <b>[by ref]</b>
     * @param array $second  <b>[by ref]</b>
     *
     * @return array
     */
    public static function mergeRecursive(array &$first, array &$second)
    {
        $merged = $first;
        foreach ($second as $key => &$value) {
            $merged[$key] =
                is_array($value) &&
                isset($merged[$key]) &&
                is_array($merged[$key])
                    ? self::mergeRecursive($merged[$key], $value)
                    : $merged[$key] = $value;
        }

        return $merged;
    }

    /**
     * Searches two-dimensional array rows for a given pair key/value and
     * returns the corresponding rows.
     *
     * Example:
     * ```php
     * use \donbidon\Utility\Arrays;
     *
     * $haystack = [
     *     'first'  => ['id' => 1, 'value' => 'BlaH'],
     *     'second' => ['id' => 2, 'value' => 'Heh'],
     *     'third'  => ['id' => 3, 'value' => 'Wow!'],
     * ];
     *
     * $result = Arrays::searchByCol($haystack, 'id', "2");
     * print_r($result);
     * ```
     * outputs
     * ```
     * Array
     * (
     *     [first] => Array
     *         (
     *             [id] => 2
     *             [value] => Heh
     *         )
     * )
     * ```
     * ```php
     * $result = Arrays::searchByCol($haystack, 'id', '2', false);
     * print_r($result);
     * ```
     * outputs
     * ```
     * Array
     * (
     *     [0] => Array
     *         (
     *             [id] => 2
     *             [value] => Heh
     *         )
     * )
     * ```
     * ```code
     * $result = Arrays::searchByCol($haystack, 'id', '2', true, true);
     * print_r($result);
     * ```
     * outputs
     * ```
     * Array
     * (
     * )
     * ```
     * ```php
     * $result = Arrays::searchByCol($haystack, 'value', 'Wow!');
     * print_r($result);
     * ```
     * outputs
     * ```
     * Array
     * (
     *     [fifth] => Array
     *         (
     *             [id] => 3
     *             [value] => Wow!
     *         )
     * )
     * ```
     * ```php
     * $result = Arrays::searchByCol($haystack, 'value', '/h$/i');
     * print_r($result);
     * ```
     * outputs
     * ```
     * Array
     * (
     *     [second] => Array
     *         (
     *             [id] => 1
     *             [value] => BlaH
     *         )
     *     [fourth] => Array
     *         (
     *             [id] => 2
     *             [value] => Heh
     *         )
     * }
     * ```
     *
     * @param array  $haystack      The array
     * @param string $key           The searched key
     * @param mixed $value          The searched value, if passed as string and
     *        starts from '/' symbol, will be processed as regular expression,
     *        in this case $strict argument will be ignored
     * @param bool   $preserveKeys  Flag specifying to maintain rows index
     *        associative
     * @param bool   $strict        Flag specifying to compare values strict way
     *
     * @return array
     */
    public static function searchByCol(
        array $haystack, $key, $value, $preserveKeys = true, $strict = false
    )
    {
        self::$key    = $key;
        self::$value  = $value;
        self::$strict = (bool)$strict;

        $result = array_filter($haystack, array('self', 'filterBySubkey'));
        if (!$preserveKeys && sizeof($result)) {
            $result = array_combine(
                range(0, sizeof($result) - 1),
                array_values($result)
            );
        }
        self::$key   = null;
        self::$value = null;

        return $result;
    }

    /**
     * Sort two-dimensional array by column preserving row keys.
     *
     * @param array  $array     <b>[by ref]</b> Array
     * @param string $column    Sort column
     * @param int    $sort      Sort type
     * @param int    $direction Sort direction: SORT_ASC or SORT_DESC
     *
     * @return void
     *
     * @link http://php.net/manual/en/function.array-multisort.php
     *       PHP documentation for sort types
     */
    public static function sortByCol(
        array &$array, $column, $sort = SORT_STRING, $direction = SORT_ASC
    )
    {
        if (!sizeof($array)) {
            return;
        }

        $index = [];
        $i = 0;
        foreach ($array as $key => $row) {
            if (is_array($row)) {
                $index['pos'][$i]  = $key;
                $index['name'][$i] = $row[$column];
                ++$i;
            }
        }
        array_multisort($index['name'], $sort, $direction, $index['pos']);
        $result = array();
        for ($j = 0; $j < $i; ++$j) {
            $result[$index['pos'][$j]] = $array[$index['pos'][$j]];
        }
        $array = $result;
    }

    /**
     * Adapt column containing sort order as integer values for correct using
     * array_multisort().
     *
     * Let we want to sort one array using other array as order:
     * ```php
     * $order = [2, 1];
     * $haystack  = ['aaa', 'bbb'];
     * array_multisort($order, SORT_NUMERIC, SORT_ASC, $haystack);
     * print_r($haystack);
     * ```
     * outputs
     * ```
     * Array
     * (
     *     [0] => bbb
     *     [1] => aaa
     * )
     * ```
     * But
     * ```php
     * $order = [1, 1];
     * $haystack  = ['bbb', 'aaa'];
     * array_multisort($order, SORT_NUMERIC, SORT_ASC, $haystack);
     * print_r($haystack);
     * ```
     * will change order of data and output:
     * ```
     * Array
     * (
     *     [0] => aaa
     *     [1] => bbb
     * )
     * ```
     * Using this class method you can prevent changing order of data.
     *
     * @param array $column  <b>[by ref]</b> Array containing integer values as
     *        order for data
     *
     * @return void
     *
     * @see http://php.net/manual/en/function.array-multisort.php
     *      array_multisort()
     */
    public static function adaptOrderCol(array &$column)
    {
        $minOrder = min($column);
        do {
            $counts = array_count_values($column);
            ksort($counts);
            $loop = false;
            foreach ($counts as $order => $count) {
                if ($count < 2) {
                    continue;
                }
                $current = array_search($order, $column);
                if ($order > $minOrder) {
                    $minOrder = $order;
                }
                foreach (array_keys($column) as $key) {
                    if ($column[$key] >= $minOrder && $key != $current) {
                        ++$column[$key];
                    }
                }
                $minOrder = $order + 1;
                $loop = true;
                break; // foreach ($counts as $order => $count)
            }
        } while ($loop);
    }

    /**
     * Filters two-dimensional array for a given pair key/value.
     *
     * @param mixed $row
     *
     * @return bool
     *
     * @see self::searchByCol()
     *
     * @internal
     */
    protected static function filterBySubkey($row)
    {
        $result = false;
        if (is_array($row) && array_key_exists(self::$key, $row)) {
            if ('/' == substr(self::$value, 0, 1)) {
                $result = preg_match(self::$value, $row[self::$key]);
            } else {
                $result = self::$strict
                    ? self::$value === $row[self::$key]
                    : self::$value == $row[self::$key];
            }
        }

        return $result;
    }
}
