<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>生徒管理設定 - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-gradient-to-r from-green-600 via-teal-600 to-cyan-600 shadow-lg">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-white">⚙️ 生徒管理設定</h1>
                        <p class="mt-1 text-sm text-white">{{ $tenant->name }}</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('students.index') }}" class="text-sm bg-white text-green-700 hover:bg-green-50 px-4 py-2 rounded-lg font-bold transition-all duration-200 shadow-md hover:shadow-lg">
                            ← 生徒一覧に戻る
                        </a>
                        <a href="{{ route('tenant.dashboard') }}" class="text-sm bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200 shadow-md">
                            ダッシュボード
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <main>
            <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <!-- Success/Error Messages -->
                @if(session('success'))
                    <div class="mb-6 rounded-md bg-green-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 rounded-md bg-red-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <ul class="list-disc list-inside text-sm text-red-800">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-1 gap-6">
                    <!-- Basic Settings -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="bg-gradient-to-r from-green-600 to-teal-600 px-6 py-4">
                            <h2 class="text-xl font-bold text-white">基本設定</h2>
                            <p class="text-sm text-green-100 mt-1">生徒管理の基本的な設定を行います</p>
                        </div>

                        <form action="{{ route('students.settings.update') }}" method="POST" class="p-6">
                            @csrf
                            @method('PUT')

                            <div class="space-y-6">
                                <!-- Guardian Requirement -->
                                <div class="border-b border-gray-200 pb-6">
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="requires_guardian_info" name="requires_guardian_info" type="checkbox" value="1"
                                                {{ $tenant->requires_guardian_info ? 'checked' : '' }}
                                                class="h-5 w-5 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                                        </div>
                                        <div class="ml-3">
                                            <label for="requires_guardian_info" class="font-bold text-gray-900">
                                                保護者情報を必須にする
                                            </label>
                                            <p class="text-sm text-gray-500 mt-1">
                                                有効にすると、生徒登録時に保護者情報の入力が必須になります。未成年の生徒を対象とする場合に推奨されます。
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                @php
                                    $studentSettings = $tenant->settings['student_management'] ?? [];
                                @endphp

                                <!-- Auto-generate Student Number -->
                                <div class="border-b border-gray-200 pb-6">
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="auto_generate_student_number" name="auto_generate_student_number" type="checkbox" value="1"
                                                {{ ($studentSettings['auto_generate_student_number'] ?? true) ? 'checked' : '' }}
                                                class="h-5 w-5 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                                        </div>
                                        <div class="ml-3">
                                            <label for="auto_generate_student_number" class="font-bold text-gray-900">
                                                生徒番号を自動生成する
                                            </label>
                                            <p class="text-sm text-gray-500 mt-1">
                                                有効にすると、生徒登録時に生徒番号が自動的に生成されます（形式: YYYYMM-NNNN）
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Invitation Validity Days -->
                                <div class="border-b border-gray-200 pb-6">
                                    <label for="invitation_validity_days" class="block text-sm font-bold text-gray-700 mb-2">
                                        招待リンク有効期限（日数）
                                    </label>
                                    <input type="number" name="invitation_validity_days" id="invitation_validity_days"
                                        value="{{ $studentSettings['invitation_validity_days'] ?? 7 }}"
                                        min="1" max="30"
                                        class="w-full md:w-64 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                    <p class="text-sm text-gray-500 mt-1">
                                        生徒・保護者への招待リンクが有効な期間を設定します（1〜30日）
                                    </p>
                                </div>

                                <!-- Max Guardians -->
                                <div class="pb-6">
                                    <label for="max_guardians_per_student" class="block text-sm font-bold text-gray-700 mb-2">
                                        1人の生徒あたり登録可能な保護者数
                                    </label>
                                    <input type="number" name="max_guardians_per_student" id="max_guardians_per_student"
                                        value="{{ $studentSettings['max_guardians_per_student'] ?? 5 }}"
                                        min="1" max="10"
                                        class="w-full md:w-64 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                    <p class="text-sm text-gray-500 mt-1">
                                        1人の生徒に対して登録できる保護者の最大数を設定します（1〜10人）
                                    </p>
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end">
                                <button type="submit"
                                    class="px-6 py-3 bg-gradient-to-r from-green-600 to-teal-600 text-white rounded-lg font-bold hover:shadow-lg transition-all">
                                    💾 設定を保存
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Custom Fields Management -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
                            <h2 class="text-xl font-bold text-white">カスタムフィールド管理</h2>
                            <p class="text-sm text-purple-100 mt-1">生徒プロフィールに追加の項目を設定できます</p>
                        </div>

                        <div class="p-6">
                            <!-- Custom Fields List -->
                            @if($customFields->count() > 0)
                                <div class="mb-6">
                                    <h3 class="text-sm font-bold text-gray-700 mb-4">登録済みカスタムフィールド</h3>
                                    <div class="space-y-3">
                                        @foreach($customFields as $field)
                                            <div class="bg-gray-50 rounded-lg p-4 flex items-center justify-between">
                                                <div class="flex-grow">
                                                    <div class="flex items-center space-x-3">
                                                        <h4 class="font-bold text-gray-900">{{ $field->field_label }}</h4>
                                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                                                            {{ $field->field_type }}
                                                        </span>
                                                        @if($field->is_required)
                                                            <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">
                                                                必須
                                                            </span>
                                                        @endif
                                                        @if(!$field->is_active)
                                                            <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full">
                                                                無効
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <p class="text-sm text-gray-500 mt-1">フィールド名: {{ $field->field_name }}</p>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <form action="{{ route('students.settings.custom-fields.destroy', $field) }}" method="POST"
                                                        onsubmit="return confirm('このカスタムフィールドを削除しますか？');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition-all">
                                                            削除
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="mb-6 text-center py-8 bg-gray-50 rounded-lg">
                                    <p class="text-gray-500">カスタムフィールドが登録されていません</p>
                                </div>
                            @endif

                            <!-- Add Custom Field Form -->
                            <div class="border-t border-gray-200 pt-6">
                                <h3 class="text-sm font-bold text-gray-700 mb-4">新規カスタムフィールド追加</h3>
                                <form action="{{ route('students.settings.custom-fields.store') }}" method="POST" id="customFieldForm">
                                    @csrf

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label for="field_name" class="block text-sm font-medium text-gray-700 mb-2">
                                                フィールド名（英数字・アンダースコア）<span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" name="field_name" id="field_name" required
                                                pattern="[a-zA-Z0-9_]+"
                                                placeholder="hobby_sport"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                        </div>

                                        <div>
                                            <label for="field_label" class="block text-sm font-medium text-gray-700 mb-2">
                                                表示ラベル<span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" name="field_label" id="field_label" required
                                                placeholder="趣味・スポーツ"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                        </div>

                                        <div>
                                            <label for="field_type" class="block text-sm font-medium text-gray-700 mb-2">
                                                フィールドタイプ<span class="text-red-500">*</span>
                                            </label>
                                            <select name="field_type" id="field_type" required
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                                <option value="text">テキスト（1行）</option>
                                                <option value="textarea">テキストエリア（複数行）</option>
                                                <option value="number">数値</option>
                                                <option value="date">日付</option>
                                                <option value="select">選択（ドロップダウン）</option>
                                                <option value="checkbox">チェックボックス</option>
                                                <option value="radio">ラジオボタン</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label for="display_order" class="block text-sm font-medium text-gray-700 mb-2">
                                                表示順序
                                            </label>
                                            <input type="number" name="display_order" id="display_order"
                                                value="0" min="0"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                        </div>

                                        <div class="md:col-span-2" id="optionsContainer" style="display: none;">
                                            <label for="field_options" class="block text-sm font-medium text-gray-700 mb-2">
                                                選択肢（JSON形式）
                                            </label>
                                            <textarea name="field_options" id="field_options" rows="3"
                                                placeholder='["オプション1", "オプション2", "オプション3"]'
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"></textarea>
                                            <p class="text-xs text-gray-500 mt-1">select、checkbox、radioタイプの場合に設定します</p>
                                        </div>

                                        <div class="md:col-span-2">
                                            <div class="flex items-center">
                                                <input id="is_required" name="is_required" type="checkbox" value="1"
                                                    class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                                                <label for="is_required" class="ml-2 text-sm text-gray-700">
                                                    このフィールドを必須項目にする
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-6 flex justify-end">
                                        <button type="submit"
                                            class="px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg font-bold hover:shadow-lg transition-all">
                                            ➕ カスタムフィールドを追加
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Show/hide options field based on field type
        document.getElementById('field_type').addEventListener('change', function() {
            const optionsContainer = document.getElementById('optionsContainer');
            const fieldType = this.value;

            if (fieldType === 'select' || fieldType === 'checkbox' || fieldType === 'radio') {
                optionsContainer.style.display = 'block';
            } else {
                optionsContainer.style.display = 'none';
            }
        });
    </script>
</body>
</html>
