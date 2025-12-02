<x-layouts.app>
    <div class="jackpot-history-container">
        <div class="history-header">
            <h1>{{ __('История игр')}}</h1>
        </div>

        <div class="history-table">
            <table>
                <thead>
                    <tr>
                        <th>Game ID</th>
                        <th>{{ __('Банк')}}</th>
                        <th>{{ __('Победитель')}}</th>
                        <th>{{ __('Шанс')}}</th>
                        <th>{{ __('Дата')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($games as $game)
                        <tr>
                            <td>{{ $game->game_id }}</td>
                            <td>{{ number_format($game->price, 2) }}₼</td>
                            <td>
                                <div class="winner-info">
                                    <img src="{{ $game->winner?->avatar }}" alt="" class="winner-avatar">
                                    <span>{{ $game->winner?->username }}</span>
                                </div>
                            <td>{{ number_format($game->winner_chance, 2) }}%</td>
                            <td>{{ $game->created_at->format('d.m.Y H:i:s') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $games->links('pagination::bootstrap-4') }}
    </div>

    <style>
    .jackpot-history-container {
        padding: 20px;
        color: #fff;
    }

    .history-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .history-header h1 {
        font-size: 24px;
        font-weight: 500;
        color: #fff;
    }

    .room-select {
        background: #1a1d24;
        border: 1px solid #2a2e35;
        color: #fff;
        padding: 8px 16px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
    }

    .history-table {
        background: #1a1d24;
        border-radius: 8px;
        overflow: auto;
    }

    .history-table table {
        width: 100%;
        border-collapse: collapse;
    }

    .history-table th {
        background: #242830;
        padding: 12px 16px;
        text-align: left;
        font-weight: 500;
        color: #9ba0a8;
        font-size: 14px;
    }

    .history-table td {
        padding: 12px 16px;
        border-bottom: 1px solid #242830;
        color: #fff;
        font-size: 14px;
    }

    .winner-info {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .winner-avatar {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        object-fit: cover;
    }

    .history-table tr:hover {
        background: #242830;
    }

    /* Стили для пагинации */
    .pagination {
        margin-top: 20px;
        display: flex;
        justify-content: center;
        gap: 8px;
    }

    .pagination .page-item {
        list-style: none;
    }

    .pagination .page-link {
        background: #1a1d24;
        border: 1px solid #2a2e35;
        color: #fff;
        padding: 8px 12px;
        border-radius: 4px;
        text-decoration: none;
        font-size: 14px;
    }

    .pagination .page-item.active .page-link {
        background: #0095ff;
        border-color: #0095ff;
    }

    .pagination .page-link:hover {
        background: #242830;
    }
    </style>

</x-layouts.app>
