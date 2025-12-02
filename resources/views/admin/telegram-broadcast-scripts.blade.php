<script>
// Character counter
document.getElementById('message').addEventListener('input', function() {
    const count = this.value.length;
    document.getElementById('charCount').textContent = `${count} / 4096`;
});

// Toggle target fields
function toggleTargetFields(target) {
    const userIdsBlock = document.getElementById('userIdsBlock');
    const singleUserBlock = document.getElementById('singleUserBlock');
    
    // Hide all blocks first
    userIdsBlock.classList.add('hidden');
    singleUserBlock.classList.add('hidden');
    
    // Show relevant block
    if (target === 'specific') {
        userIdsBlock.classList.remove('hidden');
    } else if (target === 'single') {
        singleUserBlock.classList.remove('hidden');
    }
}

// Toggle button settings
function toggleButtonSettings(show) {
    const buttonSettings = document.getElementById('buttonSettings');
    if (show) {
        buttonSettings.classList.remove('hidden');
    } else {
        buttonSettings.classList.add('hidden');
    }
}

// Insert variable
function insertVariable(variable) {
    const textarea = document.getElementById('message');
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const text = textarea.value;
    
    textarea.value = text.substring(0, start) + variable + text.substring(end);
    textarea.focus();
    textarea.selectionStart = textarea.selectionEnd = start + variable.length;
    
    // Trigger input event to update character count
    textarea.dispatchEvent(new Event('input'));
}

// Search user
async function searchUser() {
    const query = document.getElementById('user_search').value;
    
    if (!query) {
        alert('Введите ID или username пользователя');
        return;
    }

    try {
        const response = await fetch(`{{ route('admin.telegram.broadcast.searchUser') }}?query=${encodeURIComponent(query)}`);
        const data = await response.json();
        
        if (data.success) {
            // Fill user info
            document.getElementById('single_user_id').value = data.user.id;
            document.getElementById('user_avatar').src = data.user.avatar;
            document.getElementById('user_username').textContent = data.user.username;
            document.getElementById('user_id_display').textContent = data.user.id;
            document.getElementById('user_telegram_id').textContent = data.user.telegram_id;
            document.getElementById('user_last_login').textContent = data.user.last_login;
            
            // Show user info
            document.getElementById('userInfo').classList.remove('hidden');
        } else {
            alert(data.message);
        }
    } catch (error) {
        console.error('Search error:', error);
        alert('Ошибка при поиске пользователя');
    }
}

// Clear user search
function clearUserSearch() {
    document.getElementById('user_search').value = '';
    document.getElementById('single_user_id').value = '';
    document.getElementById('userInfo').classList.add('hidden');
}

// Load template
async function loadTemplate(templateId) {
    if (!templateId) {
        // Clear form
        document.getElementById('message').value = '';
        document.getElementById('has_buttons').checked = false;
        toggleButtonSettings(false);
        return;
    }

    try {
        const response = await fetch(`{{ route('admin.telegram.broadcast.getTemplate', '') }}/${templateId}`);
        const data = await response.json();
        
        if (data.success) {
            const template = data.template;
            
            // Fill message
            document.getElementById('message').value = template.message;
            document.getElementById('message').dispatchEvent(new Event('input'));
            
            // Fill buttons if exists
            if (template.has_buttons && template.buttons && template.buttons.length > 0) {
                document.getElementById('has_buttons').checked = true;
                toggleButtonSettings(true);
                document.getElementById('button_text').value = template.buttons[0].text || '';
                document.getElementById('button_url').value = template.buttons[0].url || '';
            } else {
                document.getElementById('has_buttons').checked = false;
                toggleButtonSettings(false);
            }
        }
    } catch (error) {
        console.error('Load template error:', error);
        alert('Ошибка при загрузке шаблона');
    }
}

// Preview message
async function previewMessage() {
    const message = document.getElementById('message').value;
    
    if (!message) {
        alert('Введите текст сообщения');
        return;
    }

    const hasButtons = document.getElementById('has_buttons').checked;
    const buttonText = document.getElementById('button_text').value;
    const buttonUrl = document.getElementById('button_url').value;

    try {
        const response = await fetch('{{ route('admin.telegram.broadcast.preview') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ message })
        });

        const data = await response.json();
        
        if (data.success) {
            document.getElementById('previewContent').innerHTML = data.preview;
            
            // Show button preview if enabled
            const previewButtons = document.getElementById('previewButtons');
            if (hasButtons && buttonText && buttonUrl) {
                previewButtons.innerHTML = `
                    <div class="inline-block">
                        <button class="px-4 py-2 bg-blue-500 text-white rounded-lg text-sm font-medium">
                            ${buttonText}
                        </button>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">🔗 ${buttonUrl}</p>
                    </div>
                `;
                previewButtons.classList.remove('hidden');
            } else {
                previewButtons.classList.add('hidden');
            }
            
            document.getElementById('previewModal').classList.remove('hidden');
        }
    } catch (error) {
        console.error('Preview error:', error);
        alert('Ошибка при создании предпросмотра');
    }
}

function closePreview() {
    document.getElementById('previewModal').classList.add('hidden');
}

// Submit form
document.getElementById('broadcastForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const message = document.getElementById('message').value;
    const target = document.querySelector('input[name="target"]:checked').value;
    const userIds = document.getElementById('user_ids').value;
    const singleUserId = document.getElementById('single_user_id').value;
    const templateId = document.getElementById('template_select') ? document.getElementById('template_select').value : null;
    const hasButtons = document.getElementById('has_buttons').checked;
    const buttonText = document.getElementById('button_text').value;
    const buttonUrl = document.getElementById('button_url').value;
    
    if (!message) {
        alert('Введите текст сообщения');
        return;
    }

    if (target === 'specific' && !userIds) {
        alert('Введите ID пользователей');
        return;
    }

    if (target === 'single' && !singleUserId) {
        alert('Выберите пользователя для тестовой рассылки');
        return;
    }

    if (hasButtons && (!buttonText || !buttonUrl)) {
        alert('Заполните текст и URL кнопки');
        return;
    }

    // Confirm before sending
    const targetText = {
        'all': 'всем пользователям ({{ $totalUsers }} чел.)',
        'active': 'активным пользователям ({{ $activeUsers }} чел.)',
        'inactive': 'неактивным пользователям ({{ $inactiveUsers }} чел.)',
        'specific': 'выбранным пользователям',
        'single': 'одному пользователю (тест)'
    };

    if (!confirm(`Вы уверены, что хотите отправить рассылку ${targetText[target]}?`)) {
        return;
    }

    // Show loading
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

    try {
        const response = await fetch('{{ route('admin.telegram.broadcast.send') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ 
                message, 
                target, 
                user_ids: userIds,
                single_user_id: singleUserId,
                template_id: templateId,
                has_buttons: hasButtons,
                button_text: buttonText,
                button_url: buttonUrl
            })
        });

        const data = await response.json();
        
        if (data.success) {
            // Show success modal
            const stats = data.stats;
            document.getElementById('successContent').innerHTML = `
                <div class="space-y-2">
                    <div class="flex items-center justify-between p-3 bg-gray-100 dark:bg-gray-700 rounded-lg">
                        <span class="text-sm font-medium">Всего отправлено:</span>
                        <span class="font-bold text-lg">${stats.total}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                        <span class="text-sm font-medium text-green-700 dark:text-green-300">Успешно:</span>
                        <span class="font-bold text-lg text-green-700 dark:text-green-300">${stats.success}</span>
                    </div>
                    ${stats.failed > 0 ? `
                        <div class="flex items-center justify-between p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                            <span class="text-sm font-medium text-red-700 dark:text-red-300">Ошибок:</span>
                            <span class="font-bold text-lg text-red-700 dark:text-red-300">${stats.failed}</span>
                        </div>
                    ` : ''}
                </div>
            `;
            document.getElementById('successModal').classList.remove('hidden');
            
            // Clear form
            this.reset();
            document.getElementById('charCount').textContent = '0 / 4096';
            document.getElementById('userIdsBlock').classList.add('hidden');
            document.getElementById('singleUserBlock').classList.add('hidden');
            clearUserSearch();
            toggleButtonSettings(false);
            
            // Reload page after closing modal to update history
            setTimeout(() => {
                location.reload();
            }, 3000);
        } else {
            alert(data.message || 'Ошибка при отправке рассылки');
        }
    } catch (error) {
        console.error('Send error:', error);
        alert('Ошибка при отправке рассылки');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
});

function closeSuccess() {
    document.getElementById('successModal').classList.add('hidden');
    location.reload();
}

// Template Manager
function openTemplateManager() {
    document.getElementById('templateModal').classList.remove('hidden');
}

function closeTemplateManager() {
    document.getElementById('templateModal').classList.add('hidden');
    hideNewTemplateForm();
}

function showNewTemplateForm() {
    document.getElementById('newTemplateForm').classList.remove('hidden');
}

function hideNewTemplateForm() {
    document.getElementById('newTemplateForm').classList.add('hidden');
    document.getElementById('templateForm').reset();
}

function toggleTemplateButtonFields(show) {
    const fields = document.getElementById('templateButtonFields');
    if (show) {
        fields.classList.remove('hidden');
    } else {
        fields.classList.add('hidden');
    }
}

// Save template
document.getElementById('templateForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = {
        name: document.getElementById('template_name').value,
        message: document.getElementById('template_message').value,
        category: document.getElementById('template_category').value,
        has_buttons: document.getElementById('template_has_buttons').checked,
        button_text: document.getElementById('template_button_text').value,
        button_url: document.getElementById('template_button_url').value
    };

    try {
        const response = await fetch('{{ route('admin.telegram.broadcast.saveTemplate') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();
        
        if (data.success) {
            alert('Шаблон успешно создан!');
            location.reload();
        } else {
            alert(data.message || 'Ошибка при создании шаблона');
        }
    } catch (error) {
        console.error('Save template error:', error);
        alert('Ошибка при создании шаблона');
    }
});

// Delete template
async function deleteTemplate(templateId) {
    if (!confirm('Вы уверены, что хотите удалить этот шаблон?')) {
        return;
    }

    try {
        const response = await fetch(`{{ route('admin.telegram.broadcast.deleteTemplate', '') }}/${templateId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const data = await response.json();
        
        if (data.success) {
            alert('Шаблон успешно удален!');
            location.reload();
        } else {
            alert(data.message || 'Ошибка при удалении шаблона');
        }
    } catch (error) {
        console.error('Delete template error:', error);
        alert('Ошибка при удалении шаблона');
    }
}

// Refresh stats
function refreshStats() {
    location.reload();
}

// Close modals on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePreview();
        closeSuccess();
        closeTemplateManager();
    }
});
</script>
