<x-layouts.app>
    <div x-data="{ type: 'main' }" class="min-h-screen text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-2xl sm:text-3xl font-bold text-white mb-2">{{__('Аккаунт')}}</h1>
                <p class="text-gray-400 text-sm">{{__('Управление настройками профиля')}}</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Sidebar Navigation -->
                <div class="lg:col-span-1">
                    <div class="bg-[#242932] rounded-xl p-2 space-y-1">
                        <button
                            @click="type = 'main'"
                            :class="type === 'main' ? 'bg-[#2a3142] text-white' : 'text-gray-400 hover:bg-[#2a3142]/50 hover:text-white'"
                            class="w-full text-left px-4 py-3 rounded-lg transition-all flex items-center gap-3 font-medium">
                            <i class="fa fa-user text-lg w-5"></i>
                            <span>{{__('Общие')}}</span>
                        </button>
                        <button
                            @click="type = 'second'"
                            :class="type === 'second' ? 'bg-[#2a3142] text-white' : 'text-gray-400 hover:bg-[#2a3142]/50 hover:text-white'"
                            class="w-full text-left px-4 py-3 rounded-lg transition-all flex items-center gap-3 font-medium">
                            <i class="fa fa-lock text-lg w-5"></i>
                            <span>{{__('Безопасность')}}</span>
                        </button>
                        <button
                            @click="type = 'verification'"
                            :class="type === 'verification' ? 'bg-[#2a3142] text-white' : 'text-gray-400 hover:bg-[#2a3142]/50 hover:text-white'"
                            class="w-full text-left px-4 py-3 rounded-lg transition-all flex items-center gap-3 font-medium">
                            <i class="fa fa-check-circle text-lg w-5"></i>
                            <span>{{__('Верификация')}}</span>
                        </button>
                    </div>
                </div>

                <!-- Content Area -->
                <div class="lg:col-span-3">
                    <!-- Main Tab -->
                    <div x-cloak x-show="type === 'main'" x-transition.opacity class="space-y-6">
                        <!-- Avatar -->
                        <div class="bg-[#242932] rounded-xl p-6" x-data="avatarForm()">
                            <h3 class="text-lg font-semibold mb-6">{{__('Фото профиля')}}</h3>
                            <form method="POST" action="{{route('account.avatar')}}" enctype="multipart/form-data">
                                @csrf
                                <div class="flex flex-col sm:flex-row items-center gap-6">
                                    <div class="relative group">
                                        <div class="w-24 h-24 sm:w-32 sm:h-32 rounded-full bg-cover bg-center ring-4 ring-[#2a3142]" :style="background"></div>
                                        <div class="absolute inset-0 bg-black/60 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer" @click="change()">
                                            <i class="fa fa-camera text-white text-xl"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1 text-center sm:text-left">
                                        <input type="file" @change="preview($event)" name="avatar" x-model="avatar" id="avatar-input" accept="image/*" class="hidden" required>
                                        <div x-show="!avatar" x-cloak>
                                            <button type="button" @click="change()" class="px-6 py-2.5 bg-[#fbbf24] hover:bg-[#f59e0b] text-black rounded-lg font-semibold transition-colors">
                                                {{__('Загрузить фото')}}
                                            </button>
                                            <p class="text-gray-500 text-xs mt-2">{{ __('JPG, PNG до 5MB') }}</p>
                                        </div>
                                        <div x-show="avatar" x-cloak class="flex flex-wrap gap-3 justify-center sm:justify-start">
                                            <button type="submit" class="px-6 py-2.5 bg-[#10b981] hover:bg-[#059669] text-white rounded-lg font-semibold transition-colors">
                                                {{__('Сохранить')}}
                                            </button>
                                            <button type="button" class="px-6 py-2.5 bg-[#2a3142] hover:bg-[#3a4152] text-white rounded-lg font-semibold transition-colors" @click="cancel()">
                                                {{__('Отмена')}}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Personal Data -->
                        <div class="bg-[#242932] rounded-xl p-6">
                            <h3 class="text-lg font-semibold mb-6">{{__('Личные данные')}}</h3>
                            <div class="space-y-5">
                                <div>
                                    <label class="block text-gray-400 text-sm font-medium mb-2">{{__('Логин')}}</label>
                                    <input type="text" readonly class="w-full bg-[#0f1419] border border-[#2a3142] rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-[#fbbf24]/50" value="{{auth()->user()->username}}">
                                </div>

                                <div x-data="{ isEditing: false }">
                                    <label class="block text-gray-400 text-sm font-medium mb-2">E-mail</label>
                                    <div x-show="!isEditing">
                                        <div class="flex flex-col sm:flex-row gap-3">
                                            <input type="email" readonly class="flex-1 bg-[#0f1419] border border-[#2a3142] rounded-lg px-4 py-3 text-white focus:outline-none" value="{{ auth()->user()->email }}">
                                            @if(!auth()->user()->email_changed)
                                                <button @click="isEditing = true" class="px-6 py-3 bg-[#2a3142] hover:bg-[#3a4152] text-white rounded-lg font-semibold transition-colors whitespace-nowrap">
                                                    {{__('Изменить')}}
                                                </button>
                                            @endif
                                        </div>
                                        @if(auth()->user()->email_changed)
                                            <p class="text-gray-500 text-xs mt-2">{{__('Email уже был изменен и больше не может быть изменен.')}}</p>
                                        @endif
                                    </div>
                                    @if(!auth()->user()->email_changed)
                                        <form x-show="isEditing" action="{{ route('account.update-email') }}" method="POST" class="space-y-3" x-cloak>
                                            @csrf
                                            <input type="email" name="email" class="w-full bg-[#0f1419] border border-[#2a3142] rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-[#fbbf24]/50" value="{{ auth()->user()->email }}" required>
                                            @error('email')
                                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                            @enderror
                                            <div class="flex flex-wrap gap-3">
                                                <button type="submit" class="px-6 py-2.5 bg-[#10b981] hover:bg-[#059669] text-white rounded-lg font-semibold transition-colors">
                                                    {{__('Сохранить')}}
                                                </button>
                                                <button type="button" @click="isEditing = false" class="px-6 py-2.5 bg-[#2a3142] hover:bg-[#3a4152] text-white rounded-lg font-semibold transition-colors">
                                                    {{__('Отмена')}}
                                                </button>
                                            </div>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Telegram -->
                        <div class="bg-[#242932] rounded-xl p-6">
                            <h3 class="text-lg font-semibold mb-6">{{__('Telegram')}}</h3>
                            <div class="flex justify-center sm:justify-start">
                                @if(auth()->user()->telegram_id)
                                    <div class="inline-flex items-center gap-3 px-5 py-3 bg-[#10b981]/10 border border-[#10b981]/30 rounded-lg text-[#10b981]">
                                        <i class="fa fa-check-circle text-xl"></i>
                                        <span class="font-semibold">{{__('Привязан')}}</span>
                                    </div>
                                @else
                                    <a href="{{ route('telegram.connect') }}" target="_blank" class="inline-flex items-center gap-3 px-6 py-3 bg-[#0088cc] hover:bg-[#0077b3] text-white rounded-lg font-semibold transition-colors">
                                        <i class="fa fa-telegram text-xl"></i>
                                        {{__('Привязать Telegram')}}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Security Tab -->
                    <div x-cloak x-show="type === 'second'" x-transition.opacity>
                        <div class="bg-[#242932] rounded-xl p-6">
                            <h3 class="text-lg font-semibold mb-6">{{__('Изменить пароль')}}</h3>
                            <form method="POST" action="{{route('account.password')}}" class="space-y-5 max-w-xl">
                                @csrf
                                <div>
                                    <label class="block text-gray-400 text-sm font-medium mb-2">{{__('Текущий пароль')}}</label>
                                    <input required type="password" name="curr_password" autocomplete="current-password" class="w-full bg-[#0f1419] border border-[#2a3142] rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-[#fbbf24]/50" placeholder="••••••••">
                                </div>
                                <div>
                                    <label class="block text-gray-400 text-sm font-medium mb-2">{{__('Новый пароль')}}</label>
                                    <input required type="password" name="password" autocomplete="new-password" class="w-full bg-[#0f1419] border border-[#2a3142] rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-[#fbbf24]/50" placeholder="••••••••">
                                </div>
                                <div>
                                    <label class="block text-gray-400 text-sm font-medium mb-2">{{__('Повторите пароль')}}</label>
                                    <input required type="password" name="password_confirmation" autocomplete="new-password" class="w-full bg-[#0f1419] border border-[#2a3142] rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-[#fbbf24]/50" placeholder="••••••••">
                                </div>
                                <div class="pt-2">
                                    <button type="submit" class="px-8 py-3 bg-[#fbbf24] hover:bg-[#f59e0b] text-black rounded-lg font-bold transition-colors">
                                        {{__('Сохранить')}}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Verification Tab -->
                    <div x-cloak x-show="type === 'verification'" x-transition.opacity>
                        <div class="bg-[#242932] rounded-xl p-6">
                            <h3 class="text-lg font-semibold mb-6">{{__('Верификация профиля')}}</h3>

                            @if($verification && $verification->status === 'pending')
                                <div class="bg-[#fbbf24]/10 border border-[#fbbf24]/30 rounded-xl p-5 flex items-start gap-4 text-[#fbbf24]">
                                    <i class="fa fa-clock-o text-2xl mt-1"></i>
                                    <div>
                                        <div class="font-semibold text-base mb-1">{{__('Заявка на рассмотрении')}}</div>
                                        <div class="text-gray-400 text-sm">{{__('Мы проверим ваши данные в ближайшее время')}}</div>
                                    </div>
                                </div>
                            @elseif($verification && $verification->status === 'rejected')
                                <div class="bg-[#ef4444]/10 border border-[#ef4444]/30 rounded-xl p-5 mb-6">
                                    <div class="flex items-start gap-4 text-[#ef4444] mb-3">
                                        <i class="fa fa-times-circle text-2xl mt-1"></i>
                                        <div>
                                            <div class="font-semibold text-base mb-1">{{__('Заявка отклонена')}}</div>
                                            <div class="text-gray-400 text-sm">{{__('Причина')}}: {{ $verification->reject_reason }}</div>
                                        </div>
                                    </div>
                                </div>
                                @include('components.layouts.partials.verification-form')
                            @elseif($verification && $verification->status === 'approved')
                                <div class="bg-[#10b981]/10 border border-[#10b981]/30 rounded-xl p-5 flex items-start gap-4 text-[#10b981]">
                                    <i class="fa fa-check-circle text-2xl mt-1"></i>
                                    <div>
                                        <div class="font-semibold text-base mb-1">{{__('Верификация пройдена')}}</div>
                                        <div class="text-gray-400 text-sm">{{__('Ваш аккаунт успешно верифицирован')}}</div>
                                    </div>
                                </div>
                            @else
                                @include('components.layouts.partials.verification-form')
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let defaultBg = "background: url({{ \App\Models\User::avatarUrl(auth()->id() ? auth()->user()->avatar : '') }}) no-repeat center center / cover";

        function avatarForm() {
            return {
                background: defaultBg,
                avatar: '',
                change() {
                    document.getElementById('avatar-input').click();
                },
                cancel() {
                    this.avatar = '';
                    this.background = defaultBg;
                    document.getElementById('avatar-input').value = '';
                },
                preview(e) {
                    if (this.avatar && e.target && e.target.files[0]) {
                        let img = URL.createObjectURL(e.target.files[0]);
                        this.background = `background: url(${img}) no-repeat center center / cover`;
                    }
                }
            }
        }
    </script>
</x-layouts.app>
