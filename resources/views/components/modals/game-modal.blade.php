<div id="game-modal" data-modal="game-modal" class="modal fixed inset-0 z-50 hidden flex items-center justify-center bg-dark-950/80 backdrop-blur-sm" style="display: none;">
    <div class="playgame-content bg-dark-900 rounded-xl overflow-hidden shadow-2xl max-w-lg w-full mx-4 border border-dark-800 relative">
        <span class="close absolute top-3 right-3 text-white bg-dark-800/80 hover:bg-primary rounded-full w-10 h-10 flex items-center justify-center transition-colors cursor-pointer z-10" onclick="closeModalGame()">
            <i class="fa-solid fa-xmark text-lg"></i>
        </span>

        <div class="game-header relative">
            <div class="game-img">
                <img id="modal-game-image" src="" alt="game image" class="w-full h-64 object-cover">
                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-dark-950 to-transparent h-1/2"></div>
            </div>

            <div class="game-desc absolute bottom-0 left-0 w-full p-6 text-white">
                <h4 id="modal-game-name" class="text-2xl font-bold mb-1"></h4>
                <span id="modal-game-provider" class="text-gray-300 text-sm"></span>
                <div class="like-buttons mt-2">
                    @livewire('game.game-modal-component', ['gameId' => ''], key('game-modal-component'))
                </div>
            </div>
        </div>

        <div class="game-buttons flex gap-4 p-6">
            @auth
                <a id="modal-play-link" href="#" class="flex-1 py-3 px-4 bg-gradient-to-r from-primary to-primary-dark text-white font-medium rounded-lg text-center transition-colors hover:shadow-glow">
                    <i class="fa-solid fa-play mr-2"></i> {{__('Играть')}}
                </a>
            @else
                <a href="#" class="signup-btn flex-1 py-3 px-4 bg-gradient-to-r from-primary to-primary-dark text-white font-medium rounded-lg text-center transition-colors hover:shadow-glow" onclick="openModal('register-modal')">
                    <i class="fa-solid fa-sign-in-alt mr-2"></i> {{__('Вход')}}
                </a>
            @endauth

            <a id="demo-play-link" href="#" class="demobutton flex-1 py-3 px-4 bg-dark-700 hover:bg-dark-600 text-white font-medium rounded-lg text-center transition-colors border border-dark-600">
                <i class="fa-solid fa-play mr-2"></i> {{__('Демо')}}
            </a>
        </div>
    </div>
</div>
