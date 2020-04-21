<?php
declare(strict_types=1);

namespace App\Controller;

use SimpleXMLElement;
use App\Decorator\DecoratorManager;
use App\Controller\RequestData;

class LessonController
{
    private $isProdaction;
    private $memcacheCache;
    private $nullCache;
    private $kibanaLogger;
    private $fileLogger;
    private $logger;
    private $cache;

    /**
     * @var \App\Controller\RequestData
     */
    private $requestData;

    /**
     * LessonController constructor.
     * @param RequestData $requestData
     */
    public function __construct(RequestData $requestData)
    {
        $this->requestData = $requestData;
    }

    /**
     * @param int $categoryId
     * @param string $responseType
     */
    public function getLessonsByCategoryId(int $categoryId, string $responseType = 'json')
    {
        if (!preg_match('/[0-9]{5}/', $categoryId) || $categoryId <= 0) {
            echo "Error! Category id must be integer and be more than 0";
            exit;
        }

        if ($this->isProdaction) {
            $this->logger = $this->kibanaLogger;
            $this->cache = $this->memcacheCache;
        } else {
            $this->logger = $this->fileLogger;
            $this->cache = $this->nullCache;
        }

        $decoratorManager = new DecoratorManager($this->requestData->getUser(), $this->requestData->getPassword(), $this->requestData->getHost(), $this->logger);
        $decoratorManager->setCache($this->cache);

        $data = $decoratorManager->getResponse(["categoryId" => $categoryId, '']);

        if ($data != []) {
            if ($responseType == 'xml') {
                $xml = new SimpleXMLElement('<root/>');
                array_walk_recursive($data, array ($xml, 'addChild'));
                echo $xml->asXML();
                exit;
            } elseif ($responseType == 'json') {
                echo json_encode($data);
                exit;
            }
        }

        echo "Error! Some problem with getting data from request";
        exit;
    }

}
