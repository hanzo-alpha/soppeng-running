<x-filament-panels::page>
    <x-filament::section>
        <x-filament-panels::form wire:submit="create">
            {{ $this->form }}

            {{--            <x-filament-panels::form.actions--}}
            {{--                :actions="$this->getFormActions()"--}}
            {{--            />--}}
        </x-filament-panels::form>
    </x-filament::section>
    <x-filament::section>
        <img alt="" height="300px" src="{{ asset('frontend/running/SIZE CHART.png') }}">
    </x-filament::section>
</x-filament-panels::page>
