<?php

namespace App\Http\Controllers;

use App\Mail\ProfessionalReviewRequestMail;
use App\Models\ProfessionalReview;
use App\Models\SafetyDocument;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Stripe\Checkout\Session;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeController extends Controller
{
    public function checkout(SafetyDocument $document)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $price = match (strtoupper($document->document_type)) {
            'JHA' => 1990, // $19.90
            'AHA' => 1990,
            'JSA' => 1900, // $19.00
            default => 1990,
        };

        try {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'usd',
                            'product_data' => [
                                'name' => 'Safety Document: ' . $document->project_name,
                                'description' => $document->document_type . ' Generation',
                            ],
                            'unit_amount' => $price,
                        ],
                        'quantity' => 1,
                    ]
                ],
                'mode' => 'payment',
                'success_url' => route('stripe.success', ['document' => $document->id]) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('preview.' . strtolower($document->document_type), ['id' => $document->id]),
                'metadata' => [
                    'document_id' => $document->id,
                ],
            ]);

            $document->update([
                'stripe_session_id' => $session->id,
                'amount' => $price / 100,
            ]);

            return redirect($session->url);
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            return redirect()->back()->with('error', 'Unable to connect to payment gateway. Please check your internet connection.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Payment gateway error: ' . $e->getMessage());
        }
    }

    public function success(Request $request, SafetyDocument $document)
    {
        $sessionId = $request->get('session_id');

        if ($document->stripe_session_id === $sessionId) {
            $document->update(['is_paid' => true]);

            return redirect()->route('preview.' . strtolower($document->document_type), ['id' => $document->id])->with('success', 'Document unlocked successfully!');
        }

        return redirect()->route('preview.' . strtolower($document->document_type), ['id' => $document->id])->with('error', 'Payment verification failed.');
    }

    public function professionalReviewCheckout(ProfessionalReview $review)
    {
        $document = $review->safetyDocument;

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'usd',
                            'product_data' => [
                                'name' => 'Professional Review: ' . $document->project_name,
                                'description' => 'Our professionals will review and improve your document.',
                            ],
                            'unit_amount' => 500, // $5.00
                        ],
                        'quantity' => 1,
                    ]
                ],
                'mode' => 'payment',
                'success_url' => route('stripe.review-success', ['review' => $review->id]) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('preview.' . strtolower($document->document_type), ['id' => $document->id]),
                'metadata' => [
                    'review_id' => $review->id,
                    'type' => 'professional_review',
                ],
            ]);

            $review->update(['stripe_session_id' => $session->id]);

            return redirect($session->url);
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            return redirect()->back()->with('error', 'Unable to connect to payment gateway. Please check your internet connection.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Payment gateway error: ' . $e->getMessage());
        }
    }

    public function successReview(Request $request, ProfessionalReview $review)
    {
        $sessionId = $request->get('session_id');

        if ($review->stripe_session_id === $sessionId) {
            $review->update(['is_paid' => true]);

            // Notify Admin
            $adminEmail = config('mail.admin_email') ?? User::where('role', 'admin')->first()?->email;
            Mail::to($adminEmail)->send(new ProfessionalReviewRequestMail($review));

            return redirect()->route('preview.' . strtolower($review->safetyDocument->document_type), ['id' => $review->safety_document_id])->with('success', 'Professional review requested successfully! Our team will get back to you soon.');
        }

        return redirect()->route('preview.' . strtolower($review->safetyDocument->document_type), ['id' => $review->safety_document_id])->with('error', 'Payment verification failed.');
    }

    public function webhook(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));
        $endpoint_secret = config('services.stripe.webhook_secret');

        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (\UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $type = $session->metadata->type ?? 'document';

            if ($type === 'professional_review') {
                $reviewId = $session->metadata->review_id;
                $review = ProfessionalReview::find($reviewId);
                if ($review && !$review->is_paid) {
                    $review->update(['is_paid' => true]);
                    $adminEmail = config('mail.admin_email') ?? User::where('role', 'admin')->first()?->email;
                    Mail::to($adminEmail)->send(new ProfessionalReviewRequestMail($review));
                }
            } else {
                $documentId = $session->metadata->document_id;
                $document = SafetyDocument::find($documentId);
                if ($document) {
                    $document->update(['is_paid' => true]);
                }
            }
        }

        return response()->json(['status' => 'success']);
    }
}
