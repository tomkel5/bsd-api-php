<?php
namespace Blue\Framework\Api\Response;

interface Response {

    public function getContent();
    public function getStatusCode();

}