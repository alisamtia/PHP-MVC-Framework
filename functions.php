<?php

use Core\View;
use Core\Auth;
use Core\Session;
use Core\Request;
use Core\RedirectResponse;

function require_file($path, $args = [])
{
    extract($args);
    return require BASE_PATH . $path;
}

function public_upload($path)
{
    return BASE_PATH . '/Public/uploads' . $path;
}

function dd($var)
{
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
    die();
}

function throw_expcetion($exception)
{
    throw new \Exception($exception);
}

function in_array_or_error($value, $array, $exception = "")
{
    if (!in_array($value, $array)) {
        throw_expcetion($exception);
    }
}

function abort($error_code = 404)
{
    require_file("views/errors/$error_code.php");
    http_response_code($error_code);
    die();
}

function get_config($config_name)
{
    return require_file("/config.php")[$config_name];
}

function escaped_output($var)
{
    echo htmlspecialchars($var, ENT_QUOTES, 'UTF-8');
}

function csrf($html = true)
{
    $random_value = bin2hex(random_bytes(32));
    $_SESSION['csrf'] = $random_value;
    echo $html ? "<input type='hidden' name='csrf' value='$random_value'>" : $random_value;
}

function arraysEqualUnordered(array $a, array $b)
{
    $a = array_map('strtolower', $a);
    $b = array_map('strtolower', $b);
    return sort($a) == sort($b);
}

function array_subset(array $main, array $subset)
{
    $main = array_map('strtolower', $main);
    $subset = array_map('strtolower', $subset);

    return empty(array_diff($subset, $main));
}

function now($timestamp = null)
{
    return date('Y-m-d H:i:s', $timestamp);
}

function humanDate($date, $pattern = "F jS, Y") //, g:i a
{
    return date($pattern, strtotime($date));
}

function formatHours($hours): string
{
    if ($hours < 24) {
        $hours = max(1, round($hours));

        return $hours . ' ' . ($hours === 1 ? 'hour' : 'hours');
    }

    $days = round($hours / 24);

    return $days . ' ' . ($days === 1 ? 'day' : 'days');
}

function calculate_response_time($responses_total, $responses_time)
{
    if ($responses_total <= 0) {
        return "N/a";
    }
    $hours = $responses_time / $responses_total;
    return formatHours($hours);
}

function redirect($uri)
{
    header("Location: $uri");
    exit;
}


// get the current user
function current_user()
{
    return Auth::user();
}

// check if user logged in or not
function logged_in()
{
    return Session::has("user");
}


function tPost($key, $default = "")
{
    return Request::tPost($key, $default);
}

function tAll($key, $default = "")
{
    return Request::tAll($key, $default);
}

function rAll($key, $default = "")
{
    return Request::all()[$key] ?? $default;
}

function back($uri = null)
{
    return new RedirectResponse($uri);
}

function current_url($querystring = false)
{
    $uri = trim($_SERVER['REQUEST_URI']);
    return $querystring ? $uri : explode("?", $uri)[0];
}

function array_trim(array $elements): array
{
    return array_map('trim', $elements);
}

function array_trim_to_string(array $elements, $seprator = ","): string
{
    $elements = array_trim($elements);
    return implode($seprator, $elements);
}

function create_slug($title, $seprator = "-", $explode = " ")
{
    $slug = strtolower($title);
    $array_slug = array_filter(array_trim(explode($explode, $slug)), fn($value, $key) => $value != '', ARRAY_FILTER_USE_BOTH);
    $slug = implode($seprator, $array_slug);
    $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);
    return preg_replace('/-+/', '-', $slug);
}

// Json related functions
function json_response($data)
{
    echo json_encode($data);
    exit;
}

function json_success(array $array = [])
{
    $success = true;
    $data = array_merge(compact('success'), $array);
    json_response($data);
}

function json_error(array $errors = [])
{
    $success = false;
    $data = array_merge(compact('success'), compact('errors'));
    json_response($data);
}

// View Functions

function showPaginaton(int $max = 1, int | null $current = null)
{
    if (!$current) {
        $current = $_GET['page'] ?? '1';
        if (!intval($current)) {
            $current = 1;
        }
    }
    $max = $max == null || $max == "" ? 1 : $max;
    $previous = $current - 1;
    $next = $current + 1;
    $pagination = "<div class='pagination'>";
    if ($current > 1) {
        $pagination .= "<a class='pagination-item' href='?page=$previous'>‹</a>";
    }
    for ($i = 1; $i <= $max; $i++) {
        $active = $current == $i ? " active" : "";
        $pagination .= "<a class='pagination-item$active' href='?page=$i'>$i</a>";
    }
    if ($max > $current) {
        $pagination .= "<a class='pagination-item' href='?page=$next'>›</a>";
    }
    $pagination .= '</div>';
    return $pagination;
}

// Render stars
function renderStars($rating, int $max = 5): string
{
    $rating = ($rating == null || $rating == "") ? 0 : (float) $rating;
    $stars = "<div class='stars'>";
    for ($i = 1; $i <= $max; $i++) {
        if ($i <= $rating) {
            $stars .= "<span class='star filled'>★</span>";
        } else {
            $stars .= "<span class='star'>☆</span>";
        }
    }
    $stars .= "</div>";
    return $stars;
}

// All Functions

function component($path, array $data = [])
{
    return View::render("components/$path", $data);
}

function pagination($totalPages, $currentPage)
{
    component('pagination', compact('totalPages', 'currentPage'));
}

function guest_component($path, array $data = [])
{
    return View::render("guest/components/$path", $data);
}

function old($key)
{
    return View::old($key) ?? null;
}

function error($key)
{
    return View::error($key) ?? null;
}

function success($key)
{
    return View::success($key) ?? null;
}

function old_exists($key)
{
    return View::old_exists($key);
}

function error_exists($key)
{
    return View::error_exists($key);
}

function success_exists($key)
{
    return View::success_exists($key);
}

function change_current_avatar($avatar_filename)
{
    $user = Session::get('user');
    $user['avatar'] = $avatar_filename;
    Session::set('user', $user);
}
