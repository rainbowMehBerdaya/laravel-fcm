<?php

namespace Kawankoding\Fcm;

/**
 * Class Fcm
 * @package Kawankoding\Fcm
 */
class Fcm
{
    protected $recipients;
    protected $topic;
    protected $data;
    protected $notification;
    protected $timeToLive;
    protected $priority;
    protected $package;
    protected $android;
    protected $apns;
    protected $webpush;
    protected $fcmOptions;

    protected $serverKey;
    protected $endpoint;

    protected $responseLogEnabled = false;

    public function __construct($serverKey, $projectId)
    {
        $this->serverKey = $serverKey;

        $endpoint = 'https://fcm.googleapis.com/v1/projects/' . $projectId . '/messages:send';
        $this->endpoint = $endpoint;
    }

    public function to($recipients)
    {
        $this->recipients = $recipients;

        return $this;
    }

    public function toTopic($topic)
    {
        $this->topic = $topic;

        return $this;
    }

    public function data($data = [])
    {
        $this->data = $data;

        return $this;
    }

    public function notification($notification = [])
    {
        $this->notification = $notification;

        return $this;
    }

    public function priority(string $priority)
    {
        $this->priority = $priority;

        return $this;
    }

    public function timeToLive($timeToLive)
    {
        if ($timeToLive < 0) {
            $timeToLive = 0; // (0 seconds)
        }
        if ($timeToLive > 2419200) {
            $timeToLive = 2419200; // (28 days)
        }

        $this->timeToLive = $timeToLive;

        return $this;
    }

    public function setPackage($package)
    {
        $this->package = $package;

        return $this;
    }

    public function enableResponseLog($enable = true)
    {
        $this->responseLogEnabled = $enable;

        return $this;
    }

    public function android($android = [])
    {
        $this->android = $android;

        return $this;
    }

    public function apns($apns = [])
    {
        $this->apns = $apns;

        return $this;
    }

    public function webpush($webpush = [])
    {
        $this->webpush = $webpush;

        return $this;
    }

    public function fcmOptions($fcmOptions = [])
    {
        $this->fcmOptions = $fcmOptions;

        return $this;
    }

    public function send()
    {
        $payloads = [
            'message' => [
                'name' => '',
                'data' => $this->data,
                'notification' => $this->notification,
                'android' => $this->android,
                'apns' => $this->apns,
                'webpush' => $this->webpush,
                'fcm_options' => $this->fcmOptions,
            ],
        ];

        if (!empty($this->package)) {
            $payloads['restricted_package_name'] = $this->package;
        }

        if ($this->topic) {
            $payloads['topic'] = $this->topic;
        } else {
            $payloads['token'] = $this->recipients;
        }

        if ($this->timeToLive !== null && $this->timeToLive >= 0) {
            $payloads['time_to_live'] = (int)$this->timeToLive;
        }

        $headers = [
            'Authorization: Bearer ' . $this->serverKey,
            'Content-Type: application/json',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->endpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payloads));
        $response = curl_exec($ch);

        if ($this->responseLogEnabled) {
            logger('laravel-fcm', ['response' => $response]);
        }

        $result = json_decode($response, true);
        curl_close($ch);

        return $result;
    }
}
