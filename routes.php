<?php
// {:wildcard}     All characters + number wildcard with text
// {wildcard:\d+}  Only numbers wildcard with text

// {wildcard?}     Optional wildcard with all characters etc
// methods allowed = All methods are allowed
// use Core\Auth;

// $router->get("/", "Index@index")->middleware('admin'); array or string in middlewares
// $router->get("/name{name?}", "Index@show");

$router->get("/", "Index@index");

// $router->get("/register", "Guest\User@index")->middleware('guest');
// $router->post("/register", "Guest\User@store")->middleware('guest');

// $router->get("/login", "Guest\Auth@index")->middleware('guest');
// $router->post("/login", "Guest\Auth@auth")->middleware('guest');
// $router->get("/logout", "Guest\Auth@logout")->middleware('auth');

// $router->get("/password-forgot", "Guest\PasswordReset@index");
// $router->post("/password-forgot", "Guest\PasswordReset@store");
// $router->get("/password-forgot/{:token}", "Guest\PasswordReset@resetPassword");
// $router->post("/change-password", "Guest\PasswordReset@storeNewPassword");



// $router->get("/user/dashboard", "User\Index@index")->middleware('auth');
// $router->post("/user/update", "User\Index@update")->middleware('auth');

// $router->get("/listings/create", "User\Listing@create")->middleware('auth');
// $router->post("/listings/create", "User\Listing@store")->middleware('auth');
// $router->get("/listings/edit/{:slug}", "User\Listing@edit")->middleware('auth');
// $router->patch("/listings/{:slug}", "User\Listing@update")->middleware('auth');
// $router->delete("/listings/{:slug}", "User\Listing@delete")->middleware('auth');
// $router->post("/listings/{:slug}/image", "User\Listing@imageUpload")->middleware('auth');


// $router->get("/listings/{:slug}", "User\Listing@show");


// $router->get("/tailwind", "Index@tailwind");
