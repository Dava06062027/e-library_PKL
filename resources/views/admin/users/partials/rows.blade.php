@foreach($users as $index => $user)
    @php
        $colors = ['primary', 'danger', 'info', 'success', 'warning', 'secondary'];
        $avatarColor = $colors[$index % count($colors)];
        $initial = strtoupper(substr($user->name, 0, 1));

        // Role badge styling
        $roleBadgeClass = 'bg-light text-dark';
        if ($user->role === 'Admin') {
            $roleBadgeClass = 'bg-danger text-white';
        } elseif ($user->role === 'Officer') {
            $roleBadgeClass = 'bg-warning text-dark';
        }

        // Status online/offline menggunakan Cache
        $isOnline = $user->isOnline();
    @endphp
    <tr data-id="{{ $user->id }}" class="border-bottom border-m365">
        <td>
            <input type="checkbox" class="form-check-input select-user" value="{{ $user->id }}">
        </td>
        <td>
            <div class="d-flex align-items-center">
                <span class="bg-{{ $avatarColor }} text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-semibold me-2"
                      style="width: 32px; height: 32px; font-size: 14px;">
                    {{ $initial }}
                </span>
                <span class="text-m365-blue">{{ $user->name }}</span>
            </div>
        </td>
        <td class="text-secondary">
            {{ $user->email }}
        </td>
        <td>
            <span class="badge {{ $roleBadgeClass }} border border-m365">{{ $user->role }}</span>
        </td>
        <td>
            <span class="{{ $isOnline ? 'status-online' : 'status-offline' }}"></span>
            <span class="small">{{ $isOnline ? 'Online' : 'Offline' }}</span>
        </td>
        <td class="text-center">
            <button class="btn btn-sm btn-outline-info btn-view-detail" data-id="{{ $user->id }}" title="View Detail">
                <i class="bi bi-eye"></i>
            </button>
        </td>
    </tr>
@endforeach

@if($users->isEmpty())
    <tr>
        <td colspan="6" class="text-center py-5 text-secondary">
            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
            <p class="mt-2 mb-0">No users found.</p>
        </td>
    </tr>
@endif

<style>
    .status-online {
        width: 10px;
        height: 10px;
        background-color: #92c353;
        border-radius: 50%;
        display: inline-block;
        margin-right: 6px;
    }
    .status-offline {
        width: 10px;
        height: 10px;
        background-color: #d1d1d1;
        border-radius: 50%;
        display: inline-block;
        margin-right: 6px;
    }
    .text-m365-blue {
        color: #0078d4;
    }
    .border-m365 {
        border-color: #d1d1d1 !important;
    }
</style>
