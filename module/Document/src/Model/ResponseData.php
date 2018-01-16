<?php
/**
 * Created by PhpStorm.
 * User: after8
 * Date: 1/14/18
 * Time: 9:29 PM
 */

namespace Document\Model;


use Zend\Stdlib\ResponseInterface;

class ResponseData
{

    private $response;
    private $message;
    private $status;
    private $data = null;

    public function __construct($response = null)
    {
        $this->response = $response;
    }

    /**
     * @param ResponseInterface $response
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Data should be String as Json object
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    public function setFailMessage($message){
        $this->status = 'failed';
        $this->message = $message;
    }

    public function setSuccessMessage($message){
        $this->status = 'success';
        $this->message = $message;
    }

    public function getAsJsonObject(){
        $response = '{';
        $response .= '"status":"'.$this->status.'"';
        $response .= ',"message":"'.$this->message.'"';
        if($this->data != null)
            $response .= ',"data":'.$this->data;
        $response .= '}';
        return $response;
    }

    public function getResponseAsJsonContentType(){
        if(!($this->response instanceof ResponseInterface)){
            throw new \Exception(sprintf('Call %s::setResponse($response), $response should be instance of ResponseInterface ',__CLASS__));
        }
        $this->response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $this->response->setContent($this->getAsJsonObject());
        return $this->response;
    }

}