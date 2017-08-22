<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;
use App\Http\Requests\DeviceIndexRequest;
use App\Http\Requests\DeviceWriteRequest;
use App\Http\Requests\DeviceDestroyRequest;
use App\Exceptions\UserCannotCreateDeviceException;

class DevicesController extends \Staskjs\Rest\RestController
{
    protected $model = 'Device';

    protected $allowedWith = ['group', 'user', 'creator'];

    protected $append = [];

    protected $indexRequest = DeviceIndexRequest::class;

    protected $showRequest = DeviceShowRequest::class;

    protected $storeRequest = DeviceWriteRequest::class;

    protected $updateRequest = DeviceWriteRequest::class;

    protected $destroyRequest = DeviceDestroyRequest::class;

    protected function generateMetadata() {
        return [
            'users' => \App\User::select(['id', 'fname', 'lname'])->get(),
            'groups' => \App\Group::forUser(auth()->user())->select(['id', 'name'])->get(),
        ];
    }

    protected function getFiltered() {

        $items = parent::getFiltered();

        $items = $items->forUser(auth()->user());

        if (request()->has('item_id')) {
            $items = $items->whereId(request('item_id'));
        }

        if (request()->has('user_id')) {
            $items = $items->whereUserId(request('user_id'));
        }

        if (request()->has('group_id')) {
            $items = $items->whereGroupId(request('group_id'));
        }

        if (request()->has('name')) {
            $name = request('name');
            $items = $items->where('name', 'LIKE', "%$name%");
        }

        if (request()->has('comment')) {
            $comment = request('comment');
            $items = $items->where('comment', 'LIKE', "%$comment%");
        }

        if (request()->has('ip')) {
            $items = $items->whereIp(request('ip'));
        }

        return $items;
    }

    public function store() {
        if (!auth()->user()->canCreateDevices) {
            throw new UserCannotCreateDeviceException;
        }
        return parent::store();
    }

    protected function beforeSave($object) {
        $object->creator_id = auth()->user()->id;
        return $object;
    }

    public function status($device) {
        $status = $device->getStatus();
        return response()->json(['status' => $status]);
    }

    protected function getRequestData() {
        return request()->except('user_id');
    }
}
