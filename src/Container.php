<?php

namespace MA\PHPQUICK;

use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface;
use MA\PHPQUICK\Exception\Container\NotFoundException;
use MA\PHPQUICK\Exception\Container\ContainerException;

class Container implements ContainerInterface
{
    private array $bindings = [];
    private array $instances = [];

    /**
     * Mendaftarkan layanan dengan closure atau factory.
     *
     * @param string $id
     * @param callable $resolver
     */
    public function bind(string $id, callable $resolver): void
    {
        $this->bindings[$id] = $resolver;
    }

    /**
     * Mendaftarkan layanan sebagai singleton.
     *
     * @param string $id
     * @param callable $resolver
     */
    public function singleton(string $id, callable $resolver): void
    {
        $this->bindings[$id] = $resolver;
        $this->instances[$id] = null; // Menandakan bahwa ini singleton
    }

    /**
     * Mendaftarkan instance yang sudah ada.
     *
     * @param string $id
     * @param mixed $instance
     */
    public function instance(string $id, $instance): void
    {
        $this->instances[$id] = $instance;
    }

    /**
     * Mengambil layanan dari container.
     *
     * @param string $id
     * @return mixed
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
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

    /**
     * Memeriksa apakah layanan ada di container.
     *
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        return isset($this->bindings[$id]) || isset($this->instances[$id]);
    }

    /**
     * Memodifikasi layanan yang sudah ada dalam container.
     *
     * @param string $id
     * @param callable $callback
     */
    public function extend(string $id, callable $callback): void
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

    /**
     * Resolve dependencies and create an instance of a class.
     *
     * @param string $class
     * @return object
     * @throws ContainerExceptionInterface
     */
    protected function resolveClass(string $class)
    {
        try {
            $reflector = new \ReflectionClass($class);
            $constructor = $reflector->getConstructor();

            if (! $constructor) {
                return new $class;
            }

            $parameters = $constructor->getParameters();
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
                $parameters
            );

            return $reflector->newInstanceArgs($dependencies);
        } catch (\ReflectionException $e) {
            throw new ContainerException(sprintf('Error resolving class "%s": %s', $class, $e->getMessage()), 0, $e);
        }
    }
}
