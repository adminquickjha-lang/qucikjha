<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.safety', ['seoKey' => 'privacy'])] class extends Component {
}; ?>

<div class="pt-6 pb-20">
    <div class="max-w-4xl mx-auto px-4">
        <h1 class="text-4xl md:text-5xl font-black text-slate-900 mb-4 tracking-tight">Privacy Policy</h1>
        <p class="text-slate-500 font-bold mb-12">Last Updated: April 14, 2026</p>

        <div class="prose prose-slate max-w-none space-y-10 text-slate-600 font-medium leading-relaxed">
            <section>
                <h2 class="text-2xl font-black text-slate-800 mb-4">1. Information We Collect</h2>
                <p>We collect information that you provide directly to us when you create an account, create a
                    document, or communicate with us. This includes:</p>
                <ul class="list-disc pl-6 space-y-2">
                    <li>Name and email address</li>
                    <li>Company information</li>
                    <li>Project and task details entered into our document creator</li>
                    <li>Payment information (processed by PayPal)</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-black text-slate-800 mb-4">2. How We Use Your Information</h2>
                <p>We use the information we collect to:</p>
                <ul class="list-disc pl-6 space-y-2">
                    <li>Provide, maintain, and improve our services</li>
                    <li>Create customized safety documentation</li>
                    <li>Process transactions and send related information</li>
                    <li>Send technical notices, updates, and support messages</li>
                    <li>Develop new features and train our safety AI models (using anonymized task data)</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-black text-slate-800 mb-4">3. Data Security</h2>
                <p>We take reasonable measures to help protect information about you from loss, theft, misuse, and
                    unauthorized access, disclosure, alteration, and destruction. All project data is stored securely
                    and payment data is handled by PayPal using industry-standard encryption.</p>
            </section>

            <section>
                <h2 class="text-2xl font-black text-slate-800 mb-4">4. Sharing of Information</h2>
                <p>We do not share your private project details or personal information with third parties except as
                    required to provide our service (e.g., payment processing via PayPal) or if required by law.</p>
            </section>

            <section>
                <h2 class="text-2xl font-black text-slate-800 mb-4">5. Your Rights</h2>
                <p>You have the right to access, update, or delete your personal information at any time through your
                    account dashboard. You may also contact us to request a full account deletion.</p>
            </section>
        </div>
    </div>
</div>