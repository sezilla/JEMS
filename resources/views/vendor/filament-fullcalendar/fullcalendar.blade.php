@php
    $plugin = \Saade\FilamentFullCalendar\FilamentFullCalendarPlugin::get();
@endphp

<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex justify-end flex-1 mb-4">
            <x-filament-actions::actions :actions="$this->getCachedHeaderActions()" class="shrink-0" />
        </div>

        <div class="filament-fullcalendar" wire:ignore ax-load
            ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('filament-fullcalendar-alpine', 'saade/filament-fullcalendar') }}"
            ax-load-css="{{ \Filament\Support\Facades\FilamentAsset::getStyleHref('filament-fullcalendar-styles', 'saade/filament-fullcalendar') }}"
            x-ignore x-data="fullcalendar({
                locale: @js($plugin->getLocale()),
                plugins: @js(array_merge($plugin->getPlugins(), ['multiMonth'])),
                schedulerLicenseKey: @js($plugin->getSchedulerLicenseKey()),
                timeZone: @js($plugin->getTimezone()),

                config: @js(array_merge($this->getConfig(), [
                    'initialView' => 'multiMonthYear',
                    'views' => [
                        'year' => [
                            'type' => 'multiMonth',
                            'duration' => ['months' => 12],
                            'buttonText' => 'year'
                        ],
                        'multiMonthYear' => ['type' => 'multiMonth', 'duration' => ['months' => 12]],
                        'multiMonth' => ['duration' => ['months' => 2]],
                        'dayGrid' => [
                            'dayMaxEventRows' => 2
                        ],
                        'timeGrid' => [
                            'dayMaxEventRows' => 2
                        ]
                    ],
                    'headerToolbar' => [
                        'left' => 'prev,next today',
                        'center' => 'title',
                        'right' => 'year,dayGridMonth,timeGridWeek,timeGridDay'
                    ],
                    'handleWindowResize' => true,
                    'height' => 'auto',
                    'windowResizeDelay' => 200,
                    'windowResize' => 'function(arg) {
                        // Get the current view
                        const view = arg.view;
                        
                        // Adjust event display based on window width
                        if (window.innerWidth < 768) {
                            // Mobile view
                            view.calendar.setOption("dayMaxEvents", 2);
                            view.calendar.setOption("eventMaxStack", 2);
                        } else if (window.innerWidth < 1024) {
                            // Tablet view
                            view.calendar.setOption("dayMaxEvents", 3);
                            view.calendar.setOption("eventMaxStack", 3);
                        } else {
                            // Desktop view
                            view.calendar.setOption("dayMaxEvents", true);
                            view.calendar.setOption("eventMaxStack", 4);
                        }
                        
                        // Force calendar to re-render
                        view.calendar.updateSize();
                    }',
                    'stickyHeaderDates' => true,
                    'expandRows' => true,
                    'dayMaxEvents' => true,
                    'eventMaxStack' => 3,
                    'dayPopoverFormat' => [
                        'month' => 'long',
                        'day' => 'numeric',
                        'year' => 'numeric'
                    ],
                    'moreLinkClick' => 'popover',
                    'eventDidMount' => 'function(info) {
                        // Define an array of colors
                        const colors = [
                            { background: "#4338ca", text: "#ffffff" }, // Indigo
                            { background: "#0891b2", text: "#ffffff" }, // Cyan
                            { background: "#059669", text: "#ffffff" }, // Emerald
                            { background: "#ca8a04", text: "#ffffff" }, // Yellow
                            { background: "#dc2626", text: "#ffffff" }, // Red
                            { background: "#7c3aed", text: "#ffffff" }, // Violet
                            { background: "#be185d", text: "#ffffff" }  // Pink
                        ];
                        
                        // Generate a consistent index based on the event title
                        const index = Array.from(info.event.title)
                            .reduce((acc, char) => acc + char.charCodeAt(0), 0) % colors.length;
                        
                        // Apply the colors
                        info.el.style.backgroundColor = colors[index].background;
                        info.el.style.borderColor = colors[index].background;
                        info.el.style.color = colors[index].text;
                        
                        // Add hover effect
                        info.el.style.transition = "all 0.2s ease";
                        info.el.addEventListener("mouseenter", function() {
                            this.style.filter = "brightness(85%)";
                        });
                        info.el.addEventListener("mouseleave", function() {
                            this.style.filter = "brightness(100%)";
                        });

                        tippy(info.el, {
                            content: info.event.title,
                            placement: "top",
                            trigger: "mouseenter focus"
                        });
                    }'
                ])),

                editable: @json($plugin->isEditable()),
                selectable: @json($plugin->isSelectable()),
                eventClassNames: {!! htmlspecialchars($this->eventClassNames(), ENT_COMPAT) !!},
                eventContent: {!! htmlspecialchars($this->eventContent(), ENT_COMPAT) !!},
                eventDidMount: {!! htmlspecialchars($this->eventDidMount(), ENT_COMPAT) !!},
                eventWillUnmount: {!! htmlspecialchars($this->eventWillUnmount(), ENT_COMPAT) !!},
            })">
        </div>
    </x-filament::section>

    <x-filament-actions::modals />
</x-filament-widgets::widget>
