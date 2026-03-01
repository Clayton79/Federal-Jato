import React, { useState, useEffect } from 'react'
import { createComanda, getAllComandas, getComandasByStatus, deleteComanda, updateComanda } from '../services/storageService'

export default function Comandas() {
  const [veiculo, setVeiculo] = useState('')
  const [servico, setServico] = useState('')
  const [pagamento, setPagamento] = useState('')
  const [valor, setValor] = useState('')
  const [data_hora, setData_hora] = useState(new Date().toISOString().slice(0, 16))
  const [obs, setObs] = useState('')
  const [erro, setErro] = useState('')
  const [sucesso, setSucesso] = useState('')
  const [comandas, setComandas] = useState([])
  const [filtro, setFiltro] = useState('andamento')
  const [page, setPage] = useState(1)

  const porPagina = 10

  const loadComandas = () => {
    const dados = getComandasByStatus(filtro)
    setComandas(dados.reverse())
  }

  useEffect(() => {
    loadComandas()
    setPage(1)
  }, [filtro])

  const handleAddComanda = (e) => {
    e.preventDefault()
    setErro('')
    setSucesso('')

    try {
      if (!veiculo || !servico || !pagamento || !valor || !data_hora) {
        setErro('Preenchha todos os campos obrigatórios.')
        return
      }

      createComanda({
        veiculo,
        servico,
        pagamento,
        valor,
        data_hora,
        obs
      })

      setSucesso('Comanda adicionada com sucesso!')
      setVeiculo('')
      setServico('')
      setPagamento('')
      setValor('')
      setData_hora(new Date().toISOString().slice(0, 16))
      setObs('')

      setTimeout(() => setSucesso(''), 3000)
      loadComandas()
    } catch (err) {
      setErro(err.message)
    }
  }

  const handleFinalizarComanda = (id) => {
    try {
      updateComanda(id, { situacao: 'finalizada' })
      loadComandas()
    } catch (err) {
      setErro(err.message)
    }
  }

  const handleDeleteComanda = (id) => {
    if (confirm('Tem certeza que deseja excluir esta comanda?')) {
      try {
        deleteComanda(id)
        loadComandas()
      } catch (err) {
        setErro(err.message)
      }
    }
  }

  const paginadas = comandas.slice((page - 1) * porPagina, page * porPagina)
  const totalPages = Math.ceil(comandas.length / porPagina)

  return (
    <div className="container">
      <div className="header-page">
        <h1>Gestão de Comandas</h1>
        <p>Cadastro e acompanhamento de serviços</p>
      </div>

      {erro && <div className="alert erro">{erro}</div>}
      {sucesso && <div className="alert ok">{sucesso}</div>}

      <section className="card">
        <div className="card-header">➕ Nova Comanda</div>
        <div className="card-body">
          <form onSubmit={handleAddComanda} className="form-grid">
            <div className="select-box">
              <select 
                value={veiculo} 
                onChange={(e) => setVeiculo(e.target.value)}
                className="select-clean"
                required
              >
                <option value="">Veículo</option>
                <option value="carro">Carro</option>
                <option value="moto">Moto</option>
              </select>
              <span className="select-caret">▾</span>
            </div>

            <div className="select-box">
              <select 
                value={servico} 
                onChange={(e) => setServico(e.target.value)}
                className="select-clean"
                required
              >
                <option value="">Serviço</option>
                <option value="Lavagem simples">Lavagem simples</option>
                <option value="Lavagem completa">Lavagem completa</option>
                <option value="Lavagem premium">Lavagem premium</option>
                <option value="Lavagem técnica">Lavagem técnica</option>
                <option value="Lavagem a seco">Lavagem a seco</option>
                <option value="Lavagem com cera">Lavagem com cera</option>
                <option value="Lavagem de motor">Lavagem de motor</option>
                <option value="Lavagem detalhada interna">Lavagem detalhada interna</option>
                <option value="Lavagem detalhada externa">Lavagem detalhada externa</option>
                <option value="Lavagem de rodas e caixas">Lavagem de rodas e caixas</option>
                <option value="Lavagem pós-viagem">Lavagem pós-viagem</option>
              </select>
              <span className="select-caret">▾</span>
            </div>

            <div className="select-box">
              <select 
                value={pagamento} 
                onChange={(e) => setPagamento(e.target.value)}
                className="select-clean"
                required
              >
                <option value="">Pagamento</option>
                <option value="pix">Pix</option>
                <option value="cartao">Cartão</option>
                <option value="dinheiro">Dinheiro</option>
              </select>
              <span className="select-caret">▾</span>
            </div>

            <input 
              type="number" 
              step="0.01" 
              value={valor}
              onChange={(e) => setValor(e.target.value)}
              className="input-clean"
              placeholder="Valor (R$)"
              required
            />

            <input 
              type="datetime-local" 
              value={data_hora}
              onChange={(e) => setData_hora(e.target.value)}
              className="input-clean"
              required
            />

            <textarea 
              value={obs}
              onChange={(e) => setObs(e.target.value)}
              className="input-clean"
              placeholder="Observações (opcional)"
              style={{ gridColumn: '1/-1', minHeight: '80px' }}
            />

            <div style={{ gridColumn: '1/-1', display: 'flex', gap: '10px' }}>
              <button type="submit" className="btn primario">Adicionar Comanda</button>
              <button 
                type="button" 
                className="btn neutro"
                onClick={() => {
                  setVeiculo('')
                  setServico('')
                  setPagamento('')
                  setValor('')
                  setData_hora(new Date().toISOString().slice(0, 16))
                  setObs('')
                }}
              >
                Limpar
              </button>
            </div>
          </form>
        </div>
      </section>

      <div className="section-title">
        <h2>Comandas</h2>
        <div>
          <button 
            className={`btn ${filtro === 'andamento' ? 'primario' : 'neutro'}`}
            onClick={() => setFiltro('andamento')}
          >
            Andamento
          </button>
          <button 
            className={`btn ${filtro === 'finalizada' ? 'primario' : 'neutro'}`}
            onClick={() => setFiltro('finalizada')}
          >
            Finalizadas
          </button>
          <button 
            className={`btn ${filtro === 'todas' ? 'primario' : 'neutro'}`}
            onClick={() => setFiltro('todas')}
          >
            Todas
          </button>
        </div>
      </div>

      <div className="card">
        <div className="tabela-wrap">
          <table className="tabela">
            <thead>
              <tr>
                <th>Veículo</th>
                <th>Serviço</th>
                <th>Data/Hora</th>
                <th>Valor</th>
                <th>Pagamento</th>
                <th>Situação</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              {paginadas.length === 0 ? (
                <tr>
                  <td colSpan="7" style={{ textAlign: 'center', color: '#9ca3af', padding: '22px' }}>
                    Nenhuma comanda encontrada.
                  </td>
                </tr>
              ) : (
                paginadas.map(c => (
                  <tr key={c.id}>
                    <td>{c.veiculo}</td>
                    <td>{c.servico}</td>
                    <td>{new Date(c.data_hora).toLocaleString('pt-BR')}</td>
                    <td>R$ {Number(c.valor).toFixed(2).replace('.', ',')}</td>
                    <td>{c.pagamento}</td>
                    <td>
                      <span className={`badge ${c.situacao}`}>
                        {c.situacao === 'andamento' ? '⏳ Andamento' : '✅ Finalizada'}
                      </span>
                    </td>
                    <td className="acoes">
                      {c.situacao === 'andamento' && (
                        <button 
                          className="acao"
                          onClick={() => handleFinalizarComanda(c.id)}
                          title="Finalizar"
                        >
                          ✅
                        </button>
                      )}
                      <button 
                        className="acao delete"
                        onClick={() => handleDeleteComanda(c.id)}
                        title="Excluir"
                      >
                        🗑️
                      </button>
                    </td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>

        {totalPages > 1 && (
          <div className="pagination">
            <button 
              className="btn neutro" 
              onClick={() => setPage(Math.max(1, page - 1))}
              disabled={page === 1}
            >
              ← Anterior
            </button>
            <span>{page} de {totalPages}</span>
            <button 
              className="btn neutro" 
              onClick={() => setPage(Math.min(totalPages, page + 1))}
              disabled={page === totalPages}
            >
              Próximo →
            </button>
          </div>
        )}
      </div>
    </div>
  )
}
