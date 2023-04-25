<?php

namespace Kawankoding\Fcm;

/**
 * Class Fcm
 * @package Kawankoding\Fcm
 */
class Fcm
{
    protected $token;
    protected $topic;
    protected $data;
    protected $notification;
    protected $android;
    protected $apns;
    protected $webpush;
    protected $fcmOptions;

    protected $serverKey;
    protected $endpoint;

    protected $responseLogEnabled = false;

    public function __construct(string $projectId)
    {
        $endpoint = 'https://fcm.googleapis.com/v1/projects/' . $projectId . '/messages:send';
        $this->endpoint = $endpoint;
    }

    public function serverKey(string $serverKey): self
    {
        $this->serverKey = $serverKey;

        return $this;
    }

    public function token(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function topic(?string $topic): self
    {
        $this->topic = $topic;

        return $this;
    }

    public function data(?array $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function notification(?array $notification): self
    {
        $this->notification = $notification;

        return $this;
    }

    public function enableResponseLog($enable = true): self
    {
        $this->responseLogEnabled = $enable;

        return $this;
    }

    public function android(?array $android): self
    {
        $this->android = $android;

        return $this;
    }

    public function apns(?array $apns): self
    {
        $this->apns = $apns;

        return $this;
    }

    public function webpush(?array $webpush): self
    {
        $this->webpush = $webpush;

        return $this;
    }

    public function fcmOptions(?array $fcmOptions): self
    {
        $this->fcmOptions = $fcmOptions;

        return $this;
    }

    public function send()
    {
        $payloads = [
            'message' => [],
        ];

        if ($this->data) {
            $payloads['message']['data'] = $this->data;
        }

        if ($this->notification) {
            $payloads['message']['notification'] = $this->notification;
        }

        if ($this->android) {
            $payloads['message']['android'] = $this->android;
        }

        if ($this->apns) {
            $payloads['message']['apns'] = $this->apns;
        }

        if ($this->webpush) {
            $payloads['message']['webpush'] = $this->webpush;
        }

        if ($this->fcmOptions) {
            $payloads['message']['fcm_options'] = $this->fcmOptions;
        }

        if ($this->topic) {
            $payloads['message']['topic'] = $this->topic;
        } else {
            $payloads['message']['token'] = $this->token;
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
