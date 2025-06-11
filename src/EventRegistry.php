<?php

declare(strict_types=1);

namespace Menumbing\EventStream;

use Hyperf\Di\Annotation\AnnotationCollector;
use Menumbing\EventStream\Annotation\ConsumedEvent;
use Menumbing\EventStream\Exception\EventNameConflictedException;
use Menumbing\EventStream\Exception\EventNotFoundException;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class EventRegistry
{
    protected array $events = [];

    public function init(): void
    {
        $classes = AnnotationCollector::getClassesByAnnotation(ConsumedEvent::class) ?? [];

        /**
         * @var string $class
         * @var ConsumedEvent $annotation
         */
        foreach ($classes as $class => $annotation) {
            $name = $annotation->name;
            if ($this->has($name)) {
                throw new EventNameConflictedException($name);
            }

            $this->events[$name] = $class;
        }
    }

    public function getClassByName(string $name): string
    {
        if (!$this->has($name)) {
            throw new EventNotFoundException(sprintf('Event "%s" not found.', $name));
        }

        return $this->events[$name];
    }

    public function has(string $name): bool
    {
        return array_key_exists($name, $this->events);
    }
}
