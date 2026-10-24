<dialog class="project-gallery" aria-labelledby="gallery-title" data-gallery-dialog>
    <div class="gallery-panel" data-gallery-panel>
        <div class="gallery-header">
            <div>
                <p class="gallery-eyebrow">Project Gallery</p>
                <h2 id="gallery-title" data-gallery-title></h2>
                <p class="gallery-counter" data-gallery-counter role="status" aria-live="polite" aria-atomic="true"></p>
            </div>

            <button type="button" class="gallery-close" aria-label="Close gallery" data-gallery-close autofocus>
                <svg class="size-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="gallery-stage">
            <div class="gallery-image-frame" data-gallery-frame>
                <img alt="" class="gallery-image" data-gallery-image hidden>
                <p class="gallery-image-status" data-gallery-status role="status" hidden></p>
            </div>

            <button type="button" class="gallery-arrow gallery-arrow-prev" aria-label="Previous photo" data-gallery-prev>
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                </svg>
            </button>

            <button type="button" class="gallery-arrow gallery-arrow-next" aria-label="Next photo" data-gallery-next>
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>
            </button>
        </div>

        <div class="gallery-footer">
            <div class="gallery-thumbnails" data-gallery-thumbnails role="group" aria-label="Choose a project photo"></div>
            <p class="gallery-hint">Use arrow keys or swipe to explore</p>
        </div>
    </div>
</dialog>
