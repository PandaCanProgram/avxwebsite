<x-mail::layout>
<x-slot:header>
<x-mail::header :url="config('app.url')">
AVX Engineering Solutions
</x-mail::header>
</x-slot:header>

{{ $slot }}

<x-slot:footer>
<x-mail::footer>
© {{ date('Y') }} AVX Engineering Solutions. All rights reserved.
</x-mail::footer>
</x-slot:footer>
</x-mail::layout>
