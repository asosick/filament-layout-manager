{{-- resources/views/livewire/dynamic-grid.blade.php --}}

<div x-data="{ sortable: null }"
     x-load-css="[@js(\Filament\Support\Facades\FilamentAsset::getStyleHref('filament-layout-manager-styles', package:'asosick/filament-layout-manager'))]"
     x-load-js="[@js(\Filament\Support\Facades\FilamentAsset::getScriptSrc('filament-layout-manager-scripts', package:'asosick/filament-layout-manager'))]"
     x-init="
        window.addEventListener('filament-layout-manager-scripts-loaded', () => {
            if (window.CustomSortableModule) window.CustomSortableModule.initialize();
        });
    "
    >
    <div class="flex justify-between w-full gap-y-8 py-8">
        <h1 class="fi-header-heading text-2xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-3xl">
            {{$heading}}
        </h1>
        <div class="flex justify-end space-x-2">
            @php
                $usedLayouts = min(
                    collect($this->container)->filter(fn ($component) => count($component) !==0)->count(),
                    $this->layoutCount
                );
            @endphp
            @if($editMode || $usedLayouts > 1)
                @for($i = 0; $i<$layoutCount; $i++)
                    @if($editMode || count($container[$i] ?? [])>0)
                        {{ ($this->selectLayoutAction)(['id' => $i]) }}
                    @endif
                @endfor
            @endif
            <div class="px-1 hidden md:flex">
                @if($editMode)
                    <x-filament::input.wrapper class="px-1">
                        <x-filament::input.select wire:model="selectedComponent">
                            @foreach(Arr::get($settings, 'select_options', []) as $index => $value)
                                <option value="{{$index}}">{{$value}}</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                    <div class="px-1">{{ $this->addAction }}</div>
                    <div class="px-0.5">{{ $this->saveAction }}</div>
                @endif
                @if($showLockButton)
                    <div class="px-0.5">{{$this->editAction}}</div>
                @endif
                @foreach($this->getHeaderActions() as $headerAction)
                    <div class="px-0.5">{{$headerAction}}</div>
                @endforeach
                <x-filament-actions::modals />
            </div>
        </div>
    </div>

    <div class="layout-manager-grid sm:grid block md:grid-cols-{{$columns}} gap-4" x-ref="grid">
        @foreach($container[$this->currentLayout] ?? [] as $id => $component)
            <div wire:key="grid-item-{{ $id }}"
                 data-id="{{ $id }}"
                 class="layout-manager-widget p-1"
                 style="grid-column: span {{ $component['cols'] }} / span {{ $component['cols'] }}">

                @if($editMode)
                    <div class="layout-manager-edit-controls flex gap-1 px-2 py-1 mb-2">
                        <button wire:click="removeComponent('{{ $id }}')"
                            class="text-lg font-bold">
                            ×
                        </button>
                        <button
                            wire:click="toggleSize('{{ $id }}')"
                            class="p-1 text-lg">
                            {{$component['cols'] === $columns ? '←' : '→'}}
                        </button>
                        <button
                            wire:click="increaseSize('{{ $id }}')"
                            class="text-lg">
                            +
                        </button>
                        <button
                            wire:click="decreaseSize('{{ $id }}')"
                            class="text-lg">
                            -
                        </button>
                        <div class="handle cursor-move rounded-full p-1 text-lg">
                            ⤴
                        </div>
                    </div>
                @endif
                <livewire:dynamic-component
                    :is="$component['type']['widget_class']"
                    :data="$component['type']['data'] ?? []"
                    :container_key="$id"
                    :store="$component['store'] ?? []"
                    :key="$id.'-'.$component['cols']"
                />
            </div>
        @endforeach
    </div>
</div>
