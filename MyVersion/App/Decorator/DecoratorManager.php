<?php
declare(strict_types=1);

namespace App\Decorator;

use Psr\Cache\CacheItemPoolInterface;

class DecoratorManager extends \App\Integration\DataProvider
{
    const CACE_SUFFIX = 'lesons';

    /**
     * @var null
     */
    private $cache = null;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * DecoratorManager constructor.
     * @param string $host
     * @param string $user
     * @param string $password
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(string $host, string $user, string $password, \Psr\Log\LoggerInterface $logger)
    {
        parent::__construct($host, $user, $password);
        $this->logger = $logger;
    }

    /**
     * @param CacheItemPoolInterface $cache
     */
    public function setCache(CacheItemPoolInterface $cache) {
        $this->cache = $cache;
    }

    /**
     * @param array $input
     * @return array|mixed
     */
    public function getResponse(array $input)
    {
        try {
            $cache_key = self::CACE_SUFFIX .json_encode($input);
            $cacheItem = $this->cache->getItem($cache_key);
            if ($cacheItem->isHit()) return $cacheItem->get();

            $result = parent::sendCurlRequest($input);

            $cacheItem
                ->set($result)
                ->expiresAt(
                    (new \DateTime())->modify('+1 day')
                );

            return $result;
        } catch (\Exception $e) {
            $this->logger->critical("Thrown exception ".$e->getMessage()."");
        }

        return [];
    }
}
