@props([
    'stats' => [
        ['value' => 'TBA', 'label' => 'Projects Completed'],
        ['value' => 'TBA', 'label' => 'Client Satisfaction'],
        ['value' => 'TBA', 'label' => 'Years of Experience'],
        ['value' => 'TBA', 'label' => 'Students Supported'],
    ],
])

<section class="bg-brand">
    <dl class="mx-auto grid max-w-7xl grid-cols-2 gap-y-12 px-4 py-16 sm:px-8 lg:grid-cols-4 lg:py-20">
        @foreach ($stats as $stat)
            <div class="flex flex-col items-center text-center">
                <dt class="order-last mt-1 text-blue-100 sm:text-lg">{{ $stat['label'] }}</dt>
                <dd class="font-display text-5xl font-extrabold text-white sm:text-6xl">{{ $stat['value'] }}</dd>
            </div>
        @endforeach
    </dl>
</section>
