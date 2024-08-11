<?php

namespace MA\PHPQUICK;

use Closure;
use MA\PHPQUICK\Contracts\ExtendedContainerInterface;
use MA\PHPQUICK\Exception\Container\NotFoundException;
use MA\PHPQUICK\Exception\Container\ContainerException;

class Container implements ExtendedContainerInterface
{
    public static ?Container $instance = null;

    private array $bindings = [];
    private array $instances = [];

    public function bind(string $id, Closure $resolver): void
    {
        $this->bindings[$id] = $resolver;
    }

    public function bindMany(array $bindings): void
    {
        foreach ($bindings as $id => $resolver) {
            $this->bind($id, $resolver);
        }
    }

    public function singleton(string $id, Closure $resolver): void
    {
        $this->bindings[$id] = $resolver;
        $this->instances[$id] = null; // Menandakan bahwa ini singleton
    }


    public function instance(string $id, mixed $instance): void
    {
        $this->instances[$id] = $instance;
    }

    public function get(string $id)
    {
        // Jika layanan sudah ada sebagai instance, kembalikan instance tersebut
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        // Jika layanan terdaftar sebagai binding, buat instance baru
        if (isset($this->bindings[$id])) {
            try {
                $instance = $this->bindings[$id];

                if ($instance instanceof \Closure) {
                    $instance = $instance($this);
                }

                // Jika ini adalah singleton, simpan instance-nya
                if (array_key_exists($id, $this->instances)) {
                    $this->instances[$id] = $instance;
                }

                return $instance;
            } catch (\Throwable $e) {
                throw new ContainerException(sprintf('Error resolving service "%s":  %s', $id, $e->getMessage()), 0, $e);
            }
        }

        // Jika layanan tidak ditemukan, coba resolve class
        if (class_exists($id)) {
            return $this->resolveClass($id);
        }

        throw new NotFoundException(sprintf('Service "%s" not found in container.', $id));
    }

    public function has(string $id): bool
    {
        return isset($this->bindings[$id]) || isset($this->instances[$id]);
    }

    public function extend(string $id, Closure $callback): void
    {
        if (array_key_exists($id, $this->instances)) {
            $this->instances[$id] = $callback($this->instances[$id]);
        } elseif (isset($this->bindings[$id])) {
            $existing = $this->bindings[$id];
            $this->bindings[$id] = function() use ($existing, $callback) {
                return $callback($existing($this));
            };
        }
    }

    protected function resolveClass(string $class)
    {
        try {
            $reflector = new \ReflectionClass($class);
            $constructor = $reflector->getConstructor();

            if (! $constructor) {
                return new $class;
            }

            $dependencies = array_map(
                function (\ReflectionParameter $parameter) use($class) {
                    $name = $parameter->getName();
                    $type = $parameter->getType();

                    if ($type === null) {
                        throw new ContainerException(sprintf('Failed to resolve class "%s" because param "%s" is missing a type hint', $class, $name));
                    }

                    if ($type instanceof \ReflectionUnionType) {
                        throw new ContainerException(sprintf('Failed to resolve class "%s" because of union type for param "%s"', $class, $name));
                    }

                    if ($type instanceof \ReflectionNamedType && ! $type->isBuiltin()) {
                        return $this->get($type->getName());
                    }

                    throw new ContainerException(sprintf('Failed to resolve class "%s" because invalid param "%s"', $class, $name));
                }, 
                $constructor->getParameters()
            );

            return $reflector->newInstanceArgs($dependencies);
        } catch (\ReflectionException $e) {
            throw new ContainerException(sprintf('Error resolving class "%s": %s', $class, $e->getMessage()), 0, $e);
        }
    }

    public function call($callable, array $parameters = []): mixed
    {
        if (is_array($callable)) {
            // Jika callable adalah array, berarti ini adalah method dari class/instance
            $reflection = new \ReflectionMethod($callable[0], $callable[1]);
            $instance = is_object($callable[0]) ? $callable[0] : $this->get($callable[0]);
            $dependencies = $this->resolveParameters($reflection, $parameters);
            return $reflection->invokeArgs($instance, $dependencies);
        } elseif ($callable instanceof \Closure) {
            // Jika callable adalah Closure, buat ReflectionFunction
            $reflection = new \ReflectionFunction($callable);
            $dependencies = $this->resolveParameters($reflection, $parameters);
            return $reflection->invokeArgs($dependencies); // Tidak butuh instance
        } elseif (is_string($callable)) {
            // Jika callable adalah string, itu mungkin nama function
            $reflection = new \ReflectionFunction($callable);
            $dependencies = $this->resolveParameters($reflection, $parameters);
            return $reflection->invokeArgs($dependencies); // Tidak butuh instance
        } else {
            throw new \InvalidArgumentException('Unsupported callable type');
        }
    }
    
    private function resolveParameters(\ReflectionFunctionAbstract $reflection, array $parameters = [])
    {
        $dependencies = [];
        foreach ($reflection->getParameters() as $parameter) {
            $name = $parameter->getName();
            $type = $parameter->getType();
    
            if (isset($parameters[$name])) {
                $dependencies[] = $parameters[$name];
            } elseif ($type && !$type->isBuiltin()) {
                $dependencies[] = $this->get($type->getName());
            } elseif ($parameter->isDefaultValueAvailable()) {
                $dependencies[] = $parameter->getDefaultValue();
            } else {
                throw new ContainerException("Unable to resolve dependency {$name}");
            }
        }
    
        return $dependencies;
    }
}
