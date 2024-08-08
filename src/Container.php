<?php

namespace MA\PHPQUICK;

class Container
{
    private $bindings = [];
    private $instances = [];

    /**
     * Mendaftarkan layanan dengan closure atau factory.
     *
     * @param string $key
     * @param callable $resolver
     */
    public function bind($key, callable $resolver)
    {
        $this->bindings[$key] = $resolver;
    }

    /**
     * Mendaftarkan layanan sebagai singleton.
     *
     * @param string $key
     * @param callable $resolver
     */
    public function singleton($key, callable $resolver)
    {
        $this->bindings[$key] = $resolver;
        $this->instances[$key] = null; // Menandakan bahwa ini singleton
    }

    /**
     * Mendaftarkan instance yang sudah ada.
     *
     * @param string $key
     * @param mixed $instance
     */
    public function instance($key, $instance)
    {
        $this->instances[$key] = $instance;
    }

    /**
     * Mengambil layanan dari container.
     *
     * @param string $key
     * @return mixed
     * @throws \Exception
     */
    public function resolve($key)
    {
        // Jika layanan sudah ada sebagai instance, kembalikan instance tersebut
        if (isset($this->instances[$key])) {
            return $this->instances[$key];
        }

        // Jika layanan terdaftar sebagai binding, buat instance baru
        if (isset($this->bindings[$key])) {
            $instance = $this->bindings[$key]($this);

            // Jika ini adalah singleton, simpan instance-nya
            if (array_key_exists($key, $this->instances)) {
                $this->instances[$key] = $instance;
            }

            return $instance;
        }

        throw new \Exception("Service {$key} not found in container.");
    }

    /**
     * Memeriksa apakah layanan ada di container.
     *
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        return isset($this->bindings[$key]) || isset($this->instances[$key]);
    }

    /**
     * Memodifikasi layanan yang sudah ada dalam container.
     *
     * @param string $key
     * @param callable $callback
     */
    public function extend($key, callable $callback)
    {
        if (isset($this->instances[$key])) {
            $this->instances[$key] = $callback($this->instances[$key]);
        } elseif (isset($this->bindings[$key])) {
            $existing = $this->bindings[$key];
            $this->bindings[$key] = function() use ($existing, $callback) {
                return $callback($existing());
            };
        }
    }
}
