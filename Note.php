<?php

class Note{
    private $title;
    private $category;
    private $content;

    public function __construct($title, $category, $content) {
        $this->title = $title;
        $this->category = $category;
        $this->content = $content;
    }

    public function getTitle(){
        return $this->title;
    }

    public function setTitle($title){
        $this->title = $title;
    }

    public function getCategory(){
        return $this->category;
    }

    public function setCategory($category){
        $this->category = $category;
    }

    public function getContent(){
        return $this->content;
    }

    public function setContent($content){
        $this->content = $content;
    }

    public function toArray() {
        return [
            'title' => $this->title,
            'category' => $this->category,
            'content' => $this->content
        ];
    }
}

?>