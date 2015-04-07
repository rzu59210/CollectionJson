<?php

/*
 * This file is part of CollectionJson, a php implementation
 * of the Collection+JSON Media Type
 *
 * (c) Mickaël Vieira <contact@mickael-vieira.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CollectionJson;

use JsonSerializable;

/**
 * Class BaseEntity
 * @package CollectionJson
 */
abstract class BaseEntity implements JsonSerializable, ArrayConvertible
{
    /**
     * @var string
     */
    protected $envelope;

    /**
     * @param array $data
     * @param null  $envelope
     */
    public function __construct(array $data = [], $envelope = null)
    {
        $this->inject($data);

        if (!is_null($envelope)) {
            $this->envelope = $envelope;
        }
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $data = $this->getObjectData();
        $data = $this->addEnvelope($data);

        return $data;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $data = $this->getObjectData();
        $data = $this->recursiveToArray($data);
        $data = $this->addEnvelope($data);

        return $data;
    }

    /**
     * @param string $envelope
     */
    public function setEnvelope($envelope)
    {
        $this->envelope = $envelope;
    }

    /**
     * @param array $data
     * @return array
     */
    final protected function filterEmptyArrays(array $data)
    {
        return array_filter($data, function ($value) {
            return !(is_array($value) && empty($value));
        });
    }

    /**
     * @param array $data
     * @return array
     */
    final protected function filterNullValues(array $data)
    {
        return array_filter($data, function ($value) {
            return !is_null($value);
        });
    }

    /**
     * @return string
     */
    final public function getObjectType()
    {
        return strtolower(end(explode("\\", get_class($this))));
    }

    /**
     * @return array
     */
    abstract protected function getObjectData();

    /**
     * @param array $data
     * @return array
     */
    private function recursiveToArray(array $data)
    {
        array_walk(
            $data,
            function (&$value) {
                if (is_object($value) && $value instanceof ArrayConvertible) {
                    $value = $value->toArray();
                } elseif (is_array($value)) {
                    $value = $this->recursiveToArray($value);
                }
            }
        );

        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    private function addEnvelope(array $data)
    {
        if (is_string($this->envelope)) {
            $data = [
                $this->envelope => $data
            ];
        }

        return $data;
    }

    /**
     * @param array $data
     */
    private function inject(array $data)
    {
        foreach ($data as $key => $value) {

            $setter = "set" . $this->underscoreToCamelCase($key);
            $adder  = "add" . ucfirst($key) . "Set";

            if (method_exists($this, $setter)) {
                $this->$setter($value);
            } elseif (method_exists($this, $adder)) {
                $this->$adder($value);
            }
        }
    }

    /**
     * @param string $key
     * @return string
     */
    private function underscoreToCamelCase($key)
    {
        return implode(
            "",
            array_map(
                "ucfirst",
                preg_split("/_/", strtolower($key))
            )
        );
    }
}
