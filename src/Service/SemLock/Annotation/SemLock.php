<?php
namespace Werkint\Bundle\MutexBundle\Service\SemLock\Annotation;

use Doctrine\ORM\Mapping\Annotation;

/**
 * Отмечает метод, для которого нужен логгер
 *
 * @author Bogdan Yurov <bogdan@yurov.me>
 *
 * @Annotation
 * @Target("METHOD")
 */
class SemLock
{
    const DEFAULT_TIMEOUT = 40;

    /**
     * @var int
     */
    private $waitTimeout;
    /**
     * @var string|null
     */
    private $key;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->key = isset($data['key']) ? $data['key'] : null;
        $this->waitTimeout = isset($data['waitTimeout']) ? $data['waitTimeout'] : static::DEFAULT_TIMEOUT;
    }

    // -- Accessors ---------------------------------------

    /**
     * @return string|null
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return int|null
     */
    public function getWaitTimeout()
    {
        return $this->waitTimeout;
    }
}