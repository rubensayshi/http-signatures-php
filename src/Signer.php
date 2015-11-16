<?php

namespace HttpSignatures;

use Psr\Http\Message\MessageInterface;

class Signer
{
    /** @var Key */
    private $key;

    /** @var HmacAlgorithm */
    private $algorithm;

    /** @var HeaderList */
    private $headerList;

    /**
     * @param Key $key
     * @param HmacAlgorithm $algorithm
     * @param HeaderList $headerList
     */
    public function __construct($key, $algorithm, $headerList)
    {
        $this->key = $key;
        $this->algorithm = $algorithm;
        $this->headerList = $headerList;
    }

    /**
     * @param MessageInterface $message
     * @return MessageInterface
     */
    public function sign(MessageInterface $message)
    {
        $signatureParameters = $this->signatureParameters($message);
        $message = $message->withAddedHeader("Signature", $signatureParameters->string());
        $message = $message->withAddedHeader("Authorization", "Signature " . $signatureParameters->string());
        return $message;
    }

    /**
     * @param MessageInterface $message
     * @return SignatureParameters
     */
    private function signatureParameters($message)
    {
        return new SignatureParameters(
            $this->key,
            $this->algorithm,
            $this->headerList,
            $this->signature($message)
        );
    }

    /**
     * @param MessageInterface $message
     * @return Signature
     */
    private function signature($message)
    {
        return new Signature(
            $message,
            $this->key,
            $this->algorithm,
            $this->headerList
        );
    }
}
