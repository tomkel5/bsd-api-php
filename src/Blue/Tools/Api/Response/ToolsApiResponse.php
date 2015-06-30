<?php
namespace Blue\Tools\Api\Response;

use GuzzleHttp\Message\Response;
use GuzzleHttp\Message\ResponseInterface;
use GuzzleHttp\Stream\StreamInterface;

/**
 * Class ToolsApiResponse
 * @package Blue\Tools\Api\Response
 *
 * The ToolsApiResponse is a wrapper class for the normal Guzzle Response class. This allows us to add information
 * about deferred responses.
 */
class ToolsApiResponse implements ResponseInterface {

    /** @var Response */
    private $response;

    public function __construct(ResponseInterface $response) {
        $this->response = $response;
    }


    /**
     * Determine if this response is deferred
     *
     * @return bool
     */
    public function isDeferred() {

        // An HTTP status of 202 indicates that this request was deferred
        return $this->response->getStatusCode() == 202;
    }


    /**
     * Get the deferred key, if this is a deferred response
     *
     * @return string|null
     */
    public function getDeferredKey() {

        if ($this->isDeferred()) {
            return $this->getBody()->getContents();
        }

        return null;
    }




    /**
     * Get a string representation of the message
     *
     * @return string
     */
    public function __toString()
    {
        return $this->response->__toString();
    }

    /**
     * Get the HTTP protocol version of the message
     *
     * @return string
     */
    public function getProtocolVersion()
    {
        return $this->response->getProtocolVersion();
    }

    /**
     * Sets the body of the message.
     *
     * The body MUST be a StreamInterface object. Setting the body to null MUST
     * remove the existing body.
     *
     * @param StreamInterface|null $body Body.
     */
    public function setBody(StreamInterface $body = null)
    {
        $this->response->setBody($body);
    }

    /**
     * Get the body of the message
     *
     * @return StreamInterface|null
     */
    public function getBody()
    {
        return $this->response->getBody();
    }

    /**
     * Gets all message headers.
     *
     * The keys represent the header name as it will be sent over the wire, and
     * each value is an array of strings associated with the header.
     *
     *     // Represent the headers as a string
     *     foreach ($message->getHeaders() as $name => $values) {
     *         echo $name . ": " . implode(", ", $values);
     *     }
     *
     * @return array Returns an associative array of the message's headers.
     */
    public function getHeaders()
    {
        return $this->response->getHeaders();
    }

    /**
     * Retrieve a header by the given case-insensitive name.
     *
     * @param string $header Case-insensitive header name.
     *
     * @return string
     */
    public function getHeader($header)
    {
        return $this->response->getHeader($header);
    }

    /**
     * Retrieves a header by the given case-insensitive name as an array of strings.
     *
     * @param string $header Case-insensitive header name.
     *
     * @return string[]
     */
    public function getHeaderAsArray($header)
    {
        return $this->response->getHeaderAsArray($header);
    }

    /**
     * Checks if a header exists by the given case-insensitive name.
     *
     * @param string $header Case-insensitive header name.
     *
     * @return bool Returns true if any header names match the given header
     *     name using a case-insensitive string comparison. Returns false if
     *     no matching header name is found in the message.
     */
    public function hasHeader($header)
    {
        return $this->response->hasHeader($header);
    }

    /**
     * Remove a specific header by case-insensitive name.
     *
     * @param string $header Case-insensitive header name.
     */
    public function removeHeader($header)
    {
        $this->response->removeHeader($header);
    }

    /**
     * Appends a header value to any existing values associated with the
     * given header name.
     *
     * @param string $header Header name to add
     * @param string $value Value of the header
     */
    public function addHeader($header, $value)
    {
        $this->response->addHeader($header, $value);
    }

    /**
     * Merges in an associative array of headers.
     *
     * Each array key MUST be a string representing the case-insensitive name
     * of a header. Each value MUST be either a string or an array of strings.
     * For each value, the value is appended to any existing header of the same
     * name, or, if a header does not already exist by the given name, then the
     * header is added.
     *
     * @param array $headers Associative array of headers to add to the message
     */
    public function addHeaders(array $headers)
    {
        $this->response->addHeaders($headers);
    }

    /**
     * Sets a header, replacing any existing values of any headers with the
     * same case-insensitive name.
     *
     * The header values MUST be a string or an array of strings.
     *
     * @param string $header Header name
     * @param string|array $value Header value(s)
     */
    public function setHeader($header, $value)
    {
        $this->response->setHeader($header, $value);
    }

    /**
     * Sets headers, replacing any headers that have already been set on the
     * message.
     *
     * The array keys MUST be a string. The array values must be either a
     * string or an array of strings.
     *
     * @param array $headers Headers to set.
     */
    public function setHeaders(array $headers)
    {
        $this->response->setHeaders($headers);
    }

    /**
     * Gets the response Status-Code.
     *
     * The Status-Code is a 3-digit integer result code of the server's attempt
     * to understand and satisfy the request.
     *
     * @return int Status code.
     */
    public function getStatusCode()
    {
        return $this->response->getStatusCode();
    }

    /**
     * Sets the status code of this response.
     *
     * @param int $code The 3-digit integer result code to set.
     */
    public function setStatusCode($code)
    {
        $this->response->setStatusCode($code);
    }

    /**
     * Gets the response Reason-Phrase, a short textual description of the
     * Status-Code.
     *
     * Because a Reason-Phrase is not a required element in response
     * Status-Line, the Reason-Phrase value MAY be null. Implementations MAY
     * choose to return the default RFC 2616 recommended reason phrase for the
     * response's Status-Code.
     *
     * @return string|null Reason phrase, or null if unknown.
     */
    public function getReasonPhrase()
    {
        return $this->response->getReasonPhrase();
    }

    /**
     * Sets the Reason-Phrase of the response.
     *
     * If no Reason-Phrase is specified, implementations MAY choose to default
     * to the RFC 2616 recommended reason phrase for the response's Status-Code.
     *
     * @param string $phrase The Reason-Phrase to set.
     */
    public function setReasonPhrase($phrase)
    {
        $this->response->setReasonPhrase($phrase);
    }

    /**
     * Get the effective URL that resulted in this response (e.g. the last
     * redirect URL).
     *
     * @return string
     */
    public function getEffectiveUrl()
    {
        return $this->response->getEffectiveUrl();
    }

    /**
     * Set the effective URL that resulted in this response (e.g. the last
     * redirect URL).
     *
     * @param string $url Effective URL
     */
    public function setEffectiveUrl($url)
    {
        $this->response->setEffectiveUrl($url);
    }

    /**
     * Parse the JSON response body and return the JSON decoded data.
     *
     * @param array $config Associative array of configuration settings used
     *     to control how the JSON data is parsed. Concrete implementations MAY
     *     add further configuration settings as needed, but they MUST implement
     *     functionality for the following options:
     *
     *     - object: Set to true to parse JSON objects as PHP objects rather
     *       than associative arrays. Defaults to false.
     *     - big_int_strings: When set to true, large integers are converted to
     *       strings rather than floats. Defaults to false.
     *
     *     Implementations are free to add further configuration settings as
     *     needed.
     *
     * @return mixed Returns the JSON decoded data based on the provided
     *     parse settings.
     * @throws \RuntimeException if the response body is not in JSON format
     */
    public function json(array $config = [])
    {
        return $this->response->json($config);
    }

    /**
     * Parse the XML response body and return a \SimpleXMLElement.
     *
     * In order to prevent XXE attacks, this method disables loading external
     * entities. If you rely on external entities, then you must parse the
     * XML response manually by accessing the response body directly.
     *
     * @param array $config Associative array of configuration settings used
     *     to control how the XML is parsed. Concrete implementations MAY add
     *     further configuration settings as needed, but they MUST implement
     *     functionality for the following options:
     *
     *     - ns: Set to a string to represent the namespace prefix or URI
     *     - ns_is_prefix: Set to true to specify that the NS is a prefix rather
     *       than a URI (defaults to false).
     *     - libxml_options: Bitwise OR of the libxml option constants
     *       listed at http://php.net/manual/en/libxml.constants.php
     *       (defaults to LIBXML_NONET)
     *
     * @return \SimpleXMLElement
     * @throws \RuntimeException if the response body is not in XML format
     * @link http://websec.io/2012/08/27/Preventing-XXE-in-PHP.html
     */
    public function xml(array $config = [])
    {
        return $this->response->xml($config);
    }


}