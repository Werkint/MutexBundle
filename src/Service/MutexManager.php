<?php
namespace Werkint\Bundle\MutexBundle\Service;

/**
 * @see    MutexManagerInterface
 *
 * @author Bogdan Yurov <bogdan@yurov.me>
 */
class MutexManager implements
    MutexManagerInterface
{
    protected $locksDirectory;

    /**
     * @param string $locksDirectory
     */
    public function __construct(
        $locksDirectory
    ) {
        $this->locksDirectory = $locksDirectory;
    }

    protected $locked = [];

    public function getAllLocks()
    {
        return $this->locked;
    }

    /**
     * @inheritdoc
     */
    public function lock($class, $waitTime = null)
    {
        if (isset($this->locked[$class])) {
            $this->locked[$class]['count']++;
            return true;
        }

        $lockName = $this->getLockName($class);
        $fp = fopen($lockName, 'w');

        $locked = false;
        if (!$waitTime) {
            flock($fp, LOCK_EX);
            $locked = true;
        } else {
            while ($waitTime > 0) {
                if (flock($fp, LOCK_EX | LOCK_NB)) {
                    $locked = true;
                    break;
                }
                sleep(1);
                $waitTime -= 1;
            }
        }

        if (!$locked) {
            fclose($fp);
            return false;
        }

        ftruncate($fp, 0);
        fwrite($fp, 'Locked at: ' . microtime(true));
        $this->locked[$class] = [
            'res'   => $fp,
            'count' => 1,
        ];

        return true;
    }

    /**
     * @inheritdoc
     */
    public function unlock($class, $forced = false)
    {
        if (!isset($this->locked[$class])) {
            return false;
        }

        $row = $this->locked[$class];
        if ($forced || --$row['count'] === 0) {
            flock($row['res'], LOCK_UN);
            fclose($row['res']);
            unset($this->locked[$class]);
        }

        return true;
    }

    public function unlockAll()
    {
        foreach ($this->locked as $class => $obj) {
            $this->unlock($class, true);
        }
    }

    /**
     * @param string $class
     * @return string
     */
    protected function getLockName($class)
    {
        return $this->locksDirectory . '/' . sha1($class) . '.lockfile';
    }
}