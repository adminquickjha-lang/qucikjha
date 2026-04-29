<?php

namespace App\Http\Controllers;

use App\Mail\ProfessionalReviewRequestMail;
use App\Models\ProfessionalReview;
use App\Models\SafetyDocument;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PayPalController extends Controller
{
    public function checkout(SafetyDocument $document)
    {
        $price = match (strtoupper($document->document_type)) {
            'JHA' => 19.90,
            'AHA' => 19.90,
            'JSA' => 19.00,
            default => 19.90,
        };

        $previewRoute = route('preview.'.strtolower($document->document_type), ['id' => $document->id]);

        try {
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();

            $response = $provider->createOrder([
                'intent' => 'CAPTURE',
                'application_context' => [
                    'return_url' => route('paypal.success', ['document' => $document->id]),
                    'cancel_url' => $previewRoute,
                    'landing_page' => 'BILLING',
                    'user_action' => 'PAY_NOW',
                ],
                'purchase_units' => [
                    0 => [
                        'amount' => [
                            'currency_code' => 'USD',
                            'value' => number_format($price, 2, '.', ''),
                        ],
                        'description' => 'Safety Document: '.$document->project_name,
                    ],
                ],
            ]);

            if (isset($response['id']) && $response['id'] != null) {
                $document->update([
                    'stripe_session_id' => $response['id'],
                    'amount' => $price,
                ]);

                foreach ($response['links'] as $links) {
                    if ($links['rel'] == 'approve') {
                        return redirect()->away($links['href']);
                    }
                }
            }

            $errorMessage = $response['message'] ?? 'PayPal did not return a valid order. Please try again.';
            Log::error('PayPal checkout failed', ['document_id' => $document->id, 'response' => $response]);

            return redirect($previewRoute)->with('error', $errorMessage);

        } catch (\Exception $e) {
            Log::error('PayPal checkout exception', ['document_id' => $document->id, 'error' => $e->getMessage()]);

            return redirect($previewRoute)->with('error', 'Payment could not be initiated. Please try again or contact support.');
        }
    }

    public function success(Request $request, SafetyDocument $document)
    {
        $previewRoute = route('preview.'.strtolower($document->document_type), ['id' => $document->id]);

        try {
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();

            $response = $provider->capturePaymentOrder($request['token']);

            if (isset($response['status']) && $response['status'] == 'COMPLETED') {
                $transactionId = $response['purchase_units'][0]['payments']['captures'][0]['id'] ?? null;

                $document->update([
                    'is_paid' => true,
                    'transaction_id' => $transactionId,
                ]);

                return redirect($previewRoute)->with('success', 'Document unlocked successfully via PayPal!');
            }

            Log::warning('PayPal payment not completed', ['document_id' => $document->id, 'status' => $response['status'] ?? 'unknown', 'response' => $response]);

            return redirect($previewRoute)->with('error', 'PayPal payment verification failed. Please contact support if you were charged.');

        } catch (\Exception $e) {
            Log::error('PayPal success exception', ['document_id' => $document->id, 'error' => $e->getMessage()]);

            return redirect($previewRoute)->with('error', 'Payment verification failed. Please contact support.');
        }
    }

    public function professionalReviewCheckout(ProfessionalReview $review)
    {
        $document = $review->safetyDocument;
        $previewRoute = route('preview.'.strtolower($document->document_type), ['id' => $document->id]);

        try {
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();

            $response = $provider->createOrder([
                'intent' => 'CAPTURE',
                'application_context' => [
                    'return_url' => route('paypal.review-success', ['review' => $review->id]),
                    'cancel_url' => $previewRoute,
                    'landing_page' => 'BILLING',
                    'user_action' => 'PAY_NOW',
                ],
                'purchase_units' => [
                    0 => [
                        'amount' => [
                            'currency_code' => 'USD',
                            'value' => '5.00',
                        ],
                        'description' => 'Professional Review: '.$document->project_name,
                    ],
                ],
            ]);

            if (isset($response['id']) && $response['id'] != null) {
                $review->update(['stripe_session_id' => $response['id']]);

                foreach ($response['links'] as $links) {
                    if ($links['rel'] == 'approve') {
                        return redirect()->away($links['href']);
                    }
                }
            }

            $errorMessage = $response['message'] ?? 'PayPal did not return a valid order. Please try again.';
            Log::error('PayPal review checkout failed', ['review_id' => $review->id, 'response' => $response]);

            return redirect($previewRoute)->with('error', $errorMessage);

        } catch (\Exception $e) {
            Log::error('PayPal review checkout exception', ['review_id' => $review->id, 'error' => $e->getMessage()]);

            return redirect($previewRoute)->with('error', 'Payment could not be initiated. Please try again or contact support.');
        }
    }

    public function successReview(Request $request, ProfessionalReview $review)
    {
        $previewRoute = route('preview.'.strtolower($review->safetyDocument->document_type), ['id' => $review->safety_document_id]);

        try {
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();

            $response = $provider->capturePaymentOrder($request['token']);

            if (isset($response['status']) && $response['status'] == 'COMPLETED') {
                $transactionId = $response['purchase_units'][0]['payments']['captures'][0]['id'] ?? null;

                $review->update([
                    'is_paid' => true,
                    'transaction_id' => $transactionId,
                ]);

                // Notify Admin
                $adminEmail = User::where('role', 'admin')->first()?->email ?? 'admin@example.com';
                try {
                    Mail::to($adminEmail)->send(new ProfessionalReviewRequestMail($review));
                } catch (\Exception $mailException) {
                    Log::error('Admin notification mail failed', ['error' => $mailException->getMessage()]);
                }

                return redirect($previewRoute)->with('success', 'Professional review requested successfully via PayPal!');
            }

            Log::warning('PayPal review payment not completed', ['review_id' => $review->id, 'status' => $response['status'] ?? 'unknown']);

            return redirect($previewRoute)->with('error', 'PayPal payment verification failed. Please contact support if you were charged.');

        } catch (\Exception $e) {
            Log::error('PayPal review success exception', ['review_id' => $review->id, 'error' => $e->getMessage()]);

            return redirect($previewRoute)->with('error', 'Payment verification failed. Please contact support.');
        }
    }
}
