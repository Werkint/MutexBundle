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
    private $key;
    /**
     * @var int
     */
    private $waitTimeout;

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

    /**
     * @return int
     */
    public function getWaitTimeout()
    {
        return $this->waitTimeout;
    }

    /**
     * @param int $waitTimeout
     * @return $this
     */
    public function setWaitTimeout($waitTimeout)
    {
        $this->waitTimeout = $waitTimeout;
        return $this;
    }
}