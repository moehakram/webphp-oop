<?php
namespace MA\PHPQUICK\Http\Responses;

class JsonResponse extends Response
{
    public function __construct($content = [], int $statusCode = 200, array $headers = [])
    {
        parent::__construct($content, $statusCode, $headers);

        $this->headers->set('Content-Type', ResponseHeaders::CONTENT_TYPE_JSON);
    }

    public function setContent($content)
    {
        if ($content instanceof \ArrayObject) {
            $content = $content->getArrayCopy();
        }

        $json = json_encode($content);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Failed to JSON encode content: ' . json_last_error_msg());
        }

        parent::setContent($json);
    }
}