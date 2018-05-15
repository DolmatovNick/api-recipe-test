# Каталог рецептов

## Задание
 
Реализовать АПИ для приложения “Каталог рецептов”, 
разрешается использовать микрофрэймворки (psr7 middleware,Lumen, Silex, Slim), 
АПИ желательно должно соответствовать РЕСТ стандартам, 
за исключением некоторых методов которые можно реализовать в виде RPC.

АПИ должно давать возможность:
  1. создать юзера, 
  2. залогиниться, 
  3. создавать, 
  4. редактировать и 
  5. удалять от лица этого юзера рецепты, 
  5. так же обязательным полем у рецепта является фотография, 
    следовательно нужно АПИ для загрузки фотографий. 

    Для реализации самого АПИ и сопутствующего ему функционала 
    запрещается использовать библиотеки.

База данных PostgreSql
Версия PHP > 7

Итоговый вариант АПИ ожидается на гитхабе.

# API endpoints

Support endpoints

| № | Name | Method | Need auth| Endpoint|  
| :---: | :--- | :--- | :---: | :--- | 
| 1 | Create user  | POST | No | /users |
| 2 | Create recipe | POST  | Yes | /recipes|
| 3 | Edit recipe   | PUT  | Yes | /recipes/{id} |
| 4 | Add image to recipe   | PUT  | Yes | /recipes/{id}/image |
| 5 | Delete recipe | DELETE  | Yes | /recipes{id} |
| 6 | Get users's recipes | GET | Yes | /users/{user_id}/recipes |

## Create user
##### POST /users
```json
{
    "username" : "ivanpetrov",
    "password"   : "password"
}
```
##### Response
```json
{
  "id" : 1,
  "url" : "users/1",
  "key" : "bad8f5a0cfd228821c576d61281c051e"
}
```
##### Success code is 200

## Create recipe
##### POST /recipes
```json
{
    "name"  : "Sugar and water recipe",
    "text"	: "Take one spoon of sugar. And one glass of water. Mix. Enjoy!"
}
```
##### Response
```json
{
    "id"            : 1,
    "recipe_url"	: "/recipes/1",
    "upload_image_method"	: "PUT",
    "upload_image_url"	    : "/recipes/1/image"
}
```

## Edit recipe
##### PUT /recipes/{recipe}
```json
{
    "name"  : "Sugar and water recipe",
    "text"	: "Take one spoon of sugar. And one glass of water. Mix. Enjoy!"
}
```
##### Response
```json
{
    "id"            : 1,
    "recipe_url"	: "/recipes/1",
    "upload_image_method"	: "PUT",
    "upload_image_url"	    : "/recipes/1/image"
}
```

## Add image to recipe
##### PUT /recipes/{recipe}/iamge

How to add image: Use HTTP HEAD binary format, json is no needs  
```json
{
    "id": 1,
    "recipe_url": "/recipes/1",
    "image_url": "/images/1.png"
}
```
##### Response
```json
{
    "id": 1,
    "recipe_url": "/recipes/1",
    "image_url": "/images/1.png"
}
```
     Note: API changes format input images to .png

## Delete recipe
### DELETE /recipes/{recipe}
##### Success code is 204

## Get user recipe list
##### GET /users/{user_id}recipes

##### Response
```json
[
    {
        "id"  : 1,
        "name"  : "Sugar and water recipe",
        "text"	: "Take one spoon of sugar. And one glass of water. Mix. Enjoy!",
        "image_url" : "/recipes/1.png"
    },
    {
        "id"  : 2,
        "name"  : "Sugar and water recipe",
        "text"	: "Take one spoon of sugar. And one glass of water. Mix. Enjoy!",
        "image_url" : "/recipes/2.png"
    }
]
```
##### Success code is 200

# How to use endpoints

## 1 Setup

1 Implement /setup/db.sql file on a database
 
2 Rename /config/creds.inc.php to /config/creds.php and type your credentials 

## 2 Authenticate

You have to add "Authorization" field in HTTP HEAD and fill it with a "key" 
from the "Create user" endpoint
