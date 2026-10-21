<dialog class="m-0 size-full max-h-none max-w-none bg-slate-950/95 p-0 text-white backdrop:bg-transparent" aria-labelledby="gallery-title" data-gallery-dialog>
    <div class="flex h-full flex-col">
        <div class="flex items-center justify-between gap-4 px-4 py-4 sm:px-8">
            <div>
                <h2 id="gallery-title" class="font-display text-xl font-bold tracking-wide uppercase sm:text-2xl" data-gallery-title></h2>
                <p class="mt-1 text-sm text-slate-400" data-gallery-counter></p>
            </div>

            <button type="button" class="p-2 text-slate-300 transition-colors hover:text-white" aria-label="Close gallery" data-gallery-close>
                <svg class="size-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="relative flex min-h-0 flex-1 items-center justify-center px-4 sm:px-20">
            <img src="" alt="" class="max-h-full max-w-full object-contain" data-gallery-image>

            <button type="button" class="absolute top-1/2 left-2 flex size-11 -translate-y-1/2 items-center justify-center border border-white/20 bg-slate-900/60 text-white transition-colors hover:bg-brand sm:left-6" aria-label="Previous photo" data-gallery-prev>
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                </svg>
            </button>

            <button type="button" class="absolute top-1/2 right-2 flex size-11 -translate-y-1/2 items-center justify-center border border-white/20 bg-slate-900/60 text-white transition-colors hover:bg-brand sm:right-6" aria-label="Next photo" data-gallery-next>
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>
            </button>
        </div>

        <div class="flex justify-center gap-2 overflow-x-auto px-4 py-4 sm:py-6" data-gallery-thumbnails></div>
    </div>
</dialog>
