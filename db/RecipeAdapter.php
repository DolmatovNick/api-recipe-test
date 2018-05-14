<?php


namespace DB;

use PDO;
use Recapi\Environment;
use Recapi\Models\Recipe;

class RecipeAdapter {

    public static function save(Recipe $recipe) : bool
    {
        if ($recipe->id == null) {
            return static::insert($recipe);
        } else {
            return static::update($recipe);
        }
    }

    public static function insert(Recipe $recipe) : bool
    {
        /**
         * @var $db PDO
         */
        $db = Environment::get('db');

        $db->beginTransaction();

        $sql = "INSERT INTO recipes (user_id, head, body)
            VALUES (:user_id, :head, :body)";

        /**
         * @var $sth \PDOStatement
         */
        $sth = $db->prepare($sql);
        $sth->bindValue(':user_id', $recipe->user_id, PDO::PARAM_INT);
        $sth->bindValue(':head', $recipe->head, PDO::PARAM_STR);
        $sth->bindValue(':body', $recipe->body, PDO::PARAM_STR);


        if ($sth->execute()) {
            $recipe->id = $db->lastInsertId();

            $imageIsUploaded = static::addUploadImageUri($recipe);

            if ( $imageIsUploaded ) {

                return $db->commit();
            }
        }

        $db->rollBack();
        return false;
    }

    public static function update(Recipe $recipe) : bool
    {
        /**
         * @var $db PDO
         */
        $db = Environment::get('db');

        $sql = 'UPDATE recipes SET head = :head, body = :body
            WHERE id = :id AND user_id = :user_id';

        /**
         * @var $sth \PDOStatement
         */
        $sth = $db->prepare($sql);
        $sth->bindParam(':id', $recipe->id, PDO::PARAM_INT);
        $sth->bindParam(':user_id', $recipe->user_id, PDO::PARAM_INT);
        $sth->bindValue(':head', $recipe->head, PDO::PARAM_STR);
        $sth->bindValue(':body', $recipe->body, PDO::PARAM_STR);

        return $sth->execute();
    }

    public static function addUploadImageUri(Recipe $recipe) : bool
    {
        /**
         * @var $db PDO
         */
        $db = Environment::get('db');
        $sql = 'UPDATE recipes SET image_upload_url = :image_upload_url
            WHERE id = :id';

        /**
         * @var $sth \PDOStatement
         */
        $sth = $db->prepare($sql);
        $sth->bindValue(':id', $recipe->id, PDO::PARAM_INT);
        $sth->bindValue(':image_upload_url', $recipe->getUriForUploadImage(), PDO::PARAM_STR);

        return $sth->execute();
    }

    public static function addImage(Recipe $recipe)
    {
        /**
         * @var $db PDO
         */
        $db = Environment::get('db');
        $sql = 'UPDATE recipes SET image_url = :image_url
            WHERE id = :id';

        /**
         * @var $sth \PDOStatement
         */
        $sth = $db->prepare($sql);
        $sth->bindValue(':id', $recipe->id, PDO::PARAM_INT);
        $sth->bindValue(':image_url', $recipe->getImagePath(), PDO::PARAM_STR);

        return $sth->execute();
    }
    
    public static function fillByID(int $id, int $user_id) : Recipe
    {
        /**
         * @var $db PDO
         * @var $sth \PDOStatement
         */
        $db = Environment::get('db');

        $sth = $db->prepare("SELECT id, user_id, head, body, image_url, image_upload_url FROM recipes WHERE id = :id AND user_id = :user_id");
        $sth->bindValue(':id', $id, PDO::PARAM_INT);
        $sth->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $sth->execute();
        $res = $sth->fetchAll(PDO::FETCH_CLASS, Recipe::class);

        if (count($res) == 1) {
            return $res[0];
        }

        return new Recipe();
    }

    public static function getListByUserID(int $user_id) : array
    {
        /**
         * @var $db PDO
         * @var $sth \PDOStatement
         */
        $db = Environment::get('db');

        $sth = $db->prepare("SELECT id, user_id, head, body, image_url, image_upload_url FROM recipes WHERE user_id = :user_id ORDER BY id ASC");
        $sth->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $sth->execute();
        return $sth->fetchAll(PDO::FETCH_CLASS, Recipe::class);
    }

    public static function delete(Recipe $recipe) : bool
    {
        /**
         * @var $db PDO
         */
        $db = Environment::get('db');

        $db->beginTransaction();

        $sql = 'DELETE FROM recipes 
            WHERE id = :id AND user_id = :user_id';

        /**
         * @var $sth \PDOStatement
         */
        $sth = $db->prepare($sql);
        $sth->bindValue(':id', $recipe->id, PDO::PARAM_INT);
        $sth->bindValue(':user_id', $recipe->user_id, PDO::PARAM_INT);

        if ( $sth->execute() && $db->commit() ) {
            return $recipe->detachImage();
        } else {
            $db->rollBack();
            return false;
        }
    }

}