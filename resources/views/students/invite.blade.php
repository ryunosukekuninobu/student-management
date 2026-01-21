<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>生徒招待 - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-gradient-to-r from-blue-600 via-cyan-600 to-teal-600 shadow-lg">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-white">📧 生徒招待</h1>
                        <p class="mt-1 text-sm text-white">{{ $tenant->name }}</p>
                    </div>
                    <div>
                        <a href="{{ route('students.index') }}" class="text-sm bg-white text-blue-700 hover:bg-blue-50 px-4 py-2 rounded-lg font-bold transition-all duration-200 shadow-md hover:shadow-lg">
                            ← 一覧に戻る
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <main>
            <div class="max-w-3xl mx-auto py-6 sm:px-6 lg:px-8">
                <!-- Info Card -->
                <div class="bg-blue-50 border-l-4 border-blue-600 p-6 mb-6 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-bold text-blue-800">招待機能について</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <ul class="list-disc list-inside space-y-1">
                                    <li>メールアドレスを入力して招待リンクを送信します</li>
                                    <li>招待リンクは7日間有効です</li>
                                    <li>生徒または保護者がCalema SSOでアカウントを作成できます</li>
                                    <li>招待を受けた方は、自分でパスワードを設定します</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <form action="{{ route('students.invite.send') }}" method="POST">
                    @csrf

                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-600 to-cyan-600 px-6 py-4">
                            <h2 class="text-xl font-bold text-white">招待情報</h2>
                        </div>

                        <div class="p-6 space-y-6">
                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-bold text-gray-700 mb-2">
                                    氏名 <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
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
                                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="student@example.com">
                                <p class="mt-1 text-xs text-gray-500">※ 招待リンクがこのメールアドレスに送信されます</p>
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- User Type -->
                            <div>
                                <label for="user_type" class="block text-sm font-bold text-gray-700 mb-2">
                                    招待対象 <span class="text-red-500">*</span>
                                </label>
                                <select name="user_type" id="user_type" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="student" {{ old('user_type') === 'student' ? 'selected' : '' }}>生徒本人</option>
                                    <option value="guardian" {{ old('user_type') === 'guardian' ? 'selected' : '' }}>保護者</option>
                                </select>
                                @error('user_type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Optional Information -->
                            <div class="border-t border-gray-200 pt-6">
                                <h3 class="text-sm font-bold text-gray-700 mb-4">追加情報（任意）</h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="grade" class="block text-sm font-medium text-gray-700 mb-2">
                                            学年
                                        </label>
                                        <input type="text" name="grade" id="grade" value="{{ old('grade') }}"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            placeholder="小学6年生">
                                    </div>

                                    <div>
                                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">
                                            生年月日
                                        </label>
                                        <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth') }}"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="mt-6 flex justify-end space-x-4">
                        <a href="{{ route('students.index') }}"
                            class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-bold hover:bg-gray-300 transition-all">
                            キャンセル
                        </a>
                        <button type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-lg font-bold hover:shadow-lg transition-all">
                            📧 招待を送信
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
