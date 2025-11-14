<?php

namespace App\Utils;

class Validator
{
    /**
     * Sanitizes and validates a string input.
     * @param string $input
     * @param int $filter FILTER_SANITIZE_* constant
     * @param array $options Validation options (e.g., 'min_length', 'max_length', 'required')
     * @return string|null Sanitized string or null if validation fails
     */
    public static function sanitizeAndValidateString(string $input, int $filter = FILTER_SANITIZE_STRING, array $options = []): ?string
    {
        $sanitized = filter_var(trim($input), $filter);

        if (isset($options['required']) && $options['required'] && empty($sanitized)) {
            return null;
        }

        if (isset($options['min_length']) && strlen($sanitized) < $options['min_length']) {
            return null;
        }

        if (isset($options['max_length']) && strlen($sanitized) > $options['max_length']) {
            return null;
        }

        return $sanitized;
    }

    /**
     * Validates and sanitizes an integer.
     * @param mixed $input
     * @param array $options Validation options (e.g., 'min_range', 'max_range')
     * @return int|null Sanitized integer or null if validation fails
     */
    public static function sanitizeAndValidateInt(mixed $input, array $options = []): ?int
    {
        $filtered = filter_var($input, FILTER_VALIDATE_INT, $options);
        return $filtered !== false ? (int)$filtered : null;
    }

    /**
     * Validates and sanitizes a float.
     * @param mixed $input
     * @param array $options Validation options (e.g., 'min_range', 'max_range')
     * @return float|null Sanitized float or null if validation fails
     */
    public static function sanitizeAndValidateFloat(mixed $input, array $options = []): ?float
    {
        $filtered = filter_var($input, FILTER_VALIDATE_FLOAT, $options);
        return $filtered !== false ? (float)$filtered : null;
    }

    /**
     * Validates a date string in YYYY-MM-DD format.
     * @param string $date
     * @return string|null Valid date string or null
     */
    public static function validateDate(string $date): ?string
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return null;
        }
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date ? $date : null;
    }
}
