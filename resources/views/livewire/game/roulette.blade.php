<div>
    <x-UI.search/>
    <x-UI.filter :providers="$providers"/>

    <div id="scrollTo"></div>

    <div id="lobby">
        <div class="container">
            <div class="games flex">
                @foreach($games as $game)
                    <x-game :game="$game"/>
                @endforeach
                {{ $games->links() }}
            </div>
        </div>
    </div>
</div>
