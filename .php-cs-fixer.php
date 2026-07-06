<?php

declare(strict_types=1);

use PhpCsFixer\Finder;
use PhpCsFixer\Config;

/**
 * Finder — определяет, какие файлы будут проверяться и форматироваться.
 * Здесь мы указываем директории проекта, в которых нужно искать PHP-файлы.
 */
$finder = Finder::create()
    ->in([
        __DIR__ . '/app',             // Основная бизнес-логика приложения
        __DIR__ . '/bootstrap',       // Загрузка фреймворка
        __DIR__ . '/config',          // Конфигурации Laravel
        __DIR__ . '/database',        // Миграции, сидеры, фабрики
        __DIR__ . '/public',          // Публичные PHP-файлы (редко используются)
        __DIR__ . '/routes',          // Файлы маршрутов
        __DIR__ . '/tests',           // Тесты
    ])
    ->exclude([
        'vendor',                     // Внешние пакеты — не трогаем
        'storage',                    // Логи, кеши, сессии — не форматируем
        'docker',                     // Директории контейнеризации
    ])
    ->name('*.php')                   // Обрабатывать только .php файлы
    ->ignoreDotFiles(true)            // Игнорировать скрытые файлы (.gitignore, .env.example и др.)
    ->ignoreVCS(true);                // Игнорировать VCS-файлы (.git)

/**
 * Config — сам объект конфигурации правил PHP CS Fixer.
 * Он задаёт стиль и правила, по которым будет форматироваться код.
 */
return new Config()
    ->setRiskyAllowed(true)                    // Разрешаем "опасные" фиксы (они не ломают проект, но меняют логику в редких случаях)
    ->setRules([
        '@PSR12' => true,                      // Базовый набор правил PSR-12 (в Laravel используется PSR-12, но PSR-2 тоже ок)
        'declare_strict_types' => true,        // Автоматически добавлять declare(strict_types=1)
        'no_unused_imports' => true,           // Удалять неиспользуемые use
        'ordered_imports' => ['sort_algorithm' => 'alpha'], // Сортировать use по алфавиту
    ])
    ->setFinder($finder);                      // Применяем правила к выбранным файлам


