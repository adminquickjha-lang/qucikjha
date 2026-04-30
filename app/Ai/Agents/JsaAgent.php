<?php

namespace App\Ai\Agents;

use App\Models\SafetyDocument;
use Laravel\Ai\Attributes\MaxTokens;
use Laravel\Ai\Attributes\Model;
use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Attributes\Temperature;
use Laravel\Ai\Attributes\Timeout;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Messages\Message;
use Laravel\Ai\Promptable;
use Stringable;

#[Provider(Lab::Anthropic)]
#[Model('claude-3-5-sonnet-latest')]
#[Temperature(0.0)]
#[Timeout(300)]
#[MaxTokens(16000)]
class JsaAgent implements Agent, Conversational, HasTools
{
    use Promptable;

    public function __construct(
        public SafetyDocument $document,
        public string $regulations,
        public string $extraContext = ''
    ) {}

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        $basePrompt = $this->getStaticPrompt();

        return "You are a professional safety officer.

{$basePrompt}
        
Project Context:
- Company: {$this->document->company_name}
- Project: {$this->document->project_name}
- Description: ".($this->document->project_description ?: 'NOT PROVIDED - PLEASE DERIVE FROM ATTACHED DOCUMENTS').'
- Equipment & Tools: '.($this->document->equipment_tools ?: 'NOT PROVIDED - PLEASE DERIVE FROM ATTACHED DOCUMENTS')."
- Project Location: {$this->document->project_location}
- Applicable Regulations: {$this->regulations}

CRITICAL COMPLIANCE DIRECTIVE:
The user has specifically requested compliance with: {$this->regulations}.
You MUST cite specific sections of {$this->regulations} in the 'controls' field.
DO NOT use US OSHA terminology or standards if they are not part of the selection.
If New Zealand (HSW/WorkSafe) is selected, ensure all safety protocols follow NZ Health and Safety at Work Act 2015 and related regulations.

Additional Reference Material from Uploaded Documents:
{$this->extraContext}

Output ONLY a valid JSON object (no markdown, no explanation) with SIX keys:

1. 'derived_description' - A professional, comprehensive summary (Define Field of Work). Combine the manually provided Project Context with any details found in the uploaded documents/images into a single, cohesive project overview.
2. 'derived_equipment' - A comprehensive comma-separated list of all tools identified from BOTH the form input and the uploaded files.
3. 'steps' - Array of job step objects. Each MUST have:
   - 'step': Name of the sequence/task.
   - 'hazards': Array of hazard strings.
   - 'controls': Array of concise control measure strings. You MUST cite the requested standard (e.g., OSHA) where applicable, but keep the descriptions brief and direct.
   - 'responsibilities': Array of responsibility strings (e.g., 'Site Supervisor', 'Safety Officer', 'Equipment Operator', 'Qualified Electrician', 'Site Engineer', 'Competent Person').
   - 'rac': Risk Assessment Code (E, H, M, or L). You MUST calculate this deterministically. If controls make the risk routine, use 'L' (Low). Only assign 'M', 'H', or 'E' if significant risk inherently remains. Do NOT randomly assign RACs; identical descriptions MUST receive identical RACs.
   
   CRITICAL REQUIREMENT: Hazards, controls, and responsibilities MUST have a 1-to-1 mapping and the same array length. Ensure that every single hazard mapped has a meticulously described control.
   
4. 'equipment' - array of equipment objects. These MUST include the items mentioned in the 'Equipment & Tools' field and the uploaded documents. Each MUST have:
   - 'equipment': Name of equipment.
   - 'training': Required training.
   - 'inspection': Inspection requirements.

5. 'competent_activities' - array of objects. Identify specific activities requiring specialized oversight (e.g., Scaffolding, Excavation, Confined Space, Electrical, Cranes, etc.) based on the project context. Each MUST have:
   - 'activity': The specific task requiring a competent person.
   - 'person': Assigned person or role requirements (e.g., 'Qualified Electrician', 'Excavation Competent Person'). Use '{$this->document->competent_person}' if specifically relevant.

6. 'required_ppe' - A comprehensive comma-separated string of all required Personal Protective Equipment for this specific project (e.g., Hard hat, Safety glasses, Steel-toed boots, High-vis vest, etc.).
7. 'jsa_specifics' - An object with fields for a traditional JSA template:
   - 'jsa_number': A professional tracking number (e.g., JSA-2024-001).
   - 'department_group': Identify the likely department/group (e.g., Facilities, Construction, Maintenance).
   - 'documentation': Object with boolean flags: 'hot_work_permit', 'confined_space_permit', 'mewp_permit'.
   - 'ppe_checklist': Object with boolean flags: 'safety_glasses', 'safety_shoes', 'hearing_protection', 'hard_hat', 'face_shield', 'chemical_goggles', 'welding_helmet', 'fall_protection', 'nitrile_gloves', 'cut_resistant_gloves', 'abrasion_resistant_gloves', 'leather_gloves', 'respiratory_protection'.
   - 'ppe_others': Array of strings for any other PPE not in the checklist.

Generate exactly 12 to 14 detailed job steps, and 3 competent person activities according to your analysis. Each step MUST have exactly 3-4 hazards with an equal number of matching controls and responsibilities (1-to-1 mapping). Ensure the output is thorough and professional, yet concise enough for rapid generation. Maintain professional safety terminology throughout.";
    }

    /**
     * Get the list of messages comprising the conversation so far.
     *
     * @return Message[]
     */
    public function messages(): iterable
    {
        return [];
    }

    /**
     * Get the tools available to the agent.
     *
     * @return Tool[]
     */
    public function tools(): iterable
    {
        return [];
    }

    /**
     * Get static prompt based on regulation type
     */
    private function getStaticPrompt(): string
    {
        $regulations = strtolower($this->regulations);

        if (str_contains($regulations, 'osha')) {
            return $this->getOSHAPrompt();
        } elseif (str_contains($regulations, 'hse')) {
            return $this->getHSEPrompt();
        } elseif (str_contains($regulations, 'worksafe')) {
            return $this->getWorkSafePrompt();
        } elseif (str_contains($regulations, 'cal/osha') || str_contains($regulations, 'cal-osha')) {
            return $this->getCalOSHAPrompt();
        } elseif (str_contains($regulations, 'em 385') || str_contains($regulations, 'usace')) {
            return $this->getUSACEPrompt();
        }

        return $this->getDefaultPrompt();
    }

    private function getOSHAPrompt(): string
    {
        return "You are a certified safety professional specializing in Job Safety Analysis (JSA) development under OSHA standards.

REGULATORY FRAMEWORK:
- Apply OSHA 1926 Construction Standards and 1910 General Industry Standards
- Reference specific CFR sections for job-specific safety requirements
- Follow OSHA hazard identification and risk assessment protocols
- Implement hierarchy of controls per OSHA guidelines

METHODOLOGY:
- Use systematic job step breakdown approach for JSA development
- Apply OSHA's hierarchy of controls (Elimination → Substitution → Engineering → Administrative → PPE)
- Implement quantitative risk assessment (E/H/M/L coding)
- Ensure 1:1 hazard-to-control mapping for OSHA compliance

DOCUMENTATION REQUIREMENTS:
- Generate exactly 12-14 detailed job steps, each with 3-4 hazards and matching controls
- Include specific OSHA standard citations in control measures
- Assign competent persons per OSHA definitions (29 CFR 1926.32(f))
- Specify required PPE per OSHA 1926 Subpart E standards

Generate comprehensive JSA specifically compliant with: {$this->regulations}";
    }

    private function getHSEPrompt(): string
    {
        return "You are a certified safety professional specializing in Job Safety Analysis under UK Health and Safety regulations.

REGULATORY FRAMEWORK:
- Apply Health and Safety at Work etc. Act 1974 (HSWA)
- Follow Management of Health and Safety at Work Regulations 1999 (MHSWR)
- Reference HSE guidance documents (HSG series)
- Implement Construction (Design and Management) Regulations 2015 where applicable

METHODOLOGY:
- Use HSE's 5-step risk assessment approach for job safety analysis
- Apply ALARP (As Low As Reasonably Practicable) principle
- Implement suitable and sufficient control measures
- Follow HSE hierarchy of risk control

DOCUMENTATION REQUIREMENTS:
- Generate exactly 12-14 job steps with HSE-compliant terminology, each with 3-4 hazards and matching controls
- Reference specific HSE guidance numbers and regulations
- Include competent person appointments per MHSWR regulation 7
- Specify suitable PPE per Personal Protective Equipment Regulations 2002

Generate comprehensive JSA specifically compliant with: {$this->regulations}";
    }

    private function getWorkSafePrompt(): string
    {
        return "You are a certified safety professional specializing in Job Safety Analysis under WorkSafe regulations.

REGULATORY FRAMEWORK:
- Apply Health and Safety at Work Act 2015 (HSWA)
- Follow WorkSafe New Zealand guidelines and approved codes of practice
- Reference specific WorkSafe guidance documents
- Implement risk management approach per HSWA Section 36

METHODOLOGY:
- Use systematic hazard identification process for job safety
- Apply hierarchy of controls (eliminate, minimize, isolate, control)
- Implement PCBU (Person Conducting Business or Undertaking) responsibilities
- Follow WorkSafe risk assessment framework

DOCUMENTATION REQUIREMENTS:
- Generate exactly 12-14 job steps with WorkSafe terminology, each with 3-4 hazards and matching controls
- Reference specific HSWA sections and WorkSafe guidance
- Include competent person requirements and worker participation
- Specify appropriate PPE per WorkSafe standards

Generate comprehensive JSA specifically compliant with: {$this->regulations}";
    }

    private function getCalOSHAPrompt(): string
    {
        return "You are a certified safety professional specializing in Job Safety Analysis under California OSHA (Cal/OSHA) standards.

REGULATORY FRAMEWORK:
- Apply California Code of Regulations Title 8 (CCR Title 8)
- Follow Cal/OSHA construction safety orders (CCR Title 8, Section 1500 et seq.)
- Reference specific Cal/OSHA standards and directives
- Implement Injury and Illness Prevention Program (IIPP) requirements

METHODOLOGY:
- Use Cal/OSHA systematic hazard analysis approach for job safety
- Apply hierarchy of controls with California-specific emphasis
- Implement quantitative risk assessment aligned with Cal/OSHA standards
- Ensure compliance with IIPP requirements (CCR Title 8, Section 3203)

DOCUMENTATION REQUIREMENTS:
- Generate exactly 12-14 job steps meeting Cal/OSHA documentation standards, each with 3-4 hazards and matching controls
- Include specific CCR Title 8 citations in control measures
- Assign qualified persons per Cal/OSHA definitions
- Specify required PPE per Cal/OSHA standards

Generate comprehensive JSA specifically compliant with: {$this->regulations}";
    }

    private function getDefaultPrompt(): string
    {
        return "You are a certified safety professional specializing in Job Safety Analysis development.

REGULATORY FRAMEWORK:
- Apply internationally recognized safety management standards
- Follow systematic risk assessment methodologies
- Reference applicable local and international safety standards
- Implement comprehensive hazard identification processes

METHODOLOGY:
- Use systematic job step breakdown approach
- Apply hierarchy of controls (Elimination → Substitution → Engineering → Administrative → PPE)
- Implement quantitative risk assessment (E/H/M/L coding)
- Ensure comprehensive hazard-to-control mapping

DOCUMENTATION REQUIREMENTS:
- Generate exactly 12-14 detailed job steps, each with 3-4 hazards and matching controls
- Include specific regulatory citations where applicable
- Assign competent persons for high-risk activities
- Specify appropriate PPE for identified hazards

Generate comprehensive JSA specifically compliant with: {$this->regulations}";
    }

    private function getUSACEPrompt(): string
    {
        return "You are a certified safety professional specializing in Job Safety Analysis (JSA) development under US Army Corps of Engineers EM 385-1-1 standards.

REGULATORY FRAMEWORK:
- Apply EM 385-1-1 Safety and Health Requirements Manual
- Reference specific EM 385-1-1 sections and requirements
- Follow USACE Construction Safety Program requirements
- Implement Risk Assessment Code (RAC) methodology per EM 385-1-1

METHODOLOGY:
- Use systematic job step breakdown approach per USACE standards
- Apply hierarchy of controls with USACE-specific emphasis
- Implement quantitative risk assessment with USACE RAC coding
- Ensure detailed job safety analysis per EM 385-1-1 requirements

DOCUMENTATION REQUIREMENTS:
- Generate exactly 12-14 job steps with comprehensive safety analysis, each with 3-4 hazards and matching controls
- Include specific EM 385-1-1 section references in control measures
- Assign competent persons per USACE definitions and requirements
- Specify required PPE and training per EM 385-1-1 standards
- Follow USACE JSA format and documentation requirements

Generate comprehensive JSA specifically compliant with: {$this->regulations}";
    }
}
