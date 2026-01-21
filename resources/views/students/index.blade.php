<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>生徒管理 - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-gradient-to-r from-purple-600 via-pink-600 to-rose-600 shadow-lg">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-white">生徒管理</h1>
                        <p class="mt-1 text-sm text-white">{{ $tenant->name }}</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('tenant.dashboard') }}" class="text-sm bg-purple-700 hover:bg-purple-800 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200 shadow-md">
                            ダッシュボード
                        </a>
                        <a href="{{ route('students.invite') }}" class="text-sm bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-bold transition-all duration-200 shadow-md hover:shadow-lg">
                            📧 招待する
                        </a>
                        <a href="{{ route('students.create') }}" class="text-sm bg-white text-purple-700 hover:bg-purple-50 px-4 py-2 rounded-lg font-bold transition-all duration-200 shadow-md hover:shadow-lg">
                            + 新規登録
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

                @if(session('error'))
                    <div class="mb-6 rounded-md bg-red-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Search & Filter -->
                <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                    <form method="GET" action="{{ route('students.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="生徒名、メール、生徒番号で検索"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>
                        <div>
                            <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                <option value="">全てのステータス</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>在籍中</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>休会中</option>
                                <option value="graduated" {{ request('status') === 'graduated' ? 'selected' : '' }}>卒業</option>
                                <option value="withdrawn" {{ request('status') === 'withdrawn' ? 'selected' : '' }}>退会</option>
                            </select>
                        </div>
                        <div class="flex space-x-2">
                            <button type="submit" class="flex-1 bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-bold transition-all">
                                🔍 検索
                            </button>
                            <a href="{{ route('students.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg font-bold transition-all">
                                リセット
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Students List -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
                        <h2 class="text-xl font-bold text-white">生徒一覧</h2>
                        <p class="text-sm text-white mt-1">全 {{ $students->total() }} 名</p>
                    </div>

                    @if($students->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">生徒情報</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">連絡先</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">在籍クラス</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ステータス</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">操作</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($students as $student)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    <div class="text-3xl mr-3">👤</div>
                                                    <div>
                                                        <div class="text-sm font-bold text-gray-900">{{ $student->user->name }}</div>
                                                        <div class="text-xs text-gray-500">生徒番号: {{ $student->student_number }}</div>
                                                        @if($student->age)
                                                            <div class="text-xs text-gray-500">{{ $student->age }}歳</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900">{{ $student->user->email }}</div>
                                                @if($student->user->phone)
                                                    <div class="text-xs text-gray-500">📞 {{ $student->user->phone }}</div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4">
                                                @if($student->enrollments->count() > 0)
                                                    @foreach($student->enrollments->take(2) as $enrollment)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 mr-1 mb-1">
                                                            {{ $enrollment->course->name }}
                                                        </span>
                                                    @endforeach
                                                    @if($student->enrollments->count() > 2)
                                                        <span class="text-xs text-gray-500">他{{ $student->enrollments->count() - 2 }}件</span>
                                                    @endif
                                                @else
                                                    <span class="text-xs text-gray-400">未登録</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($student->status === 'active')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        ✅ {{ $student->status_display }}
                                                    </span>
                                                @elseif($student->status === 'inactive')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        ⏸️ {{ $student->status_display }}
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                        {{ $student->status_display }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('students.show', $student) }}" class="text-purple-600 hover:text-purple-900 mr-3">詳細</a>
                                                <a href="{{ route('students.edit', $student) }}" class="text-blue-600 hover:text-blue-900">編集</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="px-6 py-4 bg-gray-50">
                            {{ $students->links() }}
                        </div>
                    @else
                        <div class="p-12 text-center">
                            <div class="text-6xl mb-4">👥</div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">生徒がまだ登録されていません</h3>
                            <p class="text-sm text-gray-500 mb-6">最初の生徒を登録しましょう</p>
                            <div class="flex justify-center space-x-4">
                                <a href="{{ route('students.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg font-bold hover:shadow-lg transition-all">
                                    + 新規登録
                                </a>
                                <a href="{{ route('students.invite') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-lg font-bold hover:shadow-lg transition-all">
                                    📧 招待する
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
</body>
</html>
