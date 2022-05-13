<?php

namespace App\Model;

use App\Model\Connection;
use PDO;

/**
 * Abstract class handling default manager.
 */
class CommentManager extends AbstractManager
{
    protected PDO $pdo;

    public const TABLE = 'comment';

    public function __construct()
    {
        $connection = new Connection();
        $this->pdo = $connection->getConnection();
    }

    /**
     * Get all row from database.
     */
    public function selectAll(string $orderBy = '', string $direction = 'ASC'): array
    {
        $query = 'SELECT c.id, c.created_at, c.comment FROM ' . static::TABLE . ' AS c
            INNER JOIN user AS u ON u.id = c.user_id' ;

        if ($orderBy) {
            $query .= ' ORDER BY ' . $orderBy . ' ' . $direction;
        }
        return $this->pdo->query($query)->fetchAll();
    }

    /**
     * Get one row from database by ID.
     */
    public function selectOneById(int $id): array|false
    {
        // prepared request
        $statement = $this->pdo->prepare("SELECT * FROM " . static::TABLE . " WHERE id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch();
    }

    /**
     * Delete row form an ID
     */
    public function delete(int $id): void
    {
        // prepared request
        $statement = $this->pdo->prepare("DELETE FROM " . static::TABLE . " WHERE id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();
    }

    public function update(array $comment): bool
    {
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . "
            SET comment = :comment WHERE id=:id");
        $statement->bindValue('id', $comment['id'], \PDO::PARAM_INT);
        $statement->bindValue('comment', $comment['comment'], \PDO::PARAM_STR);

        return $statement->execute();
    }

    public function insert(array $comment): int
    {
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE . "
            (comment, user_id)
            VALUES (:comment, :user_id)");
        $statement->bindValue('comment', $comment['comment'], \PDO::PARAM_STR);
        $statement->bindValue('user_id', $comment['user_id'], \PDO::PARAM_INT);
        $statement->execute();
        return (int)$this->pdo->lastInsertId();
    }
}
