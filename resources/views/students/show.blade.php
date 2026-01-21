<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $student->user->name }} - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-gradient-to-r from-purple-600 via-pink-600 to-rose-600 shadow-lg">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-white">{{ $student->user->name }}</h1>
                        <p class="mt-1 text-sm text-white">生徒番号: {{ $student->student_number }}</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('students.index') }}" class="text-sm bg-purple-700 hover:bg-purple-800 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200 shadow-md">
                            一覧に戻る
                        </a>
                        <a href="{{ route('students.edit', $student) }}" class="text-sm bg-white text-purple-700 hover:bg-purple-50 px-4 py-2 rounded-lg font-bold transition-all duration-200 shadow-md hover:shadow-lg">
                            ✏️ 編集
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <main>
            <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Info -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Basic Information -->
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                            <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
                                <h2 class="text-xl font-bold text-white">基本情報</h2>
                            </div>

                            <div class="p-6">
                                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">氏名</dt>
                                        <dd class="mt-1 text-sm font-bold text-gray-900">{{ $student->user->name }}</dd>
                                    </div>

                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">生徒番号</dt>
                                        <dd class="mt-1 text-sm font-bold text-gray-900">{{ $student->student_number }}</dd>
                                    </div>

                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">メールアドレス</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $student->user->email }}</dd>
                                    </div>

                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">電話番号</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $student->user->phone ?? '未登録' }}</dd>
                                    </div>

                                    @if($student->date_of_birth)
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">生年月日</dt>
                                            <dd class="mt-1 text-sm text-gray-900">
                                                {{ $student->date_of_birth->format('Y年m月d日') }}
                                                @if($student->age)
                                                    <span class="text-gray-500">({{ $student->age }}歳)</span>
                                                @endif
                                            </dd>
                                        </div>
                                    @endif

                                    @if($student->gender)
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">性別</dt>
                                            <dd class="mt-1 text-sm text-gray-900">
                                                @if($student->gender === 'male') 男性
                                                @elseif($student->gender === 'female') 女性
                                                @elseif($student->gender === 'other') その他
                                                @else 回答しない
                                                @endif
                                            </dd>
                                        </div>
                                    @endif

                                    @if($student->grade)
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">学年</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ $student->grade }}</dd>
                                        </div>
                                    @endif

                                    @if($student->school_name)
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">学校名</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ $student->school_name }}</dd>
                                        </div>
                                    @endif

                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">入会日</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            {{ $student->joined_date ? $student->joined_date->format('Y年m月d日') : '未設定' }}
                                        </dd>
                                    </div>

                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">ステータス</dt>
                                        <dd class="mt-1">
                                            @if($student->status === 'active')
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                                    ✅ {{ $student->status_display }}
                                                </span>
                                            @elseif($student->status === 'inactive')
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                                    ⏸️ {{ $student->status_display }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                                    {{ $student->status_display }}
                                                </span>
                                            @endif
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        </div>

                        <!-- Guardian Information -->
                        @if($student->guardians->count() > 0)
                            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                                <div class="bg-gradient-to-r from-blue-600 to-cyan-600 px-6 py-4">
                                    <h2 class="text-xl font-bold text-white">保護者情報</h2>
                                </div>

                                <div class="p-6 space-y-4">
                                    @foreach($student->guardians as $guardian)
                                        <div class="bg-gray-50 rounded-lg p-4">
                                            <div class="flex items-center justify-between mb-3">
                                                <h3 class="font-bold text-gray-900">{{ $guardian->name }}</h3>
                                                @if($guardian->is_primary_contact)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                        主連絡先
                                                    </span>
                                                @endif
                                            </div>
                                            <dl class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                <div>
                                                    <dt class="text-xs font-medium text-gray-500">続柄</dt>
                                                    <dd class="mt-1 text-sm text-gray-900">{{ $guardian->relationship_display }}</dd>
                                                </div>
                                                <div>
                                                    <dt class="text-xs font-medium text-gray-500">電話番号</dt>
                                                    <dd class="mt-1 text-sm text-gray-900">{{ $guardian->phone }}</dd>
                                                </div>
                                                @if($guardian->email)
                                                    <div>
                                                        <dt class="text-xs font-medium text-gray-500">メールアドレス</dt>
                                                        <dd class="mt-1 text-sm text-gray-900">{{ $guardian->email }}</dd>
                                                    </div>
                                                @endif
                                                @if($guardian->address)
                                                    <div class="md:col-span-2">
                                                        <dt class="text-xs font-medium text-gray-500">住所</dt>
                                                        <dd class="mt-1 text-sm text-gray-900">
                                                            @if($guardian->postal_code)
                                                                〒{{ $guardian->postal_code }}<br>
                                                            @endif
                                                            {{ $guardian->address }}
                                                        </dd>
                                                    </div>
                                                @endif
                                            </dl>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Health Information -->
                        @if($student->medical_notes || $student->blood_type || $student->emergency_contact_name)
                            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                                <div class="bg-gradient-to-r from-red-600 to-orange-600 px-6 py-4">
                                    <h2 class="text-xl font-bold text-white">健康・緊急連絡先</h2>
                                </div>

                                <div class="p-6">
                                    <dl class="space-y-4">
                                        @if($student->blood_type)
                                            <div>
                                                <dt class="text-sm font-medium text-gray-500">血液型</dt>
                                                <dd class="mt-1 text-sm text-gray-900">{{ $student->blood_type }}型</dd>
                                            </div>
                                        @endif

                                        @if($student->medical_notes)
                                            <div>
                                                <dt class="text-sm font-medium text-gray-500">健康上の注意事項</dt>
                                                <dd class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ $student->medical_notes }}</dd>
                                            </div>
                                        @endif

                                        @if($student->emergency_contact_name)
                                            <div class="border-t border-gray-200 pt-4">
                                                <dt class="text-sm font-medium text-gray-500 mb-2">緊急連絡先</dt>
                                                <dd class="text-sm text-gray-900">
                                                    <div class="grid grid-cols-3 gap-2">
                                                        <div>
                                                            <span class="text-xs text-gray-500">氏名:</span><br>
                                                            <span class="font-medium">{{ $student->emergency_contact_name }}</span>
                                                        </div>
                                                        @if($student->emergency_contact_phone)
                                                            <div>
                                                                <span class="text-xs text-gray-500">電話:</span><br>
                                                                <span class="font-medium">{{ $student->emergency_contact_phone }}</span>
                                                            </div>
                                                        @endif
                                                        @if($student->emergency_contact_relationship)
                                                            <div>
                                                                <span class="text-xs text-gray-500">続柄:</span><br>
                                                                <span class="font-medium">{{ $student->emergency_contact_relationship }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </dd>
                                            </div>
                                        @endif
                                    </dl>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Quick Stats -->
                        <div class="bg-white rounded-xl shadow-lg p-6">
                            <h3 class="font-bold text-gray-900 mb-4">在籍クラス</h3>
                            @if($student->enrollments->count() > 0)
                                <div class="space-y-2">
                                    @foreach($student->enrollments as $enrollment)
                                        <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg p-3">
                                            <div class="font-bold text-sm text-gray-900">{{ $enrollment->course->name }}</div>
                                            <div class="text-xs text-gray-600 mt-1">
                                                @if($enrollment->status === 'active')
                                                    <span class="text-green-600">✅ 受講中</span>
                                                @else
                                                    <span class="text-gray-500">{{ $enrollment->status }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-gray-500">未登録</p>
                            @endif
                        </div>

                        <!-- Notes -->
                        @if($student->notes)
                            <div class="bg-white rounded-xl shadow-lg p-6">
                                <h3 class="font-bold text-gray-900 mb-4">管理者メモ</h3>
                                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $student->notes }}</p>
                            </div>
                        @endif

                        <!-- Actions -->
                        <div class="bg-white rounded-xl shadow-lg p-6">
                            <h3 class="font-bold text-gray-900 mb-4">アクション</h3>
                            <div class="space-y-2">
                                <a href="{{ route('students.edit', $student) }}" class="block w-full text-center bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-bold transition-all">
                                    編集
                                </a>
                                <form action="{{ route('students.destroy', $student) }}" method="POST" onsubmit="return confirm('本当に削除しますか？');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="block w-full text-center bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-bold transition-all">
                                        削除
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
