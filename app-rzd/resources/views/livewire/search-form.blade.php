<div>
    <form wire:submit.prevent="search">
        <div class="search-container">
            <div class="search-input-group">
                <input type="text" wire:model="from" placeholder="ОТКУДА" class="search-input-field">
            </div>
            <div class="search-input-group">
                <input type="text" wire:model="to" placeholder="КУДА" class="search-input-field">
            </div>
            <div class="search-input-group">
                <input type="date" wire:model="date" placeholder="ВЫЕЗД" class="search-input-field">
            </div>
            <div class="search-passengers-container">
                <button type="button" wire:click="decrementPassengers" class="search-passenger-button">
                    -
                </button>
                <span class="search-passenger-count">{{ $passengers }}</span>
                <button type="button" wire:click="incrementPassengers" class="search-passenger-button">
                    +
                </button>
            </div>
            <button type="submit" class="search-button">
                НАЙТИ
            </button>
        </div>
    </form>
</div>