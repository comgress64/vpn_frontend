<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;
use App\Http\Requests\UserIndexRequest;
use App\Http\Requests\UserWriteRequest;
use App\Http\Requests\UserDestroyRequest;

class UsersController extends \Staskjs\Rest\RestController
{
    protected $model = 'User';

    protected $allowedWith = ['groups', 'createdDevices', 'ownDevice'];

    protected $append = ['permissions', 'groupIds'];

    protected $indexRequest = UserIndexRequest::class;

    protected $showRequest = UserShowRequest::class;

    protected $storeRequest = UserWriteRequest::class;

    protected $updateRequest = UserWriteRequest::class;

    protected $destroyRequest = UserDestroyRequest::class;

    protected function generateMetadata() {
        return [
            'groups' => \App\Group::select(['id', 'name'])->get(),
        ];
    }

    protected function getFiltered() {

        $items = parent::getFiltered();

        $items = $items->with('userPermissions');

        if (request()->has('name')) {
            $name = request('name');
            $items = $items->whereRaw('CONCAT(users.lname, \' \', users.fname) LIKE ?', ["%$name%"]);
        }

        if (request()->has('email')) {
            $email = request('email');
            $items = $items->where('email', 'LIKE', "%$email%");
        }

        if (request()->has('address')) {
            $address = request('address');
            $items = $items->where('address', 'LIKE', "%$address%");
        }

        if (request()->has('comment')) {
            $comment = request('comment');
            $items = $items->where('comment', 'LIKE', "%$comment%");
        }

        if (request()->has('group_id')) {
            $items = $items->whereHas('groups', function($query) {
                $query->whereGroupId(request('group_id'));
            });
        }

        if (request()->has('role')) {
            $items = $items->whereRole(request('role'));
        }

        return $items;

    }

    protected function beforeSave($object) {
        if (request()->exists('no_devices_restriction') && request('no_devices_restriction') == 'true') {
            $object->max_devices = -1;
        }

        return $object;
    }

    protected function afterSave($object) {
        if (request()->has('permissions')) {
            $object->syncPermissions(request('permissions'));
        }

        if (request()->has('group_ids') && !$object->isSuperadmin()) {
            $object->syncGroups(request('group_ids'));
        }

        if ($object->wasRecentlyCreated) {
            event(new \App\Events\UserCreated($object));
        }

        return $object;
    }
}
