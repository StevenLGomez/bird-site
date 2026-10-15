<?php
declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

/*
var_dump($_GET['app']);
var_dump($_GET['action']);
$app = ($_GET['app']);
$action = ($_GET['action']);
var_dump($app, $action);
*/

$files = [
        __DIR__ . '/../src/DuckApp.php',
        __DIR__ . '/../src/ChickenApp.php',
        __DIR__ . '/../src/GooseApp.php',
        __DIR__ . '/../src/TurkeyApp.php',
    ];

// foreach ($files as $file) 
// {
//     echo htmlspecialchars($file) . ' — ';    
//     var_dump(is_file($file), is_readable($file));
// }

foreach ($files as $file) 
{
    require_once $file;
}

// exit;

// echo __DIR__;
// echo '__DIR__:  ' . __DIR__ . '<br />';
// echo 'SDR:  ' . $_SERVER['DOCUMENT_ROOT'] . '<br />';

// require_once __DIR__ . '/../src/DuckApp.php';
// require_once __DIR__ . '/../src/ChickenApp.php';
// require_once __DIR__ . '/../src/GooseApp.php';
// require_once __DIR__ . '/../src/TurkeyApp.php';

// Minimal dispatch code from duck.ai session
$app = strtolower($_GET['app'] ?? '');
$action = strtolower($_GET['action'] ?? '');

$classes = [
    'duck' => DuckApp::class,
    'chicken' => ChickenApp::class,
    'goose' => GooseApp::class,
    'turkey' => TurkeyApp::class,
];

if ($app === '')
{
    echo '<h1>Bird Applications</h1>';
    // Render the top level landing page here
    //renderPage(
    //    'Bird Applications',
    //    <<<HTML
    //        <h1>Bird Applications</h1>
    //
//
//            <p>Choose an application:</p>
//
//            <ul>
//                <li><a href="/duck">Duck App</a></li>
//                <li><a href="/chicken">Chicken App</a></li>
//                <li><a href="/goose">Goose App</a></li>
//                <li><a href="/turkey">Turkey App</a></li>
//            </ul>
//        HTML
//    );

}
elseif (!isset($classes[$app]))
{
    http_response_code(404);
    exit('Unknown app: ' . htmlspecialchars($app));
}
else
{
    echo '$app: ' . $app . ' $action: ' . $action;

    $instance = new $classes[$app]();

    if ($action === '')
    {
        $action = 'page';
    }

    if (!in_array($action, ['page', 'create', 'read', 'update', 'delete'], true) || !method_exists($instance, $action)) 
    {
        http_response_code(404);
        exit('Unknown action: ' . htmlspecialchars($action));
    }
    else
    {
        $instance->$action;
    }

}


if (!method_exists($instance, $action)) {
    http_response_code(500);
    exit('Missing method: ' . htmlspecialchars($action));
}

echo $instance->$action();




function escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function renderPage(string $title, string $content): void
{
    $safeTitle = escape($title);

    echo <<<HTML
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>{$safeTitle}</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    max-width: 700px;
                    margin: 40px auto;
                    padding: 0 20px;
                    line-height: 1.5;
                }

                a {
                    color: #0645ad;
                }

                li {
                    margin: 8px 0;
                }

                .message {
                    padding: 12px;
                    background: #f0f0f0;
                    border-left: 4px solid #555;
                }
            </style>
        </head>
        <body>
            {$content}
        </body>
        </html>
    HTML;
}

function renderNotFound(): never
{
    http_response_code(404);

    renderPage(
        'Page Not Found',
        <<<HTML
            <h1>404 - Page Not Found</h1>
            <p>The requested page does not exist.</p>
            <p><a href="/">Return to home page</a></p>
        HTML
    );

    exit;
}

/*
 * Get the path without the query string.

 * Examples:
 * /                  => []
 * /duck              => ['duck']
 * /duck/create       => ['duck', 'create']
 */
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$path = trim((string) $path, '/');

$segments = $path === ''
    ? []
    : explode('/', $path);

/*
 * Top-level landing page.
 */
if ($segments === []) {
    renderPage(
        'Bird Applications',
        <<<HTML
            <h1>Bird Applications</h1>

            <p>Choose an application:</p>

            <ul>
                <li><a href="/duck">Duck App</a></li>
                <li><a href="/chicken">Chicken App</a></li>
                <li><a href="/goose">Goose App</a></li>
                <li><a href="/turkey">Turkey App</a></li>
            </ul>
        HTML
    );

    exit;
}

/*
 * Map the first URL segment to an application object.
 */
$apps = [
    'duck' => new DuckApp(),
    'chicken' => new ChickenApp(),
    'goose' => new GooseApp(),
    'turkey' => new TurkeyApp(),
];

$appSlug = strtolower($segments[0]);

if (!array_key_exists($appSlug, $apps)) {
    renderNotFound();
}

$app = $apps[$appSlug];

/*
 * /duck
 * /chicken
 * /goose
 * /turkey
 */
if (count($segments) === 1) {
    renderPage(
        $app->name() . ' App',
        $app->page()
    );

    exit;
}

/*
 * /duck/create
 * /duck/read
 * /duck/update
 * /duck/delete
 */
$action = strtolower($segments[1]);

$actions = [
    'create' => 'create',
    'read'   => 'read',
    'update' => 'update',
    'delete' => 'delete',
];

if (!array_key_exists($action, $actions) || count($segments) !== 2) {
    renderNotFound();
}

$method = $actions[$action];
$message = $app->{$method}();

$safeMessage = escape($message);

renderPage(
    ucfirst($action) . ' ' . $app->name(),
    <<<HTML
        <h1>{$action} - {$app->name()} App</h1>

        <p class="message">{$safeMessage}</p>

        <p>
            <a href="/{$appSlug}">Return to {$app->name()} App</a>
        </p>

        <p>
            <a href="/">Return to home page</a>
        </p>
    HTML
);

