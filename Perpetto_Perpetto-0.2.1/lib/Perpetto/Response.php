<?php

require_once __DIR__ . '/Exception.php';

/**
 * Class Perpetto_Response
 */
class Perpetto_Response
{
    /**
     * @var array
     */
    protected $data = array();

    /**
     * @var string
     */
    protected $error;

    /**
     * @var string
     */
    protected $json;

    /**
     * Perpetto_Response constructor.
     *
     * @param string $content
     * @throws Perpetto_Exception
     */
    public function __construct($content)
    {
        if (empty($content)) {
            throw new Perpetto_Exception('Empty content.');
        }

        $data = json_decode($content, true);

        if (JSON_ERROR_NONE != json_last_error()) {
            throw new Perpetto_Exception(json_last_error_msg());
        }

        $this->json = $content;

        if (array_key_exists('data', $data)) {
            $this->data = $data['data'];
        }

        if (array_key_exists('error', $data)) {
            $this->error = $data['error'];
        } elseif (empty($this->data)) {
            $this->error = 'No data in response.';
        }
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return bool
     */
    public function hasError()
    {
        return !empty($this->error);
    }

    /**
     * @param null $key
     * @return bool
     */
    public function hasData($key = null)
    {
        $hasData = $key && array_key_exists($key, $this->data)
            ? !empty($this->data[$key])
            : !empty($this->data);

        return $hasData;
    }

    /**
     * @param null $key
     * @return mixed
     */
    public function getData($key = null)
    {
        $value = null;

        if (!$key) {
            $value = $this->data;
        } elseif (array_key_exists($key, $this->data)) {
            $value = $this->data[$key];
        } elseif (strpos($key, '/')) {
            $parts = explode('/', $key);

            $data = &$this->data;
            while (!empty($parts)) {
                $part = array_shift($parts);
                $value = is_array($data) && array_key_exists($part, $data) ? $data[$part] : null;
                $data = &$value;
            }
        }

        return $value;
    }

    /**
     * @return string
     */
    public function getJSON()
    {
        $json = $this->json;

        return $json;
    }

    /**
     * @param $method
     * @param $args
     * @return mixed
     * @throws Exception
     */
    public function __call($method, $args)
    {
        switch (substr($method, 0, 3)) {
            case 'get' :
                $key = strtolower(substr($method, 3));
                $data = $this->getData($key);

                return $data;
        }

        throw new Exception("Invalid method " . __METHOD__);
    }

    /**
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getData($key);
    }
}
