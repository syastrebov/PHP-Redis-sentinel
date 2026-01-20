## Install

~~~bash
docker compose up -d
~~~

## Configuration

`down-after-milliseconds` in Redis Sentinel is a crucial configuration setting defining how long (in milliseconds) a master instance must be unreachable (no ping replies) before a Sentinel flags it as suspected down (SDOWN) and starts the failover process for quorum.

`failover-timeout` in Redis Sentinel is a crucial configuration parameter (set in milliseconds) that defines various timeouts and waiting periods during the failover process.

`parallel-syncs` in Redis Sentinel controls the number of replicas (slaves) that can be reconfigured to sync with a new master simultaneously after a failover.

## Testing

### 1. Open bash console in another terminal window.

~~~bash
docker exec -it php-redis-sentinel-php-1 bash
~~~

### 2. Run the script.

~~~bash
root@b1a3fcf50d8f:/var/www# php src/test.php
Connected to master 172.31.0.4:6379
Connected to slave 172.31.0.3:6379
Successfully set and retrieved key: test_key => Hello Sentinel!
~~~

### 3. Stop master redis container and run the script again.

~~~bash
docker container stop php-redis-sentinel-redis-master-1
~~~

~~~bash
root@b1a3fcf50d8f:/var/www# php src/test.php 
Unable to connect master mymaster: Connection timed out
Unable to connect master mymaster: Connection timed out
Connected to master 172.31.0.4:6379
Connected to slave 172.31.0.3:6379
Successfully set and retrieved key: test_key => Hello Sentinel!
~~~
