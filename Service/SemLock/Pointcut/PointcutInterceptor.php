<?php
namespace Werkint\Bundle\MutexBundle\Service\SemLock\Pointcut;

use CG\Proxy\MethodInterceptorInterface;
use CG\Proxy\MethodInvocation;
use Werkint\Bundle\MutexBundle\Service\SemLock\Metadata\MethodMetadata;
use Metadata\MetadataFactoryInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * Предоставляет информацию
 *
 * @author Bogdan Yurov <bogdan@yurov.me>
 */
class PointcutInterceptor implements
    MethodInterceptorInterface
{
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

        $fp = fopen('/tmp/SEMLOCK_' . sha1($key), 'w');
        flock($fp, LOCK_UN);
        $ret = $invocation->proceed();
        fclose($fp);

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