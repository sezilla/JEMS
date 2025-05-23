@php
    $plugin = \Saade\FilamentFullCalendar\FilamentFullCalendarPlugin::get();
    
    // Determine calendar type and configuration
    $calendarType = class_basename($this);
    $isTaskCalendar = $calendarType === 'UserTaskCalendar';
    $isProjectCalendar = $calendarType === 'ProjectCalendar';
    
    // Set calendar-specific configurations
    if ($isTaskCalendar) {
        $calendarLabel = 'My Tasks';
        $calendarIcon = '📋';
        $availableViews = 'dayGridMonth,timeGridWeek,timeGridDay';
        $initialView = 'dayGridMonth';
        $calendarColor = 'rgb(59, 130, 246)'; // Blue
    } elseif ($isProjectCalendar) {
        $calendarLabel = 'Events';
        $calendarIcon = '📅';
        $availableViews = 'multiMonthYear,dayGridMonth,timeGridWeek,timeGridDay';
        $initialView = 'dayGridMonth';
        $calendarColor = 'rgb(16, 185, 129)'; // Emerald
    } else {
        $calendarLabel = 'Calendar';
        $calendarIcon = '📆';
        $availableViews = 'dayGridMonth,timeGridWeek,timeGridDay';
        $initialView = 'dayGridMonth';
        $calendarColor = 'rgb(139, 92, 246)'; // Purple
    }
@endphp

<x-filament-widgets::widget>
    <x-filament::section>
        <!-- Calendar Header with Label and Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg shadow-sm" 
                     style="background: linear-gradient(135deg, {{ $calendarColor }}, {{ $calendarColor }}cc);">
                    <span class="text-xl">{{ $calendarIcon }}</span>
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                        {{ $calendarLabel }}
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        @if($isTaskCalendar)
                            Track your personal tasks and deadlines
                        @elseif($isProjectCalendar)
                            View project timelines and events
                        @else
                            Calendar overview
                        @endif
                    </p>
                </div>
            </div>
            
            <div class="flex items-center gap-2">
                <x-filament-actions::actions :actions="$this->getCachedHeaderActions()" class="shrink-0" />
            </div>
        </div>

        <!-- Calendar Container with Enhanced Styling -->
        <div class="calendar-container relative overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm">
            <!-- Mobile View Indicator -->
            <div class="sm:hidden absolute top-3 right-3 z-10">
                <div class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                    Mobile View
                </div>
            </div>

            <div class="filament-fullcalendar p-4" wire:ignore ax-load
                ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('filament-fullcalendar-alpine', 'saade/filament-fullcalendar') }}"
                ax-load-css="{{ \Filament\Support\Facades\FilamentAsset::getStyleHref('filament-fullcalendar-styles', 'saade/filament-fullcalendar') }}"
                x-ignore x-data="fullcalendar({
                    locale: @js($plugin->getLocale()),
                    plugins: @js(array_merge($plugin->getPlugins(), ['multiMonth'])),
                    schedulerLicenseKey: @js($plugin->getSchedulerLicenseKey()),
                    timeZone: @js($plugin->getTimezone()),

                    config: @js(array_merge($this->getConfig(), [
                        'initialView' => $initialView,
                        'views' => [
                            'year' => [
                                'type' => 'multiMonth',
                                'duration' => ['months' => 12],
                                'buttonText' => 'Year'
                            ],
                            'multiMonthYear' => [
                                'type' => 'multiMonth', 
                                'duration' => ['months' => 12],
                                'buttonText' => 'Year'
                            ],
                            'multiMonth' => [
                                'duration' => ['months' => 2],
                                'buttonText' => '2 Months'
                            ],
                            'dayGridMonth' => [
                                'dayMaxEventRows' => 3,
                                'buttonText' => 'Month'
                            ],
                            'timeGridWeek' => [
                                'dayMaxEventRows' => 4,
                                'buttonText' => 'Week'
                            ],
                            'timeGridDay' => [
                                'buttonText' => 'Day'
                            ]
                        ],
                        'headerToolbar' => [
                            'left' => 'prev,next today',
                            'center' => 'title',
                            'right' => $availableViews
                        ],
                        // Responsive breakpoints
                        'handleWindowResize' => true,
                        'height' => 'auto',
                        'windowResizeDelay' => 100,
                        'aspectRatio' => 1.35,
                        'contentHeight' => 'auto',
                        
                        // Mobile-specific configurations
                        'windowResize' => 'function(view) {
                            const calendar = view.calendar;
                            const width = window.innerWidth;
                            
                            if (width < 640) {
                                // Mobile phones
                                calendar.setOption("headerToolbar", {
                                    left: "prev,next",
                                    center: "title", 
                                    right: "today"
                                });
                                calendar.setOption("dayMaxEvents", 2);
                                calendar.setOption("eventMaxStack", 2);
                                calendar.setOption("aspectRatio", 0.8);
                                
                                // Force month view on mobile
                                if (calendar.view.type !== "dayGridMonth") {
                                    calendar.changeView("dayGridMonth");
                                }
                            } else if (width < 768) {
                                // Small tablets
                                calendar.setOption("headerToolbar", {
                                    left: "prev,next today",
                                    center: "title",
                                    right: "dayGridMonth,timeGridWeek"
                                });
                                calendar.setOption("dayMaxEvents", 3);
                                calendar.setOption("eventMaxStack", 3);
                                calendar.setOption("aspectRatio", 1.2);
                            } else if (width < 1024) {
                                // Large tablets
                                calendar.setOption("headerToolbar", {
                                    left: "prev,next today",
                                    center: "title",
                                    right: "' . $availableViews . '"
                                });
                                calendar.setOption("dayMaxEvents", 4);
                                calendar.setOption("eventMaxStack", 4);
                                calendar.setOption("aspectRatio", 1.35);
                            } else {
                                // Desktop
                                calendar.setOption("headerToolbar", {
                                    left: "prev,next today",
                                    center: "title", 
                                    right: "' . $availableViews . '"
                                });
                                calendar.setOption("dayMaxEvents", true);
                                calendar.setOption("eventMaxStack", 5);
                                calendar.setOption("aspectRatio", 1.35);
                            }
                            
                            // Force re-render
                            setTimeout(() => calendar.updateSize(), 100);
                        }',
                        
                        // Enhanced styling options
                        'stickyHeaderDates' => true,
                        'expandRows' => true,
                        'dayMaxEvents' => 3,
                        'eventMaxStack' => 4,
                        'dayPopoverFormat' => [
                            'month' => 'long',
                            'day' => 'numeric',
                            'year' => 'numeric'
                        ],
                        'moreLinkClick' => 'popover',
                        'moreLinkText' => 'more',
                        
                        // Enhanced event styling
                        'eventDidMount' => 'function(info) {
                            const isTaskCalendar = ' . ($isTaskCalendar ? 'true' : 'false') . ';
                            const isProjectCalendar = ' . ($isProjectCalendar ? 'true' : 'false') . ';
                            
                            // Color schemes for different calendar types
                            let colors;
                            if (isTaskCalendar) {
                                colors = [
                                    { bg: "#ef4444", text: "#ffffff", shadow: "#ef444420" }, // Red
                                    { bg: "#f97316", text: "#ffffff", shadow: "#f9731620" }, // Orange  
                                    { bg: "#eab308", text: "#ffffff", shadow: "#eab30820" }, // Yellow
                                    { bg: "#22c55e", text: "#ffffff", shadow: "#22c55e20" }, // Green
                                    { bg: "#3b82f6", text: "#ffffff", shadow: "#3b82f620" }, // Blue
                                    { bg: "#8b5cf6", text: "#ffffff", shadow: "#8b5cf620" }, // Purple
                                    { bg: "#ec4899", text: "#ffffff", shadow: "#ec489920" }  // Pink
                                ];
                            } else {
                                colors = [
                                    { bg: "#10b981", text: "#ffffff", shadow: "#10b98120" }, // Emerald
                                    { bg: "#06b6d4", text: "#ffffff", shadow: "#06b6d420" }, // Cyan
                                    { bg: "#8b5cf6", text: "#ffffff", shadow: "#8b5cf620" }, // Violet
                                    { bg: "#f59e0b", text: "#ffffff", shadow: "#f59e0b20" }, // Amber
                                    { bg: "#ef4444", text: "#ffffff", shadow: "#ef444420" }, // Red
                                    { bg: "#6366f1", text: "#ffffff", shadow: "#6366f120" }, // Indigo
                                    { bg: "#84cc16", text: "#ffffff", shadow: "#84cc1620" }  // Lime
                                ];
                            }
                            
                            // Generate consistent color based on event title
                            const colorIndex = Array.from(info.event.title)
                                .reduce((acc, char) => acc + char.charCodeAt(0), 0) % colors.length;
                            const color = colors[colorIndex];
                            
                            // Apply enhanced styling
                            info.el.style.backgroundColor = color.bg;
                            info.el.style.borderColor = color.bg;
                            info.el.style.color = color.text;
                            info.el.style.borderRadius = "6px";
                            info.el.style.border = "none";
                            info.el.style.boxShadow = `0 2px 4px ${color.shadow}`;
                            info.el.style.transition = "all 0.2s cubic-bezier(0.4, 0, 0.2, 1)";
                            info.el.style.cursor = "pointer";
                            
                            // Add responsive text sizing
                            const textEl = info.el.querySelector(".fc-event-title, .fc-event-title-container");
                            if (textEl) {
                                if (window.innerWidth < 640) {
                                    textEl.style.fontSize = "11px";
                                    textEl.style.lineHeight = "1.2";
                                } else if (window.innerWidth < 768) {
                                    textEl.style.fontSize = "12px";
                                    textEl.style.lineHeight = "1.3";
                                } else {
                                    textEl.style.fontSize = "13px";
                                    textEl.style.lineHeight = "1.4";
                                }
                            }
                            
                            // Enhanced hover effects
                            info.el.addEventListener("mouseenter", function() {
                                this.style.transform = "translateY(-1px) scale(1.02)";
                                this.style.boxShadow = `0 4px 12px ${color.shadow}`;
                                this.style.zIndex = "10";
                            });
                            
                            info.el.addEventListener("mouseleave", function() {
                                this.style.transform = "translateY(0) scale(1)";
                                this.style.boxShadow = `0 2px 4px ${color.shadow}`;
                                this.style.zIndex = "auto";
                            });

                            // Enhanced tooltip
                            if (typeof tippy !== "undefined") {
                                tippy(info.el, {
                                    content: `
                                        <div class="text-center">
                                            <div class="font-semibold text-sm mb-1">${info.event.title}</div>
                                            <div class="text-xs opacity-80">
                                                ${isTaskCalendar ? "Task" : "Event"} • ${info.event.start.toLocaleDateString()}
                                            </div>
                                        </div>
                                    `,
                                    allowHTML: true,
                                    placement: "top",
                                    theme: "light-border",
                                    arrow: true,
                                    duration: [200, 150],
                                    delay: [300, 0]
                                });
                            }
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
        </div>
    </x-filament::section>

    <x-filament-actions::modals />
</x-filament-widgets::widget>

<style>
/* Enhanced calendar styling */
.calendar-container .fc {
    font-family: inherit;
}

.calendar-container .fc-toolbar {
    margin-bottom: 1rem !important;
    gap: 0.5rem;
}

.calendar-container .fc-toolbar-title {
    font-size: 1.25rem !important;
    font-weight: 600 !important;
    color: rgb(31 41 55) !important;
}

.dark .calendar-container .fc-toolbar-title {
    color: rgb(243 244 246) !important;
}

.calendar-container .fc-button {
    border-radius: 6px !important;
    border: 1px solid rgb(209 213 219) !important;
    background: white !important;
    color: rgb(75 85 99) !important;
    font-weight: 500 !important;
    padding: 0.375rem 0.75rem !important;
    font-size: 0.875rem !important;
    transition: all 0.2s ease !important;
}

.calendar-container .fc-button:hover {
    background: rgb(249 250 251) !important;
    border-color: rgb(156 163 175) !important;
    transform: translateY(-1px);
}

.calendar-container .fc-button-primary:not(:disabled).fc-button-active {
    background: rgb(59 130 246) !important;
    border-color: rgb(59 130 246) !important;
    color: white !important;
}

.dark .calendar-container .fc-button {
    background: rgb(55 65 81) !important;
    border-color: rgb(75 85 99) !important;
    color: rgb(209 213 219) !important;
}

.dark .calendar-container .fc-button:hover {
    background: rgb(75 85 99) !important;
    border-color: rgb(107 114 128) !important;
}

.calendar-container .fc-daygrid-day {
    border-color: rgb(229 231 235) !important;
}

.dark .calendar-container .fc-daygrid-day {
    border-color: rgb(75 85 99) !important;
}

.calendar-container .fc-col-header-cell {
    background: rgb(249 250 251) !important;
    font-weight: 600 !important;
    color: rgb(75 85 99) !important;
    border-color: rgb(229 231 235) !important;
}

.dark .calendar-container .fc-col-header-cell {
    background: rgb(55 65 81) !important;
    color: rgb(209 213 219) !important;
    border-color: rgb(75 85 99) !important;
}

.calendar-container .fc-day-today {
    background: rgb(239 246 255) !important;
}

.dark .calendar-container .fc-day-today {
    background: rgb(30 58 138) !important;
}

/* Mobile responsive adjustments */
@media (max-width: 640px) {
    .calendar-container .fc-toolbar {
        flex-direction: column !important;
        gap: 0.75rem !important;
    }
    
    .calendar-container .fc-toolbar-chunk {
        display: flex !important;
        justify-content: center !important;
    }
    
    .calendar-container .fc-toolbar-title {
        font-size: 1.125rem !important;
        margin: 0 !important;
    }
    
    .calendar-container .fc-button {
        padding: 0.25rem 0.5rem !important;
        font-size: 0.75rem !important;
    }
    
    .calendar-container .fc-daygrid-event {
        font-size: 11px !important;
        padding: 2px 4px !important;
        margin-bottom: 1px !important;
    }
}

@media (max-width: 768px) {
    .calendar-container .fc-toolbar-title {
        font-size: 1.125rem !important;
    }
    
    .calendar-container .fc-daygrid-event {
        font-size: 12px !important;
    }
}

/* Animation for calendar transitions */
.calendar-container .fc-view-harness {
    transition: all 0.3s ease-in-out;
}

/* More link styling */
.calendar-container .fc-more-link {
    color: rgb(59 130 246) !important;
    font-weight: 500 !important;
    font-size: 0.75rem !important;
}

.dark .calendar-container .fc-more-link {
    color: rgb(147 197 253) !important;
}
</style>