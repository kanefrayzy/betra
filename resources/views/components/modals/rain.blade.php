<!-- Rain Modal -->
@auth()
<div id="rain-modal" data-modal="rain-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="rain-modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75" aria-hidden="true"></div>

        <!-- Modal panel -->
        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-gray-800 rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-700">
                <h3 class="text-lg font-medium leading-6 text-yellow-400" id="rain-modal-title">
                    {{ __('Сделать дождь') }}
                </h3>
                <button type="button" class="text-gray-400 hover:text-white focus:outline-none" data-dismiss="modal" aria-label="Close">
                    <span class="sr-only">Close</span>
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Body -->
            <div class="px-6 py-4">
                <div class="mb-4">
                    <label for="rainAmount" class="block mb-2 text-sm font-medium text-gray-300">
                        {{ __('Сумма дождя') }} ({{ $u->currency->symbol }})
                    </label>
                    <div class="relative">
                        <input type="number" id="rainAmount" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-md focus:ring-2 focus:ring-yellow-500 focus:border-transparent text-white" placeholder="{{ __('Введите сумму') }}">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="recipientCount" class="block mb-2 text-sm font-medium text-gray-300">
                        {{ __('Количество получателей') }}
                    </label>
                    <div class="relative">
                        <input type="number" id="recipientCount" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-md focus:ring-2 focus:ring-yellow-500 focus:border-transparent text-white" placeholder="{{ __('Введите количество') }}">
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-900 border-t border-gray-700 sm:px-6">
                <button type="button" id="sendRainButton" class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-gray-900 transition-colors bg-yellow-400 border border-transparent rounded-md hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5.5 16a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.977A4.5 4.5 0 1113.5 16h-8z"></path>
                    </svg>
                    {{ __('Сделать дождь') }}
                </button>
            </div>
        </div>
    </div>
</div>
@endauth
