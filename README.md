<div align="center" style="width: 100%;"><p align="center"><img src="https://doutorie.com.br/wp-content/plugins/phastpress/phast.php/c2VydmljZT1pbWFnZXMmc3JjPWh0dHBzJTNBJTJGJTJGZG91dG9yaWUuY29tLmJyJTJGd3AtY29udGVudCUyRnVwbG9hZHMlMkYyMDIwJTJGMDUlMkZsb2dvLWRvdXRvcmllLnBuZyZjYWNoZU1hcmtlcj0xNzE1NjA3NTYzLTYyMTMmdG9rZW49OTExZDExODBmZjM5MmNiZg.q.png" alt="Doutor-IE"style="width: 150px;" /></p></div>
<br><br>

<div style="display: inline-flex; gap: 8px; text-align: center;">
    <img src="https://img.shields.io/badge/php-%23777BB4.svg?style=for-the-badge&logo=php&logoColor=white" alt="PHP" style="width: 80px;" />
    <img src="https://img.shields.io/badge/laravel-%23FF2D20.svg?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel" style="width: 80px;" />
    <img src="https://img.shields.io/badge/JWT-black?style=for-the-badge&logo=JSON%20web%20tokens" alt="Jwt" style="width: 80px;" />
</div>

## Proposta

Criar uma API RESTful de cadastro de livros e índices/sumário. O usuário logado que realizar o cadastro será o publicador do livro.

## Requisitos

- A API deverá ser feita em php usando Laravel,
- Deverá persistir os dados no banco PostgreSQL ou MySQL
- Criar rotas para cadastro de livros conforme documentação abaixo
- Criar testes unitários de cada operação
- As rotas devem ser autenticadas.
- Publicar no github

## Diferencial

Criar o ambiente usando docker e se preferir usar docker-compose também para os serviços do php e banco de dados.

## Tabelas

- usuarios: padrão que vem no laravel
- livros: id, usuario_publicador_id , titulo
- indices: id, livro_id, indice_pai_id, titulo, pagina

## Rotas

[POST] - v1/auth/token - recuperar token de acesso para o usuário poder acessar outras rotas

[GET] - v1/livros - listar livros

#### Query Params:

- descricao: filtrar por título do livro
- titulo_do_indice: retornar livro que possui o índice com o título pesquisado juntamente com os seus ascendentes, quando houver

#### Response

```json
[
  {
    "titulo": "exemplo",
    "usuario_publicador": {
      "id": 1,
      "nome": "Bill"
    },
    "indices": [
      {
        "id": 1,
        "titulo": "Alfa",
        "pagina": 2,
        "subindices": [
          {
            "id": 2,
            "titulo": "Beta",
            "pagina": 3,
            "subindices": [
              {
                "id": 3,
                "titulo": "Gama",
                "pagina": 3,
                "subindices": []
              }
            ]
          }
        ]
      },
      {
        "id": 4,
        "titulo": "Delta",
        "pagina": 4,
        "subindices": []
      }
    ]
  }
]
```

Se pesquisar pelo índice `Beta` deve retornar o seguinte resultado:

```json
[
  {
    "titulo": "exemplo",
    "usuario_publicador": {
      "id": 1,
      "nome": "Bill"
    },
    "indices": [
      {
        "id": 1,
        "titulo": "Alfa",
        "pagina": 2,
        "subindices": [
          {
            "id": 2,
            "titulo": "Beta",
            "pagina": 3,
            "subindices": []
          }
        ]
      }
    ]
  }
]
```

[POST] - v1/livros - cadastrar livro

#### Request body

```json
{
  "titulo": "exemplo",
  "indices": [
    {
      "titulo": "indice 1",
      "pagina": 2,
      "subindices": [
        {
          "titulo": "indice 1.1",
          "pagina": 3,
          "subindices": []
        }
      ]
    },
    {
      "titulo": "indice 2",
      "pagina": 4,
      "subidices": []
    }
  ]
}
```

obs: validar a estrutura do `request body`

[POST] - v1/livros/{livroId}/importar-indices-xml - importar índices em XML

#### Criar Job para a importação do XML

```xml
<indice>
    <item pagina="1" titulo="Seção 1">
        <item pagina="1" titulo="Seção 1.1">
            <item pagina="1" titulo="Seção 1.1.1" />
            <item pagina="1" titulo="Seção 1.1.2" />
        </item>
        <item pagina="2" titulo="Seção 1.2" />
    </item>
    <item pagina="2" titulo="Seção 2" />
    <item pagina="3" titulo="Seção 3" />
</indice>
```
