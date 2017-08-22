<?php namespace Vpn\VpnApi;

use GuzzleHttp\Exception\RequestException;
use Staskjs\SimpleApi\SimpleJsonApi;

class Vpn extends SimpleJsonApi {

    protected $headers = [
        'Accept' => 'application/json',
    ];

    public function __construct($url) {
        parent::__construct($url);

        $this->default_query['token'] = config('vpn-api.token');
    }

    public function getKey($deviceId, $groupId) {
        info("Getting key for device $deviceId and group $groupId");

        $params = ['device_id' => $deviceId, 'group_id' => $groupId];
        $this->request('GET', 'api/getkey', $params);
        return $this->getData();
    }

    public function setGroups($deviceId, $groupId, $ports = [], $action = 'add') {
        $portsStr = implode(',', $ports);

        info("Setting device $deviceId to group $groupId, action $action, ports $portsStr");

        $params = [
            'device_id' => $deviceId,
            'group_id' => $groupId,
            'action' => $action,
            'ports' => $ports,
        ];
        $this->request('POST', 'api/setgroups', $params);
        return $this->getData();
    }

    public function getStatus($deviceId) {
        info("Getting status for device $deviceId");

        $this->request('GET', 'api/getstatus', [
            'device_id' => $deviceId,
        ]);
        return $this->getData();
    }

    public function stopKey($deviceId, $action) {
        info("Stopping key for device $deviceId, action $action");

        $this->request('GET', 'api/stopkey', [
            'device_id' => $deviceId,
            'action' => $action,
        ]);
        return $this->getData();
    }

    public function downloadKey($url) {
        return file_get_contents($this->url . $url);
    }
}
