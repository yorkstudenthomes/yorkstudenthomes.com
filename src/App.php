<?php

namespace Starsquare;

use PDO;

class App extends \Slim\Slim {

    public function __construct(array $options) {
        parent::__construct($options);

        $this->init();
    }

    protected function init() {
        $this->container->singleton('db', function () {
            if (!($db = $this->config('db'))) {
                throw new \Exception('Mising database config');
            }

            $url = parse_url($db);
            $dsn = 'mysql:host=' . $url['host'] . ';dbname=' . substr($url['path'], 1);

            $db = new PDO($dsn, $url['user'], $url['pass']);
            $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return $db;
        });

        $this->hook('slim.before.dispatch', function () {
            $this->view->setData('site', $this->config('site'));
            $this->view->setData('request', $this->router->getCurrentRoute()->getParams());
        });

        $this->container->singleton('auth', function () {
            return function ($route) {
                session_start();

                if (!isset($_SESSION['auth'])) {
                    $this->notFound();
                }
            };
        });
    }

    protected function message($key) {
        $messages = $this->config('messages');
        return $messages[$key];
    }

    public function login() {
        if ($this->request->isPost()) {
            $config = $this->config('auth');

            $valid = $config['passwords'];
            if (is_string($valid)) {
                $valid = preg_split('/[^a-f0-9]/', $valid);
            }

            if (!is_array($valid)) {
                throw new \Exception('Missing auth config');
            }

            $field = (isset($config['field']) ? $config['field'] : 'password');
            $password = $this->request->post($field);

            if (!in_array(md5($password), $valid)) {
                $this->flashNow('error', $this->message('INVALID_PASSWORD'));
                return false;
            }


            session_start();
            $_SESSION['auth'] = true;
            session_write_close();
            return true;
        }
    }

    public function logout() {
        session_start();
        $_SESSION = array();
        session_destroy();
    }
}
