<?php

namespace Recapi\Models;

use DB\RecipeAdapter;
use Recapi\Environment;

class Recipe {

    public $id;
    public $user_id;
    public $head;
    public $body;
    protected $image_url;
    protected $image_upload_url;

    /**
     * Construct by id
     * @param $id
     */
    public static function getByID(int $id, int $user_id) : Recipe
    {
        return RecipeAdapter::fillByID($id, $user_id);
    }

    public function delete()
    {
        return RecipeAdapter::delete($this);
    }

    public function getImagePath()
    {
        return 'images/'. $this->id . '.png';
    }

    public function getUriForUploadImage()
    {
        return '/recipes/' . $this->id . '/image';
    }

    public function save()
    {
        return RecipeAdapter::save($this);
    }

    public function attach($file)
    {
        /**
         * @var $db \PDO
         */
        $db = Environment::get('db');

        $db->beginTransaction();

        $this->image_url = $this->getImagePath();
        RecipeAdapter::addImage($this);

        $fp = fopen($this->getImagePath(), "w");

        while ($data = fread($file, 1024)) {
            fwrite($fp, $data);
        }

        $step1 = fclose($fp);
        $step2 = fclose($file);

        if ( $step1 && $step2 ) {
            return $db->commit();
        }

        $db->rollBack();
        return true;
    }

    public function detachImage() : bool
    {
        if (file_exists($this->getImagePath())) {
            return unlink($this->getImagePath());
        }

        return true;
    }

    public function output()
    {
        return [
            'id'  =>  $this->id,
            'head'  =>  $this->head,
            'body'  =>  $this->body,
            'images' => [
                [
                    'type' => 'main',
                    'image_url'  => $this->getImagePath(),
                    'image_upload_url'  => $this->getUriForUploadImage(),
                ]
            ]
        ];
    }

}

