<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * SmsGatewayService
 *
 * Handles the actual HTTP dispatch to an SMS gateway.
 * Supported drivers: ssl (SSL Wireless BD), gp (Grameenphone BSMS), twilio, log.
 * Select driver via SMS_DRIVER in .env.
 */
class SmsGatewayService
{
    protected array $cfg;

    public function __construct()
    {
        $this->cfg = config('services.sms', []);
    }

    /**
     * Send an SMS to the given E.164 phone number.
     *
     * @return array{success: bool, response: string, cost: float}
     */
    public function send(string $phone, string $message): array
    {
        $driver = $this->cfg['driver'] ?? 'log';

        return match ($driver) {
            'ssl'    => $this->sendViaSsl($phone, $message),
            'gp'     => $this->sendViaGp($phone, $message),
            'twilio' => $this->sendViaTwilio($phone, $message),
            default  => $this->sendViaLog($phone, $message),
        };
    }

    // -------------------------------------------------------------------------
    // SSL Wireless (Bangladesh — https://www.sslwireless.com/sms-api)
    // -------------------------------------------------------------------------
    protected function sendViaSsl(string $phone, string $message): array
    {
        $csmsId = Str::uuid()->toString();

        try {
            $response = Http::timeout(15)->post(
                'https://sms.sslwireless.com/pushapi/dynamic/server.php',
                [
                    'api_token' => $this->cfg['ssl_api_token'] ?? '',
                    'sid'       => $this->cfg['ssl_sid'] ?? '',
                    'msisdn'    => $phone,
                    'sms'       => $message,
                    'csmsid'    => $csmsId,
                ]
            );

            $body    = $response->body();
            $success = $response->successful()
                && str_contains(strtolower($body), 'success');

            return [
                'success'  => $success,
                'response' => $body,
                'cost'     => 0.50,   // approx BDT 0.50 / SMS
            ];
        } catch (\Throwable $e) {
            Log::error('SSL Wireless SMS error', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);
            return ['success' => false, 'response' => $e->getMessage(), 'cost' => 0.0];
        }
    }

    // -------------------------------------------------------------------------
    // Grameenphone BSMS Gateway
    // -------------------------------------------------------------------------
    protected function sendViaGp(string $phone, string $message): array
    {
        try {
            $response = Http::timeout(15)
                ->withBasicAuth(
                    $this->cfg['gp_username'] ?? '',
                    $this->cfg['gp_password'] ?? ''
                )
                ->post($this->cfg['gp_api_url'] ?? 'https://bsms.grameenphone.com/api/v2/sms/send', [
                    'from'    => $this->cfg['from_number'] ?? '',
                    'to'      => [$phone],
                    'message' => $message,
                ]);

            $data    = $response->json();
            $success = $response->successful()
                && ($data['status'] ?? '') === 'success';

            return [
                'success'  => $success,
                'response' => $response->body(),
                'cost'     => 0.45,
            ];
        } catch (\Throwable $e) {
            Log::error('GP BSMS error', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);
            return ['success' => false, 'response' => $e->getMessage(), 'cost' => 0.0];
        }
    }

    // -------------------------------------------------------------------------
    // Twilio (international / fallback)
    // -------------------------------------------------------------------------
    protected function sendViaTwilio(string $phone, string $message): array
    {
        $sid   = $this->cfg['twilio_sid']   ?? '';
        $token = $this->cfg['twilio_token'] ?? '';
        $from  = $this->cfg['from_number']  ?? '';

        try {
            $response = Http::timeout(15)
                ->withBasicAuth($sid, $token)
                ->asForm()
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                    'From' => $from,
                    'To'   => $phone,
                    'Body' => $message,
                ]);

            $data    = $response->json();
            $success = $response->successful() && isset($data['sid']);

            return [
                'success'  => $success,
                'response' => $response->body(),
                'cost'     => (float) ($data['price'] ?? 0),
            ];
        } catch (\Throwable $e) {
            Log::error('Twilio SMS error', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);
            return ['success' => false, 'response' => $e->getMessage(), 'cost' => 0.0];
        }
    }

    // -------------------------------------------------------------------------
    // Log driver — for local development / testing (no real SMS sent)
    // -------------------------------------------------------------------------
    protected function sendViaLog(string $phone, string $message): array
    {
        Log::info('[SMS LOG DRIVER] Would send SMS', [
            'to'      => $phone,
            'message' => $message,
        ]);

        return ['success' => true, 'response' => 'logged', 'cost' => 0.0];
    }
}
