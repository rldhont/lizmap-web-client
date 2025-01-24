<?php

namespace Lizmap\Request;

use Kevinrob\GuzzleCache\CacheEntry;
use Kevinrob\GuzzleCache\Storage\CacheStorageInterface;

class RequestCacheStorage implements CacheStorageInterface
{
    /**
     * @var string jCache profile
     */
    protected $profile;

    public function __construct(string $profile)
    {
        $this->profile = $profile;
    }

    public function fetch($key)
    {
        try {
            $cache = unserialize(\jCache::get($key, $this->profile));
            if ($cache instanceof CacheEntry) {
                return $cache;
            }
        } catch (\Exception $ignored) {
            return null;
        }

        return null;
    }

    public function save($key, CacheEntry $data)
    {
        try {
            $lifeTime = $data->getTTL();
            if ($lifeTime === 0) {
                return \jCache::set(
                    $key,
                    serialize($data),
                    null,
                    $this->profile
                );
            }
            if ($lifeTime > 0) {
                return \jCache::set(
                    $key,
                    serialize($data),
                    $lifeTime,
                    $this->profile
                );
            }
        } catch (\Exception $ignored) {
            // No fail if we can't save it the storage
        }

        return false;
    }

    public function delete($key)
    {
        return \jCache::delete($key, $this->profile);
    }
}
