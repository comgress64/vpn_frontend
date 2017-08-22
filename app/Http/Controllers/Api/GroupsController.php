<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;
use App\Http\Requests\GroupIndexRequest;
use App\Http\Requests\GroupWriteRequest;
use App\Http\Requests\GroupDestroyRequest;

class GroupsController extends \Staskjs\Rest\RestController
{
    protected $model = 'Group';

    protected $allowedWith = ['groupPorts'];

    protected $append = ['ports'];

    protected $indexRequest = GroupIndexRequest::class;

    protected $showRequest = GroupShowRequest::class;

    protected $storeRequest = GroupWriteRequest::class;

    protected $updateRequest = GroupWriteRequest::class;

    protected $destroyRequest = GroupDestroyRequest::class;

    protected function generateMetadata() {
        return [
            'ports' => \App\GroupPort::orderBy('port', 'ASC')->pluck('port')->unique(),
        ];
    }

    protected function getFiltered() {

        $items = parent::getFiltered();

        $items = $items->forUser(auth()->user());

        if (request()->has('name')) {
            $name = request('name');
            $items = $items->where('name', 'LIKE', "%$name%");
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

        if (request()->has('ip')) {
            $items = $items->whereIp(request('ip'));
        }

        if (request()->has('port')) {
            $items = $items->whereHas('groupPorts', function($query) {
                $query->where('port', request('port'));
            });
        }

        return $items;

    }

    protected function afterSave($object) {
        if (request()->has('ports')) {
            $object->syncPorts(request('ports'));
        }

        // Autoattach current user to new group (except superusers, they don't need it)
        if ($object->wasRecentlyCreated && !auth()->user()->isSuperadmin()) {
            $object->users()->sync([auth()->user()->id]);
        }

        return $object;
    }
}
