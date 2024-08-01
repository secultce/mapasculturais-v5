# Módulo RegistrationsDraw
Esse módulo adiciona a possibilidade de configurar uma oportunidade documental ou simplificada para ordenar aleatoriamente as inscrições em formato de ranking, via seleção aleatória (sorteio).

### Diagrama De Banco de Dados
```mermaid
erDiagram
    d ||..|{ dr : has
    d }o..|| opportunity : belongs
    d }o..|| usr : "created by"
    dr }o..|| registration : references

    dr[draw_registrations] {
        integer           id                           PK
        integer           draw_id                      FK
        integer           registration_id              FK
        integer           rank
    }
    d[draw] {
        integer           id                           PK
        integer           opportunity_id               FK
        text              category
        timestamp         create_timestamp
        integer           user_id                      FK
        boolean           published
    }
```


```mermaid
erDiagram
    rr }o..|| registration : references
    rr }o..|| opportunity : belongs
    rr }o..|| usr : "created by"

    rr[registrations_ranking] {
        integer           id                            PK
        integer           registration_id               FK
        integer           opportunity_id                FK
        text              category
        integer           rank
        integer           agent_id                      FK
        timestamp         create_timestamp
    }
```