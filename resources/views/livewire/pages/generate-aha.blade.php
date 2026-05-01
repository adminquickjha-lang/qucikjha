<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\Setting;

new #[Layout('layouts.safety')] class extends Component {
    use WithFileUploads;

    public string $type = 'aha';
    public $projectName = '';
    public $location = '';
    public $preparedBy = '';
    public $company = '';
    public $projectDescription = '';
    public $equipmentTools = '';
    public $competentPerson = '';
    public $safetyCoordinator = '';
    public $date;
    public $logo;
    public $selectedRegs = [];
    public $customRegText = '';
    public $selectedRegion = 'United States';
    public $projectDocs = [];

    public function mount()
    {
        $this->date = date('Y-m-d');
    }

    public function updatedSelectedRegion(): void
    {
        $this->selectedRegs = [];
        $this->customRegText = '';
    }

    public function getTypeInfoProperty()
    {
        $jhaPrice = Setting::where('key', 'jha_price')->value('value') ?? '19.90';
        $ahaPrice = Setting::where('key', 'aha_price')->value('value') ?? '19.90';
        $jsaPrice = Setting::where('key', 'jsa_price')->value('value') ?? '19.00';

        $info = [
            'jha' => ['title' => 'Job Hazard Analysis', 'desc' => 'Identifies hazards for each job step with control measures and risk codes.', 'price' => '$' . $jhaPrice, 'color' => 'from-blue-500 to-cyan-400'],
            'aha' => ['title' => 'Activity Hazard Analysis', 'desc' => 'Detailed USACE-compliant analysis with initial & residual risk assessment.', 'price' => '$' . $ahaPrice, 'color' => 'from-amber-500 to-orange-400'],
            'jsa' => ['title' => 'Job Safety Analysis', 'desc' => 'Job step breakdown with safe work practices. Great for toolbox talks.', 'price' => '$' . $jsaPrice, 'color' => 'from-emerald-500 to-teal-400'],
        ];

        return $info[strtolower($this->type)] ?? $info['aha'];
    }

    public function regulations()
    {
        return [
            'United States' => [
                ['id' => 'osha', 'label' => 'OSHA', 'desc' => 'occupational safety and health administration'],
                ['id' => 'cal-osha', 'label' => 'Cal/OSHA', 'desc' => 'california state safety standards'],
                ['id' => 'em-385', 'label' => 'EM 385-1-1 (USACE)', 'desc' => 'us army corps of engineers manual'],
                ['id' => 'ansi', 'label' => 'ANSI', 'desc' => 'american national standards institute'],
                ['id' => 'nfpa', 'label' => 'NFPA', 'desc' => 'national fire protection association'],
            ],
            'United Kingdom' => [
                ['id' => 'hswa-1974', 'label' => 'HSWA 1974', 'desc' => 'health and safety at work etc. act'],
                ['id' => 'mhswr-1999', 'label' => 'MHSWR 1999', 'desc' => 'management of health and safety at work regulations'],
                ['id' => 'cdm-2015', 'label' => 'CDM 2015 UK', 'desc' => 'construction (design and management) regulations'],
                ['id' => 'hse-uk', 'label' => 'HSE UK', 'desc' => 'health and safety executive guidelines'],
            ],
            'Canada' => [
                ['id' => 'cohsr', 'label' => 'COHSR', 'desc' => 'canada occupational health and safety regulations'],
                ['id' => 'worksafe-bc', 'label' => 'WorkSafeBC OHSR', 'desc' => 'british columbia safety regulations'],
                ['id' => 'ohsa-ontario', 'label' => 'OHSA Ontario', 'desc' => 'ontario occupational health and safety act'],
                ['id' => 'csa', 'label' => 'CSA', 'desc' => 'canadian standards association'],
                ['id' => 'nova-scotia', 'label' => 'Nova Scotia', 'desc' => 'nova scotia occupational health and safety'],
            ],
            'Australia' => [
                ['id' => 'aus-whs', 'label' => 'Australia WHS Regulations', 'desc' => 'australia whs regulations'],
                ['id' => 'safe-work-aus', 'label' => 'Safe Work Australia', 'desc' => 'national policy body for whs'],
                ['id' => 'asn-standards', 'label' => 'AS/NZS Standards', 'desc' => 'australian/new zealand standards'],
            ],
            'New Zealand' => [
                ['id' => 'hsw-nz', 'label' => 'HSW Regulations', 'desc' => 'health and safety at work regulations'],
                ['id' => 'worksafe-nz', 'label' => 'WorkSafe New Zealand', 'desc' => 'nz primary health and safety regulator'],
            ],
            'Singapore' => [
                ['id' => 'wsh-rm-singapore', 'label' => 'Singapore WSH Risk Management Regulations', 'desc' => 'workplace safety and health'],
            ],
            'United Arab Emirates' => [
                ['id' => 'uae-oshad', 'label' => 'UAE OSHAD-SF', 'desc' => 'abu dhabi occupational safety and health'],
                ['id' => 'dubai-construction', 'label' => 'Dubai Code of Construction Safety Practice', 'desc' => 'dubai municipality safety standards'],
                ['id' => 'mohre', 'label' => 'MOHRE', 'desc' => 'ministry of human resources and emirateisation'],
            ],
            'Other' => [
                ['id' => 'custom', 'label' => 'Custom Standards', 'desc' => 'manually specify other regulations'],
            ]
        ];
    }

    public function generate()
    {
        set_time_limit(300);
        $this->validate([
            'projectName' => 'required|min:3',
            'location' => 'required',
            'preparedBy' => 'required',
            'company' => 'required',
            'date' => 'required',
            'projectDescription' => empty($this->projectDocs) ? 'required' : 'nullable',
            'equipmentTools' => empty($this->projectDocs) ? 'required' : 'nullable',
        ]);

        $logoPath = $this->logo ? $this->logo->store('logos', 'public') : null;

        $allRegsFlat = collect($this->regulations())->flatten(1);
        $finalRegs = collect($this->selectedRegs)->map(function ($id) use ($allRegsFlat) {
            $reg = $allRegsFlat->firstWhere('id', $id);
            return $reg ? $reg['label'] : $id;
        })->filter()->toArray();

        if (in_array('custom', $this->selectedRegs) && !empty($this->customRegText)) {
            $finalRegs[] = $this->customRegText;
        }

        $isAdmin = auth()->check() && auth()->user()->role === 'admin';

        $tempDoc = new \App\Models\SafetyDocument([
            'company_name' => $this->company,
            'project_name' => $this->projectName,
            'project_location' => $this->location,
            'project_description' => $this->projectDescription,
            'equipment_tools' => $this->equipmentTools,
            'prepared_by' => $this->preparedBy,
            'competent_person' => $this->competentPerson,
            'safety_coordinator' => $this->safetyCoordinator,
            'regulations' => $finalRegs,
            'document_type' => strtoupper($this->type),
        ]);

        $extraContext = "";
        $attachments = [];

        foreach ($this->projectDocs as $file) {
            $extension = strtolower($file->getClientOriginalExtension());
            $path = $file->getRealPath();

            try {
                if ($extension === 'pdf') {
                    $parser = new \Smalot\PdfParser\Parser();
                    $pdf = $parser->parseFile($path);
                    $extraContext .= "\n--- Content from PDF ({$file->getClientOriginalName()}) ---\n" . $pdf->getText();
                } elseif (in_array($extension, ['doc', 'docx'])) {
                    $phpWord = \PhpOffice\PhpWord\IOFactory::load($path, $extension === 'docx' ? 'Word2007' : 'MsDoc');
                    foreach ($phpWord->getSections() as $section) {
                        foreach ($section->getElements() as $element) {
                            if (method_exists($element, 'getText')) {
                                $extraContext .= $element->getText() . " ";
                            }
                        }
                    }
                    $extraContext = "\n--- Content from Word ({$file->getClientOriginalName()}) ---\n" . $extraContext;
                } elseif (in_array($extension, ['jpg', 'jpeg', 'png'])) {
                    $attachments[] = \Laravel\Ai\Files\Base64Document::fromUpload($file);
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to process file {$file->getClientOriginalName()}: " . $e->getMessage());
            }
        }

        $regulationsStr = implode(', ', $finalRegs);

        try {
            $agent = new \App\Ai\Agents\AhaAgent($tempDoc, $regulationsStr, $extraContext);

            $aiResponse = null;
            $lastException = null;
            for ($attempt = 1; $attempt <= 3; $attempt++) {
                try {
                    $aiResponse = $agent->prompt("Please generate the AHA JSON now based on the provided context.", attachments: $attachments);
                    break;
                } catch (\Exception $e) {
                    $lastException = $e;
                    if ($attempt < 3) {
                        sleep(20);
                    }
                }
            }
            if ($aiResponse === null) {
                throw $lastException;
            }

            $content = $aiResponse->text;

            if (preg_match('/```json\s*(.*?)\s*```/s', $content, $matches)) {
                $content = $matches[1];
            } elseif (preg_match('/```\s*(.*?)\s*```/s', $content, $matches)) {
                $content = $matches[1];
            }

            $decoded = json_decode(trim($content), true);

            if (!$decoded || empty($decoded['steps']) || !is_array($decoded['steps'])) {
                if ($logoPath)
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($logoPath);
                $this->dispatch('swal', [
                    'title' => 'Incomplete Response',
                    'text' => 'The AI returned an incomplete document. Please try again.',
                    'icon' => 'error'
                ]);
                return;
            }

            $inputTokens = ($aiResponse->usage->promptTokens ?? 0) + ($aiResponse->usage->cacheReadInputTokens ?? 0);
            $outputTokens = ($aiResponse->usage->completionTokens ?? 0) + ($aiResponse->usage->reasoningTokens ?? 0);
            $cost = \App\Services\AiPricingService::calculateCost($inputTokens, $outputTokens);

            $doc = \App\Models\SafetyDocument::create([
                'user_id' => auth()->id(),
                'company_name' => $this->company,
                'project_name' => $this->projectName,
                'project_location' => $this->location,
                'project_description' => $decoded['derived_description'] ?? $this->projectDescription,
                'equipment_tools' => $decoded['derived_equipment'] ?? $this->equipmentTools,
                'prepared_by' => $this->preparedBy,
                'competent_person' => $this->competentPerson,
                'safety_coordinator' => $this->safetyCoordinator,
                'regulations' => $finalRegs,
                'document_type' => strtoupper($this->type),
                'logo_path' => $logoPath,
                'amount' => $isAdmin ? 0 : (float) str_replace('$', '', $this->typeInfo['price']),
                'is_paid' => $isAdmin,
                'download_ready' => true,
                'ai_response' => $decoded,
                'input_tokens' => $inputTokens,
                'output_tokens' => $outputTokens,
                'cost' => $cost,
            ]);

        } catch (\Exception $e) {
            if ($logoPath)
                \Illuminate\Support\Facades\Storage::disk('public')->delete($logoPath);
            \Illuminate\Support\Facades\Log::error('AHA Creation Error: ' . $e->getMessage());
            $this->dispatch('swal', [
                'title' => 'Creation Failed',
                'text' => 'Something went wrong. Please try again.',
                'icon' => 'error'
            ]);
            return;
        }

        session()->flash('success', 'AHA document created successfully!');

        return $this->redirectRoute('preview.aha', ['id' => $doc->id]);
    }

    public function updatedLogo()
    {
        $this->dispatch('swal', [
            'title' => 'Logo Uploaded',
            'text' => 'Company logo has been successfully processed.',
            'icon' => 'success'
        ]);
    }

    public function updatedProjectDocs()
    {
        $this->dispatch('swal', [
            'title' => 'Documents Ready',
            'text' => 'Supporting documents uploaded and analyzed.',
            'icon' => 'success'
        ]);
    }

    public function removeDoc($index)
    {
        array_splice($this->projectDocs, $index, 1);
    }
}; ?>

<div class="relative min-h-screen pt-8 pb-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
    <!-- Header Section with Tinted Background & NO Gradient Title -->
    <div class="mb-8 bg-slate-50 border border-slate-200/60 rounded-3xl p-6 md:p-8 shadow-sm relative overflow-hidden">
        <div class="absolute -right-24 -top-24 w-64 h-64 bg-primary/5 rounded-full blur-3xl"></div>

        <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div
                    class="inline-flex items-center gap-2 bg-primary/10 text-primary border border-primary/20 rounded-full px-4 py-1.5 text-[10px] font-black uppercase tracking-widest">
                    <span class="relative flex h-2 w-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                    </span>
                    Document Creation
                </div>
                <!-- Title: Background Header applied, Gradient Removed -->
                <h1 class="text-4xl md:text-4xl font-black tracking-tight text-slate-900 leading-none">
                    Create {{ $this->typeInfo['title'] }}
                </h1>
            </div>

            <div class="flex items-center gap-6 bg-white border border-slate-200 p-6 rounded-3xl shadow-sm">
                <div class="px-4 border-r border-slate-200">
                    <span
                        class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Pricing</span>
                    <div class="flex items-baseline gap-1">
                        <span class="text-3xl font-black text-slate-900">{{ $this->typeInfo['price'] }}</span>
                        <span class="text-slate-400 text-xs font-bold">/ doc</span>
                    </div>
                </div>
                <div class="px-4">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Type</span>
                    <span
                        class="px-3 py-1 rounded-full bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest shadow-lg">
                        {{ strtoupper($type) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <form wire:submit.prevent="generate" class="lg:grid lg:grid-cols-12 lg:gap-10 items-start">
        <!-- Left Column: Core Form -->
        <div class="lg:col-span-8 space-y-8">
            <div
                class="group bg-white rounded-3xl p-8 md:p-10 shadow-sm border border-slate-200/60 hover:shadow-xl hover:shadow-primary/5 transition-all duration-500 ring-1 ring-slate-100">
                <div class="flex items-center gap-4 mb-8">
                    <div
                        class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary animate-float">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z" />
                            <polyline points="14 2 14 8 20 8" />
                            <path d="m9 15 2 2 4-4" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-slate-900 tracking-tight">Project Information</h3>
                        <p class="text-sm text-slate-500 font-medium tracking-tight italic opacity-70">Complete the
                            details to create your safety document</p>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-4 mb-6">
                    <div class="space-y-1.5">
                        <label class="text-[14px] font-semibold text-slate-900 ml-1">Activity/work task <span
                                class="text-primary">*</span></label>
                        <input wire:model="projectName"
                            class="w-full h-12 px-5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:ring-4 focus:ring-primary/10 focus:border-primary outline-none transition-all placeholder:text-slate-400 font-medium"
                            placeholder="e.g. scaffolding erection" required />
                        <x-input-error :messages="$errors->get('projectName')" class="mt-1.5 text-[10px] font-bold" />
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[14px] font-semibold text-slate-900 ml-1">Job location <span
                                class="text-primary">*</span></label>
                        <input wire:model="location"
                            class="w-full h-12 px-5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:ring-4 focus:ring-primary/10 focus:border-primary outline-none transition-all placeholder:text-slate-400 font-medium"
                            placeholder="city, state" required />
                        <x-input-error :messages="$errors->get('location')" class="mt-1.5 text-[10px] font-bold" />
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-4 mb-6">
                    <div class="space-y-1.5">
                        <label class="text-[14px] font-semibold text-slate-900 ml-1">Prepared by <span
                                class="text-primary">*</span></label>
                        <input wire:model="preparedBy"
                            class="w-full h-12 px-5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:ring-4 focus:ring-primary/10 focus:border-primary outline-none transition-all placeholder:text-slate-400 font-medium"
                            placeholder="your full name" required />
                        <x-input-error :messages="$errors->get('preparedBy')" class="mt-1.5 text-[10px] font-bold" />
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[14px] font-semibold text-slate-900 ml-1">Company name <span
                                class="text-primary">*</span></label>
                        <input wire:model="company"
                            class="w-full h-12 px-5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:ring-4 focus:ring-primary/10 focus:border-primary outline-none transition-all placeholder:text-slate-400 font-medium"
                            placeholder="company name llc" required />
                        <x-input-error :messages="$errors->get('company')" class="mt-1.5 text-[10px] font-bold" />
                    </div>
                </div>

                <div class="space-y-1.5 mb-6">
                    <label class="text-[14px] font-semibold text-slate-900 ml-1">Project description <span
                            class="text-primary">*</span></label>
                    <textarea wire:model="projectDescription"
                        class="w-full h-20 p-5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-medium focus:ring-4 focus:ring-primary/10 focus:border-primary outline-none transition-all placeholder:text-slate-400 font-medium resize-y shadow-inner"
                        placeholder="describe the work activities..."></textarea>
                    <x-input-error :messages="$errors->get('projectDescription')"
                        class="mt-1.5 text-[10px] font-bold" />

                    <div class="pt-3">
                        <div
                            class="p-4 border border-slate-200 border-dashed rounded-xl flex flex-wrap items-center gap-3">
                            <label for="doc-upload"
                                class="cursor-pointer bg-primary text-white px-4 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest hover:brightness-110 transition-all shadow-md active:scale-95 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                    <polyline points="17 8 12 3 7 8" />
                                    <line x1="12" x2="12" y1="3" y2="15" />
                                </svg>
                                add documents
                            </label>
                            <input id="doc-upload" type="file" wire:model="projectDocs" multiple class="hidden"
                                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">PDF, Word,
                                Images allowed</span>
                        </div>

                        @if(!empty($projectDocs))
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach($projectDocs as $idx => $file)
                                    <div
                                        class="flex items-center gap-2 bg-white px-3 py-1.5 rounded-lg border border-slate-100 shadow-sm animate-in zoom-in duration-300">
                                        <span
                                            class="text-[10px] font-bold text-slate-600 truncate max-w-[120px]">{{ $file->getClientOriginalName() }}</span>
                                        <button type="button" wire:click="removeDoc({{ $idx }})"
                                            class="text-slate-300 hover:text-destructive transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                                fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path d="M18 6 6 18" />
                                                <path d="m6 6 12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div class="space-y-1.5 mb-6">
                    <label class="text-[14px] font-semibold text-slate-900 ml-1">Tools & equipment</label>
                    <textarea wire:model="equipmentTools"
                        class="w-full h-20 p-5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-medium focus:ring-4 focus:ring-primary/10 focus:border-primary outline-none transition-all placeholder:text-slate-400 font-medium resize-y shadow-inner"
                        placeholder="list required tools and ppe..."></textarea>
                    <x-input-error :messages="$errors->get('equipmentTools')" class="mt-1.5 text-[10px] font-bold" />
                </div>

                <div class="grid md:grid-cols-2 gap-4 mb-6">
                    <div class="space-y-1.5">
                        <label class="text-[14px] font-semibold text-slate-900 ml-1">Competent person</label>
                        <input wire:model="competentPerson"
                            class="w-full h-12 px-5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:ring-4 focus:ring-primary/10 focus:border-primary outline-none transition-all placeholder:text-slate-400 font-medium"
                            placeholder="e.g. John Doe" />
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[14px] font-semibold text-slate-900 ml-1">Safety coordinator</label>
                        <input wire:model="safetyCoordinator"
                            class="w-full h-12 px-5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:ring-4 focus:ring-primary/10 focus:border-primary outline-none transition-all placeholder:text-slate-400 font-medium"
                            placeholder="e.g. Jane Smith" />
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-4 mb-6">
                    <div class="space-y-1.5">
                        <label class="text-[14px] font-semibold text-slate-900 ml-1">Date <span
                                class="text-primary">*</span></label>
                        <input wire:model="date" type="date"
                            class="w-full h-12 px-5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:ring-4 focus:ring-primary/10 focus:border-primary outline-none" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Sidebar (Sticky on Large) -->
        <div class="lg:col-span-4 mt-10 lg:mt-0 space-y-6 lg:sticky lg:top-24">
            <!-- Logo Section -->
            <div
                class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/60 ring-1 ring-slate-100 overflow-hidden group">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-xl bg-slate-900 text-white flex items-center justify-center shadow-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="18" x="3" y="3" rx="2" ry="2" />
                            <circle cx="9" cy="9" r="2" />
                            <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21" />
                        </svg>
                    </div>
                    <span class="text-xs font-black uppercase tracking-widest text-slate-900">Branding</span>
                </div>

                <div
                    class="relative h-32 w-full rounded-2xl border-2 border-dashed border-slate-100 bg-slate-50/50 flex flex-col items-center justify-center hover:border-primary/40 hover:bg-primary/5 transition-all cursor-pointer overflow-hidden">
                    <input type="file" wire:model="logo" class="absolute inset-0 opacity-0 cursor-pointer z-20"
                        accept="image/png,image/jpeg,image/jpg" />

                    @if ($logo && method_exists($logo, 'temporaryUrl'))
                        <img src="{{ $logo->temporaryUrl() }}"
                            class="h-16 object-contain rounded-xl p-2 bg-white shadow-xl animate-in fade-in duration-500" />
                        <span class="text-[9px] font-bold text-primary mt-2 uppercase tracking-widest">replace
                            logo</span>
                    @else
                        <div class="flex flex-col items-center gap-2 group-hover:scale-105 transition-transform">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"
                                class="text-slate-300">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <polyline points="17 8 12 3 7 8" />
                                <line x1="12" x2="12" y1="3" y2="15" />
                            </svg>
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">add company
                                logo</span>
                        </div>
                    @endif

                    <div wire:loading wire:target="logo"
                        class="absolute inset-0 bg-white/80 backdrop-blur-sm flex items-center justify-center z-30">
                        <div class="w-6 h-6 border-2 border-primary/20 border-t-primary rounded-full animate-spin">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Regulations Section -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/60 ring-1 ring-slate-100">
                <div class="flex items-center gap-3 mb-4">
                    <div
                        class="w-8 h-8 rounded-xl bg-primary text-white flex items-center justify-center shadow-lg shadow-primary/20">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                        </svg>
                    </div>
                    <span class="text-xs font-black uppercase tracking-widest text-slate-900">Regulations &
                        Standards</span>
                </div>

                <div class="flex flex-wrap gap-1 mb-4">
                    @foreach(array_keys($this->regulations()) as $region)
                        <button type="button" wire:click="$set('selectedRegion', '{{ $region }}')"
                            class="px-2.5 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all {{ $selectedRegion === $region ? 'bg-primary text-white shadow-md' : 'bg-slate-50 text-slate-400 hover:bg-slate-100' }}">
                            {{ $region }}
                        </button>
                    @endforeach
                </div>

                <div class="space-y-1.5 max-h-[250px] overflow-y-auto pr-1.5 custom-scrollbar">
                    @foreach($this->regulations()[$selectedRegion] ?? [] as $reg)
                        <label
                            class="relative flex items-center p-2.5 rounded-xl border border-slate-100 hover:border-primary/30 transition-all cursor-pointer bg-slate-50/50">
                            <input type="checkbox" wire:model="selectedRegs" value="{{ $reg['id'] }}"
                                class="rounded border-slate-300 text-primary focus:ring-primary/20" />
                            <div class="ml-2.5">
                                <span
                                    class="text-[11px] font-black block text-slate-900 leading-none mb-0.5">{{ $reg['label'] }}</span>
                                <span
                                    class="text-[8px] text-slate-400 font-bold uppercase tracking-tighter line-clamp-1">{{ $reg['desc'] }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>

                @if(in_array('custom', $selectedRegs))
                    <div class="mt-3 pt-3 border-t border-slate-100 animate-in slide-in-from-top-2 duration-300">
                        <label class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 block">custom
                            specifications</label>
                        <textarea wire:model="customRegText" placeholder="enter specific rules..."
                            class="w-full h-20 p-3 bg-slate-50 border border-slate-200 rounded-xl text-[11px] font-bold focus:ring-4 focus:ring-primary/10 transition-all resize-y"></textarea>
                    </div>
                @endif
            </div>

            <!-- Submit Section -->
            <div class="pt-2">
                <button type="submit" wire:loading.attr="disabled" wire:target="generate, logo, projectDocs"
                    class="w-full h-20 bg-slate-900 text-white rounded-3xl font-black text-lg uppercase tracking-[0.2em] shadow-xl hover:brightness-110 shadow-primary/20 transition-all duration-300 flex items-center justify-center gap-4 group relative overflow-hidden">

                    <!-- Landing Page Style Hover Overlay -->
                    <div
                        class="absolute inset-0 bg-white/10 translate-y-full group-hover:translate-y-0 transition-transform duration-500 ease-in-out">
                    </div>

                    <span class="relative z-10">
                        Produce Document
                    </span>
                </button>
                <p
                    class="mt-4 text-center text-[9px] font-bold text-slate-400 leading-relaxed max-w-[200px] mx-auto capitalize">
                    By clicking produce, you agree to our terms of compliance and usage limitations.
                </p>
            </div>
        </div>
    </form>

    <!-- Document Generation Overlay -->
    <div wire:loading wire:target="generate" x-data
        x-init="new MutationObserver(() => { document.body.style.overflow = $el.style.display === 'none' ? '' : 'hidden'; }).observe($el, { attributes: true, attributeFilter: ['style'] })"
        class="fixed inset-0 bg-black/60 z-[9999] backdrop-blur-sm" style="display: none;">
        <div class="absolute inset-0 flex flex-col items-center justify-center gap-8">
            <div class="w-10 h-10 rounded-full border border-white/20 border-t-white animate-spin"></div>
            <p class="text-white/90 text-sm font-medium tracking-widest uppercase flex items-center gap-0.5">
                Please wait, your document is Creating<span class="doc-dot">.</span><span class="doc-dot">.</span><span
                    class="doc-dot">.</span>
            </p>
        </div>
    </div>

    <style>
        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-5px);
            }
        }

        .animate-float {
            animation: float 4s ease-in-out infinite;
        }

        @keyframes spin-slow {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .animate-spin-slow {
            animation: spin-slow 12s linear infinite;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #cbd5e1;
        }

        @keyframes doc-dot-bounce {

            0%,
            100% {
                transform: translateY(0);
                opacity: 0.4;
            }

            15% {
                transform: translateY(-6px);
                opacity: 1;
            }

            30% {
                transform: translateY(0);
                opacity: 0.4;
            }
        }

        .doc-dot {
            display: inline-block;
            animation: doc-dot-bounce 1.8s infinite ease-in-out;
            opacity: 0.4;
        }

        .doc-dot:nth-child(1) {
            animation-delay: 0s;
        }

        .doc-dot:nth-child(2) {
            animation-delay: 0.6s;
        }

        .doc-dot:nth-child(3) {
            animation-delay: 1.2s;
        }
    </style>

</div>