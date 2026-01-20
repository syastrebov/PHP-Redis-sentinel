<?php

require_once 'SentinelRedisConnection.php';

const TEST_KEY = 'test_key';
const TEST_VALUE = 'Hello Sentinel!';

$sentinel = new SentinelRedisConnection(
    masterName: $_ENV['MASTER_NAME'],
    host: $_ENV['SENTINEL_HOST'],
    port: $_ENV['SENTINEL_PORT'],
);

if (!$master = $sentinel->getMaster()) {
    exit("Failed to get master");
}

// Set and Get a key to verify functionality
try {
    $master->set(TEST_KEY, TEST_VALUE);
    sleep(1);

    if (!$slave = $sentinel->getSlave()) {
        exit("Failed to get slave");
    }

    $retrieved = $slave->get(TEST_KEY);
    if ($retrieved === TEST_VALUE) {
        echo sprintf("Successfully set and retrieved key: %s => %s\n", TEST_KEY, $retrieved);
    } else {
        echo "Failed to verify key value.\n";
    }

    $master->close();
    $slave->close();
} catch (RedisException $e) {
    echo "Redis error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "General error: " . $e->getMessage() . "\n";
}
