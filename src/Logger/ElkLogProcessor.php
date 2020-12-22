<?php

namespace Nzo\ElkBundle\Logger;

use Nzo\ElkBundle\Encryptor\ElkEncryptor;
use Symfony\Component\HttpFoundation\RequestStack;

class ElkLogProcessor
{
    const UUID_HEADER = 'x-uuid';

    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var ElkEncryptor
     */
    private $elkEncryptor;
    /**
     * @var string
     */
    private $appName;
    /**
     * @var string
     */
    private $appEnvironment;

    /**
     * ElkLogProcessor constructor.
     *
     * @param RequestStack $requestStack
     * @param ElkEncryptor $elkEncryptor
     * @param string $appName
     * @param string $appEnvironment
     */
    public function __construct(RequestStack $requestStack, ElkEncryptor $elkEncryptor, $appName, $appEnvironment)
    {
        $this->requestStack = $requestStack;
        $this->elkEncryptor = $elkEncryptor;
        $this->appName = $appName;
        $this->appEnvironment = $appEnvironment;
    }

    /**
     * @param array $record
     * @return array
     */
    public function processRecord(array $record)
    {
        $record['extra']['app_name'] = $this->appName;
        $record['extra']['app_environment'] = $this->appEnvironment;

        $this->elkEncryptor->resolveContextEncryption($record['context']);

        if (!$request = $this->requestStack->getCurrentRequest()) {
            return $record;
        }

        if ($uuid = $request->headers->get(self::UUID_HEADER)) {
            $record['extra']['uuid'] = $uuid;
        }

        return $record;
    }
}
