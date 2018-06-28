<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/16
 * Time: 17:38
 */

namespace Core\Base;

class Wrapper
{
    private function toCamelCase($string)
    {
        $string = str_replace('_', ' ', $string);
        $string = str_replace(' ', '', ucwords($string));
        return $string;
    }

    public function setWrapperProperties(array $input)
    {
        $properties = array_keys(get_class_vars(get_called_class()));
        foreach ($properties as $propertiesName) {
            if (isset($input[$propertiesName])) {
                $setFn = 'set' . $this->toCamelCase($propertiesName);
                if (method_exists($this, $setFn)) {
                    $this->$setFn($input[$propertiesName]);
                } else {
                    $this->$propertiesName = $input[$propertiesName];
                }
            }
        }
    }

    public function mappingToModel(Model &$model)
    {
        $modelProperties = array_keys(get_object_vars($model));
        foreach ($modelProperties as $propertiesName) {
            if (isset($this->$propertiesName)) {
                $model->$propertiesName = $this->$propertiesName;
            }
        }
    }

    public function toArray()
    {
        $arrayData = [];
        $properties = array_keys(get_class_vars(get_called_class()));
        foreach ($properties as $propertiesName) {
            $arrayData[$propertiesName] = $this->$propertiesName;
        }
        return $arrayData;
    }


}