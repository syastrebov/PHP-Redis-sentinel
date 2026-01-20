<?php

class SentinelRedisConnection
{
    private RedisSentinel $sentinel;

    public function __construct(
        private readonly string $masterName,
        string $host,
        int $port
    ) {
        $this->sentinel = new RedisSentinel([
            'host' => $host,
            'port' => $port,
            'connectTimeout' => 5,
            'retryInterval' => 1,
            'readTimeout' => 1,
        ]);
    }

    public function getMaster(): ?Redis
    {
        $attempts = 0;

        do {
            try {
                $master = $this->sentinel->master($this->masterName);
                if (!empty($master['ip']) && !empty($master['port'])) {
                    $redis = new Redis();
                    $redis->connect(
                        host: $master['ip'],
                        port: $master['port'],
                        timeout: 1,
                    );

                    echo "Connected to master {$master['ip']}:{$master['port']}\n";

                    return $redis;
                }
            } catch (RedisException $e) {
                echo "Unable to connect master {$this->masterName}: {$e->getMessage()}\n";
                $attempts++;
                if ($attempts > 5) {
                    break;
                }
            }

            sleep(1);
        } while (true);

        return null;
    }

    public function getSlave(): ?Redis
    {
        try {
            $slaves = $this->sentinel->slaves($this->masterName);
        } catch (RedisException $e) {
            echo "Unable to get slaves: {$e->getMessage()}\n";
            return null;
        }

        shuffle($slaves);

        foreach ($slaves as $slave) {
            try {
                $redis = new Redis();
                $redis->connect(
                    host: $slave['ip'],
                    port: $slave['port'],
                    timeout: 1,
                );

                echo "Connected to slave {$slave['ip']}:{$slave['port']}\n";

                return $redis;
            } catch (RedisException $e) {
                echo "Unable to connect slave {$slave['ip']}:{$slave['port']}: {$e->getMessage()}\n";
            }
        }

        return null;
    }
}
