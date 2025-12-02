@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="py-4 border-t border-slate-700">
        {{-- Mobile Pagination - полностью переделан --}}
        <div class="flex flex-col space-y-4 sm:hidden">
            {{-- Информация о результатах на мобильном --}}
            <div class="text-center">
                <p class="text-sm text-slate-400">
                    {!! __('Страница') !!}
                    <span class="font-medium text-orange-400">{{ $paginator->currentPage() }}</span>
                    {!! __('из') !!}
                    <span class="font-medium text-slate-300">{{ $paginator->lastPage() }}</span>
                </p>
                <p class="text-xs text-slate-500 mt-1">
                    {{ $paginator->total() }} {{ __('всего результатов') }}
                </p>
            </div>

            {{-- Кнопки навигации --}}
            <div class="flex justify-center space-x-2">
                @if ($paginator->onFirstPage())
                    <span class="inline-flex items-center justify-center w-12 h-10 text-slate-500 bg-slate-800 border border-slate-600 cursor-default rounded-lg">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" class="inline-flex items-center justify-center w-12 h-10 text-slate-300 bg-slate-800 border border-slate-600 rounded-lg hover:text-white hover:bg-slate-700 transition-colors duration-150">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                @endif

                {{-- Текущая страница --}}
                <span class="inline-flex items-center justify-center min-w-12 h-10 px-3 text-sm font-medium text-white bg-orange-500 border border-orange-500 rounded-lg">
                    {{ $paginator->currentPage() }}
                </span>

                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" class="inline-flex items-center justify-center w-12 h-10 text-slate-300 bg-slate-800 border border-slate-600 rounded-lg hover:text-white hover:bg-slate-700 transition-colors duration-150">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                @else
                    <span class="inline-flex items-center justify-center w-12 h-10 text-slate-500 bg-slate-800 border border-slate-600 cursor-default rounded-lg">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                @endif
            </div>

            {{-- Быстрый переход к первой/последней странице --}}
            @if ($paginator->lastPage() > 3)
                <div class="flex justify-center space-x-2">
                    @if ($paginator->currentPage() > 2)
                        <a href="{{ $paginator->url(1) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-slate-400 bg-slate-800 border border-slate-600 rounded hover:text-white hover:bg-slate-700 transition-colors duration-150">
                            1
                        </a>
                    @endif

                    @if ($paginator->currentPage() < $paginator->lastPage() - 1)
                        <a href="{{ $paginator->url($paginator->lastPage()) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-slate-400 bg-slate-800 border border-slate-600 rounded hover:text-white hover:bg-slate-700 transition-colors duration-150">
                            {{ $paginator->lastPage() }}
                        </a>
                    @endif
                </div>
            @endif
        </div>

        {{-- Desktop Pagination - без изменений --}}
        <div class="hidden sm:flex sm:items-center sm:justify-between">
            {{-- Results Info --}}
            <div>
                <p class="text-sm text-slate-400 leading-5">
                    {!! __('Показано') !!}
                    @if ($paginator->firstItem())
                        <span class="font-medium text-slate-300">{{ $paginator->firstItem() }}</span>
                        {!! __('по') !!}
                        <span class="font-medium text-slate-300">{{ $paginator->lastItem() }}</span>
                    @else
                        <span class="font-medium text-slate-300">{{ $paginator->count() }}</span>
                    @endif
                    {!! __('из') !!}
                    <span class="font-medium text-orange-400">{{ $paginator->total() }}</span>
                    {!! __('результатов') !!}
                </p>
            </div>

            {{-- Pagination Links --}}
            <div>
                <span class="relative z-0 inline-flex shadow-sm rounded-md rtl:flex-row-reverse">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                            <span class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-slate-500 bg-slate-800 border border-slate-600 cursor-default rounded-l-md leading-5" aria-hidden="true">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-slate-400 bg-slate-800 border border-slate-600 rounded-l-md leading-5 hover:text-white hover:bg-slate-700 focus:z-10 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500 transition-colors duration-150" aria-label="{{ __('pagination.previous') }}">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-slate-400 bg-slate-800 border border-slate-600 cursor-default leading-5">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-white bg-orange-500 border border-orange-500 cursor-default leading-5">{{ $page }}</span>
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-slate-400 bg-slate-800 border border-slate-600 leading-5 hover:text-white hover:bg-slate-700 focus:z-10 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500 transition-colors duration-150" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-slate-400 bg-slate-800 border border-slate-600 rounded-r-md leading-5 hover:text-white hover:bg-slate-700 focus:z-10 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500 transition-colors duration-150" aria-label="{{ __('pagination.next') }}">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                            <span class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-slate-500 bg-slate-800 border border-slate-600 cursor-default rounded-r-md leading-5" aria-hidden="true">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
