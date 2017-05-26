<?php
namespace Tiup;

use Curl;
/**
* 
*/
class Tiup
{
	protected $client_id;

	protected $client_secret;

	protected $auth_host = "https://open.tiup.cn/";

	protected $api_host = "https://tiupapis.cn/";

	//应用的 token 调用内部接口使用
	protected $app_token;

	public function __construct($config = array())
	{
		$this->auth_host = $config['auth_host'];
		$this->api_host = $config['api_host'];
		$this->client_id = $config['client_id'];
		$this->client_secret = $config['client_secret'];
	}

	/**
	 * 获取登录链接
	 * @param  string $redirectUrl 登录回调地址
	 * @param  array  $scope       默认userinfo
	 * @return string    登录链接           
	 */
	public function getAuthorizationUrl($redirectUrl, $state = '', $scope = array('userinfo','profile')){

		$config = array(
			'client_id' => $this->client_id,
			'response_type' => 'code',
			'scope' => implode(' ', $scope),
			'state' => $state,
			'redirect_uri' => $redirectUrl
			);
		
		$query =  http_build_query($config);
		$query = str_replace('+', '%20', $query);
		$authorizationUrl = $this->auth_host . 'oauth2/authorize?' . $query;
		return $authorizationUrl;
	}

	/**
	 * 回调时根据code获取 AccessToken
	 * @return AccessToken AccessToken
	 */
	public function getAccessToken(){
		if (!$code = $this->getInput('code')) {
            return null;
        }

        #todo 验证state
        // $this->validateCsrf();
        // $this->resetCsrf();

        return $this->getAccessTokenFromCode($code);
	}

	/**
	 * 根据code获取token
	 * @param  [type] $code [description]
	 * @return [type]       [description]
	 */
	public function getAccessTokenFromCode($code){
		
		$params = array(
			'code' => $code,
			'grant_type' => 'authorization_code'
			);

		return $this->_getAccessToken($params);
	}

	//获取token
	private function _getAccessToken($params){

		$curl = new Curl\Curl();
		$url = $this->auth_host . 'oauth2/token';
		$curl->setBasicAuthentication($this->client_id, $this->client_secret);
		$curl->post($url, $params);
		if ($curl->error) {
			throw new TiupException("curl error ".$curl->error_message.' '.$curl->response, 1);
		}

		return new AccessToken($curl->response);
	}

	/**
	 * 获取个人信息
	 * @param  AccessToken $accessToken [description]
	 * @return [type]                   [description]
	 */
	public function me(AccessToken $accessToken){
		$curl = new Curl\Curl();
		$user = $this->get('apis/oauth2/v1/userinfo',array(), $accessToken);
		return $user;
	}

	/**
	 * TiUP API GET请求
	 * @param  [type] $endpoint [description]
	 * @param  [type] $params   [description]
	 * @return [type]           [description]
	 */
	public function get($endpoint, $params = array(), $accessToken = ''){
		$ret = $this->request($endpoint, $params,'get', $accessToken, false);
		return $ret;
	}

	public function patch($endpoint, $params, $accessToken = '', $payload = true){
		$ret = $this->request($endpoint, $params, 'patch', $accessToken, $payload);
		return $ret;
	}

	private function request($endpoint, $params, $method = "get", $accessToken = '', $payload = true){
		$url = $this->api_host.$endpoint;
		if(empty($accessToken)){
			$accessToken = $this->getAppToken();
		}
		$authorization = $accessToken->getAuthorization();
		
		$curl = new Curl\Curl;
		$curl->setHeader('Authorization', $authorization);
		if($payload && !empty($params)){
			$params = json_encode($params);
		}
		$curl->$method($url, $params, $payload);
		
		if ($curl->error) {
			throw new TiupException("curl error ".$curl->error_message.' '.$curl->response, 1);

		}
		return json_decode($curl->response, true);
	}

	public function getAppToken($scope = null){
		if($this->app_token == null){
			$params = array(
				'grant_type' => 'client_credentials'
				);
			$this->app_token = $this->_getAccessToken($params);
		}
		
		return $this->app_token;
	}

	private function getInput($key)
    {
        return isset($_GET[$key]) ? $_GET[$key] : null;
    }

}
