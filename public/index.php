<?php

require '../vendor/autoload.php';

use Aptoma\Twig\Extension\MarkdownExtension,
    Starsquare\Twig\Extension\MarkdownEngine;

function images($home) {
    return array_map(function ($path) {
        return basename($path, '_thumb.jpg');
    }, glob(__DIR__ . "/images/$home/*_thumb.jpg"));
}

$app = new Starsquare\App(array(
    'view' => new \Slim\Views\Twig(),
    'templates.path' => '../views',
    'db' => getenv('CLEARDB_DATABASE_URL'),
    'auth' => array(
        'passwords' => getenv('YSH_ADMIN_PASSWORDS'),
    ),
    'site' => array(
        'name' => 'York Student Homes',
        'tagline' => 'A great place to find a great place to live!',
    ),
    'messages' => array(
        'INVALID_PASSWORD' => 'The password was incorrect.',
    ),
));

$app->view->parserExtensions[] = new MarkdownExtension(new MarkdownEngine());

$app->hook('slim.before.dispatch', function () use ($app) {
    $stmt = $app->db->query('select setting, value from setting');
    $app->view->setData('setting', $stmt->fetchAll(PDO::FETCH_KEY_PAIR));

    // get homes
    $navigation = $app->db->query('select slug, house_address from description order by house_order')->fetchAll(PDO::FETCH_FUNC, function ($url, $address) {
        return array(
            'url' => "/homes/$url/",
            'text' => str_replace('Street', 'St', $address)
        );
    });

    // add on other links
    $navigation = array_merge($navigation, array(
        array('url' => '/maps',    'text' => 'Maps'),
        array('url' => '/contact', 'text' => 'Contact'),
    ));

    // find selected
    array_walk($navigation, function (&$link) use ($app) {
        if (strpos($app->request->getPath(), $link['url']) === 0) {
            $link['selected'] = true;
        }
    });

    $app->view->setData('navigation', $navigation);
});

$app->get('/', function () use ($app) {
    $available = $app->db->query('select count(*) from description where rented = 0')->fetchColumn();
    $app->render('index.html', array(
        'all_rented' => ($available == 0),
    ));
});

$app->get('/homes/', function () use ($app) {
    $homes = $app->db->query('select slug as url, house_address as text from description order by house_order')->fetchAll();

    $app->render('homes.html', array(
        'title' => 'Homes',
        'homes' => $homes,
    ));
});

$app->group('/homes/:home', function () use ($app) {
    $app->get('/', function ($slug) use ($app) {
        // description / features
        $stmt = $app->db->prepare('select house_id, house_address as address, rented, type, description, feature from description inner join feature using (house_id) where slug = ? order by feature_id');
        $stmt->execute(array($slug));
        $features = $stmt->fetchAll();

        if (!count($features)) {
            $app->notFound();
        }

        $home = $features[0];

        // room bills
        $stmt = $app->db->prepare('select room_price as value, room_description as description from bill where house_id = ? order by room_price');
        $stmt->execute(array($home['house_id']));
        $bills = $stmt->fetchAll();

        // energy ratings
        $stmt = $app->db->prepare('select * from epc where house_id = ?');
        $stmt->execute(array($home['house_id']));
        $ratings = $stmt->fetch();

        $app->render('home.html', array(
            'title'    => $home['address'],
            'home'     => $home,
            'bills'    => $bills,
            'plans'    => is_file(__DIR__ . "/plans/$slug.pdf"),
            'features' => array_map(function ($row) {
                return $row['feature'];
            }, $features),
            'ratings'  => array(
                'eer' => array('current' => $ratings['eer_current'], 'potential' => $ratings['eer_potential']),
                'eir' => array('current' => $ratings['eir_current'], 'potential' => $ratings['eir_potential']),
            ),
            'images' => images($slug),
        ));
    });

    $app->get('/images/:rating', function ($home, $rating) use ($app) {
        $stmt = $app->db->prepare('select house_address, epc.* from description inner join epc using(house_id) where slug = ?');
        $stmt->execute(array($home));
        $details = $stmt->fetch();

        $app->render('image.html', array(
            'type'     => strtoupper($rating) . ' Graph',
            'address'  => $details['house_address'],
            'image'    => "/epc/$rating/" . $details[$rating . '_current'] . '/' . $details[$rating . '_potential'],
        ));
    })->conditions(array('rating' => 'e[ei]r'));

    $app->get('/images/:image', function ($home, $image) use ($app) {
        $stmt = $app->db->prepare('select house_address from description where slug = ?');
        $stmt->execute(array($home));
        $address = $stmt->fetchColumn();

        $images = images($home);
        $key = array_search($image, $images);

        if (!$address || $key === false) {
            $app->notFound();
        }

        $app->render('image.html', array(
            'type'     => 'Image',
            'address'  => $address,
            'previous' => isset($images[$key - 1]) ? $images[$key - 1] : false,
            'next'     => isset($images[$key + 1]) ? $images[$key + 1] : false,
        ));
    });
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

$app->map('/admin', $app->auth, function () use ($app) {
    if ($app->request->isPost()) {
        $stmt = $app->db->prepare('update setting set value = ? where setting = ?');
        $settings = $app->request->params();

        foreach ($settings as $key => $value) {
            $stmt->execute(array($value, $key));
        }

        // update view data
        $app->view->setData('setting', $settings);
    }

    $homes = $app->db->query('select slug as url, house_address as text from description order by house_order')->fetchAll();

    $app->render('admin/settings.html', array(
        'homes' => $homes,
        'admin' => array(
            'link' => '/admin/logout',
            'text' => 'Log out',
        ),
    ));
})->via('GET', 'POST');

$app->map('/admin/homes/:home', $app->auth, function ($slug) use ($app) {
    $stmt = $app->db->prepare('select house_id, house_address as address, rented, type, description, feature from description inner join feature using (house_id) where slug = ? order by feature_id');
    $stmt->execute(array($slug));
    $features = $stmt->fetchAll();

    if (!count($features)) {
        $app->notFound();
    }

    $home = $features[0];

    $app->render('admin/home.html', array(
        'home'  => $home,
        'title' => $home['address'],
    ));
})->via('GET', 'POST');

$app->map('/admin/login', function () use ($app) {
    if ($app->login()) {
        $app->redirect('/admin');
    }

    $app->render('login.html', array(
        'title' => 'Log In',
    ));
})->via('GET', 'POST');

$app->get('/admin/logout', function () use ($app) {
    $app->logout();
    $app->redirect('/');
});

$app->get('/epc/:type/:current/:potential(/:size)', function ($type, $current, $potential, $size = 'regular') use ($app) {
    $epc = new Starsquare\Epc($type, $current, $potential, $size);
    $epc->draw($app->response);
})->conditions(array('type' => 'e[ei]r', 'current' => '\d+', 'potential' => '\d+'));

$app->run();
