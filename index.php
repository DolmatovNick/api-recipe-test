<?php

use Recapi\Models\Recipe;
use Recapi\Models\User;
use Recapi\Middleware\Authentication as ApiAuth;

require 'bootstrap.php';

$app = new Slim\App();

$apiAuth = new ApiAuth();

// Create a user
$app->post('/users', function($request, $response, $args) {

    $_user = new User();

    $params = json_decode($request->getBody());

    $_user->username    = $params->username;
    $_user->password    = $params->password;
    $_user->apikey      = md5( $params->username . $params->password); // It is not security, just fake!

    $_user->save();

    if ($_user->id !== null) {
        $payload = $_user->output();

        return $response->withStatus(201)->withJson($payload);
    }

    return $response->withStatus(400)->withJson([
        'Error' => 'User is not created'
    ]);

});

// Create a recipe
$app->post('/recipes', function($request, $response, $args) {

    $recipe = new Recipe();

    $params = json_decode($request->getBody());

    $recipe->head       = $params->head;
    $recipe->body       = $params->body;
    $recipe->user_id    = $request->getAttribute('user_id');

    $recipe->save();

    if ($recipe->id !== null) {
        $payload = [
            'id' => $recipe->id,
            'image_upload_method' => 'PUT',
            'image_upload_url' => $recipe->getUriForUploadImage()
        ];

        return $response->withStatus(201)->withJson($payload);
    }

    return $response->withStatus(400);

})->add($apiAuth);

// Edit a recipe
$app->put('/recipes/{recipe_id}', function($request, $response, $args) {

    $recipe_id = (int)$args['recipe_id'];
    $recipe = Recipe::getByID($recipe_id, $request->getAttribute('user_id'));

    $params = json_decode($request->getBody());

    $recipe->head       = $params->head;
    $recipe->body       = $params->body;

    if ($recipe->id !== null) {
        $recipe->save();

        $payload = [
            'id' => $recipe->id,
            'image_upload_method' => 'PUT',
            'image_upload_url' => $recipe->getUriForUploadImage()
        ];

        return $response->withStatus(201)->withJson($payload);
    }

    return $response->withStatus(400);

})->add($apiAuth);

// Delete a recipe
$app->delete('/recipes/{recipe_id}', function($request, $response, $args) {

    $recipe_id = (int)$args['recipe_id'];

    $recipe = Recipe::getByID($recipe_id, $request->getAttribute('user_id'));

    if ( $recipe->delete() ) {
        return $response->withStatus(204);
    }

    return $response->withStatus(400);

})->add($apiAuth);

// Add an image
$app->put('/recipes/{recipe_id}/image', function($request, $response, $args) {

    $recipe_id = (int)$args['recipe_id'];

    $recipe = Recipe::getByID($recipe_id, $request->getAttribute('user_id'));

    $file = fopen("php://input", "r");
    if ( $recipe->id != null && $recipe->attach($file) ) {

        $payload = [
            'id' => $recipe->id,
            'recipe_url' => '/recipes/' . $recipe->id,
            'image_url' => '/' . $recipe->getImagePath()
        ];

        return $response->withStatus(201)->withJson($payload);

    }

    return $response->withStatus(400);

})->add($apiAuth);

// Get a list of recipes
$app->get('/users/{user_id}/recipes', function($request, $response, $args) {

    $user_id = (int)$args['user_id'];

    $recipes = \DB\RecipeAdapter::getListByUserID($user_id);

    $payload = [];
    foreach ($recipes as $recipe) {
        $payload[] = $recipe->output();
    }

    return $response->withStatus(200)->withJson($payload);

})->add($apiAuth);

$app->run();
