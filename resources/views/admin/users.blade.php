@extends('layouts.admin')

@section('content')
    <div class="mb-8 text-[#F4B942] text-2xl heading-font">Users</div>

    <div id="users-summary" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        @foreach([['key' => 'shops', 'title' => 'Shops', 'count' => $counts['shop']], ['key' => 'mechanics', 'title' => 'Mechanics', 'count' => $counts['mechanic']], ['key' => 'motorists', 'title' => 'Motorists', 'count' => $counts['motorist']]] as $summary)
            <div class="bg-[#111113] border border-[#1E1E21] rounded-3xl p-6 shadow-[0_12px_48px_rgba(0,0,0,0.25)]">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="text-[#F4B942] text-base uppercase tracking-[0.18em] font-semibold">{{ $summary['title'] }}</div>
                        <p class="text-[#AAA] text-sm mt-3">{{ $summary['count'] }} account{{ $summary['count'] !== 1 ? 's' : '' }}</p>
                    </div>
                    <span class="inline-flex items-center rounded-full bg-[rgb(15,17,21)] px-3 py-2 text-xs font-semibold text-[#EEE]">{{ $summary['count'] }}</span>
                </div>
                <div class="mt-6">
                    <button type="button" class="show-section inline-flex items-center justify-center w-full rounded-lg bg-[#F4B942] px-4 py-3 text-sm font-semibold text-[#0A0A0B] transition hover:bg-[#e6b12a]" data-section="{{ $summary['key'] }}">
                        View {{ strtolower($summary['title']) }}
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    @foreach([['key' => 'shops', 'title' => 'Shops', 'users' => $shops], ['key' => 'mechanics', 'title' => 'Mechanics', 'users' => $mechanics], ['key' => 'motorists', 'title' => 'Motorists', 'users' => $motorists]] as $section)
        <div id="section-{{ $section['key'] }}" class="mb-8 bg-[#111113] border border-[#1E1E21] rounded-2xl overflow-hidden hidden">
            <div class="px-6 py-4 border-b border-[#1E1E21] space-y-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <div class="text-[#F4B942] text-xl font-semibold">{{ $section['title'] }}</div>
                        <div class="text-[#AAA] text-sm mt-1">Detailed accounts for {{ strtolower($section['title']) }}.</div>
                    </div>
                    <button type="button" class="hide-section inline-flex items-center rounded-lg border border-[#1E1E21] bg-[#0A0B0D] px-4 py-2 text-sm font-semibold text-[#EEE] hover:border-[#F4B942] hover:text-[#F4B942]" data-section="{{ $section['key'] }}">
                        Back
                    </button>
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="text-sm text-[#AAA] flex items-center gap-2">
                        <button type="button" id="enter-delete-{{ $section['key'] }}" data-section="{{ $section['key'] }}" class="enter-delete-mode inline-flex items-center rounded-lg border border-[#1E1E21] bg-[#0A0B0D] px-3 py-2 text-xs font-semibold text-[#EEE] hover:border-[#F4B942]" title="Select items to delete">
                            Select
                        </button>
                        <span class="selected-count" data-section="{{ $section['key'] }}">0 selected</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" id="bulk-delete-{{ $section['key'] }}" data-section="{{ $section['key'] }}" class="bulk-delete-button inline-flex items-center rounded-lg bg-[#8F1D1D] px-4 py-2 text-xs font-semibold text-[#FFE5E5] hover:bg-[#A11F1F]" disabled style="display:none;">
                            Delete
                        </button>
                        <form id="bulk-delete-form-{{ $section['key'] }}" action="{{ route('admin.users.bulkDelete') }}" method="POST" class="hidden">
                            @csrf
                            @method('DELETE')
                            <div id="bulk-delete-inputs-{{ $section['key'] }}"></div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto overflow-y-auto max-h-[520px] bg-[#0B0C10] rounded-2xl border border-[#1E1E21]">
                <table class="w-full text-sm table-auto">
                    <thead class="border-b border-[#1E1E21] bg-[#0B0C10]">
                        <tr class="text-[#888] text-[11px] uppercase tracking-[0.15em]">
                            <th class="px-4 py-3 text-left w-12">
                                <input type="checkbox" class="select-all" data-section="{{ $section['key'] }}" style="display:none;">
                            </th>
                            <th class="px-4 py-3 text-left w-1/4">Name</th>
                            <th class="px-4 py-3 text-left w-1/4">Email</th>
                            <th class="px-4 py-3 text-left w-1/6">Shop</th>
                            <th class="px-4 py-3 text-left w-1/6">Role</th>
                            <th class="px-4 py-3 text-right w-32">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-[#111113]">
                        @forelse($section['users'] as $user)
                            <tr class="border-b border-[#1E1E21] hover:bg-[#16171A]">
                                <td class="px-4 py-4 align-top">
                                    <input type="checkbox" class="select-user" data-section="{{ $section['key'] }}" value="{{ $user->id }}" style="display:none;">
                                </td>
                                <td class="px-4 py-4 align-top">
                                    <div class="flex items-center gap-4">
                                        <div class="flex h-11 w-11 items-center justify-center rounded-full bg-[#2F6BFF] text-sm font-semibold text-white">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-semibold text-[#EEE]">{{ $user->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 align-top">
                                    <div class="text-[#888] text-xs truncate">{{ $user->email }}</div>
                                </td>
                                <td class="px-4 py-4 align-top">
                                    <span class="text-[#0B74E8] font-semibold">{{ $user->shop_name ?? '-' }}</span>
                                </td>
                                <td class="px-4 py-4 align-top">
                                    @php
                                        $roleColors = 'bg-[#222] text-[#888]';
                                        if ($user->role === 'shop') {
                                            $roleColors = 'bg-blue-900 text-blue-300';
                                        } elseif ($user->role === 'mechanic') {
                                            $roleColors = 'bg-emerald-950 text-emerald-300';
                                        } elseif ($user->role === 'motorist') {
                                            $roleColors = 'bg-amber-950 text-amber-300';
                                        }
                                    @endphp
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $roleColors }}">
                                        {{ ucfirst($user->role ?? 'Unknown') }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 align-top text-right">
                                    <form action="{{ route('admin.users.role', $user->id) }}" method="POST" class="inline-flex items-center gap-2">
                                        @csrf
                                        <select name="role" class="min-w-[110px] bg-[#111113] px-2.5 py-1.5 border border-[#1E1E21] rounded text-[#EEE] text-xs">
                                            <option value="shop" {{ $user->role === 'shop' ? 'selected' : '' }}>Shop</option>
                                            <option value="mechanic" {{ $user->role === 'mechanic' ? 'selected' : '' }}>Mechanic</option>
                                            <option value="motorist" {{ $user->role === 'motorist' ? 'selected' : '' }}>Motorist</option>
                                        </select>
                                        <button type="submit" class="rounded-lg bg-[#F4B942] px-2.5 py-1.5 text-[11px] font-semibold text-[#0A0A0B]">Change</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-[#666] text-center">No {{ strtolower($section['title']) }} accounts found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const summaryPanel = document.getElementById('users-summary');

            function updateBulkState(section) {
                const sectionEl = document.getElementById('section-' + section);
                if (!sectionEl) {
                    return;
                }
                const checkboxes = sectionEl.querySelectorAll('.select-user');
                const selected = sectionEl.querySelectorAll('.select-user:checked');
                const selectedCountEl = sectionEl.querySelector('.selected-count[data-section="' + section + '"]');
                const bulkButton = document.getElementById('bulk-delete-' + section);
                const selectAll = sectionEl.querySelector('.select-all[data-section="' + section + '"]');

                const inDeleteMode = sectionEl.classList.contains('delete-mode');

                if (selectedCountEl) {
                    selectedCountEl.textContent = selected.length + ' selected';
                }

                // show/hide bulk delete button depending on mode
                if (bulkButton) {
                    bulkButton.disabled = selected.length === 0;
                    bulkButton.style.display = inDeleteMode ? '' : 'none';
                }

                // show/hide select-all checkbox in header
                if (selectAll) {
                    selectAll.checked = selected.length > 0 && selected.length === checkboxes.length;
                    selectAll.style.display = inDeleteMode ? '' : 'none';
                }

                // show/hide individual row checkboxes
                checkboxes.forEach(function(cb) {
                    cb.style.display = inDeleteMode ? '' : 'none';
                    if (!inDeleteMode) cb.checked = false;
                });
            }

            document.querySelectorAll('.show-section').forEach(function(button) {
                button.addEventListener('click', function() {
                    const section = this.dataset.section;
                    document.querySelectorAll('[id^="section-"]').forEach(function(panel) {
                        panel.classList.add('hidden');
                    });
                    if (summaryPanel) {
                        summaryPanel.classList.add('hidden');
                    }
                    const target = document.getElementById('section-' + section);
                    if (target) {
                        target.classList.remove('hidden');
                        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                });
            });

            document.querySelectorAll('.select-all').forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    const section = this.dataset.section;
                    const sectionEl = document.getElementById('section-' + section);
                    if (!sectionEl) {
                        return;
                    }
                    sectionEl.querySelectorAll('.select-user').forEach(function(item) {
                        item.checked = checkbox.checked;
                    });
                    updateBulkState(section);
                });
            });

            document.querySelectorAll('.select-user').forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    updateBulkState(this.dataset.section);
                });
            });

            // toggle delete mode when the enter-delete button is clicked
            document.querySelectorAll('.enter-delete-mode').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const section = this.dataset.section;
                    const sectionEl = document.getElementById('section-' + section);
                    if (!sectionEl) return;
                    sectionEl.classList.toggle('delete-mode');
                    updateBulkState(section);
                });
            });

            document.querySelectorAll('.bulk-delete-button').forEach(function(button) {
                button.addEventListener('click', function() {
                    const section = this.dataset.section;
                    const sectionEl = document.getElementById('section-' + section);
                    if (!sectionEl) {
                        return;
                    }
                    const selected = sectionEl.querySelectorAll('.select-user:checked');
                    if (!selected.length) {
                        return;
                    }
                    if (!confirm('Delete ' + selected.length + ' selected account(s)? This cannot be undone.')) {
                        return;
                    }
                    const inputContainer = document.getElementById('bulk-delete-inputs-' + section);
                    if (inputContainer) {
                        inputContainer.innerHTML = '';
                        selected.forEach(function(item) {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'user_ids[]';
                            input.value = item.value;
                            inputContainer.appendChild(input);
                        });
                    }
                    document.getElementById('bulk-delete-form-' + section).submit();
                });
            });

            document.querySelectorAll('.hide-section').forEach(function(button) {
                button.addEventListener('click', function() {
                    const section = this.dataset.section;
                    const target = document.getElementById('section-' + section);
                    if (target) {
                        target.classList.add('hidden');
                    }
                    if (summaryPanel) {
                        summaryPanel.classList.remove('hidden');
                        summaryPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                });
            });
        });
    </script>
@endpush
