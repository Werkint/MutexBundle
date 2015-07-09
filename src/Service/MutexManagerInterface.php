<?php
namespace Werkint\Bundle\MutexBundle\Service;

/**
 * Управляет блокировками в рамках приложения
 *
 * @author Bogdan Yurov <bogdan@yurov.me>
 */
interface MutexManagerInterface
{
    /**
     * @param string   $class
     * @param int|null $waitTime
     * @return boolean
     */
    public function lock($class, $waitTime = null);

    /**
     * @param string $class
     * @return boolean
     */
    public function unlock($class);
}