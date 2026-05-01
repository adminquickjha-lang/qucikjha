<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>JSA - {{ Str::limit($document->project_name, 50) }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #333;
            line-height: 1.4;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .info-table td {
            border: 1px solid #000;
            padding: 5px;
            vertical-align: top;
        }
        .label {
            font-weight: bold;
            font-size: 14px;
        }
        .value {
            font-size: 14px;
        }
        .checkbox-group {
            margin-bottom: 15px;
            border: 1px solid #000;
            padding: 10px;
        }
        .checkbox-title {
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
            background: #eee;
            padding: 3px;
            border: 1px solid #000;
        }
        .ppe-grid {
            width: 100%;
            border-collapse: collapse;
        }
        .ppe-grid td {
            width: 25%;
            padding: 2px;
            vertical-align: middle;
        }
        .checkbox {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 1px solid #000;
            margin-right: 5px;
            text-align: center;
            line-height: 12px;
            font-size: 10px;
            font-weight: bold;
            vertical-align: middle;
            position: relative;
        }
        .checkbox.checked::after {
            content: '';
            position: absolute;
            left: 3px;
            top: 1px;
            width: 3px;
            height: 6px;
            border: solid #000;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }
        .task-table {
            width: 100%;
            border-collapse: collapse;
        }
        .task-table th {
            border: 1px solid #000;
            background: #000080;
            color: #fff;
            padding: 8px;
            text-align: center;
            font-size: 12px;
            text-transform: uppercase;
        }
        .task-table td {
            border: 1px solid #000;
            padding: 8px;
            vertical-align: top;
            font-size: 14px;
        }
        .disclaimer {
            font-size: 9px;
            color: #000;
            border-top: none;
            padding-top: 6px;
            margin-top: 10px;
        }
        .legal-disclaimer {
            font-size: 12px;
            color: #000;
            text-align: justify;
            margin-top: 15px;
            padding: 10px 0;
            line-height: 1.3;
            break-inside: avoid;
        }
        .legal-disclaimer strong {
            color: #000;
            font-weight: bold;
        }
        .signature-section {
            break-inside: avoid;
            page-break-inside: avoid;
            margin-top: 40px;
            min-height: 120px;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            break-inside: avoid;
            page-break-inside: avoid;
        }
        .toolbox-section {
            break-inside: avoid;
            page-break-inside: avoid;
        }
        .toolbox-table {
            width: 100%;
            border-collapse: collapse;
            break-inside: avoid;
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    @php
        $jsa = $document->ai_response['jsa_specifics'] ?? [];
        $ppe = $jsa['ppe_checklist'] ?? [];
        $docs = $jsa['documentation'] ?? [];
    @endphp

    {{-- Logo Section --}}
    <div style="text-align: center;">
        @php
            $logoSrc = '';
            $customLogoPath = $document->logo_path;

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
            <img src="{{ $logoSrc }}" style="max-height: 140px; width: auto; object-fit: contain;" alt="Logo" />
        @endif
    </div>

    {{-- Title Section --}}
    <div style="margin-bottom: 5px; text-align: left; padding: 0 4px;">
        <h1 style="font-size: 28px; font-weight: bold; margin: 0;">JOB SAFETY ANALYSIS (JSA)</h1>
    </div>

    <table class="info-table">
        <tr>
            <td colspan="2">
                <span class="label">Activity/Task:</span>
                <div class="value" style="word-break: break-all; word-wrap: break-word;">{{ Str::limit($document->project_name, 100) }}</div>
            </td>
            <td>
                <span class="label">Date:</span>
                <div class="value">{{ optional($document->created_at)->format('m/d/Y') }}</div>
            </td>
        </tr>
        <tr>
            <td width="30%">
                <span class="label">Developed by:</span>
                <div class="value" style="word-break: break-all; word-wrap: break-word;">{{ Str::limit($document->prepared_by, 100) }}</div>
            </td>
            <td>
                <span class="label">Competent Person:</span>
                <div class="value" style="word-break: break-all; word-wrap: break-word;">{{ Str::limit($document->competent_person ?? 'N/A', 100) }}</div>
            </td>
            <td>
                <span class="label">Company Name</span>
                <div class="value" style="word-break: break-all; word-wrap: break-word;">{{ Str::limit($document->company_name ?? 'N/A', 100) }}</div>
            </td>
        </tr>
        <tr>
            <td>
                <span class="label">Job Location:</span>
                <div class="value" style="word-break: break-all; word-wrap: break-word;">{{ Str::limit($document->project_location, 100) }}</div>
            </td>
            <td colspan="2">
                <span class="label">Required Equipment:</span>
                <div class="value">{{ $document->equipment_tools }}</div>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <span class="label">Required Documentation:</span>
                <div class="value" style="margin-top: 5px;">
                    <span class="checkbox {{ ($docs['hot_work_permit'] ?? false) ? 'checked' : '' }}"></span> Hot Work Permit &nbsp;&nbsp;
                    <span class="checkbox {{ ($docs['confined_space_permit'] ?? false) ? 'checked' : '' }}"></span> Confined Space Permit &nbsp;&nbsp;
                    <span class="checkbox {{ ($docs['mewp_permit'] ?? false) ? 'checked' : '' }}"></span> MEWP Permit
                </div>
            </td>
        </tr>
    </table>

    <div class="checkbox-group">
        <div style="font-weight: bold; margin-bottom: 5px; text-transform: uppercase; background: #ffffff; padding: 3px; border: 1px solid #000;">REQUIRED PERSONAL PROTECTIVE EQUIPMENT FOR ENTIRE JOB</div>
        <table class="ppe-grid">
            <tr>
                <td><span class="checkbox {{ ($ppe['safety_glasses'] ?? false) ? 'checked' : '' }}"></span> safety glasses</td>
                <td><span class="checkbox {{ ($ppe['face_shield'] ?? false) ? 'checked' : '' }}"></span> face shield (+ glasses/goggles)</td>
                <td><span class="checkbox {{ ($ppe['nitrile_gloves'] ?? false) ? 'checked' : '' }}"></span> nitrile/chem resistant</td>
                <td><span class="checkbox {{ ($ppe['respiratory_protection'] ?? false) ? 'checked' : '' }}"></span> respiratory protection</td>
            </tr>
            <tr>
                <td><span class="checkbox {{ ($ppe['safety_shoes'] ?? false) ? 'checked' : '' }}"></span> safety shoes</td>
                <td><span class="checkbox {{ ($ppe['chemical_goggles'] ?? false) ? 'checked' : '' }}"></span> chemical goggles</td>
                <td><span class="checkbox {{ ($ppe['cut_resistant_gloves'] ?? false) ? 'checked' : '' }}"></span> cut resistant gloves</td>
                <td><span class="checkbox {{ ($ppe['hearing_protection'] ?? false) ? 'checked' : '' }}"></span> hearing protection</td>
            </tr>
            <tr>
                <td><span class="checkbox {{ ($ppe['welding_helmet'] ?? false) ? 'checked' : '' }}"></span> welding goggles / helmet</td>
                <td><span class="checkbox {{ ($ppe['abrasion_resistant_gloves'] ?? false) ? 'checked' : '' }}"></span> abrasion resistant</td>
                <td><span class="checkbox {{ ($ppe['hard_hat'] ?? false) ? 'checked' : '' }}"></span> hard hat</td>
                <td><span class="checkbox {{ ($ppe['fall_protection'] ?? false) ? 'checked' : '' }}"></span> fall protection harness/lanyard</td>
            </tr>
            <tr>
                <td><span class="checkbox {{ ($ppe['leather_gloves'] ?? false) ? 'checked' : '' }}"></span> leather gloves</td>
                @php
                    $others = $jsa['ppe_others'] ?? [];
                    $othersCount = count($others);
                @endphp
                @for($i = 0; $i < 3; $i++)
                    <td>
                        @if($i < $othersCount)
                            <span class="checkbox checked"></span> {{ $others[$i] }}
                        @endif
                    </td>
                @endfor
            </tr>
        </table>
    </div>

    <!-- Disclaimer Section on First Page -->
    <div class="legal-disclaimer" style="margin-top: 30px;">
        <strong>Disclaimer:</strong>
        {{ $settings['disclaimer_text'] ?? 'The user, contractor, employer, or project owner is responsible for confirming that the contents of this document appropriately reflect the specific work activities, site conditions, and applicable laws, regulations, and project requirements before implementation. While reasonable efforts are made to provide useful and structured safety information, the provider shall not be liable for any damage, claim, or legal action arising from the use of this document.' }}
    </div>

    <!-- Move JSA Table to Second Page -->
    <div style="page-break-before: always;">
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px; page-break-inside: auto;">
        <tr style="background-color: {{ $settings['jsa_table_header_color'] ?? $settings['table_header_color'] ?? '#2c5f9e' }}; color: #ffffff; font-weight: bold; text-transform: uppercase;">
            <th style="width:180px; border: 1px solid #000; padding: 8px; font-weight: bold; color: #ffffff;">Job Steps</th>
            <th style="width:200px; border: 1px solid #000; padding: 8px; font-weight: bold; color: #ffffff;">Potential Hazards</th>
            <th style="width:220px; border: 1px solid #000; padding: 8px; font-weight: bold; color: #ffffff;">Controls</th>
            <th style="width:120px; border: 1px solid #000; padding: 8px; font-weight: bold; color: #ffffff;">Responsibility</th>
        </tr>
        @foreach($document->ai_response['steps'] ?? [] as $i => $step)
            @php
                $hazards = $step['hazards'] ?? $step['hazard'] ?? 'N/A';
                $controls = $step['controls'] ?? $step['control'] ?? 'N/A';
                $responsibilities = $step['responsibilities'] ?? $step['responsibility'] ?? 'Site Supervisor';

                // Convert to arrays if they are strings
                if (!is_array($hazards)) {
                    $hazards = array_filter(explode("\n", str_replace("\r", "", (string) $hazards)));
                    if (empty($hazards))
                        $hazards = ['N/A'];
                }

                if (!is_array($controls)) {
                    $controls = array_filter(explode("\n", str_replace("\r", "", (string) $controls)));
                    if (empty($controls))
                        $controls = ['N/A'];
                }

                if (!is_array($responsibilities)) {
                    $responsibilities = array_filter(explode("\n", str_replace("\r", "", (string) $responsibilities)));
                    if (empty($responsibilities))
                        $responsibilities = ['Site Supervisor'];
                }

                $maxStepRows = max(count($hazards), count($controls), count($responsibilities));
                $stepClass = ($i % 2 == 1) ? 'step-even' : 'step-odd';
            @endphp

            @for($r = 0; $r < $maxStepRows; $r++)
                <tr class="{{ $stepClass }}" style="page-break-inside: auto; background-color: {{ $settings['jsa_main_table_bg'] ?? '#ffffff' }};">
                    {{-- Basic Steps Column --}}
                    <td style="width:180px; font-weight:bold; color:#000; font-size:14px; 
                            border-left: 1px solid #000; border-right: 1px solid #000;
                            border-bottom: {{ $r == $maxStepRows - 1 ? '1px solid #000' : '0' }}; 
                            border-top: {{ $r == 0 ? '1px solid #000' : '0' }};
                            padding: 8px; vertical-align: top;">
                        @if($r == 0)
                            {{ $i + 1 }}. {{ preg_replace('/^(?:Step\s*\d+[\.\:\-\s]*|\d+[\.\-\s]+)+/i', '', $step['step_description'] ?? $step['step'] ?? 'N/A') }}
                        @endif
                    </td>

                    {{-- Potential Hazards Column --}}
                    <td style="width:200px; font-size:14px; border-left: 1px solid #000; border-right: 1px solid #000;
                            border-bottom: {{ $r == $maxStepRows - 1 ? '1px solid #000' : '0' }}; 
                            border-top: {{ $r == 0 ? '1px solid #000' : '0' }};
                            padding: 8px; vertical-align: top;">
                        @if(isset($hazards[$r]))
                            @php $h = trim($hazards[$r], " \t\n\r\0\x0B-.*"); @endphp
                            <div style="margin-bottom: 4px;">{{ count($hazards) > 1 ? ($r + 1) . '. ' : '' }}{{ $h }}</div>
                        @endif
                    </td>

                    {{-- Controls Column --}}
                    <td style="width:220px; font-size:14px; border-left: 1px solid #000; border-right: 1px solid #000;
                            border-bottom: {{ $r == $maxStepRows - 1 ? '1px solid #000' : '0' }}; 
                            border-top: {{ $r == 0 ? '1px solid #000' : '0' }};
                            padding: 8px; vertical-align: top;">
                        @if(isset($controls[$r]))
                            @php $c = trim($controls[$r], " \t\n\r\0\x0B-.*"); @endphp
                            <div style="margin-bottom: 4px;">{{ count($controls) > 1 ? ($r + 1) . '. ' : '' }}{{ $c }}</div>
                        @endif
                    </td>

                    {{-- Responsibility Column --}}
                    <td style="width:120px; font-size:14px; border-left: 1px solid #000; border-right: 1px solid #000;
                            border-bottom: {{ $r == $maxStepRows - 1 ? '1px solid #000' : '0' }}; 
                            border-top: {{ $r == 0 ? '1px solid #000' : '0' }};
                            padding: 8px; vertical-align: top;">
                        @if(isset($responsibilities[$r]))
                            @php $resp = trim($responsibilities[$r], " \t\n\r\0\x0B-.*"); @endphp
                            <div style="margin-bottom: 4px;">{{ count($responsibilities) > 1 ? ($r + 1) . '. ' : '' }}{{ $resp }}</div>
                        @endif
                    </td>
                </tr>
            @endfor
        @endforeach
    </table>

    <!-- Signatures Section -->
    <div class="signature-section">
        <h3 style="font-size: 16px; font-weight: bold; text-align: center; margin-bottom: 30px; text-transform: uppercase;">Signatures</h3>
        <table class="signature-table">
            <tr>
                <td style="width: 50%; padding: 20px; text-align: center; vertical-align: bottom;">
                    <div style="border-bottom: 2px solid #000; width: 80%; margin: 0 auto; height: 50px;"></div>
                    <div style="font-size: 14px; font-weight: bold; margin-top: 10px;">Prepared by</div>
                </td>
                <td style="width: 50%; padding: 20px; text-align: center; vertical-align: bottom;">
                    <div style="border-bottom: 2px solid #000; width: 80%; margin: 0 auto; height: 50px;"></div>
                    <div style="font-size: 14px; font-weight: bold; margin-top: 10px;">Approved by</div>
                </td>
            </tr>
        </table>
    </div>
    </div>

    <!-- Page Break for Toolbox Talk -->
    <div style="page-break-after: always;"></div>

    <!-- Toolbox Talk Attendance Sheet -->
    <div class="toolbox-section" style="padding: 20px 30px 30px 30px;">
        <div style="background-color: {{ $settings['header_color'] ?? '#1a3a6b' }}; color: #fff; font-weight: bold; font-size: 14px; padding: 10px; margin-top: 25px; text-transform: uppercase; text-align: center; line-height: 1.2;">
            TOOLBOX MEETING<br>
            <span style="font-size: 12px; font-weight: normal; text-transform: none;">(This JSA has been discussed with the following crew)</span>
        </div>
        <table class="toolbox-table" style="border: 1px solid #000; margin-top: 0;">
            <thead>
                <tr>
                    <th style="width: 50px; text-align: center; background-color: {{ $settings['jsa_table_header_color'] ?? $settings['table_header_color'] ?? '#2c5f9e' }}; color: #fff; padding: 8px; font-size: 14px; border: 1px solid #000; text-transform: uppercase;">No.</th>
                    <th style="width: 250px; background-color: {{ $settings['jsa_table_header_color'] ?? $settings['table_header_color'] ?? '#2c5f9e' }}; color: #fff; padding: 8px; font-size: 14px; border: 1px solid #000; text-transform: uppercase;">Name (Print)</th>
                    <th style="background-color: {{ $settings['jsa_table_header_color'] ?? $settings['table_header_color'] ?? '#2c5f9e' }}; color: #fff; padding: 8px; font-size: 14px; border: 1px solid #000; text-transform: uppercase;">Designation/Role</th>
                    <th style="width: 150px; background-color: {{ $settings['jsa_table_header_color'] ?? $settings['table_header_color'] ?? '#2c5f9e' }}; color: #fff; padding: 8px; font-size: 14px; border: 1px solid #000; text-transform: uppercase;">Signature</th>
                    <th style="width: 100px; background-color: {{ $settings['jsa_table_header_color'] ?? $settings['table_header_color'] ?? '#2c5f9e' }}; color: #fff; padding: 8px; font-size: 14px; border: 1px solid #000; text-transform: uppercase;">Date</th>
                </tr>
            </thead>
            <tbody>
                @for($i = 1; $i <= 10; $i++)
                    <tr>
                        <td style="border: 1px solid #000; height: 35px; font-size: 12px; padding: 8px; text-align: center; font-weight: bold;">{{ $i }}.</td>
                        <td style="border: 1px solid #000; height: 35px; font-size: 12px; padding: 8px;">&nbsp;</td>
                        <td style="border: 1px solid #000; height: 35px; font-size: 12px; padding: 8px;">&nbsp;</td>
                        <td style="border: 1px solid #000; height: 35px; font-size: 12px; padding: 8px;">&nbsp;</td>
                        <td style="border: 1px solid #000; height: 35px; font-size: 12px; padding: 8px;">&nbsp;</td>
                    </tr>
                @endfor
            </tbody>
        </table>
      
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $x = $pdf->get_width() - 85;
            $y = $pdf->get_height() - 25;
            $text = "Page {PAGE_NUM} of {PAGE_COUNT}";
            $font = $fontMetrics->get_font("helvetica", "bold");
            $size = 9;
            $color = array(0,0,0);
            $word_space = 0.0;
            $char_space = 0.0;
            $angle = 0.0;
            $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
        }
    </script>
</body>
</html>
