@props([
    "width" => "24",
    "height" => "24",
])

<svg
    width="{{ $width }}"
    height="{{ $height }}"
    @style(["min-width:" . $width . "px", "min-height:" . $height . "px"])
    viewBox="0 0 6 10"
    fill="none"
    xmlns="http://www.w3.org/2000/svg"
>
    <path
        d="M0.75 8.75L4.75 4.75L0.75 0.75"
        stroke="currentColor"
        stroke-width="1.5"
        stroke-linecap="round"
        stroke-linejoin="round"
    />
</svg>
