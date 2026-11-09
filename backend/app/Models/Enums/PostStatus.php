<?php

namespace App\Models\Enums;

enum PostStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
}
