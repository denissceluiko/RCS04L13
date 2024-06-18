<?php 

class Article 
{
    public mysqli $db;

    public function __construct(mysqli $db)
    {
        $this->db = $db;
    }

    public function create(array $data)
    {
        $title = $this->cleanup($data['title']);
        $image = $this->cleanup($data['image_url']);
        $body = $this->cleanup($data['contents']);
    
        $this->db->query("INSERT INTO `articles` (`title`, `image_url`, `body`) VALUES ('$title', '$image', '$body')");
    }

    public function update(array $data)
    {
        $id = $this->cleanup($data['id']);
        $title = $this->cleanup($data['title']);
        $image = $this->cleanup($data['image_url']);
        $body = $this->cleanup($data['contents']);
    
        $this->db->query("UPDATE `articles` SET `title` = '$title', `image_url` = '$image', `body` = '$body' WHERE `id`= $id");
    }

    public function edit($id)
    {
        $id = $this->cleanup($id);
        $editResult = $this->db->query("SELECT * FROM `articles` WHERE `id` = $id");
    
        if (mysqli_num_rows($editResult) > 0) {
            return mysqli_fetch_assoc($editResult);
        }
    
        return [];
    }

    public function delete(array $data)
    {
        $id = $this->cleanup($data['id']);
        $this->db->query("DELETE FROM `articles` WHERE `id`= $id");
    }

    public function list()
    {
        return $this->db->query('SELECT * FROM `articles`');
    }

    public function cleanup($value): string
    {
        if (!isset($value)) 
            return '';

        $value = strip_tags($value);
        $value = $this->db->real_escape_string($value);

        return $value;
    }
}