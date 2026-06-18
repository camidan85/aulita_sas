@props(['align' => 'end'])

<div class="dropdown">
    <div role="button" data-bs-toggle="dropdown" aria-expanded="false">
        {{ $trigger }}
    </div>

    <ul class="dropdown-menu dropdown-menu-{{ $align }} shadow-sm">
        {{ $content }}
    </ul>
</div>
