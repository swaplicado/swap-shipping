@component('mail::message')
{{-- Greeting --}}
@if (! empty($greeting))
# {{ $greeting }}
@else
@if ($level === 'error')
# @lang('Whoops!')
@else
# @lang('Hola!')
@endif
@endif

{{-- Intro Lines --}}
@foreach ($introLines as $line)
{{ $line }}

@endforeach

{{-- Action Button --}}
@isset($actionText)
<?php
    switch ($level) {
        case 'success':
        case 'error':
            $color = $level;
            break;
        default:
            $color = 'primary';
    }
?>
@component('mail::button', ['url' => $actionUrl[0], 'color' => $color])
{{ $actionText[0] }}
@endcomponent
@lang('Si se encuentra dentro de la planta de Cartro, haga click en el botón verde.')
@component('mail::button', ['url' => $actionUrl[1], 'color' => 'green'])
{{ $actionText[1] }}
@endcomponent
@endisset

{{-- Outro Lines --}}
@foreach ($outroLines as $line)
{{ $line }}

@endforeach

{{-- Salutation --}}
@if (! empty($salutation))
{{ $salutation }}
@else
@lang('Saludos'),<br>
{{ config('app.name') }}
@endif

{{-- Subcopy --}}
@isset($actionText)
@slot('subcopy')
@lang(
    "Si tiene algún problema al presionar \":actionText\" botón, copia y pega la siguiente dirección\n".
    'en tu navegador web: [:actionURL](:actionURL)'."\n si se encuentra dentro de la planta de cartro copia
    y pega la siguiente dirección en tu navegador web: [:actionURLsecond](:actionURLsecond)",
    [
        'actionText' => $actionText[0],
        'actionURL' => $actionUrl[0],
        'actionURLsecond' => $actionUrl[1]
    ]
)
@endslot
@endisset
@endcomponent
