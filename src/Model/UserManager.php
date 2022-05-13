<?php

namespace App\Model;

use App\Model\Connection;
use PDO;

/**
 * Abstract class handling default manager.
 */
class UserManager extends AbstractManager
{
    protected PDO $pdo;

    public const TABLE = 'user';

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
        $query = 'SELECT * FROM ' . static::TABLE;
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
        $statement = $this->pdo->prepare("SELECT u.firstname, u.lastname, u.email, u.bio, 
            u.registration_date, u.fridge_used,  
            p.id, p.title as promo_name, p.date_start as promo_debut, p.date_end as promo_end 
            FROM " . static::TABLE . " AS u 
            JOIN promo AS p ON p.id = u.promo_id
            WHERE u.id=:id
        ");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch();
    }

    /**
     * Get one row from database by email and password to login the user.
     */
    public function selectOneUser(string $email, string $password): array|false
    {
        // prepared request
        $statement = $this->pdo->prepare("SELECT * FROM " . static::TABLE . " WHERE email=:email AND pswd=:password");
        $statement->bindValue('email', $email, \PDO::PARAM_STR);
        $statement->bindValue('password', $password, \PDO::PARAM_STR);
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

    /**
     * Insert new item in database
     */
    public function insert(array $user): int
    {
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE . " 
            (firstname, lastname, email, pswd, bio, is_admin, fridge_used, promo_id) 
            VALUES (:firstname, :lastname, :email, :pswd, :bio, :is_admin, :fridge_used, :promo_id)");
        $statement->bindValue('firstname', $user['firstname'], \PDO::PARAM_STR);
        $statement->bindValue('lastname', $user['lastname'], \PDO::PARAM_STR);
        $statement->bindValue('email', $user['email'], \PDO::PARAM_STR);
        $statement->bindValue('pswd', $user['password'], \PDO::PARAM_STR);
        $statement->bindValue('bio', $user['bio'], \PDO::PARAM_STR);
        $statement->bindValue('is_admin', $user['is_admin'], \PDO::PARAM_BOOL);
        $statement->bindValue('fridge_used', $user['fridge_used'], \PDO::PARAM_BOOL);
        $statement->bindValue('promo_id', $user['promo_id'], \PDO::PARAM_INT);

        $statement->execute();
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Update item in database
     */
    public function update(array $user): bool
    {
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " 
            SET firstname = :firstname, lastname = :lastname, email = :email, pswd = :pswd 
            WHERE id = :id");
        $statement->bindValue('id', $user['id'], \PDO::PARAM_INT);
        $statement->bindValue('firstname', $user['firstname'], \PDO::PARAM_STR);
        $statement->bindValue('lastname', $user['lastname'], \PDO::PARAM_STR);
        $statement->bindValue('email', $user['email'], \PDO::PARAM_STR);
        $statement->bindValue('pswd', $user['password'], \PDO::PARAM_STR);

        return $statement->execute();
    }

    public function updateFridgeStatus(array $user): bool
    {
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " 
            SET fridge_used = :fridge_used WHERE id = :id");
        $statement->bindValue('id', $user['id'], \PDO::PARAM_INT);
        $statement->bindValue('fridge_used', $user['fridge_used'], \PDO::PARAM_STR);

        return $statement->execute();
    }
}
