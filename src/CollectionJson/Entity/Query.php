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

namespace CollectionJson\Entity;

use CollectionJson\BaseEntity;
use CollectionJson\DataAware;
use CollectionJson\DataContainer;

/**
 * Class Query
 * @package CollectionJson\Entity
 * @link http://amundsen.com/media-types/collection/format/
 * @link http://amundsen.com/media-types/collection/format/#arrays-queries
 */
class Query extends BaseEntity implements DataAware
{

    use DataContainer;

    /**
     * @var string
     * @link http://amundsen.com/media-types/collection/format/#properties-href
     */
    protected $href;

    /**
     * @var string
     * @link http://amundsen.com/media-types/collection/format/#properties-rel
     */
    protected $rel;

    /**
     * @var string
     * @link http://amundsen.com/media-types/collection/format/#properties-name
     */
    protected $name;

    /**
     * @var string
     * @link http://amundsen.com/media-types/collection/format/#properties-prompt
     */
    protected $prompt;

    /**
     * @param string $href
     * @return \CollectionJson\Entity\Query
     */
    public function setHref($href)
    {
        if (is_string($href) && filter_var($href, FILTER_VALIDATE_URL)) {
            $this->href = $href;
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->href;
    }

    /**
     * @param string $name
     * @return \CollectionJson\Entity\Query
     */
    public function setName($name)
    {
        if (is_string($name)) {
            $this->name = $name;
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $prompt
     * @return \CollectionJson\Entity\Query
     */
    public function setPrompt($prompt)
    {
        if (is_string($prompt)) {
            $this->prompt = $prompt;
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getPrompt()
    {
        return $this->prompt;
    }

    /**
     * @param string $rel
     * @return \CollectionJson\Entity\Query
     */
    public function setRel($rel)
    {
        if (is_string($rel)) {
            $this->rel = $rel;
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getRel()
    {
        return $this->rel;
    }

    /**
     * {@inheritdoc}
     */
    protected function getObjectData()
    {
        $data = [];

        if (!is_null($this->href) && !is_null($this->rel)) {
            $data = $this->getSortedObjectVars();
            $data = $this->filterEmptyArrays($data);
            $data = $this->filterNullValues($data);
        }
        return $data;
    }
}