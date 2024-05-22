<?php

namespace app\models\traits;

use app\models\interfaces\HasEventsInterface;

trait ObjectNameTrait
{

    /**
     * @param $name
     * @param bool $throwException
     * @return mixed
     */
    public function getRelation($name, $throwException = true)
    {
        $getter = 'get' . $name;
        $class = 'app\models\\' . $name;
        $tableName = self::getObjectByTableClassName($class);

        if (!method_exists($this, $getter) && $class instanceof HasEventsInterface) {
            return $this->hasOne($tableName, ['id' => 'object_id']);
        }

        return parent::getRelation($name, $throwException);
    }

    /**
     * @param $className
     * @return mixed
     */
    public static function getObjectByTableClassName($className)
    {
        if (method_exists($className, 'tableName')) {
            return str_replace(['{', '}', '%'], '', $className::tableName());
        }

        return $className;
    }
}