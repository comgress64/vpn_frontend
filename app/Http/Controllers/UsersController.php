<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetUserVpnKeyRequest;

class UsersController extends Controller
{
    /**
     * Download vpn key for user
     */
    public function downloadKey(GetUserVpnKeyRequest $request, $user) {
        if (!$user->ownDevice) {
            throw new \Exception('User does not have device attached');
        }

        \DB::beginTransaction();

        $result = $user->ownDevice->getVpnKey();
        $url = $result['key_url'];

        \DB::commit();

        return redirect()->to($url);
    }
}
