<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace easydowork\db;

use Traversable;
use easydowork\db\base\InvalidConfigException;

/**
 * Class ArrayExpression represents an array SQL expression.
 *
 * Expressions of this type can be used in conditions as well:
 *
 * ```php
 * $query->andWhere(['@>', 'items', new ArrayExpression([1, 2, 3], 'integer')])
 * ```
 *
 * which, depending on DBMS, will result in a well-prepared condition. For example, in
 * PostgreSQL it will be compiled to `WHERE "items" @> ARRAY[1, 2, 3]::integer[]`.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 * @since 2.0.14
 * @phpcs:disable Squiz.NamingConventions.ValidVariableName.PrivateNoUnderscore
 */
class ArrayExpression implements ExpressionInterface, \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * @var string|null the type of the array elements. Defaults to `null` which means the type is
     * not explicitly specified.
     *
     * Note that in case when type is not specified explicitly and DBMS can not guess it from the context,
     * SQL error will be raised.
     */
    private $type;
    /**
     * @var array|QueryInterface the array's content.
     * In can be represented as an array of values or a [[Query]] that returns these values.
     */
    private $value;
    /**
     * @var int the number of indices needed to select an element
     */
    private $dimension;


    /**
     * ArrayExpression constructor.
     *
     * @param array|QueryInterface|mixed $value the array content. Either represented as an array of values or a Query that
     * returns these values. A single value will be considered as an array containing one element.
     * @param string|null $type the type of the array elements. Defaults to `null` which means the type is
     * not explicitly specified. In case when type is not specified explicitly and DBMS can not guess it from the context,
     * SQL error will be raised.
     * @param int $dimension the number of indices needed to select an element
     */
    public function __construct($value, $type = null, $dimension = 1)
    {
        if ($value instanceof self) {
            $value = $value->getValue();
        }

        $this->value = $value;
        $this->type = $type;
        $this->dimension = $dimension;
    }

    /**
     * @return string|null
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return array|mixed|QueryInterface
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return int the number of indices needed to select an element
     */
    public function getDimension()
    {
        return $this->dimension;
    }

    /**
     * Whether a offset exists
     *
     * @link https://www.php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return bool true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 2.0.14
     */
    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return isset($this->value[$offset]);
    }

    /**
     * Offset to retrieve
     *
     * @link https://www.php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 2.0.14
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->value[$offset];
    }

    /**
     * Offset to set
     *
     * @link https://www.php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 2.0.14
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        $this->value[$offset] = $value;
    }

    /**
     * Offset to unset
     *
     * @link https://www.php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 2.0.14
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        unset($this->value[$offset]);
    }

    /**
     * Count elements of an object
     *
     * @link https://www.php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 2.0.14
     */
    #[\ReturnTypeWillChange]
    public function count()
    {
        return count($this->value);
    }

    /**
     * Retrieve an external iterator
     *
     * @link https://www.php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 2.0.14.1
     * @throws InvalidConfigException when ArrayExpression contains QueryInterface object
     */
    #[\ReturnTypeWillChange]
    public function getIterator()
    {
        $value = $this->getValue();
        if ($value instanceof QueryInterface) {
            throw new InvalidConfigException('The ArrayExpression class can not be iterated when the value is a QueryInterface object');
        }
        if ($value === null) {
            $value = [];
        }

        return new \ArrayIterator($value);
    }
}
