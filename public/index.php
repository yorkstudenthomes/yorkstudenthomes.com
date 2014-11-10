<?php

require '../vendor/autoload.php';

$app = new Starsquare\App(array(
    'view' => new \Slim\Views\Twig(),
    'templates.path' => '../views',
    'db' => getenv('CLEARDB_DATABASE_URL'),
    'site' => array(
        'name' => 'York Student Homes',
        'tagline' => 'A great place to find a great place to live!',
    ),
));

$app->hook('slim.before.dispatch', function () use ($app) {
    $stmt = $app->db->query('select setting, value from setting');
    $app->view->setData('setting', $stmt->fetchAll(PDO::FETCH_KEY_PAIR));
});

$app->get('/', function () use ($app) {
    $app->render('index.html', array('test' => 'world'));
});

$app->run();
