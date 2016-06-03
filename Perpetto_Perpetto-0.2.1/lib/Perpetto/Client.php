<?php

require_once __DIR__ . '/Response.php';

/**
 * Class Perpetto_Client
 */
class Perpetto_Client
{
    const API_HOST = 'https://%s.api.perpetto.com';
    const API_PATH_INFO = '/v0/info';
    const API_PATH_SLOTS = '/v0/info/slots';
    const API_PATH_UPDATE_PRODUCT = '/v0/update/product';

    /**
     * Whether to use CURL over file_get_contents
     *
     * @var bool
     */
    protected $useCurl = false;

    /**
     * Client account ID used in requests
     *
     * @var string
     */
    protected $accountId = null;

    /**
     * Client secret used in requests
     *
     * @var string
     */
    protected $clientSecret = null;

    /**
     * Perpetto_Client constructor.
     *
     * @param $accountId
     * @param $clientSecret
     */
    public function __construct($accountId, $clientSecret)
    {
        $this->accountId = $accountId;
        $this->clientSecret = $clientSecret;
        $this->useCurl = extension_loaded('curl');
    }

    /**
     * @return Perpetto_Response
     */
    public function loadInfo()
    {
        $url = $this->getBaseUrl() . self::API_PATH_INFO;
        $response = $this->request($url);

        return $response;
    }

    /**
     * @return Perpetto_Response
     */
    public function loadSlots()
    {
        $url = $this->getBaseUrl() . self::API_PATH_SLOTS;
        $response = $this->request($url);

        return $response;
    }

    /**
     * @param Perpetto_Product $product
     * @return Perpetto_Response
     */
    public function updateProduct(Perpetto_Product $product)
    {
        $url = $this->getBaseUrl() . self::API_PATH_UPDATE_PRODUCT;

        $post = array(
            'item' => $product->toArray(),
        );

        $response = $this->request($url, array(), $post);

        return $response;
    }

    /**
     * @return string
     */
    protected function getBaseUrl()
    {
        $url = sprintf(self::API_HOST, $this->accountId);

        return $url;
    }

    /**
     * @param $url
     * @param array $params
     * @return Perpetto_Response
     */
    protected function request($url, array $params = array(), array $post = array())
    {
        $params = array_merge($params, array(
            'secret' => $this->clientSecret,
        ));

        $response = $this->useCurl
            ? $this->requestCurl($url, $params, $post)
            : $this->requestRaw($url, $params, $post);

        return $response;
    }

    /**
     * @param $url
     * @param array $params
     * @param array $post
     * @return Perpetto_Response
     */
    protected function requestCurl($url, array $params = array(), array $post = array())
    {
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_AUTOREFERER => true,
            CURLOPT_CONNECTTIMEOUT => 120,
            CURLOPT_TIMEOUT => 120,
        );

        $query = http_build_query($params);
        $url .= '?' . $query;

        $ch = curl_init($url);
        curl_setopt_array($ch, $options);
        if (!empty($post)) {
            $fields = http_build_query($post);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        }
        $content = curl_exec($ch);
        curl_close($ch);

        $response = new Perpetto_Response($content);

        return $response;
    }

    /**
     * @param $url
     * @param array $params
     * @param array $post
     * @return Perpetto_Response
     */
    protected function requestRaw($url, array $params = array(), array $post = array())
    {
        $query = http_build_query($params);
        $url .= '?' . $query;

        $flags = null;
        $context = null;

        if (!empty($post)) {
            $options = array(
                'http' => array(
                    'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method' => 'POST',
                    'content' => http_build_query($post)
                )
            );
            $flags = false;
            $context = stream_context_create($options);
        }

        $content = file_get_contents($url, $flags, $context);

        $response = new Perpetto_Response($content);

        return $response;
    }

}
