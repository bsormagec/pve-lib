<?php
/**
 *
 * Created by PhpStorm.
 * User: gerwood
 * Date: 16/02/14
 * Time: 6:43 PM
 */
namespace Proxmox;

use Guzzle\Http\Client;
use Guzzle\Http\Message\EntityEnclosingRequest;
use Guzzle\Http\Message\Response;
use Guzzle\Plugin\Cookie\Cookie;
use Guzzle\Plugin\Cookie\CookiePlugin;
use Guzzle\Plugin\Cookie\CookieJar\ArrayCookieJar;
/**
 * Provides a wrapper service for managing the main calls to and from the api.
 * Class Service
 * @package Proxmox
 */
class Service {

    private $host;
    private $user;
    private $pass;
    private $realm;
    private $domain;
    private $base = "/api2/json";
    private $authData;

    private $authCookieName = "PVEAuthCookie";

    private $client;
    private $cookieJar;
    private $cookies;

    function __construct() {
        $this->cookieJar = new ArrayCookieJar();
        $this->cookies = new CookiePlugin($this->cookieJar);
        $this->authData = array();
    }

    function connect() {
        $this->client = new Client($this->host);
        $this->client->addSubscriber($this->cookies);
        /** @var EntityEnclosingRequest $response */
        $request = $this->client->post($this->base."/access/ticket",null,array(
            "username"=>$this->user,
            "realm"=>$this->realm,
            "password"=>$this->pass
        ));

        $request->getCurlOptions()->set(CURLOPT_SSL_VERIFYHOST, false);
        $request->getCurlOptions()->set(CURLOPT_SSL_VERIFYPEER, false);

        $response = $request->send();
        $result = $response->json();
        $this->authData = $result['data'];
        $cookie = new Cookie();
        $cookie->setDomain($this->domain);
        $cookie->setName($this->authCookieName);
        $cookie->setValue($this->authData['ticket']);
        $this->cookieJar->add($cookie);
    }

    function getAuth() {
            return $this->authData;
    }

    /**
     * @return mixed
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param mixed $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * @return mixed
     */
    public function getRealm()
    {
        return $this->realm;
    }

    /**
     * @param mixed $realm
     */
    public function setRealm($realm)
    {
        $this->realm = $realm;
    }

    private function _request($url,$data) {

    }

    /**
     * @return mixed
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param mixed $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getPass()
    {
        return $this->pass;
    }

    /**
     * @param mixed $pass
     */
    public function setPass($pass)
    {
        $this->pass = $pass;
    }

    /**
     * @return string
     */
    public function getBase()
    {
        return $this->base;
    }

    /**
     * @param string $base
     */
    public function setBase($base)
    {
        $this->base = $base;
    }

    /**
     * @return mixed
     */
    public function getAuthData()
    {
        return $this->authData;
    }

    /**
     * @param mixed $authData
     */
    public function setAuthData($authData)
    {
        $this->authData = $authData;
    }


} 