<form method="POST" action="{{ route('verification.store') }}" enctype="multipart/form-data" class="space-y-5 max-w-2xl">
    @csrf
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <label class="block text-gray-400 text-sm font-medium mb-2">{{__('Имя')}} <span class="text-red-500">*</span></label>
            <input type="text" name="first_name" class="w-full bg-[#0f1419] border border-[#2a3142] rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-[#fbbf24]/50" required>
        </div>
        <div>
            <label class="block text-gray-400 text-sm font-medium mb-2">{{__('Фамилия')}} <span class="text-red-500">*</span></label>
            <input type="text" name="last_name" class="w-full bg-[#0f1419] border border-[#2a3142] rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-[#fbbf24]/50" required>
        </div>
    </div>
    <div>
        <label class="block text-gray-400 text-sm font-medium mb-2">{{__('Дата рождения')}} <span class="text-red-500">*</span></label>
        <input type="date" name="birth_date" class="w-full bg-[#0f1419] border border-[#2a3142] rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-[#fbbf24]/50" required>
    </div>
    <div>
        <label class="block text-gray-400 text-sm font-medium mb-2">{{__('Тип документа')}} <span class="text-red-500">*</span></label>
        <select name="document_type" class="w-full bg-[#0f1419] border border-[#2a3142] rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-[#fbbf24]/50" required>
            <option value="passport">{{__('Паспорт')}}</option>
            <option value="id_card">{{__('ID карта')}}</option>
        </select>
    </div>
    <div class="bg-[#3b82f6]/10 border border-[#3b82f6]/30 rounded-xl p-5">
        <div class="flex gap-4">
            <i class="fas fa-info-circle text-[#3b82f6] text-xl mt-0.5 flex-shrink-0"></i>
            <div class="text-sm">
                <div class="text-[#3b82f6] font-semibold mb-2">{{__('Требования к фото')}}</div>
                <ul class="text-gray-400 space-y-1.5">
                    <li>• {{__('Селфи с паспортом в руках')}}</li>
                    <li>• {{__('Все данные документа должны быть четко видны')}}</li>
                    <li>• {{__('Ваше лицо и паспорт полностью в кадре')}}</li>
                    <li>• {{__('Форматы: JPG, PNG, PDF (до 10MB)')}}</li>
                </ul>
            </div>
        </div>
    </div>
    <div x-data="{ fileName: '' }">
        <label class="block text-gray-400 text-sm font-medium mb-2">{{__('Селфи с документом')}} <span class="text-red-500">*</span></label>
        <input type="file" id="document" name="selfie" accept=".png,.jpg,.jpeg,.pdf" required @change="fileName = $event.target.files[0]?.name || ''" class="hidden">
        <label for="document" class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-[#2a3142] rounded-xl cursor-pointer hover:border-[#fbbf24] transition-colors bg-[#0f1419] group">
            <div class="flex flex-col items-center justify-center py-6 px-4">
                <i class="fa fa-cloud-upload text-5xl text-gray-600 group-hover:text-[#fbbf24] transition-colors mb-3"></i>
                <span x-text="fileName || '{{__('Нажмите для загрузки файла')}}'" class="text-gray-400 text-sm text-center font-medium" :class="fileName ? 'text-white' : ''"></span>
                <span class="text-gray-600 text-xs mt-1">JPG, PNG, PDF</span>
            </div>
        </label>
    </div>
    <div class="pt-3">
        <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-[#fbbf24] hover:bg-[#f59e0b] text-black rounded-lg font-bold transition-colors">
            {{__('Отправить на проверку')}}
        </button>
    </div>
</form>
