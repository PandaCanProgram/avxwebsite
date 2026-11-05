<x-inquiry-email>
# New alpha-test pre-registration

**Name:** {{ $registration->name ?? 'Not provided' }}

**Email:** {{ $registration->email }}

**Registered:** {{ $registration->created_at->timezone('Asia/Manila')->format('F j, Y g:i A') }} (Philippine time)

This visitor has pre-registered to receive an invitation to alpha-test AVX's internal projects.

Their email has been saved for your team's follow-up. No acknowledgment email was sent to the visitor.

AVX Engineering Solutions
</x-inquiry-email>
