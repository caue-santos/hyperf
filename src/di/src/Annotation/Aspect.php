<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace Hyperf\Di\Annotation;

use Doctrine\Instantiator\Instantiator;
use Hyperf\Di\Aop\AroundInterface;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class Aspect extends AbstractAnnotation
{
    public function collectClass(string $className): void
    {
        $this->collect($className);
    }

    public function collectProperty(string $className, ?string $target): void
    {
        $this->collect($className);
    }

    protected function collect(string $className)
    {
        if (class_exists($className)) {
            // Create the aspect instance without invoking their constructor.
            $instantitor = new Instantiator();
            $instance = $instantitor->instantiate($className);
            switch ($instance) {
                case $instance instanceof AroundInterface:
                    $classes = property_exists($instance, 'classes') ? $instance->classes : [];
                    $annotations = property_exists($instance, 'annotations') ? $instance->annotations : [];
                    $priority = property_exists($instance, 'priority') ? $instance->priority : null;
                    AspectCollector::setAround($className, $classes, $annotations, $priority);
                    break;
            }
        }
    }
}
