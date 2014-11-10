<?php

namespace JsonCollection;

/**
 * Class BaseEntity
 * @package JsonCollection
 */
class BaseEntity extends DataExtraction implements DataInjectable
{

    use DataInjection;

    /**
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        $this->inject($data);
    }

    /**
     * @return array
     */
    protected function getObjectData()
    {
        return get_object_vars($this);
    }
}
