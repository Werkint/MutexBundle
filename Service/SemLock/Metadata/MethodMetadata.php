<?php
namespace Werkint\Bundle\MutexBundle\Service\SemLock\Metadata;

use Metadata\MethodMetadata as BaseMethodMetadata;

/**
 * TODO: write "MethodMetadata" info
 *
 * @author Bogdan Yurov <bogdan@yurov.me>
 */
class MethodMetadata extends BaseMethodMetadata
{
    /**
     * @var string
     */
    protected $key;

    // -- Accessors ---------------------------------------

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     * @return $this
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }
} 