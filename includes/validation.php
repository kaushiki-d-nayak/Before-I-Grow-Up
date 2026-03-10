<?php
function normalizeEmail(string $email): string {
    return strtolower(trim($email));
}

function isValidEmail(string $email): bool {
    if ($email === '' || strlen($email) > 254 || preg_match('/\s/', $email)) {
        return false;
    }
    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        return false;
    }
    return str_ends_with($email, '@gmail.com');
}
