<?php

namespace ShkodenkoComUa\App;


class DbComment
{
    private $dbConnection = null;

    public function __construct()
    {
        $this->dbConnection = new \PDO('mysql:host=localhost;dbname=shcomua6db',
            'shcomua6u',
            'olY2mYEBEqAvCue%'
        );
    }

    public function addComment($data)
    {
        try {
            $stmt = $this->dbConnection->prepare("INSERT INTO `comments` SET `name` = ?, `email` = ?, `author_IP` = ?, `comment_text` = ?, `comment_approved` = ?");
            $res = $stmt->execute($data);
            echo var_export(__METHOD__ . ' +' . __LINE__ . ' $res: ' . var_export($res, true));
        } catch (\PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    }
}