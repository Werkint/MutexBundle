<?php
namespace Werkint\Bundle\MutexBundle\Service\SemLock\Pointcut;

use JMS\AopBundle\Aop\PointcutInterface;
use Metadata\MetadataFactoryInterface;
use Werkint\Bundle\MutexBundle\Service\SemLock\Metadata\MethodMetadata;
use Werkint\Bundle\MutexBundle\Service\SemLock\SemLockAwareInterface;

/**
 * Ищет сервисы для предоставления информации
 *
 * @author Bogdan Yurov <bogdan@yurov.me>
 */
class SemLockPointcut implements PointcutInterface
{
    const TARGET_CLASS = SemLockAwareInterface::class;

    protected $metadataFactory;
    protected $isDebug;

    /**
     * @param MetadataFactoryInterface $metadataFactory
     * @param bool                     $isDebug
     */
    public function __construct(
        MetadataFactoryInterface $metadataFactory,
        $isDebug
    ) {
        $this->metadataFactory = $metadataFactory;
        $this->isDebug = $isDebug;
    }


    /**
     * {@inheritdoc}
     */
    public function matchesClass(\ReflectionClass $class)
    {
        if (!$class->isSubclassOf(static::TARGET_CLASS)) {
            return false;
        }

        $metadata = $this->metadataFactory->getMetadataForClass($class->getName());
        foreach ($metadata->methodMetadata as $methodMetadata) {
            if ($methodMetadata instanceof MethodMetadata) {
                return true;
            }
        }

        if ($this->isDebug) {
            throw new \Exception('Class does not have any semlock methods');
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function matchesMethod(\ReflectionMethod $method)
    {
        $metadata = $this->metadataFactory->getMetadataForClass($method->getDeclaringClass()->getName());
        foreach ($metadata->methodMetadata as $methodMetadata) {
            if ($methodMetadata instanceof MethodMetadata && $methodMetadata->name === $method->name) {
                return true;
            }
        }
        return false;
    }
}