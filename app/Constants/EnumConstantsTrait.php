<?php

declare(strict_types=1);

namespace App\Constants;

use Hyperf\Constants\ConstantsCollector;
use Hyperf\Constants\Exception\ConstantsException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

trait EnumConstantsTrait
{

    use \Hyperf\Constants\EnumConstantsTrait;

    /**
     * @param string $name
     * @param array $arguments
     * @return array|string
     * @throws ConstantsException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @Override
     */
    public static function getValue(string $name, array $arguments): array|string
    {
        if (!str_starts_with($name, 'get')) {
            throw new ConstantsException("The function {$name} is not defined!");
        }

        if (empty($arguments)) {
            throw new ConstantsException('The Code is required');
        }

        $code = array_shift($arguments);
        $name = strtolower(substr($name, 3));

        $message = ConstantsCollector::getValue(static::class, $code, $name);
        $oldMessage = $message;

        $staticClass = static::class;
        $paths = explode('\\', $staticClass);
        $path = strtolower($paths[array_key_last($paths) - 1]);
        if ($path == 'enum') {
            $message = "$path.$staticClass.$message";
        }

        $result = self::translate($message, $arguments);

        // If the result of translate doesn't exist, the result is equal with message, so we will skip it.
        if ($result && $result !== $message) {
            return $result;
        }

        $message = $oldMessage;

        if (!empty($arguments)) {
            return sprintf($message, ...(array)$arguments[0]);
        }

        return $message;
    }
}