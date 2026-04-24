<?php

namespace App\Ai\Agents;

use App\Models\SafetyDocument;
use Laravel\Ai\Attributes\MaxTokens;
use Laravel\Ai\Attributes\Temperature;
use Laravel\Ai\Attributes\Timeout;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Messages\Message;
use Laravel\Ai\Promptable;
use Stringable;

#[Temperature(0.0)]
#[Timeout(300)]
#[MaxTokens(16000)]
class ReviewAgent implements Agent, Conversational, HasTools
{
    use Promptable;

    public function __construct(
        public SafetyDocument $document,
    ) {
    }

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        $currentData = json_encode($this->document->ai_response, JSON_PRETTY_PRINT);
        $docType = strtoupper($this->document->document_type);
        $regs = is_array($this->document->regulations) ? implode(', ', $this->document->regulations) : $this->document->regulations;

        return "You are an expert safety consultant. You are tasked with improving an existing {$docType} (Safety Document) based on user feedback.

REGULATORY CONTEXT: {$regs}

CURRENT DOCUMENT DATA (JSON):
{$currentData}

YOUR TASK:
1. Review the current JSON data including job steps, hazards, controls, and equipment.
2. Apply the specific improvements requested by the user in their message.
3. If the user asks for more steps, add them but do your analysis like if he ask for too many or job steps exceding from normal increase mostly use your analysis while maintaining the regulatory context.
4. If the user asks to clarify or change existing steps, update them accordingly.
5. Ensure all RAC codes are recalculated if the controls change.
6. MANDATORY: Your output MUST be ONLY the updated JSON object.
7. Preserve all existing structure and keys exactly as they appear in the original document.
8. IF THE USER REQUEST IS UNCLEAR, VAGUE, OR NOT SPECIFIC: DO NOT ask for clarification. Make your best attempt to improve the document slightly, or simply return the EXACT original JSON. NEVER RETURN CONVERSATIONAL TEXT.

Output ONLY valid JSON. No markdown backticks, no introductory text, no explanations.";
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
}
