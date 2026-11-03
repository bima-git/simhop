@props(['submission'])

<div class="relative aspect-video w-full overflow-hidden rounded-lg bg-black">
    <iframe
        src="{{ $submission->embed_url }}"
        title="{{ $submission->title }}"
        class="absolute inset-0 h-full w-full"
        loading="lazy"
        allow="autoplay; encrypted-media; picture-in-picture; fullscreen"
        allowfullscreen
        referrerpolicy="strict-origin-when-cross-origin"
    ></iframe>
</div>