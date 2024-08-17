<?php

namespace App\Repository;

use App\Domain\User;
use PDO;

class UserRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function save(User $user): User
    {
        $sql = 'INSERT INTO users(name, username, email, password, role, is_active, activated_at)
                        VALUES(:name, :username, :email, :password, :role, :is_active, :activated_at)';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('name', $user->name);
        $statement->bindValue('username', $user->username);
        $statement->bindValue('email', $user->email);
        $statement->bindValue('password', $user->password);
        $statement->bindValue('email', $user->email);
        $statement->bindValue('role', (int)$user->role, PDO::PARAM_INT);
        $statement->bindValue('is_active', (int)$user->is_active, PDO::PARAM_INT);
        $statement->bindValue('activated_at', $user->activated_at);
        $statement->execute();

        return $user;
    }

    public function update(User $user): User
    {
        $statement = $this->connection->prepare("UPDATE users SET name = ?, password = ? WHERE id = ?");
        $statement->execute([
            $user->name, $user->password, $user->id
        ]);
        return $user;
    }

    public function findById(string $id): ?User
    {
        $statement = $this->connection->prepare("SELECT id, name, username, email, password, role, is_active, activated_at FROM users WHERE id = ?");
        $statement->execute([$id]);

        try {
            if ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                $user = new User();
                $user->id = $row['id'];
                $user->name = $row['name'];
                $user->username = $row['username'];
                $user->email = $row['email'];
                $user->password = $row['password'];
                $user->role = $row['role'];
                $user->is_active = $row['is_active'];
                $user->activated_at = $row['activated_at'];
                return $user;
            } else {
                return null;
            }
        } finally {
            $statement->closeCursor();
        }
    }

    public function findByUsername(string $username): ?User
    {
        $statement = $this->connection->prepare("SELECT id, name, username, email, password, role, is_active, activated_at FROM users WHERE username = :username");
        $statement->bindValue('username', $username);
        $statement->execute();

        try {
            if ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                $user = new User();
                $user->id = $row['id'];
                $user->name = $row['name'];
                $user->username = $row['username'];
                $user->email = $row['email'];
                $user->password = $row['password'];
                $user->role = $row['role'];
                $user->is_active = $row['is_active'];
                $user->activated_at = $row['activated_at'];
                return $user;
            } else {
                return null;
            }
        } finally {
            $statement->closeCursor();
        }
    }
    public function findByEmail(string $email): ?User
    {
        $statement = $this->connection->prepare("SELECT id, name, username, email, password, role, is_active, activated_at FROM users WHERE email = ?");
        $statement->execute([$email]);
        write_log($email);
        try {
            if ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                $user = new User();
                $user->id = $row['id'];
                $user->name = $row['name'];
                $user->username = $row['username'];
                $user->email = $row['email'];
                $user->password = $row['password'];
                $user->role = $row['role'];
                $user->is_active = $row['is_active'];
                $user->activated_at = $row['activated_at'];
                return $user;
            } else {
                return null;
            }
        } finally {
            $statement->closeCursor();
        }
    }

    public function activateUser(int $id): bool
    {   
        $statement = $this->connection->prepare('UPDATE users SET is_active = 1, activated_at = CURRENT_TIMESTAMP WHERE id= ?');  
        return $statement->execute([$id]);
    }

    public function deleteUnverifiedUser(string $email)
    {
        $statement = $this->connection->prepare('SELECT id FROM users WHERE active = 0 AND email=:email');
        return $statement->execute(['email' => $email]);
    }

    public function deleteById(string $id): void
    {
        $statement = $this->connection->prepare("DELETE FROM users WHERE id = ?");
        $statement->execute([$id]);
    }    
}
