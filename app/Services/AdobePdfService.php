<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdobePdfService
{
    protected $clientId;

    protected $clientSecret;

    protected $baseUrl = 'https://pdf-services-ue1.adobe.io';

    protected $imsUrl = 'https://ims-na1.adobelogin.com/ims/token/v3';

    public function __construct()
    {
        $this->clientId = config('services.adobe.client_id');
        $this->clientSecret = config('services.adobe.client_secret');
    }

    public function getAccessToken()
    {
        $response = Http::asForm()->post($this->imsUrl, [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'client_credentials',
            'scope' => 'openid,AdobeID,DCAPI',
        ]);

        if ($response->failed()) {
            throw new \Exception('Adobe Auth Failed: '.$response->body());
        }

        return $response->json()['access_token'];
    }

    public function convertPdfToDocx($pdfPath)
    {
        set_time_limit(180); // Ensure PHP doesn't timeout during polling
        $accessToken = $this->getAccessToken();

        // 1. Create Asset (Upload URL)
        $assetResponse = Http::withHeaders([
            'X-API-Key' => $this->clientId,
            'Authorization' => 'Bearer '.$accessToken,
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl.'/assets', [
            'mediaType' => 'application/pdf',
        ]);

        if ($assetResponse->failed()) {
            Log::error('Adobe Asset Creation Failed Body: '.$assetResponse->body());
            throw new \Exception('Adobe Asset Creation Failed: '.$assetResponse->body());
        }

        $assetData = $assetResponse->json();
        $uploadUrl = $assetData['uploadUri'];
        $assetId = $assetData['assetID'];

        Log::info('Adobe Asset Created: '.$assetId);

        // 2. Upload the PDF file
        $pdfContent = file_get_contents($pdfPath);
        $uploadResponse = Http::withBody($pdfContent, 'application/pdf')->put($uploadUrl);

        if ($uploadResponse->failed()) {
            Log::error('Adobe Upload Failed: '.$uploadResponse->status().' '.$uploadResponse->body());
            throw new \Exception('Adobe Upload Failed: '.$uploadResponse->body());
        }

        // 3. Create Export Job
        $jobResponse = Http::withHeaders([
            'x-api-key' => $this->clientId,
            'Authorization' => 'Bearer '.$accessToken,
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl.'/operation/exportpdf', [
            'assetID' => $assetId,
            'targetFormat' => 'docx',
        ]);

        if (! in_array($jobResponse->status(), [201, 202])) {
            Log::error('Adobe Export Job Failed ('.$jobResponse->status().'): '.$jobResponse->body());

            // Second attempt with different structure
            $jobResponse = Http::withHeaders([
                'x-api-key' => $this->clientId,
                'Authorization' => 'Bearer '.$accessToken,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl.'/operation/exportpdf', [
                'assetID' => $assetId,
                'exportPDF' => json_decode('{"targetFormat": "docx"}'),
            ]);
        }

        if (! in_array($jobResponse->status(), [201, 202])) {
            Log::error('Adobe Export Job Retry Failed ('.$jobResponse->status().'): '.$jobResponse->body());
            throw new \Exception('Adobe Export Job Failed: '.$jobResponse->body());
        }

        $statusUrl = $jobResponse->header('location');

        // 4. Poll for completion
        $maxRetries = 60;
        $retryCount = 0;
        $resultDownloadUrl = null;

        while ($retryCount < $maxRetries) {
            sleep(2);
            $statusCheck = Http::withHeaders([
                'x-api-key' => $this->clientId,
                'Authorization' => 'Bearer '.$accessToken,
            ])->get($statusUrl);

            if ($statusCheck->failed()) {
                Log::error('Adobe Status Check Failed ('.$statusCheck->status().'): '.$statusCheck->body());
                throw new \Exception('Adobe Status Check Failed: '.$statusCheck->body());
            }

            $statusData = $statusCheck->json();
            $currentStatus = $statusData['status'] ?? 'unknown';
            Log::info('Adobe Job Status (Attempt '.($retryCount + 1).'): '.$currentStatus);

            if ($currentStatus === 'completed' || $currentStatus === 'done') {
                $resultDownloadUrl = $statusData['asset']['downloadUri'] ?? null;
                $resultAssetId = $statusData['assetID'] ?? ($statusData['output']['assetID'] ?? null);
                break;
            }

            if ($currentStatus === 'failed' || $currentStatus === 'error') {
                Log::error('Adobe Job Failed Response: '.json_encode($statusData));
                throw new \Exception('Adobe Job Failed: '.json_encode($statusData));
            }

            $retryCount++;
        }

        if (! $resultDownloadUrl && ! $resultAssetId) {
            throw new \Exception('Adobe Job Timed Out or Result Missing');
        }

        if ($resultDownloadUrl) {
            return $resultDownloadUrl;
        }

        // 5. Get Download URL (Fallback)
        $downloadResponse = Http::withHeaders([
            'x-api-key' => $this->clientId,
            'Authorization' => 'Bearer '.$accessToken,
        ])->get($this->baseUrl.'/assets/'.$resultAssetId.'/download');

        if ($downloadResponse->failed()) {
            throw new \Exception('Adobe Download Failed: '.$downloadResponse->body());
        }

        return $downloadResponse->json()['downloadUri'];
    }

    public function downloadAsset($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Match the lenient behavior of preview
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');

        $data = curl_exec($ch);

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            throw new \Exception('CURL Download Failed: '.$error_msg);
        }

        curl_close($ch);

        return $data;
    }
}
