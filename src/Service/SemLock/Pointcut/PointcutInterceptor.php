<?php
namespace Werkint\Bundle\MutexBundle\Service\SemLock\Pointcut;

use CG\Proxy\MethodInterceptorInterface;
use CG\Proxy\MethodInvocation;
use Metadata\MetadataFactoryInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Werkint\Bundle\MutexBundle\Service\MutexManagerInterface;
use Werkint\Bundle\MutexBundle\Service\SemLock\Metadata\MethodMetadata;

/**
 * Предоставляет информацию
 *
 * @author Bogdan Yurov <bogdan@yurov.me>
 */
class PointcutInterceptor implements
    MethodInterceptorInterface
{
    protected $metadataFactory;
    protected $mutexManager;

    public function __construct(
        MetadataFactoryInterface $metadataFactory,
        MutexManagerInterface $mutexManager
    ) {
        $this->metadataFactory = $metadataFactory;
        $this->mutexManager = $mutexManager;
    }

    /**
     * {@inheritdoc}
     */
    public function intercept(MethodInvocation $invocation)
    {
        $metadata = $this->findMethodMetadata(
            get_class($invocation->object),
            $invocation->reflection->name
        );

        $key = $metadata->getKey();
        if ($key[0] === '=') {
            $lang = new ExpressionLanguage();

            $attrs = [];
            foreach ($invocation->reflection->getParameters() as $i => $param) {
                $attrs[$param->name] = $invocation->arguments[$i];
            }
            $key = $lang->evaluate(substr($key, 1), $attrs);
        }

        try {
            $this->mutexManager->lock($key, $metadata->getWaitTimeout());
            $ret = $invocation->proceed();
            $this->mutexManager->unlock($key);
        } catch (\Exception $e) {
            $this->mutexManager->unlock($key);
            throw $e;
        }

        return $ret;
    }

    /**
     * @param string $class
     * @param string $method
     * @throws \Exception
     * @return MethodMetadata|null
     */
    protected function findMethodMetadata($class, $method)
    {
        $metadata = $this->metadataFactory->getMetadataForClass($class);
        foreach ($metadata->methodMetadata as $methodMetadata) {
            /** @var MethodMetadata $methodMetadata */
            if ($methodMetadata->name !== $method) {
                continue;
            }

            if ($methodMetadata instanceof MethodMetadata) {
                return $methodMetadata;
            }
            break;
        }

        throw new \Exception('Wrong class specified: ' . $class);
    }
} 