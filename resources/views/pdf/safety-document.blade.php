<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $document->document_type }} - {{ Str::limit($document->project_name, 50) }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #000;
            background: #fff;
            margin-top: 10mm;
        }

        @page {

            size: A4 landscape;
            margin: 15mm 20mm 20mm 20mm
        }

        .main-title {
            background-color:
                {{ $settings['header_color'] ?? '#1a3a6b' }}
            ;
            /* Match Maroon */
            color: #fff;
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            padding: 10px;
            letter-spacing: 0.5px;
            margin-bottom: 0px;
        }

        .info-label {
            font-weigh-color: #f1f5f9;
            /* Match Slate-50 */
            white-space: nowrap;
            width: 140px;
            color: #1e293b;
            /* slate-800 */
        }

        .rac-matrix {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-top: 5px;
        }

        t: bold;

        background .rac-matrix th,
        .rac-matrix td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
            font-weight: bold;
        }

        .rac-matrix .header-row {
            background-color:
                {{ $settings['header_color'] ?? '#1a3a6b' }}
            ;
            color: #fff;
        }

        .rac-matrix .severity-label {
            background-color: #f8fafc;
            font-weight: bold;
            text-align: left;
            color: #0f172a;
        }

        .cell-e {
            background-color:
                {{ $settings['rac_e_color'] ?? '#c0392b' }}
                !important;
            color: #fff !important;
        }

        .cell-h {
            background-color:
                {{ $settings['rac_h_color'] ?? '#e67e22' }}
                !important;
            color: #fff !important;
        }

        .cell-m {
            background-color:
                {{ $settings['rac_m_color'] ?? '#f1c40f' }}
                !important;
            color: #000 !important;
        }

        .cell-l {
            background-color:
                {{ $settings['rac_l_color'] ?? '#27ae60' }}
                !important;
            color: #fff !important;
        }

        .section-header {
            background-color:
                {{ $settings['header_color'] ?? '#1a3a6b' }}
            ;
            color: #fff;
            font-weight: bold;
            font-size: 14px;
            padding: 4px 10px;
            margin-top: 25px;
            text-transform: uppercase;
            text-align: center;
        }

        .analysis-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
            border: 1px solid #000;
            page-break-inside: auto;
        }

        .analysis-table th {
            background-color:
                {{ $settings['jha_table_header_color'] ?? $settings['table_header_color'] ?? '#2c5f9e' }}
            ;
            color: #fff;
            padding: 8px;
            font-size: 16px;
            text-align: center;
            border: 1px solid #000;
            text-transform: uppercase;
        }

        .analysis-table tr {
            page-break-inside: auto;
        }

        .analysis-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: top;
            font-size: 16px;
            color: #000;
            page-break-inside: auto;
        }



        /* slate-50 */

        .rac-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 13px;
            color: #fff;
            text-align: center;
        }

        .rac-E,
        .rac-Extreme {
            background-color:
                {{ $settings['rac_e_color'] ?? '#c0392b' }}
            ;
        }

        .rac-H,
        .rac-High {
            background-color:
                {{ $settings['rac_h_color'] ?? '#e67e22' }}
            ;
        }

        .rac-M,
        .rac-Medium,
        .rac-Moderate {
            background-color:
                {{ $settings['rac_m_color'] ?? '#f1c40f' }}
            ;
            color: #000;
        }

        .rac-L,
        .rac-Low {
            background-color:
                {{ $settings['rac_l_color'] ?? '#27ae60' }}
            ;
        }

        .equip-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .equip-table th {
            background-color:
                {{ $settings['jha_table_header_color'] ?? $settings['table_header_color'] ?? '#2c5f9e' }}
            ;
            color: #fff;
            padding: 8px;
            font-size: 14px;
            border: 1px solid #000;
            text-transform: uppercase;
            text-align: center;
        }

        .equip-table td {
            border: 1px solid #000;
            padding: 8px;
            vertical-align: top;
        }

        .equip-table tr {
            page-break-inside: auto;
        }

        .sig-table {
            width: 100%;
            border-collapse: collapse;
        }

        .sig-table th {
            background-color:
                {{ $settings['jha_table_header_color'] ?? $settings['table_header_color'] ?? '#2c5f9e' }}
            ;
            color: #fff;
            padding: 8px;
            border: 1px solid #000;
            font-size: 14px;
            text-transform: uppercase;
            text-align: center;
        }

        .sig-table td {
            border: 1px solid #000;
            height: 35px;
            font-size: 12px;
            padding: 8px;
        }

        .sig-table tr {
            page-break-inside: auto;
        }

        .disclaimer {
            font-size: 9px;
            color: #000;
            border-top: none;
            padding-top: 6px;
            margin-top: 10px;
            font-style: italic;
            text-align: center;
        }

        .legal-disclaimer {
            font-size: 9px;
            color: #000;
            text-align: justify;
            margin-top: 15px;
            padding: 10px 0;
            line-height: 1.4;
            width: auto;
            max-width: 100%;
            overflow-wrap: break-word;
            /* padding: 10px; */
            word-wrap: break-word;
        }

        .legal-disclaimer strong {
            color: #000;
            font-weight: bold;
        }

        .main-wrapper {

            padding: 10px 30px 30px 30px;
            font-size: 14px !important;
        }

        .page-content {
            margin-top: 15px;
            /* Important: Gives breathing space at top */
            padding-top: 10px;
        }

        ul {
            margin: 0;
            padding-left: 14px;
        }

        ol {
            margin: 0;
            padding-left: 16px;
        }

        li {
            margin-bottom: 8px;
        }


        strong {
            font-size: 14px;
        }

        thead {
            display: table-row-group;
        }

        /* Styles for pages after the first page */
        .after-page-one,
        .after-page-one .analysis-table td,
        .after-page-one .equip-table td,
        .after-page-one .sig-table td,
        .after-page-one .sig-table th,
        .after-page-one li {
            font-size: 16px !important;
        }

        .after-page-one .section-header,
        .after-page-one .analysis-table th,
        .after-page-one .equip-table th {
            font-size: 16px !important;
        }
    </style>
</head>

<body>


    @php
        $racEColor = $settings['rac_e_color'] ?? '#c0392b';
        $racHColor = $settings['rac_h_color'] ?? '#e67e22';
        $racMColor = $settings['rac_m_color'] ?? '#f1c40f';
        $racLColor = $settings['rac_l_color'] ?? '#27ae60';
        $requiredPpeRaw = $document->ai_response['required_ppe'] ?? 'Hard hat, Safety glasses, Hearing protection, Safety-toed work shoes.';
        $requiredPpe = is_array($requiredPpeRaw) ? implode(', ', $requiredPpeRaw) : (string) $requiredPpeRaw;

        $racValues = collect($document->ai_response['steps'] ?? [])->pluck('initial_rac')->map(fn($r) => is_array($r) ? ($r[0] ?? null) : $r)->filter(fn($r) => is_string($r) && $r !== '')->toArray();
        if (empty($racValues)) {
            $racValues = collect($document->ai_response['steps'] ?? [])->pluck('rac')->map(fn($r) => is_array($r) ? ($r[0] ?? null) : $r)->filter(fn($r) => is_string($r) && $r !== '')->toArray();
        }
        $racOrder = ['E' => 4, 'H' => 3, 'M' => 2, 'L' => 1];
        $overallRac = collect($racValues)->sortByDesc(fn($r) => $racOrder[$r] ?? 0)->first() ?? 'M';
        $overallRacLetter = strtoupper(substr($overallRac, 0, 1));
        $overallRacColors = ['E' => $racEColor, 'H' => $racHColor, 'M' => $racMColor, 'L' => $racLColor];
        $overallRacBg = $overallRacColors[$overallRacLetter] ?? '#fff';
        $overallRacText = in_array($overallRacLetter, ['M', 'L']) ? '#000' : '#fff';
    @endphp

    <div class="main-wrapper">
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
            <h1 style="font-size: 28px; font-weight: bold; margin: 0;">JOB HAZARD ANALYSIS (JHA)</h1>
        </div>

        {{-- The Main Grid Table --}}
         <table
            style="width: 100%; border: 1px solid #000; border-collapse: collapse; table-layout: fixed; color: #000; font-family: Arial, sans-serif; ">
            <!-- Row 1: Activity & Overall RAC -->
            <tr>
                <td style="width: 50.33%; border: 1px solid #000; padding: 10px; font-size: 14px; word-break: normal; overflow-wrap: anywhere; word-wrap: break-word;">
                   Project Name: <strong>{{ Str::limit($document->project_name, 100) }}</strong><br>

                </td>
                <td style="width: 49.66%; border: 1px solid #000; padding: 4px; vertical-align: middle;">
                    <table style="width: 100%; border-collapse: collapse; border: none;">
                        <tr>
                            <td style="font-size: 14px; width: 80%; line-height: 1.1;">Overall Initial Risk Assessment Code
                                (RAC) (Use highest code)</td>
                            <td style="width: 20%; text-align: center;">
                                <div
                                    style="border: 3px solid #000; padding: 6px 0; font-weight: bold; width: 50px; background: {{ $overallRacBg }}; color: {{ $overallRacText }};">
                                    {{ $overallRacLetter }}
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <!-- Row 2: Work Order & Matrix Title -->
            <tr>
                <td style="border: 1px solid #000; padding: 10px; font-size: 14px; word-break: normal; overflow-wrap: anywhere; word-wrap: break-word;">
                    Project Location: <strong>{{ Str::limit($document->project_location, 100) }}</strong>
                </td>
                <td
                    style="border: 1px solid #000; padding: 6px; font-size: 14px; font-weight: bold; text-transform: uppercase; background: #fff; text-align: center; color: #000;">
                    Risk Assessment Code (RAC) Matrix
                </td>
            </tr>

            <!-- Row 3: Contractor info + Matrix Rows -->
            <tr>
                <td style="border: 1px solid #000; padding: 0; vertical-align: top;">
                    <table style="width: 100%; border-collapse: collapse; border: none;">
                          <tr>
                            <td
                                style="padding: 9px; border-bottom: 1px solid #000; font-size: 14px; vertical-align: top; word-break: normal; overflow-wrap: anywhere; word-wrap: break-word;">
                                Company Name: <strong style="font-size: 15px;">{{ Str::limit($document->company_name, 100) }}</strong>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 9px; border-bottom: 1px solid #000; font-size: 14px; word-break: normal; overflow-wrap: anywhere; word-wrap: break-word;">
                                Competent person: <strong style="font-size: 15px;">{{ Str::limit($document->competent_person ?? '—', 100) }}</strong>
                             </td>
                        </tr>
                      
                        <tr>
                            <td
                                style="padding: 9px; border-bottom: 1px solid #000; font-size: 14px; vertical-align: top;">
                                Date Prepared: <strong style="font-size: 15px;">{{ optional($document->created_at)->format('m/d/y') }}</strong>
                            </td>
                        </tr>
                        <tr>
                            <td
                                style="padding: 10px; border-bottom: 1px solid #000; font-size: 14px; vertical-align: top; word-break: normal; overflow-wrap: anywhere; word-wrap: break-word;">
                                Prepared by (Name/Title): <strong style="font-size: 15px;">{{ Str::limit($document->prepared_by ?? '—', 100) }}</strong>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; font-size: 14px;  vertical-align: top; word-break: normal; overflow-wrap: anywhere; word-wrap: break-word;">
                                Reviewed by (Name/Title): <strong style="font-size: 15px;">{{ Str::limit($document->safety_coordinator ?? '—', 100) }}</strong>
                            </td>

                        </tr>
                        <!-- <tr>
                            <td style="padding: 8px; font-size: 14px; border-bottom: none;">
                                DFOW:<br>
                                <div style="font-weight: bold; line-height: 1.2;">
                                    {{ \Illuminate\Support\Str::limit($document->project_description ?? $document->document_type, 150, '...') }}
                                </div>
                            </td>
                        </tr> -->
                        <!-- <tr>
                        <td style="padding: 4px; font-size: 14px; vertical-align: top;">
                            Reviewed by (Name/Title): <strong>{{ $document->safety_coordinator ?? '—' }}</strong>
                        </td>
                        
                    </tr> -->
                    </table>
                </td>
                <td style="border: 1px solid #000; padding: 0; vertical-align: top;">
                    <table
                        style="width: 100%; border-collapse: collapse; border: none; text-align: center; font-weight: bold; font-size: 14px;">
                        <thead>
                            <tr style="border-bottom: 1px solid #000;">
                                <th rowspan="2"
                                    style="border-top: 1px solid #000;border-right: 1px solid #000; width: 30%; padding: 4px; background: #fff; color: #000; font-size: 14px;">
                                    Severity</th>
                                <th colspan="5"
                                    style="padding: 4px; text-transform: uppercase; background: #fff; color: #000; border-bottom: 1px solid #000;border-top: 1px solid #000;border-right: 1px solid #000; font-size: 14px;">
                                    Probability</th>
                            </tr>
                            <tr style="border-bottom: 1px solid #000;">
                                <th
                                    style="border-right: 1px solid #000; font-weight: bold; font-size: 12px; background: #fff;">
                                    Freq.</th>
                                <th
                                    style="border-right: 1px solid #000; font-weight: bold; font-size: 12px; background: #fff;">
                                    Likely</th>
                                <th
                                    style="border-right: 1px solid #000; font-weight: bold; font-size: 12px; background: #fff;">
                                    Occas.</th>
                                <th
                                    style="border-right: 1px solid #000; font-weight: bold; font-size: 12px; background: #fff;">
                                    Seldom</th>
                                <th style="font-weight: bold; font-size: 12px; background: #fff;">Unlikely</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="border-bottom: 1px solid #000;">
                                <td
                                    style="border-right: 1px solid #000; padding: 10px 6px; text-align: center; background: #fff; text-transform: uppercase; font-size: 11px; font-weight: bold;">
                                    Catastrophic</td>
                                <td style="background: {{ $racEColor }}; color: #000; border-right: 1px solid #000;">E
                                </td>
                                <td style="background: {{ $racEColor }}; color: #000; border-right: 1px solid #000;">E
                                </td>
                                <td style="background: {{ $racHColor }}; color: #000; border-right: 1px solid #000;">H
                                </td>
                                <td style="background: {{ $racHColor }}; color: #000; border-right: 1px solid #000;">H
                                </td>
                                <td style="background: {{ $racMColor }}; color: #000;">M</td>
                            </tr>
                            <tr style="border-bottom: 1px solid #000;">
                                <td
                                    style="border-right: 1px solid #000; padding: 11px 6px; text-align: center; background: #fff; text-transform: uppercase; font-size: 11px; font-weight: bold;">
                                    Critical</td>
                                <td style="background: {{ $racEColor }}; color: #000; border-right: 1px solid #000;">E
                                </td>
                                <td style="background: {{ $racHColor }}; color: #000; border-right: 1px solid #000;">H
                                </td>
                                <td style="background: {{ $racHColor }}; color: #000; border-right: 1px solid #000;">H
                                </td>
                                <td style="background: {{ $racMColor }}; color: #000; border-right: 1px solid #000;">M
                                </td>
                                <td style="background: {{ $racLColor }}; color: #000;">L</td>
                            </tr>
                            <tr style="border-bottom: 1px solid #000;">
                                <td
                                    style="border-right: 1px solid #000; padding: 10px 6px; text-align: center; background: #fff; text-transform: uppercase; font-size: 11px; font-weight: bold;">
                                    Marginal</td>
                                <td style="background: {{ $racHColor }}; color: #000; border-right: 1px solid #000;">H
                                </td>
                                <td style="background: {{ $racMColor }}; color: #000; border-right: 1px solid #000;">M
                                </td>
                                <td style="background: {{ $racMColor }}; color: #000; border-right: 1px solid #000;">M
                                </td>
                                <td style="background: {{ $racLColor }}; color: #000; border-right: 1px solid #000;">L
                                </td>
                                <td style="background: {{ $racLColor }}; color: #000;">L</td>
                            </tr>
                            <tr style="border-bottom: 1px solid #000;">
                                <td
                                    style="border-right: 1px solid #000; padding: 9px 6px; text-align: center; background: #fff; text-transform: uppercase; font-size: 11px; font-weight: bold;">
                                    Negligible</td>
                                <td style="background: {{ $racMColor }}; color: #000; border-right: 1px solid #000;">M
                                </td>
                                <td style="background: {{ $racLColor }}; color: #000; border-right: 1px solid #000;">L
                                </td>
                                <td style="background: {{ $racLColor }}; color: #000; border-right: 1px solid #000;">L
                                </td>
                                <td style="background: {{ $racLColor }}; color: #000; border-right: 1px solid #000;">L
                                </td>
                                <td style="background: {{ $racLColor }}; color: #000;">L</td>
                            </tr>
                            <tr>
                                <td colspan="6" style="padding: 4px; border-top: 1px solid #000; font-size: 10px; font-weight: bold; text-align: center;">
                                    Review each "Hazard" with identified safety "Controls" and determine RAC (See above)
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>

            <!-- Row 4: Bottom section split (Instructions & RAC Chart) -->
            <tr>
                <td style="border: 1px solid #000; padding: 0; vertical-align: top;">
                    <table style="width: 100%; border-collapse: collapse; border: none;">

                        <tr>
                            <td style="padding: 8px; font-size: 12px; line-height: 1.2;  vertical-align: bottom;">
                                <div style="margin-bottom: 5px;">
                                    Notes: (Field Notes, Review Comments, etc.)
                                </div>
                                <!-- <div style="margin-bottom: 5px;">Required PPE: {{ $requiredPpe }}</div> -->
                                <!-- <div style="height: 10px;"></div> -->
                                <!-- <div style=" font-size: 11px; color: #b91c1c; font-style: italic; border-top: none; ">
                                    The signed copy needs to be scanned and placed in the red folder under preparatory
                                    meetings and another copy in the JHA binder at the right to know station.
                                </div> -->
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="border: 1px solid #000; padding: 0; vertical-align: top;">
                    <table style="width: 100%; border-collapse: collapse; border: none;">
                        <tr>
                            <td
                                style="width: 60%; border-right: 1px solid #000; padding: 6px; font-size: 12px; vertical-align: top; line-height: 1.2;">
                                <p style="margin-bottom: 6px; border-bottom: 1px solid #000; text-align: justify;">Step
                                    1: Review each "Hazard" with identified safety "Controls" and determine
                                    RAC (See above) </p>
                                <p style="margin-bottom: 6px; border-bottom: 1px solid #000; text-align: justify;">
                                    <strong>"Severity"</strong> is the outcome/degree if an incident, near miss, or
                                    accident did occur and identified as: Catastrophic, Critical, Marginal, or
                                    Negligible.
                                </p>
                                <p style="text-align: justify;"><strong>"Severity" </strong> is the outcome/degree if an
                                    incident, near miss, or accident did
                                    occur and identified as: Catastrophic, Critical, Marginal, or Negligible
                                    on AHA. </p>
                                <p style="text-align: justify; border-top: 1px solid #000;">Annotate the overall highest
                                    RAC at the top of AHA
                                    on AHA. </p>
                            </td>
                            <td style="width: 40%; padding: 0; vertical-align: top;">
                                <div
                                    style="background: #1a3a6b; color: #fff; font-size: 12px; font-weight: bold; text-align: center; padding: 10px; border-bottom: 1px solid #000; text-transform: uppercase; letter-spacing: 1px;border-right: 1px solid #000;">
                                    RAC Chart</div>
                                <div style="font-size: 12px; font-weight: bold;">
                                    <div
                                        style="background: {{ $racEColor }}; color: #000; padding: 10px 10px; border-bottom: 1px solid #000;border-right: 1px solid #000;">
                                        E = Extremely High</div>
                                    <div
                                        style="background: {{ $racHColor }}; color: #000; padding: 10px 10px; border-bottom: 1px solid #000;border-right: 1px solid #000;">
                                        H = High Risk</div>
                                    <div
                                        style="background: {{ $racMColor }}; color: #000; padding: 10px 10px; border-bottom: 1px solid #000;border-right: 1px solid #000;">
                                        M = Moderate Risk</div>
                                    <div
                                        style="background: {{ $racLColor }}; color: #000; padding: 10px 10px 11px 10px;border-right: 1px solid #000;">
                                        L = Low Risk</div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- <div style="padding: 5px 0; text-align: justify; font-size: 10px; color: #000; line-height: 1.4; margin-top: 40px;">
            <strong>Disclaimer:</strong>
            {{ $settings['disclaimer_text'] ?? 'The user, contractor, employer, or project owner is responsible for confirming that the contents of this document appropriately reflect the specific work activities, site conditions, and applicable laws, regulations, and project requirements before implementation. While reasonable efforts are made to provide useful and structured safety information, the provider shall not be liable for any damage, claim, or legal action arising from the use of this document.' }}
        </div> -->

    </div>{{-- end first page main-wrapper --}}



    <div style="page-break-after: always;"></div>

    <div class="after-page-one">
        <div class="main-wrapper page-content">
            <table class="analysis-table">
                <tbody>
                    <tr>
                        <th style="width:220px; border: 1px solid #000;">Job Steps</th>
                        <th style="width:250px; border: 1px solid #000;">Hazards</th>
                        <th style="border: 1px solid #000;">Risk Controls</th>
                        <th style="width:60px; text-align:center; border: 1px solid #000;">RAC</th>
                    </tr>
                    @foreach($document->ai_response['steps'] ?? [] as $i => $step)
                        @php
                            $hazards = $step['hazards'] ?? $step['hazard'] ?? 'N/A';
                            $controls = $step['controls'] ?? $step['control'] ?? 'N/A';

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

                            $maxStepRows = max(count($hazards), count($controls));
                            $stepClass = ($i % 2 == 1) ? 'step-even' : 'step-odd';

                            $initialRacRaw = $step['initial_rac'] ?? $step['rac'] ?? $step['risk'] ?? 'N/A';
                            $initialRac = is_array($initialRacRaw) ? ($initialRacRaw[0] ?? 'N/A') : $initialRacRaw;
                            $initialRacClass = '';
                            $initialRacChar = strtoupper(substr((string) $initialRac, 0, 1));
                            if (in_array($initialRacChar, ['E']))
                                $initialRacClass = 'cell-e';
                            elseif (in_array($initialRacChar, ['H']))
                                $initialRacClass = 'cell-h';
                            elseif (in_array($initialRacChar, ['M']))
                                $initialRacClass = 'cell-m';
                            elseif (in_array($initialRacChar, ['L']))
                                $initialRacClass = 'cell-l';
                        @endphp

                        @for($r = 0; $r < $maxStepRows; $r++)
                            <tr class="{{ $stepClass }}" style="page-break-inside: auto; background-color: {{ $settings['jha_main_table_bg'] ?? '#ffffff' }};">
                                {{-- Job Step Column --}}
                                <td style="width:220px; font-weight:bold; color:#000; font-size:16px; 
                                        border-left: 1px solid #000; border-right: 1px solid #000;
                                        border-bottom: {{ $r == $maxStepRows - 1 ? '1px solid #000' : '0' }}; 
                                        border-top: {{ $r == 0 ? '1px solid #000' : '0' }};">
                                    @if($r == 0)
                                        {{ $i + 1 }}. {{ preg_replace('/^(?:Step\s*\d+[\.\:\-\s]*|\d+[\.\-\s]+)+/i', '', $step['step_description'] ?? $step['step'] ?? 'N/A') }}
                                    @endif
                                </td>

                                {{-- Hazards Column --}}
                                <td style="width:250px; border-left: 1px solid #000; border-right: 1px solid #000;
                                        border-bottom: {{ $r == $maxStepRows - 1 ? '1px solid #000' : '0' }}; 
                                        border-top: {{ $r == 0 ? '1px solid #000' : '0' }};">
                                    @if(isset($hazards[$r]))
                                        @php $h = trim($hazards[$r], " \t\n\r\0\x0B-.*"); @endphp
                                        <div style="margin-bottom: 4px;">{{ count($hazards) > 1 ? ($r + 1) . '. ' : '' }}{{ $h }}
                                        </div>
                                    @endif
                                </td>

                                {{-- Controls Column --}}
                                <td style="border-left: 1px solid #000; border-right: 1px solid #000;
                                        border-bottom: {{ $r == $maxStepRows - 1 ? '1px solid #000' : '0' }}; 
                                        border-top: {{ $r == 0 ? '1px solid #000' : '0' }};">
                                    @if(isset($controls[$r]))
                                        @php $c = trim($controls[$r], " \t\n\r\0\x0B-.*"); @endphp
                                        <div style="margin-bottom: 4px;">{{ count($controls) > 1 ? ($r + 1) . '. ' : '' }}{{ $c }}
                                        </div>
                                    @endif
                                </td>

                                {{-- RAC Column (Initial Risk) --}}
                                <td class="{{ $initialRacClass }}" style="width:60px; text-align:center; vertical-align:middle; font-weight:bold; font-size:16px;
                                        border-left: 1px solid #000; border-right: 1px solid #000;
                                        border-bottom: {{ $r == $maxStepRows - 1 ? '1px solid #000' : '0' }}; 
                                        border-top: {{ $r == 0 ? '1px solid #000' : '0' }};">
                                    @if($r == 0)
                                        {{ $initialRac }}
                                    @endif
                                </td>
                            </tr>
                        @endfor
                    @endforeach
                </tbody>
            </table>

            {{-- EQUIPMENT TABLE --}}
            <div class="section-header">Equipment to be Used | Training | Inspection</div>
            <table class="equip-table">
                <thead>
                    <tr>
                        <th style="width:33%;">Equipment to be Used</th>
                        <th style="width:33%;">Training Required</th>
                        <th style="width:34%;">Inspection Requirements</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($document->ai_response['equipment'] ?? [] as $eq)

                        <tr>
                            <td style="color: #000;">{{ $eq['equipment'] ?? '' }}</td>
                            <td style="color: #000;">{{ $eq['training'] ?? '' }}</td>
                            <td style="color: #000;">{{ $eq['inspection'] ?? '' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="text-align:center; color:#64748b; font-style:italic; padding:10px;">
                                Equipment details not specified.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <table class="analysis-table" style="margin-top: 35px;">
                <thead>
                    <tr>
                        <th colspan="2"
                            style="text-align: center; font-size: 14px; background-color: {{ $settings['header_color'] ?? '#1a3a6b' }}; border: 1px solid #000;">
                            Activities Requiring a Competent or Qualified Person – Attach Proof of Competency
                        </th>
                    </tr>
                    <tr>
                        <th style="width: 50%; background-color: {{ $settings['jha_table_header_color'] ?? $settings['table_header_color'] ?? '#2c5f9e' }}">
                            Activity</th>
                        <th style="width: 50%; background-color: {{ $settings['jha_table_header_color'] ?? $settings['table_header_color'] ?? '#2c5f9e' }}">
                            Designated Competent or Qualified Person</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $competentActivities = $document->ai_response['competent_activities'] ?? [];
                    @endphp
                    @forelse($competentActivities as $act)
                        <tr>
                            <td style="font-weight: bold; color: #000;">{{ $act['activity'] ?? 'General Supervision' }}</td>
                            <td style="color: #000;">{{ $act['person'] ?? 'On-site Supervisor' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td style="font-weight: bold; color: #000;">General Safety Oversight</td>
                            <td style="color: #000;">{{ $document->competent_person ?? 'To be designated' }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- SIGNATURES --}}
            <div class="section-header">Signatures / Verification of Review</div>
            <table class="sig-table">
                <thead>
                    <tr>
                        <th style="width:34%">Name (Print)</th>
                        <th style="width:33%">Signature</th>
                        <th style="width:33%">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @for($r = 0; $r < 6; $r++)
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    @endfor
                </tbody>
            </table>

            <table class="sig-table" style="margin-top: 20px;">
                <thead>
                    <tr>
                        <th colspan="3"
                            style="text-align: left; font-size: 14px; background-color: {{ $settings['header_color'] ?? '#1a3a6b' }}; border: 1px solid #000;">
                            JHA Modified and Reviewed
                        </th>
                    </tr>
                    <tr>
                        <th style="width: 34%">Name (Print)</th>
                        <th style="width: 33%">Signature</th>
                        <th style="width: 33%">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @for($r = 0; $r < 3; $r++)
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    @endfor
                </tbody>
            </table>

        </div>

        <div style="margin-top: 30px;"></div>

        <div class="main-wrapper" style="page-break-inside: avoid;">
            <div class="section-header page-content" style="text-align: center; padding: 10px; line-height: 1.2;">
                TOOLBOX MEETING<br>
                <span style="font-size: 12px; font-weight: normal; text-transform: none;">(This JHA has been discussed
                    with the following crew)</span>
            </div>
            <table class="sig-table">
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;">No.</th>
                        <th style="width: 250px;">Name (Print)</th>
                        <th>Designation/Role</th>
                        <th style="width: 150px;">Signature</th>
                        <th style="width: 100px;">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @for($i = 1; $i <= 10; $i++)
                        <tr>
                            <td style="text-align: center; font-weight: bold;">{{ $i }}.</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    @endfor
                </tbody>
            </table>
            
            <div style="padding: 5px 0; text-align: justify; font-size: 10px; color: #000; line-height: 1.4; margin-top: 20px;">
                <strong>Disclaimer:</strong>
                {{ $settings['disclaimer_text'] ?? 'The user, contractor, employer, or project owner is responsible for confirming that the contents of this document appropriately reflect the specific work activities, site conditions, and applicable laws, regulations, and project requirements before implementation. While reasonable efforts are made to provide useful and structured safety information, the provider shall not be liable for any damage, claim, or legal action arising from the use of this document.' }}
            </div>
        </div>
    </div>

    <!-- <div class="disclaimer">
    Report Powered by InstantJHA AI Intelligence &mdash; OSHA Compliant
</div> -->

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