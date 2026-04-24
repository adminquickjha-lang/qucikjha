<x-mail::message>
<div align="center" style="margin-bottom: 30px;">
<img src="{{ $message->embed(public_path('logo.jpg')) }}" alt="{{ config('app.name') }}" style="height: 100px; width: auto;">
</div>

# New Professional Review Request

Hello Admin,

A user has requested a professional manual review for their safety document.

**User:** {{ $userEmail }}
**Document:** {{ $documentName }}

**User's Message:**
{{ $reviewMessage }}

<x-mail::button :url="$url">
View & Review Document
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>