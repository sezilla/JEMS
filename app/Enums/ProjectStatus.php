<?php

namespace App\Enums;

enum ProjectStatus: int
{
    case ACTIVE = 10;
    case COMPLETED = 200;
    case ARCHIVED = 100;
    case CANCELLED = 0;
    case ON_HOLD = 50;

    // Optional: Add a method to get a human-readable label
    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::COMPLETED => 'Completed',
            self::ARCHIVED => 'Archived',
            self::CANCELLED => 'Cancelled',
            self::ON_HOLD => 'On Hold',
        };
    }

    // Optional: Add a method to get the color for the status
    public function color(): string
    {
        return match ($this) {
            self::ACTIVE => 'info',
            self::COMPLETED => 'primary',
            self::ARCHIVED => 'warning',
            self::CANCELLED => 'danger',
            self::ON_HOLD => 'secondary',
        };
    }
}
