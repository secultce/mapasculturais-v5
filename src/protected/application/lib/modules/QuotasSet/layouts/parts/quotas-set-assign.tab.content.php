<style>
    #atribuir .search-input {
        padding: .5rem;
        margin-top: .6rem;
        min-height: 3rem;
        min-width: 50rem;
        width: fit-content;
        text-align: left;
        vertical-align: text-top;
        border-radius: .3rem;
        border: 1px solid #bbbbbb;
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        gap: .4rem .8rem;
        align-items: flex-start;
    }

    .input-container {
        display: flex;
        flex-direction: row;
        align-items: flex-end;
    }
    .search-input {
    }
    #search-input {
        border: none;
        display: inline-block;
        outline: none;
        width: 100%;
        position: relative;
    }

    .clear-button {
        border: none;
        background: transparent;
        font-weight: bold;
        font-size: 1rem;
        margin-left: 1rem;
        color: #0b0b0b;
        cursor: pointer;
        text-align: left;
    }

    .input-entry {
        background: #E8E8E8;
        border-radius: .3rem;
        font-size: 1rem;
        color: #0b0b0b;
        padding: .4rem;
    }
</style>

<div id="atribuir">
    <label for="search-input" style="font-weight: bold; font-size: 1rem">Busque por agentes culturais</label>
    <p>Você pode buscar um agente individualmente ou utilizar vários CPFs e nomes para encontrá-los</p>

    <label for="search-input" class="input-container">
        <span class="search-input">
            <input id="search-input" placeholder="Busque por CPF ou nome" width="100%" style="border: none; outline: none" />
        </span>

        <button class="clear-button">Limpar pesquisas</button>
    </label>
    <input type="hidden" name="search-values" value="[]">

    <div id="agent-results" style="display: block; margin-top: 2rem">
        <h4>Lista de agentes cotistas</h4>
        <hr>
        <div class="agentes-list">
            <p>Ainda não possuem agentes culturais com cotas atribuídas.</p>
            <p><strong>Utilize a busca para encontrar agentes culturais</strong></p>
        </div>
    </div>
</div>

<script>
    const searchInput = document.getElementById('search-input');
    const clearButton = document.querySelector('.clear-button');
    const searchValues = {
        element: document.querySelector('[name=search-values]'),
        values: JSON.parse(document.querySelector('[name=search-values]').value),
        push: (value) => {
            searchValues.values.push(value);
            searchValues.element.value = JSON.stringify(searchValues.values);
            dispatchEvent(new Event('change'));
        },
        clear: () => {
            searchValues.values = [];
            searchValues.element.value = JSON.stringify(searchValues.values)
        },
        pop: () => {
            searchValues.values.pop();
            searchValues.element.value = JSON.stringify(searchValues.values);
        },
    };

    clearButton.addEventListener('click', () => {
        $('.input-entry').remove();
        searchInput.value = '';
        searchValues.clear();
    });

    searchInput.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            const inputValue = searchInput.value.trim();
            if (inputValue !== '') {
                searchInput.parentNode.insertBefore($(`<span class="input-entry">${inputValue}</span>`).each2(node => node)[0], searchInput);
                searchInput.value = '';
                searchValues.push(inputValue);
            }
        } else if (event.key === 'Backspace' && searchInput.value === '') {
            $('.input-entry').last().remove();
            searchValues.pop();
        }
    });

    searchInput.addEventListener('paste', event => {
        event.preventDefault();
        const values = (event.clipboardData || window.clipboardData).getData('text').split("\n");
        if (values.length > 1) {
            values.forEach(value => {
                if (value !== '') {
                    searchInput.parentNode.insertBefore($(`<span class="input-entry">${value}</span>`).each2(node => node)[0], searchInput);
                    searchValues.push(value)
                }
            });
        }
    })
</script>
