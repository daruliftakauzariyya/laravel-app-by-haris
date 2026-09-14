<?php

namespace App\Enums;

use App\Models\Status;

class QuestionStatus
{
    public const PENDING    = 1;
    public const ACCEPTED   = 2;
    public const ASSIGNED   = 3;
    public const ANSWERED   = 4;
    public const APPROVED   = 5;
    public const PUBLISHED  = 6;
    public const REJECTED   = 7;
    public const CORRECTION = 8;

    public const SLUG_PENDING    = 'pending';
    public const SLUG_ACCEPTED   = 'accepted';
    public const SLUG_ASSIGNED   = 'assigned';
    public const SLUG_ANSWERED   = 'answered';
    public const SLUG_APPROVED   = 'approved';
    public const SLUG_PUBLISHED  = 'published';
    public const SLUG_REJECTED   = 'rejected';
    public const SLUG_CORRECTION = 'correction';

    /**
     * Map slug to canonical integer ID
     */
    public static function toId(string|int|null $value): int
    {
        return Status::codeToId($value);
    }

    /**
     * Map integer ID to canonical string slug
     */
    public static function toSlug(int|string|null $id): string
    {
        return Status::idToCode($id);
    }

    /**
     * Get human label for any status ID or slug
     */
    public static function label(int|string|null $status): string
    {
        return Status::getLabel($status);
    }

    /**
     * Render global badge HTML
     */
    public static function badge(int|string|null $status): string
    {
        return Status::renderBadge($status);
    }

    /**
     * All statuses with metadata
     */
    public static function all()
    {
        return Status::allCached();
    }
}
