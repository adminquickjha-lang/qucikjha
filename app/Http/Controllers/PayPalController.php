<?php

namespace App\Http\Controllers;

use App\Mail\ProfessionalReviewRequestMail;
use App\Models\ProfessionalReview;
use App\Models\SafetyDocument;
use App\Models\User;
use Illuminate\Http\Request;
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

        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $token = $provider->getAccessToken();

        $response = $provider->createOrder([
            'intent' => 'CAPTURE',
            'application_context' => [
                'return_url' => route('paypal.success', ['document' => $document->id]),
                'cancel_url' => route('preview.'.strtolower($document->document_type), ['id' => $document->id]),
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
            // Update document with paypal order id using stripe_session_id as a temp field to avoid schema change
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

        return redirect()->route('preview.'.strtolower($document->document_type), ['id' => $document->id])->with('error', $response['message'] ?? 'Something went wrong with PayPal.');
    }

    public function success(Request $request, SafetyDocument $document)
    {
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

            return redirect()->route('preview.'.strtolower($document->document_type), ['id' => $document->id])->with('success', 'Document unlocked successfully via PayPal!');
        }

        return redirect()->route('preview.'.strtolower($document->document_type), ['id' => $document->id])->with('error', 'PayPal payment verification failed.');
    }

    public function professionalReviewCheckout(ProfessionalReview $review)
    {
        $document = $review->safetyDocument;

        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $token = $provider->getAccessToken();

        $response = $provider->createOrder([
            'intent' => 'CAPTURE',
            'application_context' => [
                'return_url' => route('paypal.review-success', ['review' => $review->id]),
                'cancel_url' => route('preview.'.strtolower($document->document_type), ['id' => $document->id]),
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

        return redirect()->route('preview.'.strtolower($document->document_type), ['id' => $document->id])->with('error', $response['message'] ?? 'Something went wrong with PayPal.');
    }

    public function successReview(Request $request, ProfessionalReview $review)
    {
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
            Mail::to($adminEmail)->send(new ProfessionalReviewRequestMail($review));

            return redirect()->route('preview.'.strtolower($review->safetyDocument->document_type), ['id' => $review->safety_document_id])->with('success', 'Professional review requested successfully via PayPal!');
        }

        return redirect()->route('preview.'.strtolower($review->safetyDocument->document_type), ['id' => $review->safety_document_id])->with('error', 'PayPal payment verification failed.');
    }
}
