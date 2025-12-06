<div>
  <div class="bg-dark-800/60 backdrop-blur-sm rounded-xl p-4 sm:p-6 w-full border border-dark-700/50 mb-8" wire:poll.3s="refreshTransactions">
      <div class="flex items-center justify-between mb-6">
          <div class="flex items-center space-x-3">
              <h2 class="text-lg sm:text-xl font-bold text-white">{{ __('Игровые транзакции') }}</h2>
              <div class="flex items-center space-x-2">
                  <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                  <span class="text-green-400 text-sm font-medium">LIVE</span>
              </div>
          </div>
      </div>

      {{-- Transactions table --}}
      <div class="transactions bg-dark-900/60 rounded-xl overflow-hidden border border-dark-700/50">
          <div class="overflow-x-auto">
              <table class="trans-table w-full" id="transactions-table">
                  <thead>
                      <tr class="bg-dark-800/50 border-b border-dark-700/50">
                          <th class="px-3 sm:px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase">
                              {{ __('Игра') }}
                          </th>
                          <th class="px-3 sm:px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercast hidden md:table-cell">
                              {{ __('Игрок') }}
                          </th>
                          <th class="px-3 sm:px-6 py-3 text-right text-xs font-semibold text-gray-400 uppercaset hidden md:table-cell">
                              {{ __('Ставка') }}
                          </th>
                          <th class="px-3 sm:px-6 py-3 text-right text-xs font-semibold text-gray-400 uppercase">
                              {{ __('Выигрыш') }}
                          </th>
                          <th class="px-3 sm:px-6 py-3 text-right text-xs font-semibold text-gray-400 uppercase hidden md:table-cell">
                              {{ __('Коэфф.') }}
                          </th>
                      </tr>
                  </thead>
                  <tbody class="divide-y divide-dark-700/30">
                      @forelse($transactions as $index => $transaction)
                          <tr class="group hover:bg-dark-800/40 transition-all duration-200 {{ isset($transaction['game_slug']) ? 'cursor-pointer' : '' }}" 
                              style="animation: fadeIn 0.3s ease-in {{ $index * 0.05 }}s;"
                              @if(isset($transaction['game_slug']))
                                  @auth
                                      onclick="window.location.href='{{ route('slots.play', $transaction['game_slug']) }}'"
                                  @else
                                      onclick="if (typeof openLoginModal === 'function') { openLoginModal(); }"
                                  @endauth
                              @endif>
                              {{-- Game name --}}
                              <td class="px-3 sm:px-6 py-3">
                                  <div class="flex items-center space-x-2 sm:space-x-3">
                                      <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg bg-dark-700/50 flex items-center justify-center flex-shrink-0 {{ isset($transaction['game_slug']) ? 'group-hover:bg-dark-600/50' : '' }}">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-[#ffb300]" fill="currentColor" viewBox="0 0 96 96">
                                            <path fill-rule="evenodd" d="M56.8 47.08a49.76 49.76 0 0 0-5.6 22.8v5H32.32a55.6 55.6 0 0 1 5-22.76A87 87 0 0 1 50.8 31h-28V16.36H72v7.76a134 134 0 0 0-15.2 22.96m26.4 16.24a30.56 30.56 0 0 0-6 13.04l-.6 3L60 76.32a38.12 38.12 0 0 1 13.36-22.28l-12-2.36 5.04-10.64L96 46.88l-.92 4.64a85.5 85.5 0 0 0-11.88 11.8m-58.52 9.32a30.1 30.1 0 0 1 0-14.36 79.7 79.7 0 0 1 5.8-15.84l-1.12-4.6L0 44.88v11.68l12-2.84a37.88 37.88 0 0 0-2.88 25.92l16.28-4z" clip-rule="evenodd"/>
                                        </svg>
                                      </div>
                                      <div class="min-w-0">
                                          <p class="text-white font-semibold text-xs sm:text-sm truncate {{ isset($transaction['game_slug']) ? 'group-hover:text-[#ffb300]' : '' }}" title="{{ $transaction['game_name'] }}">
                                              {{ strlen($transaction['game_name']) > 20 ? substr($transaction['game_name'], 0, 20) . '...' : $transaction['game_name'] }}
                                          </p>

                                      </div>
                                  </div>
                              </td>

                              {{-- Player --}}
                              <td class="px-3 sm:px-6 py-3 hidden md:table-cell">
                                  <div class="flex items-center space-x-2">
                                      <div class="relative flex-shrink-0">
                                          <img
                                              src="{{ $transaction['user']['avatar'] }}"
                                              alt="{{ $transaction['user']['username'] }}"
                                              class="w-8 h-8 rounded-full border border-dark-600"
                                              loading="lazy"
                                              onerror="this.src='/assets/images/avatar-placeholder.png'"
                                          >
                                          <div class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-green-400 border-2 border-dark-800 rounded-full"></div>
                                      </div>
                                      <div class="min-w-0">
                                          <p class="text-white font-medium text-xs sm:text-sm truncate" title="{{ $transaction['user']['username'] }}">
                                              {{ strlen($transaction['user']['username']) > 12 ? substr($transaction['user']['username'], 0, 12) . '...' : $transaction['user']['username'] }}
                                          </p>

                                      </div>
                                  </div>
                              </td>

                              {{-- Bet amount --}}
                              <td class="px-3 sm:px-6 py-3 text-right hidden md:table-cell">
                                  <div class="flex items-center justify-end space-x-1">
                                      <span class="text-[#ffb300] font-mono font-bold text-xs sm:text-sm">
                                          {{ $transaction['bet_amount'] }}
                                      </span>
                                      <span class="text-gray-400 text-xs">
                                          {{ $transaction['currency']['symbol'] }}
                                      </span>
                                  </div>
                              </td>

                              {{-- Win amount --}}
                              <td class="px-3 sm:px-6 py-3 text-right">
                                  <div class="flex items-center justify-end space-x-1">
                                      @if($transaction['win_amount'] > 0)
                                          <span class="text-green-400 font-mono font-bold text-xs sm:text-sm">
                                              {{ $transaction['win_amount'] }}
                                          </span>
                                      @else
                                          <span class="font-mono font-bold text-xs sm:text-sm">
                                              {{ $transaction['win_amount'] }}
                                          </span>
                                      @endif
                                      <span class="text-gray-400 text-xs">
                                          {{ $transaction['currency']['symbol'] }}
                                      </span>
                                  </div>
                              </td>

                              {{-- Coefficient --}}
                              <td class="px-3 sm:px-6 py-3 text-right hidden md:table-cell">
                                  @if($transaction['coefficient'] > 1)
                                      <span class="inline-flex items-center px-2 sm:px-3 py-1 rounded-md text-xs font-bold bg-green-500/20 text-green-400 border border-green-500/30">
                                          {{ number_format($transaction['coefficient'], 2) }}x
                                      </span>
                                  @elseif($transaction['coefficient'] == 1)
                                      <span class="inline-flex items-center px-2 sm:px-3 py-1 rounded-md text-xs font-bold bg-yellow-500/20 text-yellow-400 border border-yellow-500/30">
                                          {{ number_format($transaction['coefficient'], 2) }}x
                                      </span>
                                  @else
                                      <span class="inline-flex items-center px-2 sm:px-3 py-1 rounded-md text-xs font-bold bg-red-500/20 text-red-400 border border-red-500/30">
                                          {{ number_format($transaction['coefficient'], 2) }}x
                                      </span>
                                  @endif
                              </td>
                          </tr>
                      @empty
                          <tr>
                              <td colspan="5" class="px-6 py-12 text-center">
                                  <div class="flex flex-col items-center space-y-3">
                                      <div class="w-16 h-16 bg-dark-700/50 rounded-full flex items-center justify-center">
                                          <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                          </svg>
                                      </div>
                                      <div class="text-center">
                                          <p class="text-gray-300 font-semibold">{{ __('Нет транзакций') }}</p>
                                          <p class="text-gray-500 text-sm mt-1">{{ __('Транзакции появятся здесь') }}</p>
                                      </div>
                                  </div>
                              </td>
                          </tr>
                      @endforelse
                  </tbody>
              </table>
          </div>
      </div>
  </div>

  <style>
      @keyframes fadeIn {
          from {
              opacity: 0;
              transform: translateY(10px);
          }
          to {
              opacity: 1;
              transform: translateY(0);
          }
      }
  </style>
</div>
