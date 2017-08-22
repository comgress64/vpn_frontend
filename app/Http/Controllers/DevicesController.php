<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetDeviceVpnKeyRequest;

class DevicesController extends Controller
{
    /**
     * Download vpn key for device
     */
    public function downloadKey(GetDeviceVpnKeyRequest $request, $device) {
        \DB::beginTransaction();

        $result = $device->getVpnKey();
        $url = $result['key_url'];

        \DB::commit();

        return redirect()->to($url);
    }
}
