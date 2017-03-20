<?php

namespace App\Cache;

use Predis\Client as Predis;

class RedisAdapter implements CacheInterface
{
	protected $client;

	public function __construct(Predis $client)
	{
		$this->client = $client;
	}

	public function get($key)
	{
		return $this->client->get($key);
	}

	public function put($key, $value, $minutes = null)
	{
		if ($minutes === null) {
			return $this->forever($key, $value);
		}

		return $this->client->setex($key, (int) max(1, $minutes * 60), $value);
	}

	public function forever($key, $value)
	{
		return $this->client->set($key, $value);
	}

	public function remember($key, $minutes = null, callable $callback)
	{
		if (!is_null($value = $this->get($key))) {
			//print("GOT FROM CACHE");
			return $value;
		}

		$this->put($key, $value = $callback(), $minutes);
		//print("RAN FUNCTION & CACHED");
		return $value;
	}

	public function forget($key)
	{
		return $this->client->del($key);
	}
}
