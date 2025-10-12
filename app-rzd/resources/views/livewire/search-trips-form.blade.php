<div>
    <form wire:submit.prevent="search">
        <div class="search-container">
            <div class="search-input-group">
                <input type="text" wire:model="from_city" placeholder="ОТКУДА" class="search-input-field">
                @error('from_city') <span class="error-text">{{ $message }}</span> @enderror
            </div>
            <div class="search-input-group">
                <input type="text" wire:model="to_city" placeholder="КУДА" class="search-input-field">
                @error('to_city') <span class="error-text">{{ $message }}</span> @enderror
            </div>
            <div class="search-input-group">
                <input type="date" wire:model="date" placeholder="ВЫЕЗД" class="search-input-field">
                @error('date') <span class="error-text">{{ $message }}</span> @enderror
            </div>
            <div class="search-passengers-container">
                <button type="button" wire:click="decrementPassengers" class="search-passenger-button">
                    -
                </button>
                <span class="search-passenger-count">{{ $passenger_count }}</span>
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