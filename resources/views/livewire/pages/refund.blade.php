<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.safety', ['seoKey' => 'refund'])] class extends Component {
}; ?>

<div class="pt-6 pb-20">
    <div class="max-w-4xl mx-auto px-4">
        <h1 class="text-4xl md:text-5xl font-black text-slate-900 mb-4 tracking-tight">Refund Policy</h1>
        <p class="text-slate-500 font-bold mb-12">Last Updated: April 14, 2026</p>

        <div class="prose prose-slate max-w-none space-y-10 text-slate-600 font-medium leading-relaxed">
            <section>
                <h2 class="text-2xl font-black text-slate-800 mb-4">1. Digital Nature of Service</h2>
                <p>QuickJHA provides digital, downloadable safety documentation. Due to the digital nature of these
                    products, once a document has been created and accessed, we generally do not offer refunds.</p>
            </section>

            <section>
                <h2 class="text-2xl font-black text-slate-800 mb-4">2. Technical Issues</h2>
                <p>If you experience a technical failure that prevents you from accessing a document you have paid for,
                    please contact our support team immediately. If we cannot resolve the issue and provide the document
                    within 24 hours, we will issue a full refund for that specific document.</p>
            </section>

            <section>
                <h2 class="text-2xl font-black text-slate-800 mb-4">3. Accuracy of Tech Output</h2>
                <p>QuickJHA uses advanced Tech to assist in document Creation. While we strive for absolute accuracy,
                    the Tech may occasionally produce outputs that require refinement. Refunds are not typically issued
                    based on the content of the Tech output, as users are provided with a preview and the ability to
                    edit the document before final export.</p>
            </section>

            <section>
                <h2 class="text-2xl font-black text-slate-800 mb-4">4. Refund Process</h2>
                <p>To request a refund for a technical issue, please email support@quickjha.com within 7 days of your
                    purchase. Please include your account email and the document ID (if available). Approved refunds
                    will be credited back to the original payment method through Stripe within 5-10 business days.</p>
            </section>
        </div>
    </div>
</div>