<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\VpnKey;

class VpnKeyController extends Controller
{
    // Download key contents
    public function download($hash) {
        $key = VpnKey::where(\DB::raw('MD5(CONCAT(vpn_keys.device_id, vpn_keys.key, vpn_keys.valid_till))'), $hash)->first();
        if (!$key || $key->expired) {
            return response()->json('No key', 422);
        }

        $callback = function() use ($key) {
            $file = fopen('php://output', 'w');
            fputs($file, $key->key);
            fclose($file);
        };

        $nameParts = [];

        $group = $key->device->group;

        if ($group) {
            $nameParts[] = str_replace(' ', '-', $group->name);
        }

        $nameParts[] = str_replace(' ', '-', $key->device->name);

        $nameParts[] = $key->device->ip;

        $filename = implode('_', $nameParts);

        $headers = [
            'Content-Type' => 'application/x-openvpn-profile',
            'Content-Disposition' => "attachment; filename=$filename.ovpn",
        ];

        return response()->stream($callback, 200, $headers);
    }
}
