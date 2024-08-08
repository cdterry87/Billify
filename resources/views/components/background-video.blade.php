@props(['assetPath', 'type' => 'video/mp4'])

<div class="background-video-container background-video-overlay">
    <video
        autoplay
        muted
        loop
        id="background-video"
    >
        <source
            src="{{ asset($assetPath) }}"
            type="{{ $type }}"
        >
        Your browser does not support the video tag.
    </video>
</div>
