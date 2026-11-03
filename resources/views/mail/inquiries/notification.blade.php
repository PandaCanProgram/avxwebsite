<x-inquiry-email>
# New {{ $inquiry->kind === 'investment' ? 'investor' : 'project' }} inquiry

**Reference:** {{ $inquiry->reference() }}

**Name:** {{ $inquiry->name }}

**Email:** {{ $inquiry->email }}

@if ($inquiry->organization)
**Company / organization:** {{ $inquiry->organization }}
@endif

@if ($inquiry->phone)
**Phone:** {{ $inquiry->phone }}
@endif

@if ($inquiry->interest)
**Area of interest:** {{ $inquiry->interest }}
@endif

**Subject:** {{ $inquiry->subject }}

## Message

<div style="white-space: pre-wrap;">{{ $inquiry->message }}</div>

Use Reply to respond directly to the visitor.

AVX Engineering Solutions
</x-inquiry-email>
