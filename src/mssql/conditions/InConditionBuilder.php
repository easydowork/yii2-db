<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace easydowork\db\mssql\conditions;

use easydowork\db\base\NotSupportedException;
use easydowork\db\Expression;

/**
 * {@inheritdoc}
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 * @since 2.0.14
 */
class InConditionBuilder extends \easydowork\db\conditions\InConditionBuilder
{
    /**
     * {@inheritdoc}
     * @throws NotSupportedException if `$columns` is an array
     */
    protected function buildSubqueryInCondition($operator, $columns, $values, &$params)
    {
        if (is_array($columns)) {
            throw new NotSupportedException(__METHOD__ . ' is not supported by MSSQL.');
        }

        return parent::buildSubqueryInCondition($operator, $columns, $values, $params);
    }

    /**
     * {@inheritdoc}
     */
    protected function buildCompositeInCondition($operator, $columns, $values, &$params)
    {
        $quotedColumns = [];
        foreach ($columns as $i => $column) {
            if ($column instanceof Expression) {
                $column = $column->expression;
            }
            $quotedColumns[$i] = strpos($column, '(') === false ? $this->queryBuilder->db->quoteColumnName($column) : $column;
        }
        $vss = [];
        foreach ($values as $value) {
            $vs = [];
            foreach ($columns as $i => $column) {
                if ($column instanceof Expression) {
                    $column = $column->expression;
                }
                if (isset($value[$column])) {
                    $phName = $this->queryBuilder->bindParam($value[$column], $params);
                    $vs[] = $quotedColumns[$i] . ($operator === 'IN' ? ' = ' : ' != ') . $phName;
                } else {
                    $vs[] = $quotedColumns[$i] . ($operator === 'IN' ? ' IS' : ' IS NOT') . ' NULL';
                }
            }
            $vss[] = '(' . implode($operator === 'IN' ? ' AND ' : ' OR ', $vs) . ')';
        }

        return '(' . implode($operator === 'IN' ? ' OR ' : ' AND ', $vss) . ')';
    }
}
