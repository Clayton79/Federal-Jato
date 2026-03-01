import React, { useState, useEffect } from 'react'
import { createDespesa, getAllDespesas, deleteDespesa, getTotalDespesas } from '../services/storageService'

export default function Despesas() {
  const [descricao, setDescricao] = useState('')
  const [valor, setValor] = useState('')
  const [data, setData] = useState(new Date().toISOString().split('T')[0])
  const [erro, setErro] = useState('')
  const [sucesso, setSucesso] = useState('')
  const [despesas, setDespesas] = useState([])
  const [page, setPage] = useState(1)

  const porPagina = 10

  const loadDespesas = () => {
    const dados = getAllDespesas()
    setDespesas(dados.reverse())
  }

  useEffect(() => {
    loadDespesas()
  }, [])

  const handleAddDespesa = (e) => {
    e.preventDefault()
    setErro('')
    setSucesso('')

    try {
      if (!descricao || !valor || !data) {
        setErro('Preencha todos os campos obrigatórios.')
        return
      }

      createDespesa({
        descricao,
        valor,
        data
      })

      setSucesso('Despesa adicionada com sucesso!')
      setDescricao('')
      setValor('')
      setData(new Date().toISOString().split('T')[0])

      setTimeout(() => setSucesso(''), 3000)
      loadDespesas()
    } catch (err) {
      setErro(err.message)
    }
  }

  const handleDeleteDespesa = (id) => {
    if (confirm('Tem certeza que deseja excluir esta despesa?')) {
      try {
        deleteDespesa(id)
        loadDespesas()
      } catch (err) {
        setErro(err.message)
      }
    }
  }

  const paginadas = despesas.slice((page - 1) * porPagina, page * porPagina)
  const totalPages = Math.ceil(despesas.length / porPagina)
  const total = getTotalDespesas()

  return (
    <div className="container">
      <div className="header-page">
        <h1>Gestão de Despesas</h1>
        <p>Acompanhe suas despesas operacionais</p>
      </div>

      {erro && <div className="alert erro">{erro}</div>}
      {sucesso && <div className="alert ok">{sucesso}</div>}

      <div className="cards-resumo">
        <div className="card-resumo">
          <div className="resumo-label">Total de Despesas</div>
          <div className="resumo-valor">R$ {total.toFixed(2).replace('.', ',')}</div>
          <div className="resumo-desc">{despesas.length} registros</div>
        </div>
      </div>

      <section className="card">
        <div className="card-header">➕ Nova Despesa</div>
        <div className="card-body">
          <form onSubmit={handleAddDespesa} className="form-grid">
            <input 
              type="text" 
              value={descricao}
              onChange={(e) => setDescricao(e.target.value)}
              className="input-clean"
              placeholder="Descrição"
              style={{ gridColumn: '1/-1' }}
              required
            />

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
              type="date" 
              value={data}
              onChange={(e) => setData(e.target.value)}
              className="input-clean"
              required
            />

            <div style={{ gridColumn: '1/-1', display: 'flex', gap: '10px' }}>
              <button type="submit" className="btn primario">Salvar</button>
              <button 
                type="button" 
                className="btn neutro"
                onClick={() => {
                  setDescricao('')
                  setValor('')
                  setData(new Date().toISOString().split('T')[0])
                }}
              >
                Limpar
              </button>
            </div>
          </form>
        </div>
      </section>

      <section className="card">
        <div className="card-header">Histórico de Despesas</div>
        <div className="card-body">
          <div className="tabela-wrap">
            <table className="tabela">
              <thead>
                <tr>
                  <th>Data</th>
                  <th>Descrição</th>
                  <th>Valor</th>
                  <th>Ações</th>
                </tr>
              </thead>
              <tbody>
                {paginadas.length === 0 ? (
                  <tr>
                    <td colSpan="4" style={{ textAlign: 'center', color: '#9ca3af', padding: '22px' }}>
                      Nenhuma despesa cadastrada.
                    </td>
                  </tr>
                ) : (
                  paginadas.map(d => (
                    <tr key={d.id}>
                      <td>{new Date(d.data).toLocaleDateString('pt-BR')}</td>
                      <td>{d.descricao}</td>
                      <td>R$ {Number(d.valor).toFixed(2).replace('.', ',')}</td>
                      <td className="acoes">
                        <button 
                          className="acao delete"
                          onClick={() => handleDeleteDespesa(d.id)}
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
      </section>
    </div>
  )
}
