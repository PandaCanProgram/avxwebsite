<x-inquiry-email>
# We have received your inquiry

Hello,

Thank you for contacting AVX Engineering Solutions. Your inquiry has been received, and our team will review it and get back to you.

**Your reference:** {{ $inquiry->reference() }}

You can reply to this email if you would like to add more information. Please include your reference so we can match it to your inquiry.

Thanks,<br>
AVX Engineering Solutions<br>
{{ config('inquiries.recipient') }}<br>
0934 917 0567
</x-inquiry-email>
