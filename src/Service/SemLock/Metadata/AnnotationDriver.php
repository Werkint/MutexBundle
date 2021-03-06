<?php
namespace Werkint\Bundle\MutexBundle\Service\SemLock\Metadata;

use Doctrine\Common\Annotations\Reader;
use Metadata\Driver\DriverInterface;
use Metadata\MergeableClassMetadata;
use Werkint\Bundle\MutexBundle\Service\SemLock\Annotation\SemLock;

/**
 * TODO: write "AnnotationDriver" info
 *
 * @author Bogdan Yurov <bogdan@yurov.me>
 */
class AnnotationDriver implements
    DriverInterface
{
    const ANNOTATION_CLASS = SemLock::class;

    protected $reader;

    /**
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * {@inheritdoc}
     */
    public function loadMetadataForClass(\ReflectionClass $class)
    {
        $classMetadata = new MergeableClassMetadata($class->getName());

        foreach ($class->getMethods() as $method) {
            $annotation = $this->reader->getMethodAnnotation(
                $method,
                static::ANNOTATION_CLASS
            );

            if ($annotation instanceof SemLock) {
                $propertyMetadata = new MethodMetadata($class->getName(), $method->getName());

                if (!$annotation->getKey()) {
                    throw new \Exception('Lock key not specified');
                }

                $propertyMetadata->setKey($annotation->getKey());
                $propertyMetadata->setWaitTimeout($annotation->getWaitTimeout());
                $classMetadata->addMethodMetadata($propertyMetadata);
            }
        }

        return $classMetadata;
    }
} 