<?php
require_once '../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// import and register all business logic files (services) to FlightPHP
require_once __DIR__ . '/services/UserService.php';
require_once __DIR__ . '/services/BlogService.php';
require_once __DIR__ . '/services/FeaturedService.php';
require_once __DIR__ . '/services/FavoriteService.php';
require_once __DIR__ . '/services/CategoryService.php';
require_once __DIR__ . '/services/ImgurService.php';
require_once __DIR__ . '/services/LikeService.php';

Flight::register('userService', "UserService");
Flight::register('blogService', "BlogService");
Flight::register('featuredService', "FeaturedService");
Flight::register('favoriteService', "FavoriteService");
Flight::register('categoryService', "CategoryService");
Flight::register('imgurService', "ImgurService");
Flight::register('likeService', "LikeService");

// middleware
Flight::route('/*', function () {
    $path = Flight::request()->url;

    if ($path == '/login' || $path == '/register' || $path == '/google-login' || strpos($path, '/google-callback') === 0 || $path == '/docs.json') {
        return TRUE;
    }

    $headers = getallheaders();
    if (!isset($headers['Authentication'])) {
        Flight::json(["message" => "Authorization is missing"], 403);
        return FALSE;
    } else {
        try {
            $decoded = (array)JWT::decode($headers['Authentication'], new Key(Config::JWT_SECRET(), 'HS256'));
            Flight::set('user', $decoded);
            return TRUE;
        } catch (\Exception $e) {
            Flight::json(["message" => "Authorization token is not valid", "jwt_error" => $e->getMessage()], 403);
            return FALSE;
        }
    }
});

// import routes
require_once __DIR__ . '/routes/UserRoutes.php';
require_once __DIR__ . '/routes/BlogRoutes.php';
require_once __DIR__ . '/routes/FeaturedBlogRoutes.php';
require_once __DIR__ . '/routes/FavoriteBlogRoutes.php';
require_once __DIR__ . '/routes/CategoryRoutes.php';
require_once __DIR__ . '/routes/LikeRoutes.php';

// Custom routes here
Flight::route("GET /", function () {
    echo "Start page";
});

/* REST API documentation endpoint */
Flight::route('GET /docs.json', function () {
    $openapi = \OpenApi\scan('routes');
    header('Content-Type: application/json');
    echo $openapi->toJson();
});

Flight::start();
