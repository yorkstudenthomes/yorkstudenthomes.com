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
    }
}
