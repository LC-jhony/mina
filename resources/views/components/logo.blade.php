<div
    x-data="{ mode: 'light' }"
    x-on:dark-mode-toggled.window="mode = $event.detail"
>
    <span x-show="mode === 'light'">
        Light mode
    </span>

    <span x-show="mode === 'dark'">
        Dark mode
    </span>
</div>