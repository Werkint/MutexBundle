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
    protected $key;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->key = isset($data['key']) ? $data['key'] : null;
    }

    // -- Accessors ---------------------------------------

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }
} 