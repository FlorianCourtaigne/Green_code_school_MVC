<?php

namespace App\Model;

use App\Model\Connection;
use PDO;

/**
 * Abstract class handling default manager.
 */
class PlanningManager extends AbstractManager
{
    protected PDO $pdo;

    public const TABLE = 'planning';

    public function __construct()
    {
        $connection = new Connection();
        $this->pdo = $connection->getConnection();
    }

    /**
     * Get all row from database.
     */

    public function selectPlannings(int $week, string $orderBy = '', string $direction = 'ASC'): array
    {
        $query = 'SELECT p.id, p.week, promo.title AS promo_name FROM ' . static::TABLE . ' AS p  
            JOIN planning_promo AS pp ON pp.planning_id = p.id 
            JOIN promo ON promo.id = pp.promo_id 
            WHERE week >= ' . $week;
        if ($orderBy) {
            $query .= ' ORDER BY ' . $orderBy . ' ' . $direction;
        }

        $query .= ' LIMIT 5';

        return $this->pdo->query($query)->fetchAll();
    }

    /**
     * Delete row form an ID
     */
    public function deletePromo(int $id): void
    {
        // prepared request
        $statement = $this->pdo->prepare("DELETE FROM " . static::TABLE . " WHERE id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();
    }

    /**
     * Insert new item in database
     */
    public function insertPromo(array $user): int
    {
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE . " 
            (firstname, lastname, email, pswd, is_admin, fridge_used) 
            VALUES (:firstname, :lastname, :email, :pswd, :is_admin, :fridge_used)");
        $statement->bindValue('firstname', $user['firstname'], \PDO::PARAM_STR);
        $statement->bindValue('lastname', $user['lastname'], \PDO::PARAM_STR);
        $statement->bindValue('email', $user['email'], \PDO::PARAM_STR);
        $statement->bindValue('pswd', $user['password'], \PDO::PARAM_STR);
        $statement->bindValue('is_admin', $user['is_admin'], \PDO::PARAM_BOOL);
        $statement->bindValue('fridge_used', $user['fridge_used'], \PDO::PARAM_BOOL);

        $statement->execute();
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Update item in database
     */
    public function update(array $user): bool
    {
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " 
            SET firstname = :firstname, lastname = :lastname, email = :email, pswd = :pswd, is_admin = :is_admin");
        $statement->bindValue('id', $user['id'], \PDO::PARAM_INT);
        $statement->bindValue('firstname', $user['firstname'], \PDO::PARAM_STR);
        $statement->bindValue('lastname', $user['lastname'], \PDO::PARAM_STR);
        $statement->bindValue('email', $user['email'], \PDO::PARAM_STR);
        $statement->bindValue('pswd', $user['password'], \PDO::PARAM_STR);
        $statement->bindValue('is_admin', $user['is_admin'], \PDO::PARAM_BOOL);

        return $statement->execute();
    }
}
