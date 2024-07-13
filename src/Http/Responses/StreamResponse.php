<?php
namespace MA\PHPQUICK\Http\Responses;
/**
 * Defines a stream response whose contents are only output once
 */
class StreamResponse extends Response
{
    protected $streamCallback = null;
    protected bool $hasSentStream = false;

    public function __construct(
        callable $streamCallback = null,
        int $statusCode = 200,
        array $headers = []
    ) {
        parent::__construct('', $statusCode, $headers);

        if ($streamCallback !== null) {
            $this->setStreamCallback($streamCallback);
        }
    }
    
    public function sendContent()
    {
        if (!$this->hasSentStream && $this->streamCallback !== null) {
            ($this->streamCallback)();
            $this->hasSentStream = true;
        }
    }

    public function setContent($content)
    {
        if ($content !== null && $content !== '') {
            throw new \LogicException('Cannot set content in a stream response');
        }
    }

    public function setStreamCallback(callable $streamCallback)
    {
        $this->streamCallback = $streamCallback;
    }
}