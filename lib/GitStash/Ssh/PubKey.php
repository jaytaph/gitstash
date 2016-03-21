<?php

namespace GitStash\Ssh;

class PubKey {

    /** @var string */
    protected $pubKey;

    /** @var array */
    protected $keyParts = array();

    /** @var array */
    protected $fingerprint = array();

    /**
     * @param $pubKey
     */
    function __construct($pubKey)
    {
        $this->pubKey = $pubKey;

        $pubKey = explode(" ", $pubKey);
        $pubKey = base64_decode($pubKey[1]);

        $this->keyParts = array();
        while (! empty($pubKey)) {
            $len = unpack("N1", $pubKey);
            $pubKey = substr($pubKey, 4);

            $data = unpack("A".$len[1], $pubKey);
            $pubKey = substr($pubKey, $len[1]);

            $this->keyParts[] = array('len' => $len[1], 'data' => $data[1]);
        }
    }

    /**
     * @param string $hashAlgo
     * @return mixed
     */
    function getFingerprint($hashAlgo = "md5")
    {
        if (! isset($this->fingerprint[$hashAlgo])) {
            $this->fingerprint[$hashAlgo] = hash($hashAlgo, $this->pubKey);
        }

        return $this->fingerprint[$hashAlgo];
    }

    /**
     * @return int
     */
    function getExponent()
    {
        return hexdec(bin2hex($this->keyParts[1]['data']));
    }

    /**
     * @return string
     */
    function getModulus()
    {
        return bin2hex($this->keyParts[2]['data']);
    }

    /**
     * @return int
     */
    function getKeySize()
    {
        return ((strlen($this->keyParts[2]['data'])-1)*8);
    }

}
