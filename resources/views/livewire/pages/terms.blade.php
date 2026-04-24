<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.safety', ['seoKey' => 'terms'])] class extends Component {
}; ?>

<div class="pt-6 pb-20">
    <div class="max-w-4xl mx-auto px-4">
        <h1 class="text-4xl md:text-5xl font-black text-slate-900 mb-4 tracking-tight">Terms of Service</h1>
        <p class="text-slate-500 font-bold mb-12">Last Updated: April 14, 2026</p>

        <div class="prose prose-slate max-w-none space-y-10 text-slate-600 font-medium leading-relaxed">
            <section>
                <h2 class="text-2xl font-black text-slate-800 mb-4">1. Acceptance of Terms</h2>
                <p>By accessing and using QuickJHA (the "Service"), you agree to be bound by these Terms of Service. If
                    you do not agree to these terms, please do not use the Service. These terms apply to all visitors,
                    users, and others who access or use the Service.</p>
            </section>

            <section>
                <h2 class="text-2xl font-black text-slate-800 mb-4">2. Description of Service</h2>
                <p>QuickJHA provides an AI-powered platform for creating safety documentation, including Job Hazard
                    Analysis (JHA), Activity Hazard Analysis (AHA), and Job Safety Analysis (JSA). The Service is
                    provided "as is" and "as available".</p>
            </section>

            <section>
                <h2 class="text-2xl font-black text-slate-800 mb-4">3. User Responsibility</h2>
                <p class="font-bold text-slate-900 border-l-4 border-primary pl-4 py-2 bg-slate-50">IMPORTANT: Safety
                    documentation created by QuickJHA is intended to assist in safety planning but must be reviewed,
                    verified, and approved by a qualified safety professional before use on any job site.</p>
                <p>You are solely responsible for ensuring that the documentation meets the specific legal and safety
                    requirements of your jurisdiction and project. QuickJHA is not a substitute for professional safety
                    advice or on-site inspections.</p>
            </section>

            <section>
                <h2 class="text-2xl font-black text-slate-800 mb-4">4. Payments and Refunds</h2>
                <p>QuickJHA operates on a pay-per-document basis. All payments are processed securely through our
                    third-party payment processor (Stripe). Please refer to our Refund Policy for information regarding
                    cancellations and refunds.</p>
            </section>

            <section>
                <h2 class="text-2xl font-black text-slate-800 mb-4">5. Intellectual Property</h2>
                <p>The Service and its original content, features, and functionality are and will remain the exclusive
                    property of QuickJHA and its licensors. Documentation created for you remains your property for
                    your own business use.</p>
            </section>

            <section>
                <h2 class="text-2xl font-black text-slate-800 mb-4">6. Limitation of Liability</h2>
                <p>In no event shall QuickJHA, nor its directors, employees, partners, agents, suppliers, or affiliates,
                    be liable for any indirect, incidental, special, consequential, or punitive damages, including
                    without limitation, loss of profits, data, use, goodwill, or other intangible losses, resulting from
                    your access to or use of or inability to access or use the Service.</p>
            </section>
        </div>
    </div>
</div>