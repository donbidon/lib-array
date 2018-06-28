<?php
/**
 * Arrays class unit tests.
 *
 * @copyright <a href="http://donbidon.rf.gd/" target="_blank">donbidon</a>
 * @license   https://opensource.org/licenses/mit-license.php
 */

namespace donbidon\Lib;

/**
 * Arrays class unit tests.
 *
 * @todo Cover Arrays::mergeRecursive().
 */
class ArraysTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Array for tests
     *
     * @var array
     */
    protected $array;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->array = [
            'first'  => ['id' => 2,  'name' => 'Patricia Peloquin', 'sex' => 'yes',      ],
            'second' => ['id' => 12, 'name' => 'Deedee Koerner',    'sex' => 'no',       ],
            'third'  => 'not an array, will never be found',
            'fourth' => ['id' => 85, 'name' => 'Buford Devereaux',  'sex' => 'male',     ],
            'fifth'  => ['id' => 06, 'name' => 'Kaci Hillyard',     'sex' => 'of course', ],
        ];
    }

    /**
     * Tests searching by subkey value.
     *
     * @return void
     * @covers \donbidon\Lib\Arrays::searchByCol
     */
    public function testSearchRowBySubkeyValue()
    {
        $actual = Arrays::searchByCol($this->array, 'id', '2');
        $expected = [
            'first' => [
                'id'   => 2,
                'name' => 'Patricia Peloquin',
                'sex'  => 'yes',
            ],
        ];
        $this->assertEquals($expected, $actual);

        $actual = Arrays::searchByCol($this->array, 'id', '2', FALSE);
        $expected = [
            [
                'id'   => 2,
                'name' => 'Patricia Peloquin',
                'sex'  => 'yes',
            ],
        ];
        $this->assertEquals($expected, $actual);

        $actual = Arrays::searchByCol($this->array, 'id', '2', TRUE, TRUE);
        $expected = [];
        $this->assertEquals($expected, $actual);

        $actual = Arrays::searchByCol($this->array, 'name', 'Kaci Hillyard');
        $expected = [
            'fifth' => [
                'id'   => 6,
                'name' => 'Kaci Hillyard',
                'sex'  => 'of course',
            ],
        ];
        $this->assertEquals($expected, $actual);

        $actual = Arrays::searchByCol($this->array, 'name', '/ER/i');
        $expected = [
            'second' => [
                'id'   => 12,
                'name' => 'Deedee Koerner',
                'sex'  => 'no',
            ],
            'fourth' => [
                'id'   => 85,
                'name' => 'Buford Devereaux',
                'sex'  => 'male',
            ],
        ];
        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests sorting by column.
     *
     * @return void
     * @covers \donbidon\Lib\Arrays::sortByCol
     */
    public function testSortByCol()
    {
        $actual = $this->array;
        Arrays::sortByCol($actual, 'name');
        $expected = [
            'fourth' => [
                'id'   => 85,
                'name' => 'Buford Devereaux',
                'sex'  => 'male',
            ],
            'second' => [
                'id'   => 12,
                'name' => 'Deedee Koerner',
                'sex'  => 'no',
            ],
            'fifth' => [
                'id'   => 6,
                'name' => 'Kaci Hillyard',
                'sex'  => 'of course',
            ],
            'first' => [
                'id'   => 2,
                'name' => 'Patricia Peloquin',
                'sex'  => 'yes',
            ],
        ];
        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests adapting column containing sort order as integer values
     * for using array_multisort().
     *
     * @return void
     * @covers \donbidon\Lib\Arrays::adaptOrderCol
     */
    public function testAdaptOrderCol()
    {
        $actual   = [10, 20, 30];
        $expected = $actual;
        Arrays::adaptOrderCol($actual);
        $this->assertEquals($expected, $actual);

        $actual   = [10, 20, 10];
        $expected = [10, 21, 11];
        Arrays::adaptOrderCol($actual);
        $this->assertEquals($expected, $actual);

        $actual   = [2, 3, 2];
        $expected = [2, 4, 3];
        Arrays::adaptOrderCol($actual);
        $this->assertEquals($expected, $actual);

        $actual   = [2, 1, 1, 2, 1];
        $expected = [4, 1, 2, 5, 3];
        Arrays::adaptOrderCol($actual);
        $this->assertEquals($expected, $actual);

        $actual   = [-100, -200, -100, -200];
        $expected = [ -99, -200,  -98, -199];
        Arrays::adaptOrderCol($actual);
        $this->assertEquals($expected, $actual);
    }
}
