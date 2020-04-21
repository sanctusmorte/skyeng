<?php
declare(strict_types=1);

namespace App\Integration;

class DataProvider
{
    // обновить phpDoc
    private $host;
    // обновить phpDoc
    private $user;
    // обновить phpDoc
    private $password;


    /**
     * @param string $host
     * @param string $user
     * @param string $password
     */
    // обновить phpDoc
    public function __construct($host, $user, $password)
    {
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
    }

    // название перменной не раскрывает суть
    // обновить phpDoc
    public function get(array $request)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->host . '?' . http_build_query($request));
        curl_setopt($ch, CURLOPT_USERPWD, $this->user . ":" . $this->password);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        if(curl_errno($ch)){
            throw new \Exception(curl_error($ch));
        }
        curl_close($ch);

        // закомментированный участок кода не допустим!

//        // --- dump ---
//        echo '<pre>';
//        echo __FILE__ . chr(10);
//        echo __METHOD__ . chr(10);
//        var_dump($output);
//        echo '</pre>';
//        exit;
//        // --- // ---

        // выставить второй параметр как true для получения ассоциативного массива вместо объекта
        return json_decode($output);
    }
}
// Согласно стандарту PSR-2 закрывающий тег должен отсутствовать, если в файле только PHP код
?>