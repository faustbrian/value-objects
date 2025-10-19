<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\ValueObjects\Email;
use Cline\ValueObjects\Exceptions\Validation\InvalidEmailException;

dataset('valid_emails', [
    'standard' => ['user@example.com'],
    'with subdomain' => ['user@mail.example.com'],
    'with plus addressing' => ['user+tag@example.com'],
    'with dots in local' => ['first.last@example.com'],
    'with numbers' => ['user123@example456.com'],
    'with hyphens' => ['user-name@example-domain.com'],
]);

dataset('invalid_emails', [
    'no at sign' => ['userexample.com'],
    'missing domain' => ['user@'],
    'missing local' => ['@example.com'],
    'empty string' => [''],
    'just at sign' => ['@'],
    'double at' => ['user@@example.com'],
    'spaces' => ['user @example.com'],
]);

describe('Email', function (): void {
    describe('Happy Paths', function (): void {
        test('creates from valid email formats', function (string $email): void {
            $vo = Email::createFromString($email);

            expect($vo)->toBeInstanceOf(Email::class);
            expect($vo->toString())->toBe($email);
        })->with('valid_emails');

        test('returns correct string representation', function (): void {
            $validEmail = 'test@example.com';
            $email = Email::createFromString($validEmail);

            expect($email->toString())->toBe($validEmail);
        });
    });

    describe('Sad Paths', function (): void {
        test('throws exception for invalid email formats', function (string $email): void {
            Email::createFromString($email);
        })->with('invalid_emails')->throws(InvalidEmailException::class);
    });

    describe('Edge Cases', function (): void {
        test('handles maximum length local part', function (): void {
            $maxLocal = str_repeat('a', 64);
            $email = Email::createFromString($maxLocal.'@example.com');

            expect($email->toString())->toBe($maxLocal.'@example.com');
        });

        test('handles multiple subdomains', function (): void {
            $email = Email::createFromString('user@mail.corporate.example.com');

            expect($email->toString())->toBe('user@mail.corporate.example.com');
        });

        test('handles special characters in local part', function (): void {
            $email = Email::createFromString('user.name+tag@example.com');

            expect($email->toString())->toBe('user.name+tag@example.com');
        });

        test('preserves case sensitivity', function (): void {
            $email = Email::createFromString('User@Example.COM');

            expect($email->toString())->toBe('User@Example.COM');
        });
    });

    describe('Regressions', function (): void {
        // Add when bugs are discovered
    });
});
