<?php
namespace Tiup;
class TiupException extends \Exception
{
    public function getData(){
        $data = json_decode($this->message, true);
        if(is_array($data)){
            return $data;
        }
        return ['message' => $this->message];
    }
}
