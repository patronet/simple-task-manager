<?php

namespace PatroNet\Core\Request;

use PatroNet\Core\Content\Source as ContentSource;
use PatroNet\Core\Content\StaticSource;
use PatroNet\Core\Request\Response;


/**
 * Response builder
 */
class ResponseBuilder
{
    
    protected $oContentSource;
    
    protected $httpStatus;
    
    protected $headers;
    
    protected $infos;
    
    protected $applicableStatus;
    
    protected $complete;
    
    /**
     * @param \PatroNet\Core\Request\Response $oResponse
     */
    public function __construct(Response $oResponse = null)
    {
        if (is_null($oResponse)) {
            $this->reset();
        } else {
            $this->initResponse($oResponse);
        }
    }
    
    /**
     * Resets the builder
     *
     * @return self
     */
    public function reset()
    {
        $this->oContentSource = new StaticSource("");
        $this->httpStatus = 200;
        $this->headers = [];
        $this->infos = [];
        $this->applicableStatus = Response::APPLICABLE_HANDLED;
        $this->complete = true;
        return $this;
    }
    
    /**
     * Sets the response' content source
     *
     * @param \PatroNet\Core\Content\Source $oContentSource
     * @return self
     */
    public function setContentSource(ContentSource $oContentSource)
    {
        $this->oContentSource = $oContentSource;
        return $this;
    }
    
    /**
     * Sets the response' HTTP status
     *
     * @param int $httpStatus
     * @return self
     */
    public function setHttpStatus($httpStatus)
    {
        $this->httpStatus = $httpStatus;
        return $this;
    }
    
    /**
     * Sets the response' content
     *
     * @param string|\PatroNet\Core\Content\Source|mixed $content
     * @return self
     */
    public function setContent($content)
    {
        $oContentSource = $content;
        if (!$oContentSource instanceof ContentSource) {
            $oContentSource = new StaticSource("" . $oContentSource);
        }
        $this->setContentSource($oContentSource);
        return $this;
    }
    
    /**
     * Sets the response' header list
     *
     * @param string[] $headers
     * @return self
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
        return $this;
    }
    
    /**
     * Adds a header to the response
     *
     * @param string $header
     * @return self
     */
    public function addHeader($header)
    {
        $this->headers[] = $header;
        return $this;
    }
    
    /**
     * Adds multiple headers to the response
     *
     * @param string[] $headers
     * @return self
     */
    public function addHeaders(array $headers)
    {
        foreach ($headers as $header) {
            $this->addHeader($header);
        }
        return $this;
    }
    
    /**
     * Sets the additional informations of the response
     *
     * @param string[string] $infos
     * @return self
     */
    public function setInfos(array $infos)
    {
        $this->infos = $infos;
        return $this;
    }
    
    /**
     * Adds additional information to the response
     *
     * @param string $name
     * @return self
     */
    public function addInfo($name, $value)
    {
        $this->infos[$name] = $value;
        return $this;
    }
    
    /**
     * Adds additional informations to the response
     *
     * @param string[string] $infos
     * @return self
     */
    public function addInfos(array $infos)
    {
        foreach ($infos as $name => $value) {
            $this->addInfo($name, $value);
        }
        return $this;
    }
    
    /**
     * Sets the applicable status of the response
     *
     * @param string $applicableStatus
     * @return self
     */
    public function setApplicableStatus($applicableStatus)
    {
        $this->applicableStatus = $applicableStatus;
        return $this;
    }
    
    /**
     * Sets complete status of the response
     *
     * @param boolean $complete
     * @return self
     */
    public function setComplete($complete)
    {
        $this->complete = $complete;
        return $this;
    }
    
    /**
     * Initializes from an existing response
     *
     * @param boolean $complete
     * @return self
     */
    public function initResponse(Response $oResponse)
    {
        $this->oContentSource = $oResponse->getContentSource();
        $this->httpStatus = $oResponse->getHttpStatus();
        $this->headers = $oResponse->getHeaders();
        $this->infos = $oResponse->getInfos();
        $this->applicableStatus = $oResponse->getApplicableStatus();
        $this->complete = $oResponse->isComplete();
        return $this;
    }
    
    /**
     * Initializes a redirecting response
     *
     * @param string $url
     * @return self
     */
    public function initRedirect($url)
    {
        $this->reset();
        $url = "" . $url;
        if (strlen($url) == 0) {
            $url = $this->getMainUrl();
        } elseif ($url[0] == "/" && !preg_match('#^//#', $url)) {
            $url = $this->getMainUrl() . substr($url, 1);
        }
        $this->addHeader("Location: " . $url);
        return $this;
    }
    
    /**
     * Initializes a plain text response
     *
     * @param \PatroNet\Core\Content\Source|string $content
     * @return self
     */
    public function initText($content = "")
    {
        $this->reset();
        $this->addHeader("Content-type: text/plain; charset=utf-8");
        $this->setContent($content);
        return $this;
    }
    
    /**
     * Initializes a HTML page response
     *
     * @param \PatroNet\Core\Content\Source|string $content
     * @return self
     */
    public function initHtml($content = "")
    {
        $this->reset();
        $this->addHeader("Content-type: text/html; charset=utf-8");
        $this->setContent($content);
        return $this;
    }
    
    /**
     * Initializes an incomplete HTML content response
     *
     * @param \PatroNet\Core\Content\Source|string $content
     * @return self
     */
    public function initHtmlIncomplete($content = "")
    {
        $this->initHtml($content);
        $this->setComplete(false);
        return $this;
    }
    
    /**
     * Initializes a JSON response
     *
     * @param mixed $data
     * @return self
     */
    public function initJson($data = [])
    {
        $this->reset();
        $this->addHeader("Content-type: application/json; charset=utf-8");
        $this->setContent(json_encode($data));
        return $this;
    }
    
    
    /**
     * Initializes a Http Exception response
     *
     * @param \PatroNet\Core\Request\HttpException $oHttpException
     * @return self
     */
    public function initHttpException($oHttpException)
    {
        $this->reset();
        $this->addHeader("Content-type: text/plain; charset=utf-8");
        $this->setContent($oHttpException->getMessage());
        return $this;
    }
    
    
    /**
     * Builds the response
     *
     * @return \PatroNet\Core\Request\Response
     */
    public function build()
    {
        return new Response(
            $this->oContentSource,
            $this->httpStatus,
            $this->headers,
            $this->infos,
            $this->applicableStatus,
            $this->complete
        );
    }
    
    // FIXME!
    protected function getMainUrl()
    {
        return "http" . (empty($_SERVER["HTTPS"]) ? "" : "s") . "://" . $_SERVER["HTTP_HOST"] . "/";
    }
    
}
