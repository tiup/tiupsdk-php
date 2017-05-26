<?php
namespace Tiup;

/**
* 
*/
class AccessToken
{
	protected $value;

	protected $expiresAt;

	protected $uid;

	protected $type;

    protected $scope;

	public function __construct($data)
	{
		if(!is_array($data)){
            $data = json_decode($data, true);
        }

		$this->value = $data['access_token'];
        if(isset($data['uid'])){
            $this->uid = $data['uid'];
        }
        $this->type = $data['token_type'];
        if(isset($data['scope'])){
            $this->scope = $data['scope'];
        }
        
        if(isset($data['expires_in'])){
            $expiresAt = time() + $data['expires_in'];
        }elseif(isset($data['expires_at'])){
            $expiresAt = $data['expires_at'];
        }
       
        if ($expiresAt) {
            $this->expiresAt = $expiresAt;
        }
	}

    /**
     * Checks the expiration of the access token.
     *
     * @return boolean|null
     */
    public function isExpired()
    {
        return $this->getExpiresAt() < time();
    }

    /**
     * Getter for expiresAt.
     *
     * @return \DateTime|null
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    public function getAuthorization(){
        return $this->type.' '.$this->value;
    }

    public function __toString(){
        $data = array(
            'uid' => $this->uid,
            'token_type' => $this->type,
            'access_token' => $this->value,
            'expires_at' => $this->expiresAt,
            );  
        return json_encode($data);
    }
}