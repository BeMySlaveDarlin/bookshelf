<?php

declare(strict_types=1);

namespace App\Helper;

use Webmozart\Assert\Assert;

class TypeCaster
{
    public static function cast($value, ?string $type = null, bool $isNullable = false)
    {
        if (null !== $type) {
            $method = $isNullable ? 'asNullable' . \ucfirst($type) : 'as' . \ucfirst($type);
            if (\method_exists(self::class, $method)) {
                return self::$method($value);
            }
        }

        return $value;
    }

    public static function asArray($value): array
    {
        return \is_array($value) ? $value : (array) $value;
    }

    public static function asString($value): string
    {
        Assert::scalar($value);

        return (string) $value;
    }

    public static function asInt($value): int
    {
        Assert::scalar($value);

        return (int) $value;
    }

    public static function asBool($value): bool
    {
        Assert::nullOrScalar($value);

        return \filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    public static function asFloat($value): float
    {
        Assert::nullOrScalar($value);

        return (float) $value;
    }

    public static function asNullableInt($value): ?int
    {
        Assert::nullOrScalar($value);

        return \is_numeric($value) ? static::asInt($value) : null;
    }

    public static function asNullableFloat($value): ?float
    {
        Assert::nullOrScalar($value);

        return \is_numeric($value) ? static::asFloat($value) : null;
    }

    public static function asNullableBool($value): ?bool
    {
        Assert::nullOrScalar($value);

        return null !== $value ? static::asBool($value) : null;
    }

    public static function asNullableString($value): ?string
    {
        Assert::nullOrScalar($value);

        return null !== $value ? static::asString($value) : null;
    }

    /**
     * @return string[]
     */
    public static function asArrayOfString(array $value): array
    {
        $result = [];

        foreach ($value as $key => $arrayValue) {
            $result[$key] = static::asString($arrayValue);
        }

        return $result;
    }

    /**
     * @return float[]
     */
    public static function asArrayOfFloat(array $value): array
    {
        $result = [];

        foreach ($value as $key => $arrayValue) {
            $result[$key] = static::asFloat($arrayValue);
        }

        return $result;
    }

    /**
     * @return int[]
     */
    public static function asArrayOfInt(array $value): array
    {
        $result = [];

        foreach ($value as $key => $arrayValue) {
            $result[$key] = static::asInt($arrayValue);
        }

        return $result;
    }

    /**
     * @return bool[]
     */
    public static function asArrayOfBool(array $value): array
    {
        $result = [];

        foreach ($value as $key => $arrayValue) {
            $result[$key] = static::asBool($arrayValue);
        }

        return $result;
    }

    public static function nullIfEmptyString($value)
    {
        return '' === $value ? null : $value;
    }

    public static function nullIfEmpty($value)
    {
        return empty($value) ? null : $value;
    }
}
