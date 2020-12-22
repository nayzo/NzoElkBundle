<?php

namespace Nzo\ElkBundle\Encryptor;

use Minwork\Helper\Arr;
use Nzo\UrlEncryptorBundle\Encryptor\Encryptor;

class ElkEncryptor
{
    /**
     * @var Encryptor
     */
    private $encryptor;
    /**
     * @var array|null
     */
    private $logEncryptorConfig;

    /**
     * ElkEncryptor constructor.
     *
     * @param Encryptor $encryptor
     * @param array|null $logEncryptorConfig
     */
    public function __construct(Encryptor $encryptor, $logEncryptorConfig)
    {
        $this->encryptor = $encryptor;
        $this->logEncryptorConfig = $logEncryptorConfig;
    }

    public function resolveContextEncryption(&$context)
    {
        if (empty($context)) {
            return;
        }

        if (empty($this->logEncryptorConfig['fields'])) {
            return;
        }

        foreach ($this->logEncryptorConfig['fields'] as $field) {
            if (Arr::has($context, $field)) {
                $data = Arr::get($context, $field);
                if (!\is_array($data)) {
                    $context = Arr::set($context, $field, $this->encryptor->encrypt($data));
                }
            }
        }
    }
}
