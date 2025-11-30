<?php
class Publication {
    private $id;
    private $title;
    private $picture;
    private $description;
    private $datetime;
    private $is_published;

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getPicture() {
        return $this->picture;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getDatetime() {
        return $this->datetime;
    }

    public function getIsPublished() {
        return $this->is_published;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function setPicture($picture) {
        $this->picture = $picture;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function setDatetime($datetime) {
        $this->datetime = $datetime;
    }

    public function setIsPublished($is_published) {
        $this->is_published = $is_published;
    }
}

?>