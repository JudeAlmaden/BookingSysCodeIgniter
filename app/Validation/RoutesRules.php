<?php

namespace App\Validation;

class RoutesRules
{
    public function stopsAndDistanceCountMatch(string $str, string $fields, array $data): bool
    {
        // Access the stations and distances from the $data array
        $stations = $data['station'] ?? [];
        $distances = $data['distance'] ?? [];
    
        // Check if both are arrays and their lengths match
        return is_array($stations) && is_array($distances) && count($stations) === count($distances);
    }
}
