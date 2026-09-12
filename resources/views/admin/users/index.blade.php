@php($search = request('search', ''))
@php($statusFilter = request('status', ''))

@extends('layouts.app')

@section('title', 'Управление пользователями')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Управление пользователями</h1>

        <a href="{{ route('admin.users.create') }}" class="btn btn-success">
            ➕ Добавить пользователя
        </a>
    </div>

    {{-- Вывод системных сообщений об успехе или ошибках --}}
    @if(session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- Форма поиска и фильтрации --}}
    <form method="GET" action="{{ route('admin.users.index') }}" class="card card-body mb-3">
        <div class="row g-2 align-items-end">
            <div class="col-12 col-md-6 col-lg-6">
                <label for="search" class="form-label mb-1">Поиск пользователя</label>
                <input type="text"
                       name="search"
                       id="search"
                       value="{{ $search }}"
                       class="form-control"
                       placeholder="Имя, фамилия или Email">
            </div>

            <div class="col-12 col-md-4 col-lg-4">
                <label for="status" class="form-label mb-1">Статус</label>
                <select name="status" id="status" class="form-select">
                    <option value="">Все статусы</option>
                    <option value="active" @selected($statusFilter === 'active')>Активные</option>
                    <option value="blocked" @selected($statusFilter === 'blocked')>Заблокированные</option>
                </select>
            </div>

            <div class="col-12 col-md-2 col-lg-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">Применить</button>
                <a href="{{ route('admin.users.index') }}" class="btn alert-secondary text-nowrap">
                    Сбросить
                </a>
            </div>
        </div>
    </form>

    {{-- Таблица со списком пользователей --}}
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                <tr>
                    <th scope="col" style="width: 70px;">ID</th>
                    <th scope="col">Пользователь</th>
                    <th scope="col">Контакты</th>
                    <th scope="col">Роль</th>
                    <th scope="col">Статус</th>
                    <th scope="col" class="text-end" style="width: 280px;">Действия</th>
                </tr>
                </thead>
                <tbody>
                @forelse($users as $user)
                    <tr>
                        <td><code>#{{ $user->id }}</code></td>
                        <td>
                            <div class="fw-semibold">{{ $user->first_name }} {{ $user->last_name }}</div>
                        </td>
                        <td>
                            <div class="small">{{ $user->email }}</div>
                            @if($user->phone)
                                <div class="text-muted small">{{ $user->phone }}</div>
                            @endif
                        </td>
                        <td>
                            {{-- Выводим название роли или slug, если названия нет --}}
                            <span class="badge bg-secondary">
                                {{ $user->roles->first()?->name ?? 'Без роли' }}
                            </span>
                        </td>
                        <td>
                            @if($user->status === 'active')
                                <span class="badge bg-success">Активен</span>
                            @else
                                <span class="badge bg-danger">Заблокирован</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-1">
                                {{-- Кнопка просмотра --}}
                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-info" title="Просмотр">
                                    👁️
                                </a>

                                {{-- Кнопка редактирования --}}
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-warning" title="Изменить">
                                    ✏️
                                </a>

                                {{-- Кнопка удаления --}}
                                <form action="{{ route('admin.users.destroy', $user) }}"
                                      method="POST"
                                      class="d-inline mb-0"
                                      onsubmit="return confirm('Вы уверены, что хотите полностью удалить пользователя {{ $user->first_name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Удалить" @disabled($user->id === auth()->id())>
                                        🗑️
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            Пользователи не найдены.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Пагинация --}}
        @if($users->hasPages())
            <div class="card-footer bg-white pt-3">
                {{ $users->links() }}
            </div>
        @endif
    </div>
@endsection
