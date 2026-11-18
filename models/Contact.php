<?php
declare(strict_types=1);

/**
 * Contact model. All SQL uses prepared statements with bound parameters;
 * user input is never concatenated into a query string.
 */
final class Contact
{
    public static function all(): array
    {
        $stmt = Database::connection()->query(
            'SELECT id, name, email, phone, created_at FROM contacts ORDER BY created_at DESC, id DESC'
        );
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT id, name, email, phone, created_at FROM contacts WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public static function create(array $data): int
    {
        $pdo  = Database::connection();
        $stmt = $pdo->prepare('INSERT INTO contacts (name, email, phone) VALUES (:name, :email, :phone)');
        $stmt->execute([
            'name'  => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
        ]);
        return (int) $pdo->lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $stmt = Database::connection()->prepare(
            'UPDATE contacts SET name = :name, email = :email, phone = :phone WHERE id = :id'
        );
        return $stmt->execute([
            'name'  => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'id'    => $id,
        ]);
    }

    public static function delete(int $id): bool
    {
        $stmt = Database::connection()->prepare('DELETE FROM contacts WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Server-side validation (client-side checks are only a convenience).
     *
     * @return array<string,string> field => error message (empty array = valid)
     */
    public static function validate(array $data): array
    {
        $errors = [];

        $name  = $data['name'];
        $email = $data['email'];
        $phone = $data['phone'];

        // Name
        if ($name === '') {
            $errors['name'] = 'Name is required.';
        } elseif (!preg_match('//u', $name) || preg_match('/\p{C}/u', $name)) {
            $errors['name'] = 'Name contains invalid characters.';
        } elseif (mb_strlen($name) > 100) {
            $errors['name'] = 'Name must be 100 characters or fewer.';
        }

        // Email
        if ($email === '') {
            $errors['email'] = 'Email is required.';
        } elseif (strlen($email) > 255 || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $errors['email'] = 'Please enter a valid email address.';
        }

        // Phone: digits plus + ( ) - and spaces, 5 to 15 digits in total.
        $digits = preg_match_all('/\d/', $phone);
        if ($phone === '') {
            $errors['phone'] = 'Phone is required.';
        } elseif (!preg_match('/^[0-9+()\-\s]{5,20}$/', $phone) || $digits < 5 || $digits > 15) {
            $errors['phone'] = 'Phone may contain digits, spaces, + ( ) - and needs 5 to 15 digits.';
        }

        return $errors;
    }
}
