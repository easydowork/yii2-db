<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace easydowork\db\sqlite\conditions;

/**
 * {@inheritdoc}
 */
class LikeConditionBuilder extends \easydowork\db\conditions\LikeConditionBuilder
{
    /**
     * {@inheritdoc}
     */
    protected $escapeCharacter = '\\';
}
