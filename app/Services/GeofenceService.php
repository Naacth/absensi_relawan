<?php

namespace App\Services;

use App\Models\Setting;

class GeofenceService
{
    /**
     * Hitung jarak dua koordinat GPS menggunakan Haversine Formula (dalam satuan meter).
     *
     * @param float $lat1 Latitude titik 1 (user)
     * @param float $lon1 Longitude titik 1 (user)
     * @param float $lat2 Latitude titik 2 (SPPG)
     * @param float $lon2 Longitude titik 2 (SPPG)
     * @return float Jarak dalam meter
     */
    public static function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // Radius bumi dalam meter

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo   = deg2rad($lat2);
        $lonTo   = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(
            pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)
        ));

        return round($angle * $earthRadius, 2);
    }

    /**
     * Validasi apakah user berada dalam radius geofencing SPPG.
     *
     * @param float $userLat
     * @param float $userLng
     * @return array [ 'is_within' => bool, 'distance_m' => float, 'allowed_radius_m' => float ]
     */
    public static function checkWithinRadius(float $userLat, float $userLng): array
    {
        $sppgLat    = (float) Setting::get('sppg_latitude', config('services.sppg.latitude', -6.2577369));
        $sppgLng    = (float) Setting::get('sppg_longitude', config('services.sppg.longitude', 106.5500299));
        $radiusM    = (float) Setting::get('geofence_radius', config('services.sppg.radius_meters', 100));

        $distance = static::calculateDistance($userLat, $userLng, $sppgLat, $sppgLng);

        return [
            'is_within'        => $distance <= $radiusM,
            'distance_m'       => $distance,
            'allowed_radius_m' => $radiusM,
            'sppg_lat'         => $sppgLat,
            'sppg_lng'         => $sppgLng,
        ];
    }
}

