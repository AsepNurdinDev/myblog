<?php

namespace App\Models\Enums;

enum ProjectStatus: string
{
    case Ongoing = 'ongoing';
    case Completed = 'completed';
    case Archived = 'archived';
}
