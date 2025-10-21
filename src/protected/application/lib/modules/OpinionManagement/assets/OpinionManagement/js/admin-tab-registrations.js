$(document).ready(() => {
    // Promise criada para aguardar os elementos serem carregados na página
    const waitLoadRegistrationList = () => {
        return new Promise(resolve => {
            const selector = '.showOpinion'

            if(document.querySelectorAll(selector).length > 0) {
                return resolve(document.querySelectorAll(selector))
            }
        })
    }

    // Esse observer observando mudanças no DOM da tabela de inscritos
    // e chama a função que atribui o callback ao evento de clique aos botões de visualizar parecer
    const observer = new MutationObserver(mutations => {
        if(document.querySelectorAll('.showOpinion').length > 0) {
            waitLoadRegistrationList().then(showOpinionButtons => {
                for(const arrayIndex of showOpinionButtons.keys()) {

                    showOpinionButtons[arrayIndex].onclick = e => {
                        showOpinions(e.target.getAttribute('data-id'))
                    }
                }
            })
        }

        // @todo: Melhorar a validação para remover o MutationObserver caso todos os inscritos já carregaram na paǵina
        if(document.querySelectorAll("tr[id^='#registration']").length % 50 !== 0) {
            observer.disconnect()
        }
    })
    observer.observe(document.querySelector('#registrations-table'), {
        childList: true,
        subtree: true
    })
})