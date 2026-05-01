<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\Setting;

new #[Layout('layouts.safety')] class extends Component {
    use WithFileUploads;

    public $id;
    public $paid = false;
    public $project;

    public $newLogo;

    public $isEditing = false;
    public $projectName;
    public $projectLocation;
    public $preparedBy;
    public $safetyCoordinator;
    public $companyName;
    public $competentPerson;
    public $steps = [];
    public $competentActivities = [];
    public $equipment = [];

    // Review Feature
    public $showReviewModal = false;
    public $reviewRequest = '';
    public $isReviewing = false;
    public $reviewCount = 0;

    // Professional Review Feature
    public $showProfessionalReviewModal = false;
    public $professionalReviewMessage = '';
    public $activeReviewId;

    public function mount($id)
    {
        $this->id = $id;
        $this->project = \App\Models\SafetyDocument::findOrFail($id);

        abort_unless(
            auth()->id() === $this->project->user_id || auth()->user()?->role === 'admin',
            403
        );

        $this->paid = $this->project->is_paid;

        $this->projectName = $this->project->project_name;
        $this->projectLocation = $this->project->project_location;
        $this->companyName = $this->project->company_name;
        $this->preparedBy = $this->project->prepared_by;
        $this->safetyCoordinator = $this->project->safety_coordinator;
        $this->competentPerson = $this->project->competent_person;

        $this->steps = $this->project->ai_response['steps'] ?? [];

        // Ensure each step has a step_description for wire:model binding
        foreach ($this->steps as &$step) {
            $rawStep = $step['step_description'] ?? $step['step'] ?? '';
            // Strip leading "1. " or "1) " style numbering from the string
            $step['step_description'] = preg_replace('/^\d+[\.\)]\s*/', '', $rawStep);
        }
        $this->competentActivities = $this->project->ai_response['competent_activities'] ?? [];
        while (count($this->competentActivities) < 3) {
            $this->competentActivities[] = ['activity' => '', 'person' => ''];
        }
        $this->equipment = $this->project->ai_response['equipment'] ?? [];
        $this->reviewCount = $this->project->ai_response['review_count'] ?? 0;
    }

    public function jhaHazards()
    {
        return $this->steps;
    }

    public function toggleEdit()
    {
        if (!$this->paid)
            return;
        $this->isEditing = !$this->isEditing;
        if (!$this->isEditing) {
            $this->dispatch('edit-closed');
        }
    }

    public function cancelEdit(): void
    {
        if (!$this->paid)
            return;
        // Re-read all fields from DB to revert user changes
        $this->projectName = $this->project->project_name;
        $this->projectLocation = $this->project->project_location;
        $this->companyName = $this->project->company_name;
        $this->preparedBy = $this->project->prepared_by;
        $this->safetyCoordinator = $this->project->safety_coordinator;
        $this->competentPerson = $this->project->competent_person;
        $this->steps = $this->project->ai_response['steps'] ?? [];
        foreach ($this->steps as &$step) {
            $rawStep = $step['step_description'] ?? $step['step'] ?? '';
            $step['step_description'] = preg_replace('/^\d+[\.\)]\s*/', '', $rawStep);
        }
        $this->competentActivities = $this->project->ai_response['competent_activities'] ?? [];
        while (count($this->competentActivities) < 3) {
            $this->competentActivities[] = ['activity' => '', 'person' => ''];
        }
        $this->equipment = $this->project->ai_response['equipment'] ?? [];
        $this->isEditing = false;
        $this->dispatch('edit-closed');
    }

    public function save()
    {
        if (!$this->paid)
            return;

        $aiResponse = $this->project->ai_response;
        $aiResponse['steps'] = $this->steps;
        $aiResponse['competent_activities'] = $this->competentActivities;
        $aiResponse['equipment'] = $this->equipment;

        $this->project->update([
            'project_name' => $this->projectName,
            'project_location' => $this->projectLocation,
            'company_name' => $this->companyName,
            'prepared_by' => $this->preparedBy,
            'safety_coordinator' => $this->safetyCoordinator,
            'competent_person' => $this->competentPerson,
            'ai_response' => $aiResponse,
        ]);

        $this->isEditing = false;
        $this->dispatch('edit-closed');
        $this->dispatch('swal', ['title' => 'Saved!', 'text' => 'Document updated successfully!', 'icon' => 'success']);
    }

    public function deleteStep(int $index): void
    {
        if (!$this->paid) {
            return;
        }
        array_splice($this->steps, $index, 1);
        $this->steps = array_values($this->steps);
    }

    public function addStep(): void
    {
        if (!$this->paid) {
            return;
        }
        $this->steps[] = [
            'step_description' => '',
            'hazards' => [''],
            'controls' => [''],
            'initial_rac' => 'M',
        ];
    }

    public function addStepItem(int $stepIndex, string $field): void
    {
        if (!$this->paid) {
            return;
        }
        if (!is_array($this->steps[$stepIndex][$field] ?? null)) {
            $this->steps[$stepIndex][$field] = [$this->steps[$stepIndex][$field] ?? ''];
        }
        $this->steps[$stepIndex][$field][] = '';
    }

    public function deleteStepItem(int $stepIndex, string $field, int $itemIndex): void
    {
        if (!$this->paid) {
            return;
        }
        array_splice($this->steps[$stepIndex][$field], $itemIndex, 1);
        $this->steps[$stepIndex][$field] = array_values($this->steps[$stepIndex][$field]);
    }

    public function addCompetentActivity(): void
    {
        if (!$this->paid) {
            return;
        }
        $this->competentActivities[] = ['activity' => '', 'person' => ''];
    }

    public function deleteCompetentActivity(int $index): void
    {
        if (!$this->paid) {
            return;
        }
        array_splice($this->competentActivities, $index, 1);
        $this->competentActivities = array_values($this->competentActivities);
    }

    public function addEquipment(): void
    {
        if (!$this->paid) {
            return;
        }
        $this->equipment[] = ['equipment' => '', 'training' => '', 'inspection' => ''];
    }

    public function deleteEquipment(int $index): void
    {
        if (!$this->paid) {
            return;
        }
        array_splice($this->equipment, $index, 1);
        $this->equipment = array_values($this->equipment);
    }

    public function updatedNewLogo(): void
    {
        $this->validate(['newLogo' => 'image|max:3072']);

        if ($this->project->logo_path) {
            \Storage::disk('public')->delete($this->project->logo_path);
        }

        $path = $this->newLogo->store('logos', 'public');
        $this->project->update(['logo_path' => $path]);
        $this->newLogo = null;
    }

    public function handlePayment($method = null)
    {
        if ($method === 'stripe') {
            return redirect()->route('stripe.checkout', ['document' => $this->id]);
        }
        if ($method === 'paypal') {
            return redirect()->route('paypal.checkout', ['document' => $this->id]);
        }

        $this->paymentSuccess = true;

        sleep(1); // Simulate network

        $this->project->update(['is_paid' => true]);
        $this->paid = true;
        $this->showModal = false;
        $this->paymentSuccess = false;
    }

    public function review()
    {
        set_time_limit(300);
        if (!$this->paid)
            return;

        if ($this->reviewCount >= 5) {
            $this->dispatch('notify', ['message' => 'Review limit reached (Max 5 per document).', 'type' => 'error']);
            $this->dispatch('close-review-modal');
            return;
        }

        $this->validate([
            'reviewRequest' => 'required|string|min:10|max:1000'
        ]);

        try {
            $this->isReviewing = true;

            $agent = new \App\Ai\Agents\ReviewAgent($this->project);
            $aiResponse = $agent->prompt($this->reviewRequest);
            $content = $aiResponse->text;

            // Robust JSON extraction: find the first { and last }
            $firstBrace = strpos($content, '{');
            $lastBrace = strrpos($content, '}');
            if ($firstBrace !== false && $lastBrace !== false) {
                $content = substr($content, $firstBrace, $lastBrace - $firstBrace + 1);
            }

            $newData = json_decode(trim($content), true);

            if (!$newData) {
                \Illuminate\Support\Facades\Log::error('Invalid JSON', [
                    'document_id' => $this->id,
                    'raw_response' => $aiResponse->text
                ]);
                throw new \Exception("Please try again with a more specific request.");
            }

            // Support multiple possible keys for steps
            $stepsKey = isset($newData['steps']) ? 'steps' : (isset($newData['jsa_steps']) ? 'jsa_steps' : (isset($newData['safety_steps']) ? 'safety_steps' : null));

            if (!$stepsKey) {
                \Illuminate\Support\Facades\Log::error('Missing steps key', [
                    'document_id' => $this->id,
                    'json_keys' => array_keys($newData)
                ]);
                throw new \Exception("Please try being more specific.");
            }

            // Increment review count and update project
            $newData['review_count'] = $this->reviewCount + 1;

            $this->project->update([
                'ai_response' => $newData
            ]);

            // Re-mount to refresh everything
            $this->mount($this->id);

            // Save review history
            $inputTokens = ($aiResponse->usage->promptTokens ?? 0) + ($aiResponse->usage->cacheReadInputTokens ?? 0);
            $outputTokens = ($aiResponse->usage->completionTokens ?? 0) + ($aiResponse->usage->reasoningTokens ?? 0);
            $cost = \App\Services\AiPricingService::calculateCost($inputTokens, $outputTokens);

            \App\Models\DocumentReview::create([
                'safety_document_id' => $this->id,
                'prompt' => $this->reviewRequest,
                'input_tokens' => $inputTokens,
                'output_tokens' => $outputTokens,
                'cost' => $cost,
            ]);

            $this->reviewRequest = '';
            $this->dispatch('close-review-modal');
            $this->dispatch('swal', ['title' => 'Document Improved!', 'text' => 'The changes have been applied to your safety document.', 'icon' => 'success']);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Review failed: ' . $e->getMessage());
            $knownMessages = ['Please try again with a more specific request.', 'Please try being more specific.'];
            $text = in_array($e->getMessage(), $knownMessages) ? $e->getMessage() : 'Something went wrong. Please try again in a moment.';
            $this->dispatch('swal', ['title' => 'Review failed!', 'text' => $text, 'icon' => 'error']);
        } finally {
            $this->isReviewing = false;
        }
    }

    public function startProfessionalReview()
    {
        if (empty(trim($this->professionalReviewMessage))) {
            $this->dispatch('swal', ['title' => 'Instructions Required', 'text' => 'Please enter some instructions for our professional team.', 'icon' => 'warning']);
            return;
        }

        $review = \App\Models\ProfessionalReview::create([
            'user_id' => auth()->id(),
            'safety_document_id' => $this->id,
            'message' => $this->professionalReviewMessage,
            'token' => \Illuminate\Support\Str::random(60),
            'progress' => 1,
            'is_paid' => false,
        ]);

        $this->activeReviewId = $review->id;
        $this->dispatch('close-pro-review-modal');

        return $this->handleProfessionalReviewPayment('paypal');
    }

    public function handleProfessionalReviewPayment($method)
    {
        if ($method === 'stripe') {
            return redirect()->route('stripe.review-checkout', ['review' => $this->activeReviewId]);
        }
        if ($method === 'paypal') {
            return redirect()->route('paypal.review-checkout', ['review' => $this->activeReviewId]);
        }

        // Fake Payment Flow
        $review = \App\Models\ProfessionalReview::findOrFail($this->activeReviewId);
        $review->update(['is_paid' => true]);

        // Notify Admin
        $adminEmail = \App\Models\User::where('role', 'admin')->first()?->email ?? 'admin@example.com';
        try {
            \Illuminate\Support\Facades\Mail::to($adminEmail)->send(new \App\Mail\ProfessionalReviewRequestMail($review));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Mail failed: ' . $e->getMessage());
        }

        $this->showProfessionalReviewPaymentModal = false;
        $this->dispatch('swal', ['title' => 'Success', 'text' => 'Your request has been forwarded to our professionals!', 'icon' => 'success']);
    }

    public function getPriceProperty()
    {
        $type = strtolower($this->project->document_type);
        $settingKey = "{$type}_price";
        return Setting::where('key', $settingKey)->value('value') ?? '19.90';
    }

    public function exportWord(\App\Services\AdobePdfService $adobeService)
    {
        if (!$this->paid && auth()->id() !== $this->project->user_id) {
            return;
        }

        $document = $this->project;

        // 1. Load admin-controlled template settings
        $settings = \App\Models\Setting::whereIn('key', [
            'header_color',
            'table_header_color',
            'rac_e_color',
            'rac_h_color',
            'rac_m_color',
            'rac_l_color',
            'required_ppe',
            'disclaimer_text',
        ])->pluck('value', 'key')->toArray();

        // 2. Generate PDF first (Temp file)
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.safety-document', compact('document', 'settings'))
            ->setPaper('a4', 'landscape')
            ->setOptions(['isPhpEnabled' => true]);

        $pdfFileName = 'temp_' . $document->id . '.pdf';
        $pdfPath = storage_path('app/public/' . $pdfFileName);
        $pdf->save($pdfPath);

        try {
            // 3. Convert to Word using Adobe
            $downloadUrl = $adobeService->convertPdfToDocx($pdfPath);

            // 4. Download file from Adobe and send to user
            $wordContent = $adobeService->downloadAsset($downloadUrl);
            $cleanName = str_replace([' ', '/', '\\'], '_', $document->project_name);
            $wordFileName = "{$document->document_type}_{$cleanName}_{$document->id}.docx";

            // Cleanup temp PDF
            if (file_exists($pdfPath))
                unlink($pdfPath);

            return response()->streamDownload(function () use ($wordContent) {
                echo $wordContent;
            }, $wordFileName);

        } catch (\Exception $e) {
            // Cleanup on failure
            if (file_exists($pdfPath))
                unlink($pdfPath);

            \Illuminate\Support\Facades\Log::error('Adobe Conversion Failed: ' . $e->getMessage());
            $this->dispatch('notify', ['message' => 'Word conversion failed. Please try again.', 'type' => 'error']);
            return;
        }
    }
}; ?>

<div x-data="{ reviewOpen: false, proReviewOpen: false, isEditing: false }" x-on:edit-closed.window="isEditing = false" class="pt-4 pb-20 px-4 max-w-6xl mx-auto print:pt-0 print:pb-0 print:px-0 min-h-screen">
    <!-- Header Controls -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-6 print:hidden">
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-3 mb-3">
                <span class="bg-primary text-primary-foreground font-black px-3 py-1 rounded-full text-[9px] uppercase tracking-widest shadow-lg shadow-primary/20">
                    {{ $project->document_type }}
                </span>
                <span class="px-3 py-1 rounded-full text-[9px] uppercase tracking-widest font-black flex items-center gap-2 {{ $paid ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-secondary text-black border border-border' }}">
                    @if($paid)
                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                        Paid & Unlocked
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        Preview Mode
                    @endif
                </span>
            </div>
            <h1 class="text-3xl md:text-4xl font-black tracking-tighter leading-tight break-words">{{ Str::limit($project->project_name, 100) }}</h1>
            <p class="text-xs font-medium text-black mt-2">Safety Analysis Report • Ref: #{{ substr($id, 0, 8) }}</p>
        </div>
        
        <div class="flex flex-col gap-3 w-full sm:w-fit">
            @if($paid)
                <div x-show="isEditing" class="flex flex-wrap gap-3 w-full" style="display:none;">
                    <button wire:click="save" wire:loading.attr="disabled" wire:target="save" class="flex-1 justify-center bg-primary text-primary-foreground font-black px-4 py-2.5 rounded-xl text-sm uppercase tracking-wider flex items-center gap-3 hover:brightness-110 active:scale-[0.98] transition-all shadow-lg disabled:opacity-60">
                        <svg wire:loading.remove wire:target="save" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        <svg wire:loading wire:target="save" class="animate-spin" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="2" x2="12" y2="6"/><line x1="12" y1="18" x2="12" y2="22"/><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"/><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"/><line x1="2" y1="12" x2="6" y2="12"/><line x1="18" y1="12" x2="22" y2="12"/><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"/><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"/></svg>
                        <span wire:loading.remove wire:target="save">Save</span>
                        <span wire:loading wire:target="save">Saving...</span>
                    </button>
                    <button wire:click="cancelEdit" class="flex-1 justify-center bg-primary text-primary-foreground font-black px-4 py-2.5 rounded-xl text-sm uppercase tracking-wider flex items-center gap-3 hover:brightness-110 active:scale-[0.98] transition-all shadow-lg">
                        Cancel
                    </button>
                </div>

                <div x-show="!isEditing" class="flex flex-wrap gap-3 w-full">
                    <a href="{{ route('document.pdf', ['id' => $id]) }}" class="flex-1 justify-center bg-primary text-primary-foreground font-black px-4 py-2.5 rounded-xl text-sm uppercase tracking-wider flex items-center gap-3 hover:brightness-110 active:scale-[0.98] transition-all shadow-lg whitespace-nowrap">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                        PDF
                    </a>
                    <button wire:click="exportWord" wire:loading.attr="disabled" wire:target="exportWord" class="flex-1 justify-center bg-primary text-primary-foreground font-black px-4 py-2.5 rounded-xl text-sm uppercase tracking-wider flex items-center gap-3 hover:brightness-110 active:scale-[0.98] transition-all shadow-lg disabled:opacity-50 whitespace-nowrap">
                        <svg wire:loading.remove wire:target="exportWord" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                        <svg wire:loading wire:target="exportWord" class="animate-spin" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="2" x2="12" y2="6"/><line x1="12" y1="18" x2="12" y2="22"/><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"/><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"/><line x1="2" y1="12" x2="6" y2="12"/><line x1="18" y1="12" x2="22" y2="12"/><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"/><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"/></svg>
                        <span wire:loading.remove wire:target="exportWord">Word</span>
                        <span wire:loading wire:target="exportWord">Exporting...</span>
                    </button>
                    <button @click="isEditing = true" class="flex-1 justify-center bg-primary text-primary-foreground font-black px-4 py-2.5 rounded-xl text-sm uppercase tracking-wider flex items-center gap-3 hover:brightness-110 active:scale-[0.98] transition-all shadow-lg whitespace-nowrap">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        Edit
                    </button>
                </div>

                <div x-show="!isEditing" class="flex flex-wrap gap-3 w-full">
                    @if($reviewCount < 5)
                        <button @click="reviewOpen = true" class="flex-1 justify-center bg-primary text-primary-foreground font-black px-4 py-2.5 rounded-xl text-sm uppercase tracking-wider flex items-center gap-3 hover:brightness-110 active:scale-[0.98] transition-all shadow-lg whitespace-nowrap">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            Review ({{ 5 - $reviewCount }})
                        </button>
                    @endif

                    <button @click="proReviewOpen = true" class="flex-1 justify-center bg-primary text-primary-foreground font-black px-4 py-2.5 rounded-xl text-sm uppercase tracking-wider flex items-center gap-3 hover:brightness-110 active:scale-[0.98] transition-all shadow-lg whitespace-nowrap">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="m16 11 2 2 4-4"/></svg>
                        Professional Review ($5)
                    </button>
                </div>
            @else
                <button wire:click="handlePayment('paypal')" class="bg-primary text-primary-foreground font-black px-6 py-3.5 rounded-xl text-sm uppercase tracking-[0.2em] flex items-center justify-center gap-3 whitespace-nowrap hover:scale-[1.02] shadow-xl shadow-primary/20 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                    Unlock Document — ${{ $this->price }}
                </button>
                @if(app()->isLocal())
                    <a href="{{ route('test.checkout', $id) }}" class="text-xs text-amber-600 border border-amber-400 bg-amber-50 px-3 py-1.5 rounded-lg font-bold hover:bg-amber-100 transition-colors">
                        ⚡ Test Unlock (local only)
                    </a>
                @endif
            @endif
        </div>
    </div>

    <!-- Document Rendering Container -->
        <div class="relative">
        <div wire:loading wire:target="toggleEdit,cancelEdit,save" class="fixed inset-0 bg-white/60 z-[100] flex items-center justify-center print:hidden">
            <svg class="animate-spin text-primary" xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="2" x2="12" y2="6"/><line x1="12" y1="18" x2="12" y2="22"/><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"/><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"/><line x1="2" y1="12" x2="6" y2="12"/><line x1="18" y1="12" x2="22" y2="12"/><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"/><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"/></svg>
        </div>
        @if(!$paid)
            <div class="absolute inset-0 bg-white/40 backdrop-blur-[2px] z-20 flex items-center justify-center pointer-events-none overflow-hidden print:hidden">
                <div class="bg-foreground text-background px-12 py-5 rounded-3xl font-black shadow-2xl rotate-[-7deg] text-2xl tracking-[0.3em] uppercase opacity-90 scale-110">
                    Preview Only
                </div>
                <div class="absolute inset-0 grid grid-cols-2 md:grid-cols-4 gap-20 opacity-[0.03] select-none text-[80px] font-black -rotate-12 pointer-events-none">
                    @for($i = 0; $i < 20; $i++)
                        <span class="whitespace-nowrap italic">DRAFT ONLY • PREVIEW ONLY</span>
                    @endfor
                </div>
            </div>
        @endif

        @php
            $headerColor = \App\Models\Setting::where('key', 'header_color')->value('value') ?? '#1a3a6b';
            $tableHeaderColor = \App\Models\Setting::where('key', 'jha_table_header_color')->value('value') ?? \App\Models\Setting::where('key', 'table_header_color')->value('value') ?? '#2c5f9e';
            $racEColor = \App\Models\Setting::where('key', 'rac_e_color')->value('value') ?? '#c0392b';
            $racHColor = \App\Models\Setting::where('key', 'rac_h_color')->value('value') ?? '#e67e22';
            $racMColor = \App\Models\Setting::where('key', 'rac_m_color')->value('value') ?? '#f1c40f';
            $racLColor = \App\Models\Setting::where('key', 'rac_l_color')->value('value') ?? '#27ae60';
            $requiredPpe = $this->project->ai_response['required_ppe'] ?? 'Hard hat, Safety glasses, Hearing protection, Safety-toed work shoes.';
            $disclaimerText = \App\Models\Setting::where('key', 'disclaimer_text')->value('value') ?? 'This JHA has been reviewed for general compliance with jobsite safety requirements.';

            $racColors = ['E' => $racEColor, 'H' => $racHColor, 'M' => $racMColor, 'L' => $racLColor, 'Extreme' => $racEColor, 'High' => $racHColor, 'Medium' => $racMColor, 'Low' => $racLColor];
            $steps = $this->jhaHazards();
            $racRank = fn($r) => is_string($r) ? (['E' => 4, 'H' => 3, 'M' => 2, 'L' => 1][$r] ?? 0) : 0;
            $overallInitialRac = collect($steps)->pluck('initial_rac')->filter(fn($r) => is_string($r) && $r !== '')->sortByDesc($racRank)->first() ?? collect($steps)->pluck('rac')->filter(fn($r) => is_string($r) && $r !== '')->sortByDesc($racRank)->first() ?? 'M';
        @endphp

        <div class="bg-white ring-1 ring-border shadow-2xl overflow-x-auto font-['Arial',sans-serif] text-sm print:shadow-none p-4 md:p-10">
            <div class="min-w-[900px]">

            {{-- Refined Standardized JHA Header --}}
            <div class="bg-white p-4">
                {{-- Logo Section --}}
                <div class="flex flex-col items-center mb-6">
                    @php
                        $logoSrc = '';
                        $customLogoPath = $project->logo_path;

                        if ($customLogoPath && \Storage::disk('public')->exists($customLogoPath)) {
                            $fullPath = \Storage::disk('public')->path($customLogoPath);
                            $logoData = base64_encode(file_get_contents($fullPath));
                            $logoMime = mime_content_type($fullPath);
                            $logoSrc = 'data:' . $logoMime . ';base64,' . $logoData;
                        } else {
                            $fallbackLogo = public_path('logo.svg');
                            if (file_exists($fallbackLogo)) {
                                $logoData = base64_encode(file_get_contents($fallbackLogo));
                                $logoMime = mime_content_type($fallbackLogo);
                                $logoSrc = 'data:' . $logoMime . ';base64,' . $logoData;
                            }
                        }
                    @endphp
                    @if($logoSrc)
                        <img src="{{ $logoSrc }}" class="max-h-20 w-auto object-contain" alt="Logo" />
                    @endif
                    <div x-show="isEditing" class="mt-2 print:hidden" style="display:none;">
                        <label class="cursor-pointer inline-flex items-center gap-2 text-xs font-bold text-primary border border-primary rounded-lg px-3 py-1.5 hover:bg-primary hover:text-primary-foreground transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/></svg>
                            {{ $logoSrc ? 'Change Logo' : 'Upload Logo' }}
                            <input type="file" wire:model="newLogo" accept="image/*" class="hidden">
                        </label>
                        <div wire:loading wire:target="newLogo" class="text-xs text-gray-500 mt-1">Uploading...</div>
                        @error('newLogo') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Title Section --}}
                <div class="mb-1 text-left px-1">
                    <h1 class="text-[30px] font-bold" style="margin-bottom: 10px;">JOB HAZARD ANALYSIS (JHA)</h1>
                </div>

                {{-- The Main Grid Table --}}
                 <div class="text-black">
                    <table style="width: 100%; border: 1px solid #000; border-collapse: collapse; table-layout: fixed; color: #000; font-family: Arial, sans-serif;">
                        <tr>
                            <!-- Left Column: Consolidated Project Info -->
                            <td rowspan="3" style="width: 50.33%; border: 1px solid #000; padding: 0; vertical-align: top;">
                                <table style="width: 100%; border-collapse: collapse; border: none;">
                                    <tr>
                                        <td style="padding: 6px; border-bottom: 1px solid #000; font-size: 16px; word-break: normal; overflow-wrap: anywhere;">
                                            Project Name:
                                            <input x-show="isEditing" type="text" wire:model="projectName" class="border-0 p-0 font-bold w-full focus:ring-0 text-[16px]" style="display:none;">
                                            <strong x-show="!isEditing" class="text-[16px]">{{ Str::limit($projectName, 100) }}</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 6px; border-bottom: 1px solid #000; font-size: 16px; word-break: normal; overflow-wrap: anywhere;">
                                            Project Location:
                                            <input x-show="isEditing" type="text" wire:model="projectLocation" class="border-0 p-0 font-bold w-full focus:ring-0 text-[16px]" style="display:none;">
                                            <strong x-show="!isEditing" class="text-[16px]">{{ Str::limit($projectLocation, 100) }}</strong>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td style="padding: 6px; border-bottom: 1px solid #000; font-size: 16px; word-break: normal; overflow-wrap: anywhere;">
                                              Company Name :
                                              <input x-show="isEditing" type="text" wire:model="companyName" class="border-0 p-0 font-bold w-full focus:ring-0 text-[16px]" style="display:none;">
                                              <strong x-show="!isEditing" class="text-[16px]">{{ Str::limit($companyName ?? '—', 100) }}</strong>
                                        </td>
                                        </td>

                                    </tr>
                                    <tr>
                                        <td style="padding: 6px; border-bottom: 1px solid #000; font-size: 16px;">
                                            Date Prepared: <strong>{{ $project->created_at->format('m/d/y') }}</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 6px; border-bottom: 1px solid #000; font-size: 16px; word-break: normal; overflow-wrap: anywhere;">
                                            Prepared By (Name/Title):
                                            <input x-show="isEditing" type="text" wire:model="preparedBy" class="border-0 p-0 font-bold w-full focus:ring-0 text-[16px]" style="display:none;">
                                            <strong x-show="!isEditing" class="text-[16px]">{{ Str::limit($preparedBy ?? '—', 100) }}</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 6px; border-bottom: 1px solid #000; font-size: 16px; word-break: normal; overflow-wrap: anywhere;">
                                            Reviewed By (Name/Title):
                                            <input x-show="isEditing" type="text" wire:model="safetyCoordinator" class="border-0 p-0 font-bold w-full focus:ring-0 text-[16px]" style="display:none;">
                                            <strong x-show="!isEditing" class="text-[16px]">{{ Str::limit($safetyCoordinator ?? '—', 100) }}</strong>
                                        </td>
                                    </tr>
                                <tr>
                                    <td style="padding: 6px; font-size: 16px; line-height: 1.2; word-break: normal; overflow-wrap: anywhere;">
                                             Competent person:
                                             <input x-show="isEditing" type="text" wire:model="competentPerson" class="border-0 p-0 font-bold w-full focus:ring-0 text-[16px]" style="display:none;">
                                             <strong x-show="!isEditing" class="text-[16px]">{{ Str::limit($competentPerson ?? '—', 100) }}</strong>
                                        </td>
                                <tr>
                                    
                                </tr>
                                </tr>
                                </table>
                            </td>

                            <!-- Right Column: RAC Information -->
                            <td style="width: 49.66%; border: 1px solid #000; padding: 2px; vertical-align: middle;">
                                <table style="width: 100%; border-collapse: collapse; border: none;">
                                    <tr>
                                        <td style="font-size: 15px; width: 80%; line-height: 1.1;">Overall Initial Risk Assessment Code (RAC) (Use highest code)</td>
                                        <td style="width: 20%; text-align: center;">
                                            @php
                                                $overallInitialRacChar = strtoupper(substr($overallInitialRac, 0, 1));
                                                $overallInitialRacColor = $racColors[$overallInitialRacChar] ?? '#9ca3af';
                                            @endphp
                                            <div style="border: 3px solid #000; padding: 3px 0; font-weight: bold; width: 50px; margin: 0 auto; background: {{ $overallInitialRacColor }}; color: {{ in_array($overallInitialRacChar, ['M', 'L']) ? '#000' : '#fff' }}; font-size: 28px;">
                                                {{ $overallInitialRacChar }}
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <tr>
                            <td style="border: 1px solid #000; padding: 6px; font-size: 16px; font-weight: bold; text-transform: uppercase; background: #fff; text-align: center; color: #000;">
                                Risk Assessment Code (RAC) Matrix
                            </td>
                        </tr>

                        <tr>
                            <td style="border: 1px solid #000; padding: 0; vertical-align: top;">
                                <table style="width: 100%; border-collapse: collapse; text-align: center; font-weight: bold; font-size: 10px;">
                                    <thead>
                                        <tr style="border-bottom: 1px solid #000;">
                                            <th rowspan="2" style="border-right: 1px solid #000; width: 30%; padding: 2px; background: #fff; color: #000; font-size: 15px;">Severity</th>
                                            <th colspan="5" style="padding: 4px; text-transform: uppercase; background: #fff; color: #000; border-bottom: 1px solid #000; font-size: 15px;"><strong>Probability</strong></th>
                                        </tr>
                                        <tr style="border-bottom: 1px solid #000;">
                                            <th style="border-right: 1px solid #000; font-weight: bold; font-size: 15px; background: #fff;">Freq.</th>
                                            <th style="border-right: 1px solid #000; font-weight: bold; font-size: 15px; background: #fff;">Likely</th>
                                            <th style="border-right: 1px solid #000; font-weight: bold; font-size: 15px; background: #fff;">Occas.</th>
                                            <th style="border-right: 1px solid #000; font-weight: bold; font-size: 15px; background: #fff;">Seldom</th>
                                            <th style="font-weight: bold; font-size: 16px; background: #fff;">Unlikely</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr style="border-bottom: 1px solid #000;">
                                            <td style="border-right: 1px solid #000; padding: 3px; text-align: center; background: #fff; text-transform: uppercase; font-size: 15px; font-weight: bold;">Catastrophic</td>
                                            <td style="background: {{ $racEColor }}; color: #000; border-right: 1px solid #000; font-size: 15px;">E</td>
                                            <td style="background: {{ $racEColor }}; color: #000; border-right: 1px solid #000; font-size: 15px;">E</td>
                                            <td style="background: {{ $racHColor }}; color: #000; border-right: 1px solid #000; font-size: 15px;">H</td>
                                            <td style="background: {{ $racHColor }}; color: #000; border-right: 1px solid #000; font-size: 15px;">H</td>
                                            <td style="background: {{ $racMColor }}; color: #000; font-size: 15px;">M</td>
                                        </tr>
                                        <tr style="border-bottom: 1px solid #000;">
                                            <td style="border-right: 1px solid #000; padding: 3px; text-align: center; background: #fff; text-transform: uppercase; font-size: 15px; font-weight: bold;">Critical</td>
                                            <td style="background: {{ $racEColor }}; color: #000; border-right: 1px solid #000; font-size: 15px;">E</td>
                                            <td style="background: {{ $racHColor }}; color: #000; border-right: 1px solid #000; font-size: 15px;">H</td>
                                            <td style="background: {{ $racHColor }}; color: #000; border-right: 1px solid #000; font-size: 15px;">H</td>
                                            <td style="background: {{ $racMColor }}; color: #000; border-right: 1px solid #000; font-size: 15px;">M</td>
                                            <td style="background: {{ $racLColor }}; color: #000; font-size: 15px;">L</td>
                                        </tr>
                                        <tr style="border-bottom: 1px solid #000;">
                                            <td style="border-right: 1px solid #000; padding: 3px; text-align: center; background: #fff; text-transform: uppercase; font-size: 15px; font-weight: bold;">Marginal</td>
                                            <td style="background: {{ $racHColor }}; color: #000; border-right: 1px solid #000; font-size: 15px;">H</td>
                                            <td style="background: {{ $racMColor }}; color: #000; border-right: 1px solid #000; font-size: 15px;">M</td>
                                            <td style="background: {{ $racMColor }}; color: #000; border-right: 1px solid #000; font-size: 15px;">M</td>
                                            <td style="background: {{ $racLColor }}; color: #000; border-right: 1px solid #000; font-size: 15px;">L</td>
                                            <td style="background: {{ $racLColor }}; color: #000; font-size: 15px;">L</td>
                                        </tr>
                                        <tr style="border-bottom: 1px solid #000;">
                                            <td style="border-right: 1px solid #000; padding: 3px; text-align: center; background: #fff; text-transform: uppercase; font-size: 15px; font-weight: bold;">Negligible</td>
                                            <td style="background: {{ $racMColor }}; color: #000; border-right: 1px solid #000; font-size: 15px;">M</td>
                                            <td style="background: {{ $racLColor }}; color: #000; border-right: 1px solid #000; font-size: 15px;">L</td>
                                            <td style="background: {{ $racLColor }}; color: #000; border-right: 1px solid #000; font-size: 15px;">L</td>
                                            <td style="background: {{ $racLColor }}; color: #000; border-right: 1px solid #000; font-size: 15px;">L</td>
                                            <td style="background: {{ $racLColor }}; color: #000; font-size: 15px;">L</td>
                                        </tr>
                                        <tr>
                                            <td colspan="6" style="padding: 2px; border-top: 1px solid #000; font-size: 12px; font-weight: bold; text-align: center;">
                                                Review each "Hazard" with identified safety "Controls" and determine RAC (See above)
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>

                        <!-- Row 4: Bottom section split (Instructions & RAC Chart) -->
                        <tr>
                            <td style="padding: 0; vertical-align: top;">
                                <table style="width: 100%; border-collapse: collapse; border: none;">
                                 
                                    <tr>
                                      <td style="padding: 6px; font-size: 16px; line-height: 1.2;">
                                           Notes: (Field Notes, Review Comments, etc.)
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td style="border: 1px solid #000; padding: 0; vertical-align: top;">
                                <table style="width: 100%; border-collapse: collapse; border: none;">
                                    <tr>
                                        <td style="width: 60%; border-right: 1px solid #000; padding: 6px; font-size: 12px; vertical-align: top; line-height: 1.2;">
                                            <p style="margin-bottom: 6px; border-bottom: 1px solid #000; text-align: justify;">Step 1: Review each "Hazard" with identified safety "Controls" and determine
RAC (See above) </p>
                                            <p style="margin-bottom: 6px; border-bottom: 1px solid #000; text-align: justify;"><strong>"Severity"</strong> is the outcome/degree if an incident, near miss, or accident did occur and identified as: Catastrophic, Critical, Marginal, or Negligible.</p>
                                                <p style="text-align: justify;"><strong>"Severity" </strong> is the outcome/degree if an incident, near miss, or accident did
occur and identified as: Catastrophic, Critical, Marginal, or Negligible 
on JHA. </p>
 <p style="text-align: justify; border-top: 1px solid #000;">Annotate the overall highest RAC at the top of JHA. </p>
                                        </td>
                                        <td style="width: 25%; padding: 0; vertical-align: top;">
                                            <div style="background: #1a3a6b; color: #fff; font-size: 15px; font-weight: bold; text-align: center; padding: 10px; border-bottom: 1px solid #000; text-transform: uppercase; letter-spacing: 1px;">RAC Chart</div>
                                            <div style="font-size: 14px; font-weight: bold;">
                                                <div style="background: {{ $racEColor }}; color: #000; padding: 5px 5px; border-bottom: 1px solid #000; text-align: center;">E = Extremely High</div>
                                                <div style="background: {{ $racHColor }}; color: #000; padding: 5px 5px; border-bottom: 1px solid #000; text-align: center;">H = High Risk</div>
                                                <div style="background: {{ $racMColor }}; color: #000; padding: 5px 5px; border-bottom: 1px solid #000; text-align: center;">M = Moderate Risk</div>
                                                <div style="background: {{ $racLColor }}; color: #000; padding: 5px 5px; text-align: center;">L = Low Risk</div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>


            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-[14px]">
                    <thead>
                        <tr class="text-white text-[11px] font-black uppercase tracking-widest" style="background-color: {{ $tableHeaderColor }}">
                            <th class="px-3 py-3 border border-gray-400 text-center w-56 text-[16px]">Job Steps</th>
                            <th class="px-3 py-3 border border-gray-400 text-center w-64 text-[16px]">Hazards</th>
                            <th class="px-3 py-3 border border-gray-400 text-center text-[16px]">Risk Controls</th>
                            <th class="px-3 py-3 border border-gray-400 text-center w-16 text-[16px]">RAC</th>
                            <th x-show="isEditing" class="px-2 py-3 border border-gray-400 w-10 print:hidden" style="display:none;"></th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($steps as $i => $h)
                        <tr class="bg-white" wire:key="step-{{ $i }}">
                            <td class="px-3 py-3 border border-gray-300 font-bold text-gray-900 align-top">
                                {{ $i + 1 }}.
                                <textarea x-show="isEditing" wire:model="steps.{{ $i }}.step_description" class="w-full border-0 p-0 focus:ring-0 font-bold bg-transparent resize-none" rows="3" style="display:none;"></textarea>
                                <span x-show="!isEditing">{{ preg_replace('/^(?:Step\s*\d+[\.\:\-\s]*|\d+[\.\-\s]+)+/i', '', $h['step_description'] ?? $h['step'] ?? 'N/A') }}</span>
                            </td>
                            <td class="px-3 py-3 border border-gray-300 align-top text-black">
                                <div x-show="isEditing" style="display:none;">
                                    @if(is_array($h['hazards']))
                                        <ol class="list-decimal ml-4 space-y-2">
                                            @foreach($h['hazards'] as $hj => $hazard)
                                                <li class="flex items-center gap-1">
                                                    <input type="text" wire:model="steps.{{ $i }}.hazards.{{ $hj }}" class="flex-1 border-gray-200 rounded p-1 text-sm focus:ring-1 focus:ring-blue-500">
                                                    <button wire:click="deleteStepItem({{ $i }}, 'hazards', {{ $hj }})" type="button" class="text-red-400 hover:text-red-600 flex-shrink-0">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                                                    </button>
                                                </li>
                                            @endforeach
                                        </ol>
                                        <button wire:click="addStepItem({{ $i }}, 'hazards')" type="button" class="text-green-600 hover:text-green-700 text-xs mt-1 flex items-center gap-1 ml-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                                            Add
                                        </button>
                                    @else
                                        <textarea wire:model="steps.{{ $i }}.hazards" class="w-full border-gray-200 rounded p-2 text-sm focus:ring-1 focus:ring-blue-500 bg-transparent resize-none" rows="3"></textarea>
                                    @endif
                                </div>
                                <div x-show="!isEditing">
                                    @if(is_array($h['hazards']))
                                        <ol class="list-decimal ml-3 space-y-2">@foreach($h['hazards'] as $hazard)<li>{{ $hazard }}</li>@endforeach</ol>
                                    @else {!! nl2br(e($h['hazards'] ?? $h['hazard'] ?? 'N/A')) !!} @endif
                                </div>
                            </td>
                            <td class="px-3 py-3 border border-gray-300 align-top text-black">
                                <div x-show="isEditing" style="display:none;">
                                    @if(is_array($h['controls']))
                                        <ol class="list-decimal ml-4 space-y-2">
                                            @foreach($h['controls'] as $hc => $control)
                                                <li class="flex items-center gap-1">
                                                    <input type="text" wire:model="steps.{{ $i }}.controls.{{ $hc }}" class="flex-1 border-gray-200 rounded p-1 text-sm focus:ring-1 focus:ring-blue-500">
                                                    <button wire:click="deleteStepItem({{ $i }}, 'controls', {{ $hc }})" type="button" class="text-red-400 hover:text-red-600 flex-shrink-0">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                                                    </button>
                                                </li>
                                            @endforeach
                                        </ol>
                                        <button wire:click="addStepItem({{ $i }}, 'controls')" type="button" class="text-green-600 hover:text-green-700 text-xs mt-1 flex items-center gap-1 ml-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                                            Add
                                        </button>
                                    @else
                                        <textarea wire:model="steps.{{ $i }}.controls" class="w-full border-gray-200 rounded p-2 text-sm focus:ring-1 focus:ring-blue-500 bg-transparent resize-none" rows="3"></textarea>
                                    @endif
                                </div>
                                <div x-show="!isEditing">
                                    @if(is_array($h['controls']))
                                        <ol class="list-decimal ml-3 space-y-2">@foreach($h['controls'] as $control)<li>{{ $control }}</li>@endforeach</ol>
                                    @else {!! nl2br(e($h['controls'] ?? $h['control'] ?? 'N/A')) !!} @endif
                                </div>
                            </td>
                            @php
                                $initialRacRaw = $h['initial_rac'] ?? $h['rac'] ?? $h['risk'] ?? 'N/A';
                                $initialRac = is_array($initialRacRaw) ? ($initialRacRaw[0] ?? 'N/A') : $initialRacRaw;
                                $initialRacChar = strtoupper(substr((string) $initialRac, 0, 1));
                                $initialRacColor = $racColors[$initialRacChar] ?? '#9ca3af';
                                $initialTextColor = in_array($initialRacChar, ['M', 'L']) ? '#000' : '#fff';
                            @endphp
                            <td class="border border-gray-300 text-center align-middle font-black text-[14px]" :style="isEditing ? 'background-color: #fff; color: #000;' : 'background-color: {{ $initialRacColor }}; color: {{ $initialTextColor }};'" style="width: 70px;">
                                <select x-show="isEditing" wire:model.live="steps.{{ $i }}.initial_rac"
                                    class="w-full text-sm font-bold border border-gray-400 rounded px-1 py-1.5 focus:ring-1 focus:ring-blue-500 bg-white text-black cursor-pointer" style="display:none;">
                                    <option value="E" style="background:#c0392b;color:#fff;">E — Extreme</option>
                                    <option value="H" style="background:#e67e22;color:#fff;">H — High</option>
                                    <option value="M" style="background:#f1c40f;color:#000;">M — Medium</option>
                                    <option value="L" style="background:#27ae60;color:#fff;">L — Low</option>
                                </select>
                                <span x-show="!isEditing">{{ $initialRac }}</span>
                            </td>
                            <td x-show="isEditing" class="border border-gray-300 p-2 align-top print:hidden" style="display:none;">
                                <button wire:click="deleteStep({{ $i }})" type="button" class="text-red-400 hover:text-red-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <button x-show="isEditing" wire:click="addStep" type="button"
                class="mt-3 flex items-center gap-2 text-sm font-bold text-primary border border-primary rounded-lg px-4 py-2 hover:bg-primary hover:text-primary-foreground transition-colors print:hidden" style="display:none;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                Add Step
            </button>

            {{-- Equipment Table --}}
            <div class="text-white font-black text-[16px] px-4 py-2.5 tracking-widest uppercase mt-6 text-center" style="background-color: {{ $headerColor }}">
                Equipment to be Used | Training | Inspection
            </div>
            <table class="w-full border-collapse text-[14px]">
                <thead>
                    <tr class="text-white text-[11px] font-black uppercase" style="background-color: {{ $tableHeaderColor }}">
                        <th class="px-3 py-2 border border-gray-400 text-center w-1/3 text-[16px]">Equipment to be Used</th>
                        <th class="px-3 py-2 border border-gray-400 text-center w-1/3 text-[16px]">Training Required</th>
                        <th class="px-3 py-2 border border-gray-400 text-center w-1/3 text-[16px]">Inspection Requirements</th>
                        <th x-show="isEditing" class="px-2 py-2 border border-gray-400 w-10 print:hidden" style="display:none;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($equipment as $i => $eq)
                        <tr class="{{ $i % 2 === 1 ? 'bg-gray-50' : 'bg-white' }}">
                            <td class="px-3 py-2.5 border border-gray-300 text-black">
                                <input x-show="isEditing" type="text" wire:model="equipment.{{ $i }}.equipment" class="w-full border-0 p-0 focus:ring-0 bg-transparent" style="display:none;">
                                <span x-show="!isEditing">{{ $eq['equipment'] ?? '' }}</span>
                            </td>
                            <td class="px-3 py-2.5 border border-gray-300 text-black">
                                <input x-show="isEditing" type="text" wire:model="equipment.{{ $i }}.training" class="w-full border-0 p-0 focus:ring-0 bg-transparent" style="display:none;">
                                <span x-show="!isEditing">{{ $eq['training'] ?? '' }}</span>
                            </td>
                            <td class="px-3 py-2.5 border border-gray-300 text-black">
                                <input x-show="isEditing" type="text" wire:model="equipment.{{ $i }}.inspection" class="w-full border-0 p-0 focus:ring-0 bg-transparent" style="display:none;">
                                <span x-show="!isEditing">{{ $eq['inspection'] ?? '' }}</span>
                            </td>
                            <td x-show="isEditing" class="border border-gray-300 p-2 align-middle print:hidden" style="display:none;">
                                <button wire:click="deleteEquipment({{ $i }})" type="button" class="text-red-400 hover:text-red-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr class="bg-white">
                            <td :colspan="isEditing ? 4 : 3" class="px-3 py-4 border border-gray-300 text-center text-gray-500 italic">Equipment details not specified.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <button x-show="isEditing" wire:click="addEquipment" type="button"
                class="mt-2 flex items-center gap-2 text-sm font-bold text-primary border border-primary rounded-lg px-4 py-1.5 hover:bg-primary hover:text-primary-foreground transition-colors print:hidden" style="display:none;">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                Add Equipment
            </button>

            <table class="w-full border-collapse text-[13px] mt-6">
                <thead>
                    <tr class="text-white text-[11px] font-black uppercase" style="background-color: {{ $headerColor }}">
                        <th :colspan="isEditing ? 3 : 2" class="px-4 py-3 border border-blue-400/30 text-center text-[16px]">
                            Activities Requiring a Competent or Qualified Person – Attach Proof of Competency
                        </th>
                    </tr>
                    <tr class="text-white text-[11px] font-black uppercase" style="background-color: {{ $tableHeaderColor }}">
                        <th class="px-3 py-2 border border-gray-400 text-center w-1/2 text-[16px]">Activity</th>
                        <th class="px-3 py-2 border border-gray-400 text-center w-1/2 text-[16px]">Designated Competent or Qualified Person</th>
                        <th x-show="isEditing" class="px-2 py-2 border border-gray-400 w-10 print:hidden" style="display:none;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($competentActivities as $i => $act)
                        <tr class="{{ $i % 2 === 1 ? 'bg-gray-50' : 'bg-white' }}">
                            <td class="px-3 py-2.5 border border-gray-300 font-bold text-black">
                                <input x-show="isEditing" type="text" wire:model="competentActivities.{{ $i }}.activity" class="w-full border-0 p-0 focus:ring-0 bg-transparent font-bold" style="display:none;">
                                <span x-show="!isEditing">{{ $act['activity'] ?? 'General Supervision' }}</span>
                            </td>
                            <td class="px-3 py-2.5 border border-gray-300 text-black">
                                <input x-show="isEditing" type="text" wire:model="competentActivities.{{ $i }}.person" class="w-full border-0 p-0 focus:ring-0 bg-transparent" style="display:none;">
                                <span x-show="!isEditing">{{ $act['person'] ?? 'On-site Supervisor' }}</span>
                            </td>
                            <td x-show="isEditing" class="border border-gray-300 p-2 align-middle print:hidden" style="display:none;">
                                <button wire:click="deleteCompetentActivity({{ $i }})" type="button" class="text-red-400 hover:text-red-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr class="bg-white">
                            <td class="px-3 py-2.5 border border-gray-300 font-bold text-black">General Safety Oversight</td>
                            <td class="px-3 py-2.5 border border-gray-300 text-black">{{ $project->competent_person ?? 'To be designated' }}</td>
                            <td x-show="isEditing" class="border border-gray-300 p-2 print:hidden" style="display:none;"></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <button x-show="isEditing" wire:click="addCompetentActivity" type="button"
                class="mt-2 flex items-center gap-2 text-sm font-bold text-primary border border-primary rounded-lg px-4 py-1.5 hover:bg-primary hover:text-primary-foreground transition-colors print:hidden" style="display:none;">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                Add Activity
            </button>

            {{-- Signatures --}}
            <div class="text-white font-black text-[16px] px-4 py-2.5 tracking-widest uppercase text-center" style="background-color: {{ $headerColor }}">
                Signatures / Verification of Review
            </div>
            <table class="w-full border-collapse text-[14px]">
                <thead>
                    <tr class="text-white text-[11px] font-black uppercase" style="background-color: {{ $tableHeaderColor }}">
                        <th class="px-4 py-2.5 border border-gray-400 text-center text-[16px]">Name (Print)</th>
                        <th class="px-4 py-2.5 border border-gray-400 text-center text-[16px]">Signature</th>
                        <th class="px-4 py-2.5 border border-gray-400 text-center w-28 text-[16px]">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @for($r = 0; $r < 3; $r++)
                        <tr class="bg-white">
                            <td class="px-4 py-5 border border-gray-300">&nbsp;</td>
                            <td class="px-4 py-5 border border-gray-300">&nbsp;</td>
                            <td class="px-4 py-5 border border-gray-300">&nbsp;</td>
                        </tr>
                    @endfor
                </tbody>
            </table>

        <table class="w-full border-collapse text-[14px] mt-6">
            <thead>
                <tr class="text-white text-[11px] font-black uppercase" style="background-color: {{ $headerColor }}">
                    <th colspan="3" class="px-4 py-3 border border-blue-400/30 text-center text-[16px]">
                        JHA Modified and Reviewed
                    </th>
                </tr>
                <tr class="text-white text-[11px] font-black uppercase" style="background-color: {{ $tableHeaderColor }}">
                    <th class="px-4 py-2.5 border border-gray-400 text-center text-[16px]">Name (Print)</th>
                    <th class="px-4 py-2.5 border border-gray-400 text-center text-[16px]">Signature</th>
                    <th class="px-4 py-2.5 border border-gray-400 text-center w-28 text-[16px]">Date</th>
                </tr>
            </thead>
            <tbody>
                @for($r = 0; $r < 3; $r++)
                    <tr class="bg-white">
                        <td class="px-4 py-5 border border-gray-300">&nbsp;</td>
                        <td class="px-4 py-5 border border-gray-300">&nbsp;</td>
                        <td class="px-4 py-5 border border-gray-300">&nbsp;</td>
                    </tr>
                @endfor
            </tbody>
        </table>

            <!-- <div class="px-4 py-3 border-t border-gray-300 text-[9px] text-gray-400 italic text-center">
                Report Powered by InstantJHA AI Intelligence &mdash; OSHA Compliant
            </div> -->


            <div class="print:break-before-page">
                <div class="text-white font-black text-[16px] px-4 py-3 tracking-widest uppercase text-center leading-tight mt-10 print:mt-0" style="background-color: {{ $headerColor }}">
                    TOOLBOX MEETING<br>
                    <span class="text-[14px] font-normal normal-case opacity-90">(This JHA has been discussed with the following crew)</span>
                </div>
                <table class="w-full border-collapse text-[14px]">
                    <thead>
                        <tr class="text-white text-[11px] font-black uppercase" style="background-color: {{ $tableHeaderColor }}">
                            <th class="px-3 py-2.5 border border-gray-400 text-center w-12 text-[16px]">No.</th>
                            <th class="px-4 py-2.5 border border-gray-400 text-center text-[16px]">Name (Print)</th>
                            <th class="px-4 py-2.5 border border-gray-400 text-center text-[16px]">Designation/Role</th>
                            <th class="px-4 py-2.5 border border-gray-400 text-center text-[16px]">Signature</th>
                            <th class="px-4 py-2.5 border border-gray-400 text-center w-28 text-[16px]">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for($i = 1; $i <= 10; $i++)
                            <tr class="bg-white">
                                <td class="px-3 py-4 border border-gray-300 text-center font-bold text-[14px]">{{ $i }}.</td>
                                <td class="px-4 py-4 border border-gray-300 font-bold text-[14px]">&nbsp;</td>
                                <td class="px-4 py-4 border border-gray-300 font-bold text-[14px]">&nbsp;</td>
                                <td class="px-4 py-4 border border-gray-300 font-bold text-[14px]">&nbsp;</td>
                                <td class="px-4 py-4 border border-gray-300 font-bold text-[14px]">&nbsp;</td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
                <div class="px-4 py-8 text-[13px] text-black text-justify leading-relaxed border-t border-gray-100 break-words mt-10 font-sans">
                    <strong class="text-black font-bold">Disclaimer:</strong> {{ \App\Models\Setting::where('key', 'disclaimer_text')->value('value') ?? 'The user, contractor, employer, or project owner is responsible for confirming that the contents of this document appropriately reflect the specific work activities, site conditions, and applicable laws, regulations, and project requirements before implementation. While reasonable efforts are made to provide useful and structured safety information, the provider shall not be liable for any damage, claim, or legal action arising from the use of this document.' }}
                </div>
            </div>

            </div>

        </div>

    <div
            x-show="reviewOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            x-on:close-review-modal.window="reviewOpen = false"
            class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[300] flex items-center justify-center p-4"
            style="display: none;"
        >
            <div
                x-show="reviewOpen"
                x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200 transform"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                class="bg-white rounded-3xl p-8 max-w-lg w-full shadow-2xl relative"
            >

                <button @click="reviewOpen = false" class="absolute top-6 right-6 text-slate-400 hover:text-slate-900 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>

                <div class="mb-8 text-center flex flex-col items-center">
                    <div class="mb-6">
                        <img src="/logo.svg" alt="QuickJHA Logo" class="h-12 w-auto object-contain" />
                    </div>
                    <h3 class="text-2xl font-black text-black-900 uppercase tracking-tighter italic">Review Document</h3>
                    <p class="text-black-500 font-medium mt-2">Tell us exactly what you want to improve or add to your document. Remaining reviews: {{ 5 - $reviewCount }}</p>
                </div>

                <div class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-black-400 mb-2">Improvement Request</label>
                        <textarea wire:model="reviewRequest" 
                            class="w-full rounded-2xl bg-slate-50 border-0 ring-1 ring-slate-200 focus:ring-2 focus:ring-indigo-500 p-4 min-h-[150px] font-medium text-slate-900 placeholder:text-slate-300 transition-all"
                            placeholder="e.g., 'Add more detailed controls for working at heights' or 'Include specific safety regulations for electrical tools'"></textarea>
                        @error('reviewRequest') <span class="text-rose-600 text-[10px] font-bold uppercase mt-2 block">{{ $message }}</span> @enderror
                    </div>

                    <button wire:click="review" wire:loading.attr="disabled"
                        class="w-full bg-primary text-primary-foreground font-black py-4 rounded-2xl uppercase tracking-widest text-sm hover:brightness-110 active:scale-[0.98] transition-all shadow-xl shadow-primary/20 disabled:opacity-50 disabled:cursor-not-allowed group">
                        <span wire:loading.remove wire:target="review" class="flex items-center justify-center gap-2">
                            Apply Changes
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="group-hover:translate-x-1 transition-transform"><path d="m9 18 6-6-6-6"/></svg>
                        </span>
                        <span wire:loading wire:target="review" class="flex items-center justify-center">
                            <svg class="animate-spin" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="2" x2="12" y2="6"/><line x1="12" y1="18" x2="12" y2="22"/><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"/><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"/><line x1="2" y1="12" x2="6" y2="12"/><line x1="18" y1="12" x2="22" y2="12"/><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"/><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"/></svg>
                        </span>
                    </button>

                    <p class="text-[10px] text-center text-black-400 font-bold uppercase tracking-tight">
                        Note: This will replace your current analysis with the improved version.
                    </p>
                </div>
            </div>
        </div>
    </div>



    {{-- Professional Review Instructions Modal --}}
    <div
            x-show="proReviewOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            x-on:close-pro-review-modal.window="proReviewOpen = false"
            class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[300] flex items-center justify-center p-4"
            style="display: none;"
        >
            <div
                x-show="proReviewOpen"
                x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200 transform"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                class="bg-white rounded-3xl p-8 max-w-lg w-full shadow-2xl relative"
            >
                <button @click="proReviewOpen = false" class="absolute top-6 right-6 text-slate-400 hover:text-slate-900 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>

                <div class="mb-8 text-center flex flex-col items-center">
                    <div class="mb-6">
                        <img src="/logo.svg" alt="QuickJHA Logo" class="h-12 w-auto object-contain" />
                    </div>
                    <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tighter italic">Professional Review</h3>
                    <p class="text-black-500 font-medium mt-2">Your document will be reviewed by our professional team for $5. Tell us what level of improvements you need.</p>
                </div>

                <div class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-black-400 mb-2">Instructions</label>
                        <textarea wire:model="professionalReviewMessage"
                            class="w-full rounded-2xl bg-slate-50 border-0 ring-1 ring-slate-200 focus:ring-2 focus:ring-indigo-500 p-4 min-h-[150px] font-medium text-slate-900 placeholder:text-slate-300 transition-all"
                            placeholder="e.g., 'Ensure all roof safety protocols are covered...'"></textarea>
                    </div>

                    <button wire:click="startProfessionalReview" wire:loading.attr="disabled" wire:target="startProfessionalReview"
                        class="w-full bg-primary text-primary-foreground font-black py-4 rounded-2xl uppercase tracking-widest text-sm hover:brightness-110 active:scale-[0.98] transition-all shadow-xl shadow-primary/20 disabled:opacity-60 group">
                        <span wire:loading.remove wire:target="startProfessionalReview" class="flex items-center justify-center gap-2">
                            Request Expert Review ($5)
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                        </span>
                        <span wire:loading wire:target="startProfessionalReview" class="flex items-center justify-center">
                            <svg class="animate-spin" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="2" x2="12" y2="6"/><line x1="12" y1="18" x2="12" y2="22"/><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"/><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"/><line x1="2" y1="12" x2="6" y2="12"/><line x1="18" y1="12" x2="22" y2="12"/><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"/><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"/></svg>
                        </span>
                    </button>
                </div>
            </div>
        </div>

    <!-- Review Processing Overlay -->
    <div wire:loading wire:target="review"
         x-data
         x-init="new MutationObserver(() => { document.body.style.overflow = $el.style.display === 'none' ? '' : 'hidden'; }).observe($el, { attributes: true, attributeFilter: ['style'] })"
         class="fixed inset-0 bg-black/60 z-[9999] backdrop-blur-sm"
         style="display: none;">
        <div class="absolute inset-0 flex flex-col items-center justify-center gap-8">
            <div class="w-10 h-10 rounded-full border border-white/20 border-t-white animate-spin"></div>
            <p class="text-white/90 text-sm font-medium tracking-widest uppercase flex items-center gap-0.5">
                Please wait, your document is Updating<span class="doc-dot">.</span><span class="doc-dot">.</span><span class="doc-dot">.</span>
            </p>
        </div>
    </div>

    <style>
        @keyframes doc-dot-bounce {
            0%, 100% { transform: translateY(0); opacity: 0.35; }
            15%       { transform: translateY(-5px); opacity: 1; }
            30%       { transform: translateY(0); opacity: 0.35; }
        }
        .doc-dot {
            display: inline-block;
            animation: doc-dot-bounce 1.8s infinite ease-in-out;
            opacity: 0.35;
        }
        .doc-dot:nth-child(1) { animation-delay: 0s; }
        .doc-dot:nth-child(2) { animation-delay: 0.6s; }
        .doc-dot:nth-child(3) { animation-delay: 1.2s; }
    </style>
</div>
