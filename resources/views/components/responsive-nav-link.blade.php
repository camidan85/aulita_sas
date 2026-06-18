@props(['active' => false])

<a {{ $attributes->merge(['class' => 'nav-link px-2'.($active ? ' active fw-semibold' : '')]) }}>
    {{ $slot }}
</a>
