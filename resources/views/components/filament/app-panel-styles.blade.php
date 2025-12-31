@php
    $cssPath = resource_path('css/filament/app-panel.css');
    $cssContent = file_exists($cssPath) ? file_get_contents($cssPath) : '';
@endphp

@if($cssContent)
<style>
{!! $cssContent !!}
</style>
@endif

