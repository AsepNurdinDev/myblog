<?php

namespace App\Http\Controllers\Api\V1\Concerns;

use Illuminate\Http\Request;

trait ResolvesPerPage
{
    /**
     * Read ?per_page from the request, clamped between 1 and 50.
     * Invalid or missing values fall back to the given default.
     */
    protected function perPage(Request $request, int $default = 12, int $max = 50): int
    {
        $value = $request->query('per_page');

        if (! is_numeric($value)) {
            return $default;
        }

        return max(1, min($max, (int) $value));
    }
}
