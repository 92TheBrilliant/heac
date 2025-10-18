@props([
    'src',
    'alt' => '',
    'class' => '',
    'width' => null,
    'height' => null,
    'placeholder' => null,
])

<img 
    src="{{ $placeholder ?? 'data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 1 1\'%3E%3C/svg%3E' }}"
    data-src="{{ $src }}"
    alt="{{ $alt }}"
    class="lazy {{ $class }}"
    @if($width) width="{{ $width }}" @endif
    @if($height) height="{{ $height }}" @endif
    loading="lazy"
    {{ $attributes }}
>
