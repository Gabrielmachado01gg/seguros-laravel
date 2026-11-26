# Cuidados com dados pessoais (LGPD)

O sistema guarda dados pessoais de clientes (nome, CPF, e-mail e telefone). Medidas adotadas:

- **Criptografia em repouso**: o CPF é gravado criptografado (`encrypted` cast do Laravel, usando `APP_KEY`).
- **Minimização**: a API e o painel mostram o CPF mascarado (`***.123.456-**`); o número completo não sai do banco.
- **Sem duplicidade sem exposição**: a checagem de CPF repetido usa um HMAC-SHA256 (`cpf_hash`), não o dado em claro.
- **Acesso controlado**: a API exige chave (`X-API-Key`) e o painel exige login (HTTP Basic).
- **Logs enxutos**: a tabela `notificacoes` guarda só canal, status e mensagem de erro, sem conteúdo das mensagens.
- **Exclusão**: remover um cliente apaga em cascata suas apólices e notificações, o que atende pedidos de eliminação de dados.

Pontos que ficariam para uma versão de produção: política de retenção, registro de consentimento e trilha de auditoria de acessos.
