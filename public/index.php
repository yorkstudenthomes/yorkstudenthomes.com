<?php

require '../vendor/autoload.php';

use Aptoma\Twig\Extension\MarkdownExtension,
    Starsquare\Twig\Extension\MarkdownEngine;

function images($home) {
    return array_map(function ($path) {
        return basename($path, '_thumb.jpg');
    }, glob(__DIR__ . "/images/$home/*_thumb.jpg"));
}

function getHomeData($slug, $app) {
    // description / features
    $stmt = $app->db->prepare('select house_id, house_address as address, rented, type, description, feature_id, feature from description inner join feature using (house_id) where slug = ? order by feature_id');
    $stmt->execute(array($slug));
    $features = $stmt->fetchAll();

    if (!count($features)) {
        $app->notFound();
    }

    $home = $features[0];

    // room bills
    $stmt = $app->db->prepare('select bill_id as id, room_price as value, room_description as description from bill where house_id = ? order by room_price');
    $stmt->execute(array($home['house_id']));
    $bills = $stmt->fetchAll();

    // energy ratings
    $stmt = $app->db->prepare('select * from epc where house_id = ?');
    $stmt->execute(array($home['house_id']));
    $ratings = $stmt->fetch();

    return array(
        'title'    => $home['address'],
        'home'     => $home,
        'bills'    => $bills,
        'plans'    => is_file(__DIR__ . "/plans/$slug.pdf"),
        'features' => array_map(function ($row) {
            return array(
                'id'          => $row['feature_id'],
                'description' => $row['feature'],
            );
        }, $features),
        'ratings'  => array(
            'eer' => array('current' => $ratings['eer_current'], 'potential' => $ratings['eer_potential']),
            'eir' => array('current' => $ratings['eir_current'], 'potential' => $ratings['eir_potential']),
        ),
        'images' => images($slug),
    );
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
        'description' => 'providing 4–6-bedroom houses to students in York for over 20 years.',
        'keywords' => 'york student homes, york student houses, accommodation, living, renting, homes, houses, properties, aparments, living, students, york',
    ),
    'messages' => array(
        'INVALID_PASSWORD' => 'The password was incorrect.',
    ),
));

$app->view->parserExtensions[] = new MarkdownExtension(new MarkdownEngine());

$app->hook('slim.before.dispatch', function () use ($app) {
    // remove trailing slash
    $path = $app->request->getPath();
    if (substr($path, -1) == '/' && $path != '/') {
        $app->redirect(substr($path, 0, -1), 301);
    }

    // site settings
    $stmt = $app->db->query('select setting, value from setting');
    $app->view->setData('setting', $stmt->fetchAll(PDO::FETCH_KEY_PAIR));

    // get homes
    $navigation = $app->db->query('select slug, house_address from description order by house_order')->fetchAll(PDO::FETCH_FUNC, function ($url, $address) {
        return array(
            'url' => "/homes/$url",
            'text' => str_replace('Street', 'St', $address),
        );
    });

    // add on other links
    $navigation = array_merge($navigation, array(
        array('url' => '/maps',    'text' => 'Maps'),
        array('url' => '/contact', 'text' => 'Contact'),
    ));

    // find selected
    array_walk($navigation, function (&$link) use ($path) {
        if (strpos($path, $link['url']) === 0) {
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
        $app->render('home.html', getHomeData($slug, $app));
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

$app->post('/admin/markup', $app->auth, function () use ($app) {
    $params = $app->request->params();
    $output = array();

    array_walk_recursive($params, function ($item, $key) use ($app, &$output) {
        $output["preview_$key"] = trim($app->view->render('admin/markup.html', array('value' => $item)));
    });

    $app->response->headers->set('Content-Type', 'application/json');
    echo json_encode($output);
});

$app->map('/admin/homes/:home', $app->auth, function ($slug) use ($app) {
    $data = getHomeData($slug, $app);

    if ($app->request->isPost()) {
        $errors = array();
        $params = $app->request->params();

        if (empty($params['house_type'])) {
            $errors['house_type'] = 'The home must have a tag line.';
        }

        if (empty($params['house_desc'])) {
            $errors['house_desc'] = 'The home must have a description.';
        }

        $bills = array();
        foreach ($params['bill'] as $billId => $bill) {
            if (!preg_match('/^\d+(?:-\d+)?$/', $bill['price'])) {
                $errors["bill_$billId"] = 'Price "' . $bill['price'] . '" is in the wrong format.';
            }

            // match structure for template
            $bills[] = array(
                'id'          => $billId,
                'value'       => $bill['price'],
                'description' => $bill['desc'],
            );
        }

        $features = array();
        foreach ($params['feature'] as $featureId => $feature) {
            if (trim($feature) == '') {
                $errors["feature_$featureId"] = 'Feature must not be empty.';
            }

            $features[] = array(
                'id'          => $featureId,
                'description' => $feature,
            );
        }

        foreach ($params['epc'] as $rating => $value) {
            $int = intval($value);

            if ($int <= 0 || $int > 100) {
                $errors["epc_$rating"] = 'EPC value "' . $value . '" is not a percentage.';
            }
        }

        if (count($errors)) {
            $app->flashNow('error', $errors);
        }
        else {
            // check rented change
            $rented = $data['home']['rented'];
            if (isset($params['set_available'])) { $rented = false; }
            if (isset($params['set_rented'])) { $rented = true; }

            $homeId = $data['home']['house_id'];

            // update home details
            $stmt = $app->db->prepare('update description set type = ?, description = ?, rented = ? where house_id = ?');
            $stmt->execute(array(
                trim($params['house_type']),
                trim($params['house_desc']),
                $rented,
                $homeId,
            ));

            // update bills
            $stmt = $app->db->prepare('delete from bill where house_id = ?');
            $stmt->execute(array($homeId));

            $stmt = $app->db->prepare('insert into bill (house_id, room_price, room_description) values (?, ?, ?)');
            foreach ($bills as $bill) {
                $stmt->execute(array(
                    $homeId,
                    intval($bill['value']),
                    trim($bill['description']),
                ));
            }

            // update features
            $stmt = $app->db->prepare('delete from feature where house_id = ?');
            $stmt->execute(array($homeId));

            $stmt = $app->db->prepare('insert into feature (house_id, feature) values (?, ?)');
            foreach ($features as $feature) {
                $stmt->execute(array($homeId, trim($feature['description'])));
            }

            // update EPC
            $stmt = $app->db->prepare('update epc set eer_current = ?, eer_potential = ?, eir_current = ?, eir_potential = ? where house_id = ?');
            $stmt->execute(array(
                intval($params['epc']['ec']),
                intval($params['epc']['ep']),
                intval($params['epc']['ic']),
                intval($params['epc']['ip']),
                $homeId,
            ));

            $app->flashNow('success', 'The details have been updated.');
        }

        // replace template data from POST
        $data['home']['type'] = $params['house_type'];
        $data['home']['description'] = $params['house_desc'];
        $data['home']['rented'] = $rented;
        $data['bills'] = $bills;
        $data['features'] = $features;
        $data['ratings'] = array(
            'eer' => array('current' => $params['epc']['ec'], 'potential' => $params['epc']['ep']),
            'eir' => array('current' => $params['epc']['ic'], 'potential' => $params['epc']['ip']),
        );

    }

    $app->render('admin/home.html', $data);
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
