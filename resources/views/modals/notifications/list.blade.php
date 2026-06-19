@forelse($notifications as $notification)
    <a href="{{ $notification->link }}" 
       class="block px-4 py-3 border-b hover:bg-gray-50 notification-item {{ !$notification->is_read ? 'bg-blue-50' : '' }}"
       data-id="{{ $notification->id }}">
        <div class="flex justify-between">
            <p class="font-medium text-gray-800">{{ $notification->title }}</p>
            <time class="text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</time>
        </div>
        <p class="text-sm text-gray-600 mt-1">{{ $notification->message }}</p>
    </a>
@empty
    <div class="px-4 py-6 text-center text-gray-500">
        <i class="fas fa-bell-slash text-2xl mb-2"></i>
        <p>Aucune notification</p>
    </div>
@endforelse