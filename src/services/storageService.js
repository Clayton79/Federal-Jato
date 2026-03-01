export const STORAGE_KEYS = {
  USERS: 'federal_jato_users',
  CURRENT_USER: 'federal_jato_current_user',
  COMANDAS: 'federal_jato_comandas',
  DESPESAS: 'federal_jato_despesas',
  HISTORICO: 'federal_jato_historico'
};


export const createUser = (userData) => {
  const users = getAllUsers();
  

  if (users.find(u => u.email === userData.email)) {
    throw new Error('E-mail já cadastrado.');
  }

  const newUser = {
    id: Date.now(),
    nome: userData.nome,
    sobrenome: userData.sobrenome,
    email: userData.email,
    celular: userData.celular || '',
    senha: userData.senha,
    data_nascimento: userData.data_nascimento || null,
    criado_em: new Date().toISOString()
  };

  users.push(newUser);
  localStorage.setItem(STORAGE_KEYS.USERS, JSON.stringify(users));
  
  return newUser;
};

export const getAllUsers = () => {
  const data = localStorage.getItem(STORAGE_KEYS.USERS);
  return data ? JSON.parse(data) : [];
};

export const loginUser = (email, senha) => {
  const users = getAllUsers();
  const user = users.find(u => u.email === email && u.senha === senha);
  
  if (!user) {
    throw new Error('E-mail ou senha inválidos.');
  }

  const currentUser = {
    id: user.id,
    nome: user.nome,
    email: user.email
  };

  localStorage.setItem(STORAGE_KEYS.CURRENT_USER, JSON.stringify(currentUser));
  return currentUser;
};

export const logoutUser = () => {
  localStorage.removeItem(STORAGE_KEYS.CURRENT_USER);
};

export const getCurrentUser = () => {
  const data = localStorage.getItem(STORAGE_KEYS.CURRENT_USER);
  return data ? JSON.parse(data) : null;
};

export const isUserLoggedIn = () => {
  return getCurrentUser() !== null;
};

export const createComanda = (comandaData) => {
  const comandas = getAllComandas();

  const newComanda = {
    id: Date.now(),
    data_hora: comandaData.data_hora,
    veiculo: comandaData.veiculo,
    servico: comandaData.servico,
    valor: parseFloat(comandaData.valor),
    pagamento: comandaData.pagamento,
    situacao: 'andamento',
    obs: comandaData.obs || '',
    criado_em: new Date().toISOString()
  };

  comandas.push(newComanda);
  localStorage.setItem(STORAGE_KEYS.COMANDAS, JSON.stringify(comandas));
  addToHistorico('comanda', 'criada', newComanda)
  return newComanda;
};

export const getAllComandas = () => {
  const data = localStorage.getItem(STORAGE_KEYS.COMANDAS);
  return data ? JSON.parse(data) : [];
};

export const getComandaById = (id) => {
  const comandas = getAllComandas();
  return comandas.find(c => c.id === id);
};

export const updateComanda = (id, updates) => {
  const comandas = getAllComandas();
  const index = comandas.findIndex(c => c.id === id);
  
  if (index === -1) throw new Error('Comanda não encontrada');
  
  const oldComanda = { ...comandas[index] };
  comandas[index] = { ...comandas[index], ...updates };
  localStorage.setItem(STORAGE_KEYS.COMANDAS, JSON.stringify(comandas));
  
  if (updates.situacao && updates.situacao !== oldComanda.situacao) {
    addToHistorico('comanda', `situação alterada para ${updates.situacao}`, comandas[index]);
  }
  
  return comandas[index];
};

export const deleteComanda = (id) => {
  const comandas = getAllComandas();
  const comanda = comandas.find(c => c.id === id);
  
  if (!comanda) throw new Error('Comanda não encontrada');
  
  const filtered = comandas.filter(c => c.id !== id);
  localStorage.setItem(STORAGE_KEYS.COMANDAS, JSON.stringify(filtered));
  
  addToHistorico('comanda', 'excluída', comanda);
  
  return true;
};

export const getComandasByStatus = (status) => {
  const comandas = getAllComandas();
  if (status === 'todas') return comandas;
  return comandas.filter(c => c.situacao === status);
};

export const createDespesa = (despesaData) => {
  const despesas = getAllDespesas();

  const newDespesa = {
    id: Date.now(),
    data: despesaData.data,
    descricao: despesaData.descricao,
    valor: parseFloat(despesaData.valor),
    criado_em: new Date().toISOString()
  };

  despesas.push(newDespesa);
  localStorage.setItem(STORAGE_KEYS.DESPESAS, JSON.stringify(despesas));
  addToHistorico('despesa', 'criada', newDespesa)
  return newDespesa;
};

export const getAllDespesas = () => {
  const data = localStorage.getItem(STORAGE_KEYS.DESPESAS);
  return data ? JSON.parse(data) : [];
};

export const getDespesaById = (id) => {
  const despesas = getAllDespesas();
  return despesas.find(d => d.id === id);
};

export const deleteDespesa = (id) => {
  const despesas = getAllDespesas();
  const despesa = despesas.find(d => d.id === id);
  
  if (!despesa) throw new Error('Despesa não encontrada');
  
  const filtered = despesas.filter(d => d.id !== id);
  localStorage.setItem(STORAGE_KEYS.DESPESAS, JSON.stringify(filtered));
  
  addToHistorico('despesa', 'excluída', despesa);
  
  return true;
};

export const getTotalDespesas = () => {
  const despesas = getAllDespesas();
  return despesas.reduce((sum, d) => sum + d.valor, 0);
};

export const addToHistorico = (tipo, acao, dados) => {
  const historico = getAllHistorico();

  const novoRegistro = {
    id: Date.now(),
    tipo,
    acao,
    dados,
    usuario: getCurrentUser()?.nome || 'Usuário',
    data: new Date().toISOString()
  };

  historico.push(novoRegistro);
  localStorage.setItem(STORAGE_KEYS.HISTORICO, JSON.stringify(historico));
  
  return novoRegistro;
};

export const getAllHistorico = () => {
  const data = localStorage.getItem(STORAGE_KEYS.HISTORICO);
  return data ? JSON.parse(data) : [];
};

export const clearHistorico = () => {
  localStorage.removeItem(STORAGE_KEYS.HISTORICO);
};

export const clearAllData = () => {
  Object.values(STORAGE_KEYS).forEach(key => {
    localStorage.removeItem(key);
  });
};

export const initializeSampleData = () => {
  if (getAllUsers().length > 0) return;

  const sampleUser = {
    nome: 'João',
    sobrenome: 'Silva',
    email: 'demo@example.com',
    celular: '11999999999',
    senha: '123456',
    data_nascimento: '1990-01-01'
  };

  createUser(sampleUser);
};
