@props([
    "width" => "24",
    "height" => "24",
])

<svg
    width="{{ $width }}"
    height="{{ $height }}"
    @style(["min-width:" . $width . "px", "min-height:" . $height . "px"])
    viewBox="0 0 24 24"
    fill="none"
    xmlns="http://www.w3.org/2000/svg"
>
    <g clip-path="url(#clip0_6_13427)">
        <path
            d="M12 4L10.59 5.41L16.17 11H4V13H16.17L10.59 18.59L12 20L20 12L12 4Z"
            fill="currentColor"
        />
    </g>
    <defs>
        <clipPath id="clip0_6_13427">
            <rect
                width="24"
                height="24"
                fill="white"
            />
        </clipPath>
    </defs>
</svg>
