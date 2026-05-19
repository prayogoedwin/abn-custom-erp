<aside :class="{ 'w-full md:w-64': sidebarOpen, 'w-0 md:w-16 hidden md:block': !sidebarOpen }"
    class="bg-sidebar text-sidebar-foreground border-r border-gray-200 dark:border-gray-700 sidebar-transition overflow-hidden">
    <!-- Sidebar Content -->
    <div class="h-full flex flex-col">
        <!-- Sidebar -->
        <nav class="flex-1 overflow-y-auto custom-scrollbar py-4">
            <ul class="space-y-1 px-2">
                
                @foreach(config('sidebar') as $menu)
                    @hasMenuPermission($menu['permission'])
                        
                        {{-- LEVEL 1: Menu Tunggal (Tanpa Anak) --}}
                        @if(!isset($menu['children']))
                            <x-layouts.sidebar-link href="{{ route($menu['route']) }}" :icon="$menu['icon']" :active="request()->routeIs($menu['active'])">
                                {{ $menu['title'] }}
                            </x-layouts.sidebar-link>
                        
                        {{-- LEVEL 2: Menu dengan Sub-Menu --}}
                        @else
                            <x-layouts.sidebar-two-level-link-parent :title="$menu['title']" :icon="$menu['icon']" :active="request()->routeIs($menu['active'])">
                                
                                @foreach($menu['children'] as $child)
                                    @hasMenuPermission($child['permission'])
                                        
                                        {{-- LEVEL 3: Jika Sub-Menu punya anak lagi --}}
                                        @if(isset($child['children']))
                                            <x-layouts.sidebar-three-level-parent :title="$child['title']" :icon="$child['icon']" :active="request()->routeIs($child['active'])">
                                                @foreach($child['children'] as $grandChild)
                                                    @hasMenuPermission($grandChild['permission'])
                                                        <x-layouts.sidebar-three-level-link href="{{ route($grandChild['route']) }}" :active="request()->routeIs($grandChild['active'])">
                                                            {{ $grandChild['title'] }}
                                                        </x-layouts.sidebar-three-level-link>
                                                    @endhasMenuPermission
                                                @endforeach
                                            </x-layouts.sidebar-three-level-parent>
                                        
                                        {{-- Normal Level 2 Link --}}
                                        @else
                                            <x-layouts.sidebar-two-level-link href="{{ route($child['route']) }}" :icon="$child['icon']" :active="request()->routeIs($child['active'])">
                                                {{ $child['title'] }}
                                            </x-layouts.sidebar-two-level-link>
                                        @endif

                                    @endhasMenuPermission
                                @endforeach

                            </x-layouts.sidebar-two-level-link-parent>
                        @endif

                    @endhasMenuPermission
                @endforeach

            </ul>
        </nav>
    </div>
</aside>