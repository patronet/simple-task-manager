<?php

namespace PatroNet\Core\Request;

use PatroNet\Core\Content\Source as ContentSource;


/**
 * Response object
 */
class Response
{
    
    const APPLICABLE_HANDLED = "handled";
    const APPLICABLE_CONTINUE = "continue";
    const APPLICABLE_BREAK = "break";
    
    protected $oContentSource;
    
    protected $httpStatus;
    
    protected $headers;
    
    protected $infos;
    
    protected $applicableStatus;
    
    protected $complete;
    
    /**
     * Response object
     *
     * @param \PatroNet\Core\Content\Source $oContentSource
     * @param int $httpStatus
     * @param array $headers
     * @param array $headers
     */
    public function __construct(
        ContentSource $oContentSource,
        $httpStatus = 200,
        $headers = [],
        $infos = [],
        $applicableStatus = self::APPLICABLE_HANDLED,
        $complete = true
    ) {
        $this->oContentSource = $oContentSource;
        $this->httpStatus = $httpStatus;
        $this->headers = $headers;
        $this->infos = $infos;
        $this->applicableStatus = $applicableStatus;
        $this->complete = $complete;
    }
    
    /**
     * Provides the response content
     *
     * @return \PatroNet\Core\Content\Source
     */
    public function getContentSource() {
        return $this->oContentSource;
    }
    
    /**
     * Gets the HTTP status code
     *
     * @return int
     */
    public function getHttpStatus() {
        return $this->httpStatus;
    }
    
    /**
     * Provides the HTTP headers
     *
     * Status header not included in this array.
     *
     * @return array
     */
    public function getHeaders() {
        return $this->headers;
    }
    
    /**
     * Provides infos associated with the response object
     *
     * @return array
     */
    public function getInfos() {
        return $this->infos;
    }
    
    /**
     * Gets the applicable status of the request
     *
     * Runflow in a router chain directed by this information.
     *
     * @return string
     */
    public function getApplicableStatus() {
        return $this->applicableStatus;
    }
    
    /**
     * Checks whether the response's data is complete
     *
     * If a HTML response is not complete, then the system may add a layout frame.
     *
     * @return boolean
     */
    public function isComplete() {
        return $this->complete;
    }
    
    protected function createHttpStatusHeader($httpStatus)
    {
        // TODO
        $knownStatuses = [
            200 => "OK",
            202 => "Accepted",
            404 => "Not found",
            405 => "Method Not Allowed",
            500 => "Internal Server Error",
        ];
        $fallbackStatus = 500;
        if (array_key_exists($httpStatus, $knownStatuses)) {
            $httpStatus = $fallbackStatus;
        }
        return "HTTP/1.1 200 " . $knownStatuses[$httpStatus];
    }
    
    /**
     * Sends the request to the client
     */
    public function send()
    {
        header($this->createHttpStatusHeader($this->httpStatus));
        foreach ($this->getHeaders() as $header) {
            header($header);
        }
        $this->oContentSource->flush();
    }
    
}
