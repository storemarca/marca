@extends('layouts.admin')

@section('title', safe_trans('admin.language_settings'))
@section('page-title', safe_trans('admin.language_settings'))

@section('content')
    <div class="row">
        <div class="col-lg-3 mb-4">
            @include('admin.settings.partials.sidebar')
        </div>
        
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ safe_trans('admin.language_settings') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.language.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- إعدادات اللغة -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">{{ safe_trans('admin.language_configuration') }}</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="default_language" class="form-label">{{ safe_trans('admin.default_language') }}</label>
                                    <select class="form-select @error('default_language') is-invalid @enderror" id="default_language" name="default_language">
                                        <option value="ar" {{ old('default_language', $settings['default_language'] ?? 'ar') == 'ar' ? 'selected' : '' }}>{{ safe_trans('admin.arabic') }}</option>
                                        <option value="en" {{ old('default_language', $settings['default_language'] ?? 'ar') == 'en' ? 'selected' : '' }}>{{ safe_trans('admin.english') }}</option>
                                    </select>
                                    @error('default_language')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="show_language_switcher" class="form-label">{{ safe_trans('admin.show_language_switcher') }}</label>
                                    <select class="form-select @error('show_language_switcher') is-invalid @enderror" id="show_language_switcher" name="show_language_switcher">
                                        <option value="1" {{ old('show_language_switcher', $settings['show_language_switcher'] ?? '1') == '1' ? 'selected' : '' }}>{{ safe_trans('admin.yes') }}</option>
                                        <option value="0" {{ old('show_language_switcher', $settings['show_language_switcher'] ?? '1') == '0' ? 'selected' : '' }}>{{ safe_trans('admin.no') }}</option>
                                    </select>
                                    @error('show_language_switcher')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <!-- مدير الترجمات -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">{{ safe_trans('admin.translation_manager') }}</h6>
                            <div class="alert alert-info">
                                {{ safe_trans('admin.translation_manager_info') }}
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="translation_file" class="form-label">{{ safe_trans('admin.select_translation_file') }}</label>
                                    <select class="form-select" id="translation_file">
                                        <option value="admin">{{ safe_trans('admin.admin_translations') }}</option>
                                        <option value="frontend">{{ safe_trans('admin.frontend_translations') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="translation_language" class="form-label">{{ safe_trans('admin.select_language') }}</label>
                                    <select class="form-select" id="translation_language">
                                        <option value="ar">{{ safe_trans('admin.arabic') }}</option>
                                        <option value="en">{{ safe_trans('admin.english') }}</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-3">
                                <button type="button" class="btn btn-primary" id="load-translations">
                                    <i class="fas fa-sync-alt me-1"></i> {{ safe_trans('admin.load_translations') }}
                                </button>
                                <button type="button" class="btn btn-success" id="add-translation">
                                    <i class="fas fa-plus me-1"></i> {{ safe_trans('admin.add_new_translation') }}
                                </button>
                            </div>
                            
                            <div class="translation-editor d-none">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover" id="translations-table">
                                        <thead>
                                            <tr>
                                                <th>{{ safe_trans('admin.key') }}</th>
                                                <th>{{ safe_trans('admin.translation') }}</th>
                                                <th style="width: 100px;">{{ safe_trans('admin.actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- سيتم تحميل الترجمات هنا -->
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
                                    <button type="button" class="btn btn-primary" id="save-translations">
                                        <i class="fas fa-save me-1"></i> {{ safe_trans('admin.save_translations') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <!-- إعدادات الترجمة -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">{{ safe_trans('admin.translation_settings') }}</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="cache_translations" class="form-label">{{ safe_trans('admin.cache_translations') }}</label>
                                    <select class="form-select @error('cache_translations') is-invalid @enderror" id="cache_translations" name="cache_translations">
                                        <option value="1" {{ old('cache_translations', $settings['cache_translations'] ?? '1') == '1' ? 'selected' : '' }}>{{ safe_trans('admin.yes') }}</option>
                                        <option value="0" {{ old('cache_translations', $settings['cache_translations'] ?? '1') == '0' ? 'selected' : '' }}>{{ safe_trans('admin.no') }}</option>
                                    </select>
                                    @error('cache_translations')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="fallback_locale" class="form-label">{{ safe_trans('admin.fallback_locale') }}</label>
                                    <select class="form-select @error('fallback_locale') is-invalid @enderror" id="fallback_locale" name="fallback_locale">
                                        <option value="en" {{ old('fallback_locale', $settings['fallback_locale'] ?? 'en') == 'en' ? 'selected' : '' }}>{{ safe_trans('admin.english') }}</option>
                                        <option value="ar" {{ old('fallback_locale', $settings['fallback_locale'] ?? 'en') == 'ar' ? 'selected' : '' }}>{{ safe_trans('admin.arabic') }}</option>
                                    </select>
                                    @error('fallback_locale')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <button type="button" class="btn btn-warning" id="clear-translation-cache">
                                    <i class="fas fa-broom me-1"></i> {{ safe_trans('admin.clear_translation_cache') }}
                                </button>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> {{ safe_trans('admin.save_settings') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const loadTranslationsBtn = document.getElementById('load-translations');
        const addTranslationBtn = document.getElementById('add-translation');
        const saveTranslationsBtn = document.getElementById('save-translations');
        const clearCacheBtn = document.getElementById('clear-translation-cache');
        const translationEditor = document.querySelector('.translation-editor');
        const translationsTable = document.getElementById('translations-table').querySelector('tbody');
        
        // Load translations
        loadTranslationsBtn.addEventListener('click', function() {
            const file = document.getElementById('translation_file').value;
            const language = document.getElementById('translation_language').value;
            
            // Mostrar indicador de carga
            translationsTable.innerHTML = '<tr><td colspan="3" class="text-center">{{ safe_trans("admin.loading") }}...</td></tr>';
            translationEditor.classList.remove('d-none');
            
            // Aquí iría la llamada AJAX para cargar las traducciones
            fetch(`/admin/settings/language/translations?file=${file}&locale=${language}`)
                .then(response => response.json())
                .then(data => {
                    translationsTable.innerHTML = '';
                    
                    if (Object.keys(data).length === 0) {
                        translationsTable.innerHTML = '<tr><td colspan="3" class="text-center">{{ safe_trans("admin.no_translations_found") }}</td></tr>';
                        return;
                    }
                    
                    for (const key in data) {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${key}</td>
                            <td>
                                <input type="text" class="form-control" name="translations[${key}]" value="${data[key]}">
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-danger delete-translation">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        `;
                        translationsTable.appendChild(row);
                    }
                    
                    // Agregar event listeners a los botones de eliminar
                    document.querySelectorAll('.delete-translation').forEach(button => {
                        button.addEventListener('click', function() {
                            if (confirm('{{ safe_trans("admin.confirm_delete_translation") }}')) {
                                this.closest('tr').remove();
                            }
                        });
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    translationsTable.innerHTML = '<tr><td colspan="3" class="text-center text-danger">{{ safe_trans("admin.error_loading_translations") }}</td></tr>';
                });
        });
        
        // Add new translation
        addTranslationBtn.addEventListener('click', function() {
            if (translationEditor.classList.contains('d-none')) {
                translationEditor.classList.remove('d-none');
            }
            
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <input type="text" class="form-control" name="new_key[]" placeholder="{{ safe_trans("admin.enter_key") }}">
                </td>
                <td>
                    <input type="text" class="form-control" name="new_value[]" placeholder="{{ safe_trans("admin.enter_translation") }}">
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger delete-translation">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
            translationsTable.appendChild(row);
            
            // Add event listener to the delete button
            row.querySelector('.delete-translation').addEventListener('click', function() {
                if (confirm('{{ safe_trans("admin.confirm_delete_translation") }}')) {
                    this.closest('tr').remove();
                }
            });
            
            // Focus on the new key input
            row.querySelector('input[name^="new_key"]').focus();
        });
        
        // Save translations
        saveTranslationsBtn.addEventListener('click', function() {
            const file = document.getElementById('translation_file').value;
            const language = document.getElementById('translation_language').value;
            const translations = {};
            
            // Recopilar traducciones existentes
            document.querySelectorAll('input[name^="translations["]').forEach(input => {
                const key = input.name.match(/\[(.*?)\]/)[1];
                translations[key] = input.value;
            });
            
            // Recopilar nuevas traducciones
            const newKeys = document.querySelectorAll('input[name="new_key[]"]');
            const newValues = document.querySelectorAll('input[name="new_value[]"]');
            
            for (let i = 0; i < newKeys.length; i++) {
                const key = newKeys[i].value.trim();
                const value = newValues[i].value.trim();
                
                if (key && value) {
                    translations[key] = value;
                }
            }
            
            // Enviar datos al servidor
            fetch('/admin/settings/language/translations', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    file: file,
                    locale: language,
                    translations: translations
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('{{ safe_trans("admin.translations_saved_successfully") }}');
                    // Recargar las traducciones
                    loadTranslationsBtn.click();
                } else {
                    alert('{{ safe_trans("admin.error_saving_translations") }}');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('{{ safe_trans("admin.error_saving_translations") }}');
            });
        });
        
        // Clear translation cache
        clearCacheBtn.addEventListener('click', function() {
            if (confirm('{{ safe_trans("admin.confirm_clear_translation_cache") }}')) {
                fetch('/admin/settings/language/clear-cache', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('{{ safe_trans("admin.translation_cache_cleared") }}');
                    } else {
                        alert('{{ safe_trans("admin.error_clearing_translation_cache") }}');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('{{ safe_trans("admin.error_clearing_translation_cache") }}');
                });
            }
        });
    });
</script>
@endsection 