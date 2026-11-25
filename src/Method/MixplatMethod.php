<?php

namespace MixplatClient\Method;

use MixplatClient\Configuration;

class MixplatMethod
{
    /**
     * @return string
     */
    public function getMethod()
    {
        return 'mixplat_method';
    }

    /**
     * @param Configuration $config
     * @return array
     */
    public function getParams($config)
    {
        return array();
    }

    /**
     * @return array
     */
    protected function parseParams()
    {
        $params = array();
        foreach (get_object_vars($this) as $key => $value) {
            $params[$this->decamelize($key)] = $value;
        }

        return $params;
    }

    /**
     * @param string $signatureString
     * @return string
     */
    protected function encryptSignature($signatureString)
    {
        return md5($signatureString);
    }

    protected function decamelize($string)
    {
        return strtolower(preg_replace(array('/([a-z\d])([A-Z])/', '/([^_])([A-Z][a-z])/'), '$1_$2', $string));
    }

}
