<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>生徒情報編集 - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-gradient-to-r from-purple-600 via-pink-600 to-rose-600 shadow-lg">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-white">生徒情報編集</h1>
                        <p class="mt-1 text-sm text-white">{{ $student->user->name }}</p>
                    </div>
                    <div>
                        <a href="{{ route('students.index') }}" class="text-sm bg-white text-purple-700 hover:bg-purple-50 px-4 py-2 rounded-lg font-bold transition-all duration-200 shadow-md hover:shadow-lg">
                            ← 一覧に戻る
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <main>
            <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
                <form action="{{ route('students.update', $student) }}" method="POST" id="studentForm">
                    @csrf
                    @method('PUT')

                    <!-- Basic Information Section -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
                        <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
                            <h2 class="text-xl font-bold text-white">基本情報</h2>
                        </div>

                        <div class="p-6 space-y-6">
                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-bold text-gray-700 mb-2">
                                    氏名 <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" id="name" value="{{ old('name', $student->user->name) }}" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                    placeholder="山田 太郎">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-bold text-gray-700 mb-2">
                                    メールアドレス <span class="text-red-500">*</span>
                                </label>
                                <input type="email" name="email" id="email" value="{{ old('email', $student->user->email) }}" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                    placeholder="student@example.com">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Phone -->
                            <div>
                                <label for="phone" class="block text-sm font-bold text-gray-700 mb-2">
                                    電話番号
                                </label>
                                <input type="tel" name="phone" id="phone" value="{{ old('phone', $student->user->phone) }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                    placeholder="090-1234-5678">
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div>
                                <label for="password" class="block text-sm font-bold text-gray-700 mb-2">
                                    パスワード
                                </label>
                                <input type="password" name="password" id="password"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                    placeholder="空欄の場合は自動生成されます">
                                <p class="mt-1 text-xs text-gray-500">※ 空欄の場合、仮パスワード「temporary123」が設定されます</p>
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Date of Birth & Gender -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="date_of_birth" class="block text-sm font-bold text-gray-700 mb-2">
                                        生年月日
                                    </label>
                                    <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', $student->date_of_birth?->format('Y-m-d')) }}"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                    @error('date_of_birth')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="gender" class="block text-sm font-bold text-gray-700 mb-2">
                                        性別
                                    </label>
                                    <select name="gender" id="gender" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                        <option value="">選択してください</option>
                                        <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>男性</option>
                                        <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>女性</option>
                                        <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>その他</option>
                                        <option value="prefer_not_to_say" {{ old('gender') === 'prefer_not_to_say' ? 'selected' : '' }}>回答しない</option>
                                    </select>
                                    @error('gender')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Grade & School -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="grade" class="block text-sm font-bold text-gray-700 mb-2">
                                        学年
                                    </label>
                                    <input type="text" name="grade" id="grade" value="{{ old('grade', $student->grade) }}"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                        placeholder="小学6年生">
                                    @error('grade')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="school_name" class="block text-sm font-bold text-gray-700 mb-2">
                                        学校名
                                    </label>
                                    <input type="text" name="school_name" id="school_name" value="{{ old('school_name', $student->school_name) }}"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                        placeholder="○○小学校">
                                    @error('school_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Emergency Contact Section -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
                        <div class="bg-gradient-to-r from-red-600 to-orange-600 px-6 py-4">
                            <h2 class="text-xl font-bold text-white">緊急連絡先</h2>
                        </div>

                        <div class="p-6 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="emergency_contact_name" class="block text-sm font-bold text-gray-700 mb-2">
                                        連絡先氏名
                                    </label>
                                    <input type="text" name="emergency_contact_name" id="emergency_contact_name" value="{{ old('emergency_contact_name', $student->emergency_contact_name) }}"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                                </div>

                                <div>
                                    <label for="emergency_contact_phone" class="block text-sm font-bold text-gray-700 mb-2">
                                        電話番号
                                    </label>
                                    <input type="tel" name="emergency_contact_phone" id="emergency_contact_phone" value="{{ old('emergency_contact_phone', $student->emergency_contact_phone) }}"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                                </div>

                                <div>
                                    <label for="emergency_contact_relationship" class="block text-sm font-bold text-gray-700 mb-2">
                                        続柄
                                    </label>
                                    <input type="text" name="emergency_contact_relationship" id="emergency_contact_relationship" value="{{ old('emergency_contact_relationship', $student->emergency_contact_relationship) }}"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                        placeholder="母">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Health Information Section -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
                        <div class="bg-gradient-to-r from-green-600 to-teal-600 px-6 py-4">
                            <h2 class="text-xl font-bold text-white">健康情報</h2>
                        </div>

                        <div class="p-6 space-y-6">
                            <div>
                                <label for="blood_type" class="block text-sm font-bold text-gray-700 mb-2">
                                    血液型
                                </label>
                                <select name="blood_type" id="blood_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                    <option value="">選択してください</option>
                                    <option value="A" {{ old('blood_type') === 'A' ? 'selected' : '' }}>A型</option>
                                    <option value="B" {{ old('blood_type') === 'B' ? 'selected' : '' }}>B型</option>
                                    <option value="AB" {{ old('blood_type') === 'AB' ? 'selected' : '' }}>AB型</option>
                                    <option value="O" {{ old('blood_type') === 'O' ? 'selected' : '' }}>O型</option>
                                </select>
                            </div>

                            <div>
                                <label for="medical_notes" class="block text-sm font-bold text-gray-700 mb-2">
                                    健康上の注意事項
                                </label>
                                <textarea name="medical_notes" id="medical_notes" rows="4"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                    placeholder="アレルギー、持病、服用中の薬など">{{ old('medical_notes', $student->medical_notes) }}</textarea>
                                <p class="mt-1 text-xs text-gray-500">※ 重要な医療情報がある場合は必ず記入してください</p>
                            </div>
                        </div>
                    </div>

                    <!-- Guardian Section -->
                    @if($tenant->requires_guardian_info)
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
                            <div class="bg-gradient-to-r from-blue-600 to-cyan-600 px-6 py-4">
                                <h2 class="text-xl font-bold text-white">保護者情報</h2>
                            </div>

                            <div class="p-6" id="guardians-container">
                                <div class="guardian-item space-y-4 p-4 bg-gray-50 rounded-lg mb-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                                保護者氏名 <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" name="guardians[0][name]" required
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                                続柄 <span class="text-red-500">*</span>
                                            </label>
                                            <select name="guardians[0][relationship]" required
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                                <option value="father">父親</option>
                                                <option value="mother">母親</option>
                                                <option value="grandfather">祖父</option>
                                                <option value="grandmother">祖母</option>
                                                <option value="other">その他</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                                電話番号 <span class="text-red-500">*</span>
                                            </label>
                                            <input type="tel" name="guardians[0][phone]" required
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                                メールアドレス
                                            </label>
                                            <input type="email" name="guardians[0][email]"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="flex items-center">
                                            <input type="checkbox" name="guardians[0][is_primary_contact]" value="1" checked
                                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                            <span class="ml-2 text-sm font-medium text-gray-700">主連絡先に設定</span>
                                        </label>
                                    </div>
                                </div>

                                <button type="button" onclick="addGuardian()" class="text-blue-600 hover:text-blue-800 font-bold text-sm">
                                    + 保護者を追加
                                </button>
                            </div>
                        </div>
                    @endif

                    <!-- Notes Section -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
                        <div class="bg-gradient-to-r from-gray-600 to-gray-700 px-6 py-4">
                            <h2 class="text-xl font-bold text-white">管理者メモ</h2>
                        </div>

                        <div class="p-6">
                            <textarea name="notes" id="notes" rows="4"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent"
                                placeholder="管理者用メモ（生徒には表示されません）">{{ old('notes', $student->notes) }}</textarea>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('students.index') }}"
                            class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-bold hover:bg-gray-300 transition-all">
                            キャンセル
                        </a>
                        <button type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg font-bold hover:shadow-lg transition-all">
                            生徒を登録
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        let guardianCount = 1;

        function addGuardian() {
            const container = document.getElementById('guardians-container');
            const newGuardian = document.createElement('div');
            newGuardian.className = 'guardian-item space-y-4 p-4 bg-gray-50 rounded-lg mb-4';
            newGuardian.innerHTML = `
                <div class="flex justify-between items-center mb-2">
                    <h3 class="font-bold text-gray-700">保護者 ${guardianCount + 1}</h3>
                    <button type="button" onclick="this.closest('.guardian-item').remove()" class="text-red-600 hover:text-red-800 text-sm font-bold">
                        削除
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            保護者氏名 <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="guardians[${guardianCount}][name]" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            続柄 <span class="text-red-500">*</span>
                        </label>
                        <select name="guardians[${guardianCount}][relationship]" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="father">父親</option>
                            <option value="mother">母親</option>
                            <option value="grandfather">祖父</option>
                            <option value="grandmother">祖母</option>
                            <option value="other">その他</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            電話番号 <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" name="guardians[${guardianCount}][phone]" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            メールアドレス
                        </label>
                        <input type="email" name="guardians[${guardianCount}][email]"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
            `;

            container.insertBefore(newGuardian, container.lastElementChild);
            guardianCount++;
        }
    </script>
</body>
</html>
