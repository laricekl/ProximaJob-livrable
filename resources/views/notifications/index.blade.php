@extends('layouts.candidat')

@section('title', 'Notifications')

@php
  $isPaginatedNotifications = method_exists($notifications, 'items');
  $notificationItems = $isPaginatedNotifications ? collect($notifications->items()) : collect($notifications);
  $unreadCount = $notificationItems->where('is_read', false)->count();
  $todayNotifications = $notificationItems->filter(fn ($notification) => $notification->created_at?->isToday());
  $yesterdayNotifications = $notificationItems->filter(fn ($notification) => $notification->created_at?->isYesterday());
  $olderNotifications = $notificationItems->reject(fn ($notification) => $notification->created_at?->isToday() || $notification->created_at?->isYesterday());

  $notificationMeta = function ($type) {
      return match ($type) {
          'matching' => ['icon' => 'auto_awesome', 'iconBg' => 'bg-secondary-container/10', 'iconColor' => 'text-secondary-container', 'badge' => 'Matching IA'],
          'application' => ['icon' => 'send', 'iconBg' => 'bg-info-light', 'iconColor' => 'text-info', 'badge' => 'Candidatures'],
          'message' => ['icon' => 'chat', 'iconBg' => 'bg-success-light', 'iconColor' => 'text-success', 'badge' => 'Messages'],
          default => ['icon' => 'info', 'iconBg' => 'bg-surface-container', 'iconColor' => 'text-outline', 'badge' => 'Système'],
      };
  };
@endphp

@section('styles')
  <style>
    .notif-item { transition: all 0.2s ease; }
    .notif-item.unread { background: rgba(var(--pj-accent-rgb),0.03); border-left: 3px solid var(--pj-accent); }
    .filter-pill { transition: all 0.2s ease; }
    .filter-pill.active { background: var(--pj-accent); color: white; border-color: var(--pj-accent); }
  </style>
@endsection

@section('content')
  <main class="flex-grow pt-32 pb-16">
    <a href="{{ route('user.home') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-outline hover:text-primary transition-colors mb-6"><section class="px-4 md:px-10">larr; Retour au tableau de bord</a>
      <section class="px-4 md:px-10">
      <div class="max-w-4xl mx-auto">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
          <div>
            <h1 class="text-2xl font-bold font-serif text-primary">Notifications</h1>
            <p class="text-sm text-on-surface-variant mt-1"><span id="unreadCount">{{ $unreadCount }}</span> non lues</p>
          </div>
          <button id="markAllRead" type="button" class="text-sm font-bold text-secondary-container hover:underline flex items-center gap-1.5 self-start">
            <span class="material-symbols-outlined text-lg">done_all</span> Tout marquer comme lu
          </button>
        </div>

        <div class="flex items-center gap-2 mb-6 overflow-x-auto pb-2">
          <button type="button" class="filter-pill active px-4 py-2 rounded-full text-sm font-semibold whitespace-nowrap bg-white border border-outline-variant/20" data-filter="all">Toutes</button>
          <button type="button" class="filter-pill px-4 py-2 rounded-full text-sm font-semibold whitespace-nowrap bg-white border border-outline-variant/20 text-outline" data-filter="matching">Matching IA</button>
          <button type="button" class="filter-pill px-4 py-2 rounded-full text-sm font-semibold whitespace-nowrap bg-white border border-outline-variant/20 text-outline" data-filter="application">Candidatures</button>
          <button type="button" class="filter-pill px-4 py-2 rounded-full text-sm font-semibold whitespace-nowrap bg-white border border-outline-variant/20 text-outline" data-filter="message">Messages</button>
          <button type="button" class="filter-pill px-4 py-2 rounded-full text-sm font-semibold whitespace-nowrap bg-white border border-outline-variant/20 text-outline" data-filter="system">Système</button>
        </div>

        <div id="notifList" class="space-y-6">
          @forelse ([
            'Aujourd\'hui' => $todayNotifications,
            'Hier' => $yesterdayNotifications,
            'Plus anciennes' => $olderNotifications,
          ] as $sectionTitle => $sectionNotifications)
            @continue($sectionNotifications->isEmpty())

            <div class="space-y-2 notification-section">
              <p class="text-xs font-bold text-outline uppercase tracking-wider pt-2 pb-2">{{ $sectionTitle }}</p>

              @foreach ($sectionNotifications as $notification)
                @php($meta = $notificationMeta($notification->type))
                <article class="notif-item {{ $notification->is_read ? '' : 'unread' }} flex items-start gap-4 p-4 bg-white/70 backdrop-blur-sm rounded-2xl border border-outline-variant/10" data-category="{{ $notification->type ?: 'system' }}" data-read="{{ $notification->is_read ? '1' : '0' }}" data-id="{{ $notification->id }}">
                  <div class="w-10 h-10 rounded-xl {{ $meta['iconBg'] }} flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined {{ $meta['iconColor'] }}">{{ $meta['icon'] }}</span>
                  </div>

                  <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                      <span class="font-bold text-sm text-primary">{{ $notification->title }}</span>
                      @unless ($notification->is_read)
                        <span class="w-2 h-2 rounded-full bg-secondary-container flex-shrink-0 unread-dot"></span>
                      @endunless
                      <span class="px-2 py-0.5 rounded-full bg-surface-container-low text-2xs font-bold uppercase tracking-wider text-outline">{{ $meta['badge'] }}</span>
                    </div>
                    <p class="text-sm text-on-surface-variant">{{ $notification->message }}</p>
                    <div class="flex flex-wrap items-center gap-3 mt-3">
                      <span class="text-xs text-outline">{{ $notification->created_at?->diffForHumans() }}</span>
                      @if ($notification->link)
                        <a href="{{ $notification->link }}" class="text-xs font-bold text-secondary-container hover:underline">Ouvrir</a>
                      @endif
                    </div>
                  </div>

                  @unless ($notification->is_read)
                    <button type="button" class="mark-read flex-shrink-0 w-8 h-8 rounded-full hover:bg-surface-container-low flex items-center justify-center transition-colors" data-id="{{ $notification->id }}" title="Marquer comme lu">
                      <span class="material-symbols-outlined text-outline text-lg">check</span>
                    </button>
                  @endunless
                </article>
              @endforeach
            </div>
          @empty
            <div id="emptyState" class="text-center py-16">
              <span class="material-symbols-outlined text-6xl text-outline mb-4">notifications_off</span>
              <p class="text-lg font-bold font-serif text-primary mb-2">Aucune notification</p>
              <p class="text-sm text-on-surface-variant">Vous êtes à jour ! Les notifications apparaîtront ici.</p>
            </div>
          @endforelse
        </div>

        @if ($isPaginatedNotifications && $notifications->hasPages())
          <div class="mt-8">
            {{ $notifications->links() }}
          </div>
        @endif
      </div>
    </section>
  </main>
@endsection

@section('scripts')
  <script>
    (() => {
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      const unreadCountNode = document.getElementById('unreadCount');
      const filterButtons = [...document.querySelectorAll('.filter-pill')];
      const notificationItems = [...document.querySelectorAll('.notif-item')];
      const markAllButton = document.getElementById('markAllRead');

      const updateUnreadCount = () => {
        const unread = document.querySelectorAll('.notif-item[data-read="0"]').length;
        if (unreadCountNode) unreadCountNode.textContent = unread;
      };

      const markCardAsRead = (card) => {
        if (!card) return;
        card.dataset.read = '1';
        card.classList.remove('unread');
        card.querySelector('.unread-dot')?.remove();
        card.querySelector('.mark-read')?.remove();
        updateUnreadCount();
      };

      const postJson = async (url) => {
        const response = await fetch(url, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
          },
        });

        if (!response.ok) throw new Error('Request failed');
        return response.json();
      };

      filterButtons.forEach((button) => {
        button.addEventListener('click', () => {
          const filter = button.dataset.filter;

          filterButtons.forEach((pill) => pill.classList.remove('active'));
          button.classList.add('active');

          notificationItems.forEach((item) => {
            const shouldShow = filter === 'all' || item.dataset.category === filter;
            item.style.display = shouldShow ? '' : 'none';
          });

          document.querySelectorAll('.notification-section').forEach((section) => {
            const visibleItems = [...section.querySelectorAll('.notif-item')].some((item) => item.style.display !== 'none');
            section.style.display = visibleItems ? '' : 'none';
          });
        });
      });

      document.querySelectorAll('.mark-read').forEach((button) => {
        button.addEventListener('click', async () => {
          const card = button.closest('.notif-item');

          try {
            await postJson('{{ url('/notifications') }}/' + button.dataset.id + '/mark-as-read');
            markCardAsRead(card);
          } catch (error) {
            console.error(error);
          }
        });
      });

      markAllButton?.addEventListener('click', async () => {
        try {
          await postJson('{{ url('/notifications/mark-all-read') }}');
          document.querySelectorAll('.notif-item[data-read="0"]').forEach(markCardAsRead);
        } catch (error) {
          console.error(error);
        }
      });
    })();
  </script>
@endsection
