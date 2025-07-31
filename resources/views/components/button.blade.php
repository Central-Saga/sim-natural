@props([
    'variant' => 'primary',
    'color' => 'emerald',
    'type' => 'button',
    'size' => 'base',
    'class' => '',
])

<flux:button 
    :variant="$variant" 
    :color="$color" 
    :type="$type" 
    :size="$size" 
    :class="$class"
>
    {{ $slot }}
</flux:button> 