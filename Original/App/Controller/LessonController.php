<?php
declare(strict_types=1);

namespace App\Controller;

use SimpleXMLElement;
use App\Decorator\DecoratorManager;

class LessonController
{
    // переделать область видимости этих переменных на private
    public $isProdaction;

    // host, user, password вынесем в отдельный подключаемый класс
    // возможно потребуется получение этих данных например с бд
    public $host;
    public $user;
    public $password;

    public $memcacheCache;
    public $nullCache;
    public $kibanaLogger;
    public $fileLogger;

    /**
     * @param int $cat
     * @param string $rt
     */
    // название метода должно раскрывать суть
    // название переменной $cat недопустимо, не раскрывается суть
    // название переменной $rt недопустимо, не раскрывается суть
    public function action($cat, $rt = null)
    {
        if (!preg_match('/[0-9]{5}/', $cat) || $cat <= 0) {
            echo "error"; // не раскрыта суть ошибка
            exit;
        }

        // зачем передавать в метод значение переменной $rt по умолчанию как null
        // чтобы потом проверять на null и ставить значение как 'json'?
        /// по умолчанию сделаем переменную $rt в этом методе как 'json'
        /// если в этот метод не будет передаваться второй параметр, $rt будет по умолчанию как 'json'
        /// если надо будет получить XML передадим второй аргумент для метода
        if (is_null($rt))
        {
            $rt = 'json';
        }

        // сделаем обертку для kibanaLogger и fileLogger
        // сделаем обертку для memcacheCache и nullCache
        if ($this->isProdaction) {
            // согласно стандарту PSR наименование переменных должно быть одинаковым в рамках одной структуры
            // нужно переделать на $decoratorManager
            $decorator_manager = new DecoratorManager($this->user, $this->password, $this->host, $this->kibanaLogger);
            $decorator_manager->setCache($this->memcacheCache);
        } else {
            $decorator_manager = new DecoratorManager($this->user, $this->password, $this->host, $this->fileLogger);
            $decorator_manager->setCache($this->nullCache);
        }

        // использовать [] вместо array()
        $data = $decorator_manager->getResponse(array("categoryId" => $cat, ''));
        if ($data != []) {
            // вывод ответа вынести в отдельный метод
            if ($rt == 'xml') {
                $xml = new SimpleXMLElement('<root/>');
                array_walk_recursive($data, array ($xml, 'addChild'));
                echo $xml->asXML();
                exit;
            } elseif ($rt == 'json') {
                echo json_encode($data);
                exit;
            }
        }

        echo "error"; // не раскрыта суть ошибка
        exit;
    }
}
// Согласно стандарту PSR-2 закрывающий тег должен отсутствовать, если в файле только PHP код
?>