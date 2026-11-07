@props([
    'width' => '24',
    'height' => '24',
])
  <svg
    width="{{ $width }}" height="{{ $height }}" @style([
        'min-width:' . $width . 'px',
        'min-height:' . $height . 'px'
    ]) 
    viewBox="0 0 24 24"
    fill="none"
    xmlns="http://www.w3.org/2000/svg"
    className={className}
  >
    <circle cx="12" cy="12" r="9" stroke="currentColor" strokeWidth="1.5" />
    <circle cx="9" cy="10" r="1.5" fill="currentColor" />
    <circle cx="15" cy="10" r="1.5" fill="currentColor" />
    <path
      d="M8.5 14.5C9.5 16 10.5 16.5 12 16.5C13.5 16.5 14.5 16 15.5 14.5"
      stroke="currentColor"
      strokeWidth="1.5"
      strokeLinecap="round"
    />
    <path
      d="M12 3V1M12 23V21"
      stroke="currentColor"
      strokeWidth="1.5"
      strokeLinecap="round"
    />
    <path
      d="M3 12H1M23 12H21"
      stroke="currentColor"
      strokeWidth="1.5"
      strokeLinecap="round"
    />
  </svg>