# Federal Jato 🚗

**Sistema de Gestão para Lava-Jato** - Aplicação React moderna sem servidor backend, pronta para GitHub Pages.

## ⚡ Início Rápido

```bash
# 1. Instalar dependências
npm install

# 2. Rodar em desenvolvimento
npm run dev

# 3. Acessar no navegador
# http://localhost:5173/Federal-Jato/
```

### Login Padrão

```
Email: demo@example.com
Senha: 123456
```

## 🚀 Deploy em GitHub Pages

### Automático

```bash
npm run deploy
```

**Resultado**: App disponível em `https://seu-usuario.github.io/Federal-Jato/`

## 📋 Recursos

✅ **3 Módulos Principais**
- **Comandas**: Cadastro de serviços por veículo
- **Despesas**: Controle de gastos operacionais  
- **Histórico**: Log de todas as ações

✅ **Funcionalidades**
- Autenticação com email/senha
- Registro de novos usuários
- Persistência em localStorage
- Design responsivo (mobile/tablet/desktop)
- Interface em português
- 11 tipos de serviços de lavagem
- 3 formas de pagamento (Pix, Cartão, Dinheiro)

✅ **Sem Servidor**
- Dados armazenados no navegador (localStorage)
- Nenhum backend necessário
- Funciona offline após carregamento inicial

## 📁 Estrutura do Projeto

```
src/
├── components/
│   ├── Login.jsx         # Tela de login
│   ├── SignUp.jsx        # Criar conta
│   ├── Dashboard.jsx     # Painel principal
│   ├── Navbar.jsx        # Navegação
│   ├── Comandas.jsx      # Gestão de comandas
│   ├── Despesas.jsx      # Gestão de despesas
│   ├── Historico.jsx     # Histórico
│   ├── Auth.css          # Estilos de autenticação
│   └── Dashboard.css     # Estilos do dashboard
├── services/
│   └── storageService.js # Gerenciamento dados
├── App.jsx               # Componente raiz
├── main.jsx              # Entrada
├── App.css               # Estilos globais
└── index.css             # Reset de estilos

public/
├── criacao_de_login.jpeg # Imagem de login
└── .nojekyll             # Config GitHub Pages

vite.config.js            # Configuração
package.json              # Dependências
LICENSE                   # Licença
```

## 🛠️ Comandos

| Comando | Descrição |
|---------|-----------|
| `npm install` | Instala dependências |
| `npm run dev` | Servidor desenvolvimento |
| `npm run build` | Build produção |
| `npm run deploy` | Build + deploy GitHub Pages |

## 💾 Dados

### Usuários
- Nome, sobrenome, email, celular, data de nascimento

### Comandas
- Veículo (carro/moto)
- Serviço (11 opções)
- Pagamento (pix/cartão/dinheiro)
- Valor e data/hora
- Situação (andamento/finalizada)

### Despesas
- Data, descrição, valor

### Histórico
- Log de todas as ações

## 🎨 Cores

| Cor | Uso |
|-----|-----|
| `#4c5fd5` | Azul primário |
| `#3d4ec4` | Azul gradiente |
| `#fe6201` | Laranja (accent) |
| `#fef3c7` | Amarelo (em andamento) |
| `#dcfce7` | Verde (finalizado/sucesso) |

## 📱 Responsivo

- ✅ Desktop (1200px+)
- ✅ Tablet (768px - 1199px)
- ✅ Mobile (até 767px)

## 🔧 Tecnologias

- **React** 18.x
- **Vite** 4.x
- **CSS3**
- **localStorage**
- **GitHub Pages**

## ⚠️ Importante

- **localStorage**: Dados salvos no navegador
- **Sem backend**: 100% client-side
- **Offline**: Funciona offline após carregamento
- **URL**: Use `https://seu-usuario.github.io/Federal-Jato/`

## 🐛 Problemas Comuns

### Página em branco no GitHub Pages
- Verifique URL com `/Federal-Jato/`
- Limpe cache (Ctrl+Shift+Delete)
- Aguarde 1-2 min após push

### Dados não persistem
- Ative localStorage
- Não use modo privado
- Limpe cookies

### Build falha
```bash
rm -rf node_modules package-lock.json
npm install
npm run build
```

## 📄 Licença

Veja [LICENSE](./LICENSE)

---

**Desenvolvido com React + Vite**
