<?php

namespace Database\Seeders;

use App\Models\Seo;
use Illuminate\Database\Seeder;

class SeoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            [
                'key' => 'home',
                'label' => 'Home Page',
                'title' => 'AI Powered Safety Documents - Automated JHA, AHA, & JSA',
                'description' => 'Generate professional safety documents like JHA, AHA, and JSA in minutes using our AI technology.',
                'keywords' => 'JHA, AHA, JSA, Safety Documents, AI Safety',
            ],
            [
                'key' => 'about',
                'label' => 'About Us',
                'title' => 'About Us - AI Powered Safety Documents',
                'description' => 'Learn more about our mission to automate safety compliance using AI.',
                'keywords' => 'About Safety, AI Platform, Mission',
            ],
            [
                'key' => 'contact',
                'label' => 'Contact Us',
                'title' => 'Contact Us - Get in Touch',
                'description' => 'Have questions? Contact our support team for help with your safety documents.',
                'keywords' => 'Contact, Support, Help',
            ],
            [
                'key' => 'jha',
                'label' => 'JHA Service',
                'title' => 'Job Hazard Analysis (JHA) - AI Powered Generation',
                'description' => 'Create professional JHA documents in minutes. OSHA compliant and industry-ready.',
                'keywords' => 'JHA, Job Hazard Analysis, OSHA compliance',
            ],
            [
                'key' => 'aha',
                'label' => 'AHA Service',
                'title' => 'Activity Hazard Analysis (AHA) - Professional Safety Documents',
                'description' => 'Generate Activity Hazard Analysis (AHA) documents compliant with EM 385-1-1 and ANSI standards.',
                'keywords' => 'AHA, Activity Hazard Analysis, USACE safety',
            ],
            [
                'key' => 'jsa',
                'label' => 'JSA Service',
                'title' => 'Job Safety Analysis (JSA) - Fast & Accurate',
                'description' => 'Break down tasks and identify hazards with our professional JSA generation tool.',
                'keywords' => 'JSA, Job Safety Analysis, task safety',
            ],
            [
                'key' => 'privacy',
                'label' => 'Privacy Policy',
                'title' => 'Privacy Policy - AI Powered Safety Documents',
                'description' => 'Read our privacy policy to understand how we handle your data.',
                'keywords' => 'Privacy, Data Protection, Security',
            ],
            [
                'key' => 'terms',
                'label' => 'Terms of Service',
                'title' => 'Terms of Service - Agreement & Conditions',
                'description' => 'Review our terms of service for using the AI Powered Safety Documents platform.',
                'keywords' => 'Terms, Agreement, Legal',
            ],
            [
                'key' => 'refund',
                'label' => 'Refund Policy',
                'title' => 'Refund Policy - Satisfaction Guarantee',
                'description' => 'Understand our refund and cancellation policies.',
                'keywords' => 'Refund, Cancellation, Money Back',
            ],
        ];

        foreach ($pages as $page) {
            Seo::updateOrCreate(['key' => $page['key']], $page);
        }
    }
}
