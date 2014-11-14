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
    $app->render('index.html');
});

$app->get('/homes/', function () use ($app) {
    $homes = $app->db->query('select slug as url, house_address as text from description order by house_order')->fetchAll();

    $app->render('homes.html', array(
        'title' => 'Homes',
        'homes' => $homes,
    ));
});

$app->get('/homes/:home/', function ($slug) use ($app) {
    $stmt = $app->db->prepare('select house_id, house_address as address, rented, type, description, feature from description inner join feature using (house_id) where slug = ? order by feature_id');
    $stmt->execute(array($slug));
    $features = $stmt->fetchAll();
    $home = $features[0];

    $stmt = $app->db->prepare('select room_price as value, room_description as description from bill where house_id = ? order by room_price');
    $stmt->execute(array($home['house_id']));
    $bills = $stmt->fetchAll();

    $app->render('home.html', array(
        'home'     => $home,
        'bills'    => $bills,
        'plans'    => is_file(__DIR__ . "/plans/$slug.pdf"),
        'features' => array_map(function ($row) {
            return $row['feature'];
        }, $features),
    ));
});

$app->get('/accommodation-for-students/', function () use ($app) {
    $app->render('afs.html');
});

$app->get('/contact/', function () use ($app) {
    $app->render('contact.html');
});

$app->get('/maps/', function () use ($app) {
    $homes = $app->db->query('select house_address, postcode from description order by house_order')->fetchAll(PDO::FETCH_FUNC, function ($address, $postcode) {
        return array(
            'url'  => 'https://www.google.co.uk/maps/place/' . urlencode("$address, York $postcode"),
            'text' => $address,
        );
    });
    $app->render('maps.html', array(
        'title' => 'Maps',
        'homes' => $homes,
    ));
});

$app->run();
