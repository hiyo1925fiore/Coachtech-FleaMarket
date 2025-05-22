
<div class="search-form__content">
    <form wire:submit.prevent="search" class="search-form" method="get">
        <input class="search-form__input"
            wire:model.debounce.500ms="searchTerm"
            type="text"
            name="keyword"
            placeholder="なにをお探しですか？">
    </form>
</div>