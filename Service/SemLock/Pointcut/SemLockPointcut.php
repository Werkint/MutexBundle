<?php
namespace Werkint\Bundle\MutexBundle\Service\SemLock\Pointcut;

use Werkint\Bundle\MutexBundle\Service\SemLock\Metadata\MethodMetadata;
use Werkint\Bundle\MutexBundle\Service\SemLock\SemLockAwareInterface;
use JMS\AopBundle\Aop\PointcutInterface;
use Metadata\MetadataFactoryInterface;

/**
 * Ищет сервисы для предоставления информации
 *
 * @author Bogdan Yurov <bogdan@yurov.me>
 */
class SemLockPointcut implements PointcutInterface
{
    const TARGET_CLASS = SemLockAwareInterface::class;

    protected $metadataFactory;

    /**
     * @param MetadataFactoryInterface $metadataFactory
     */
    public function __construct(
        MetadataFactoryInterface $metadataFactory
    ) {
        $this->metadataFactory = $metadataFactory;
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

        throw new \Exception('Class does not have any semlock methods');
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