<?php
namespace API\Middleware;

class OAuth2Auth extends \Slim\Middleware
{
    public function __construct($root = '')
    {
		$this->root = $root;

        if (!isset($this->app)) {
            $this->app = \Slim\Slim::getInstance();
        }
    }

    public function call()
    {
        $req = $this->app->request();
        $res = $this->app->response();

        if (preg_match(
            '|^' . $this->root . '.*|',
            $req->getResourceUri()
        )) {
        
            // We just need the user
            $authToken = $req->headers('Authorization');

            if (!($authToken && $this->verify($authToken))) {
                $res->status(401);
            }

        }
        
        $this->next->call();
    }
    
    protected function verify($authToken)
    {   
        return true;
    }
}